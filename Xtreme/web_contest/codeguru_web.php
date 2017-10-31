<?php
require_once('backend.inc.php');

$MAX_SURVIVORS_AND_ZOMBIES = 32;

function format_applet($survivor_list, $zombie_list) {
    $result = "";
    
    $num_of_survivors = count($survivor_list);
    $num_of_zombies = count($zombie_list);
       
    $result .= <<<END
    <applet code="corewars.gui.CoreWarsApplet" archive="codeguru_web.jar" height="480" width="640">
			        <param name="num_of_survivors" value="$num_of_survivors" />
			        <param name="num_of_zombies" value="$num_of_zombies" />
                                
END;

    $counter = 0;

    foreach($survivor_list as $survivor_name => $survivor_code) {
        $result .= "<param name=\"survivor_code$counter\" value=\"$survivor_code\" />\n";
        $result .= "<param name=\"survivor_name$counter\" value=\"$survivor_name\" />\n";
        $counter++;
    }

    $counter = 0;
    foreach($zombie_list as $survivor_name => $survivor_code) {
        $result .= "<param name=\"zombie_code$counter\" value=\"$survivor_code\" />\n";
        $counter++;
    }    
    $result .= <<<END
    </applet>
END;
    return $result;
}

function fetch_params($received_data, $received_files) {
    global $MAX_SURVIVORS_AND_ZOMBIES, $SURVIVOR_MAX_LENGTH, $UPLOAD_ERR_OK;
    
    $result = array(array(), array());
    $used_names = array();
                              
    for($i = 0; $i < $MAX_SURVIVORS_AND_ZOMBIES; $i++) {
                
        if(!isset($received_data["survivor_name$i"]))
            continue;
        
        $survivor_zombie = isset($received_data["survivor_zombie$i"]);
        $survivor_name = $received_data["survivor_name$i"];
        if (isset($used_names[$survivor_name])) {
            $used_names[$survivor_name]++;
        } else {
            $used_names[$survivor_name] = 0;
        }
        $survivor_name = $survivor_name . $used_names[$survivor_name];
        
        if(isset($received_data["survivor_code$i"]))
            $survivor_code = $received_data["survivor_code$i"];
        else
            if(isset($received_files["survivor_code$i"])) {
                if($received_files["survivor_code$i"]['error'] != $UPLOAD_ERR_OK)
                    continue;
                $survivor_code = substr(file_get_contents($received_files["survivor_code$i"]['tmp_name']),
                                               0, $SURVIVOR_MAX_LENGTH);
                $survivor_code = bin2hex($survivor_code);
            } else
                continue;
        
        if($survivor_zombie) {
            $result[1][$survivor_name] = $survivor_code;
        } else {
            $result[0][$survivor_name] = $survivor_code;
        }
    }
    
    return $result;
}

function main($received_data, $received_files) {
    do {
        $result = database_connect();        
        if(!$result->success)
            break;
        
        $survivor_array = fetch_params($received_data, $received_files);
        $result_str = format_applet($survivor_array[0], $survivor_array[1]);
        $result = new Result(true, $result_str);
    } while(false);
    return $result;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>CodeGuru Web Constest</title>
</head>
<body style="font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10pt;background-color: #072C35;color: #D6E5B0;">
    <h1 style="text-align:center;">CodeGuru Applet!</h1>
    <br />
    <?php
        $result = main($_POST, $_FILES);
        print($result->public_payload);
    ?>
</body>
</html>
