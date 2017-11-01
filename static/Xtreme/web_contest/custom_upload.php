<?php
require_once('backend.inc.php');

function decode_mysql_binary_representation($input_string) {
    $result = array();
    $len = strlen($input_string);
    
    for($i = 0; $i < $len; $i += 2) {
        $result[] =  chr(hexdec(substr($input_string, $i, 2)));
    }
    return implode('', $result);
}

function retrieve_relevant_survivors($survivors_array) {
    global $SQL_SURVIVORS_TABLE;
    
    $survivors_set_string = implode(',', array_map(retrieve_number_from_index, $survivors_array));
    
    do {
        $result_handle = mysql_query("SELECT survivor_ID, survivor_code, survivor_name " .
                                   "FROM `$SQL_SURVIVORS_TABLE` " .
                                   "WHERE survivor_ID IN ($survivors_set_string)");
        
        if(!$result_handle) {
            $result = new Result(false, "שליפת הנתונים נכשלה", mysql_error());
            break;
        }
        
        $survivors_list = array();
        while($row = mysql_fetch_assoc($result_handle)) {
            $survivor_ID = (int)$row['survivor_ID'];
            $survivor_code = decode_mysql_binary_representation($row['survivor_code']);
            $survivors_list[$survivor_ID] = new Survivor($row['survivor_name'],
                                                            $row['author_name'],
                                                            $survivor_code,
                                                            $survivor_ID);
        }
        
        $result = new Result(true, $survivors_list);
    } while(false);
    return $result;
}

function retrieve_number_from_index($survivor_index) {
    return $survivor_index->number;
}

class Survivor_Index {
    function Survivor_Index($number, $is_zombie) {
        $this->number = $number;
        $this->is_zombie = $is_zombie;
    }
}

function fetch_int_list($params_array, $key, $max_length) {
    $counter = 0;
    
    $result = array();
    if (isset($params_array[$key])) {
        foreach(explode('_', $params_array[$key]) as $element) {
            if ($counter >= $max_length)
                break;
            
            $counter++;
            $number = (int)substr($element, 1);
            $result[] = new Survivor_Index($number, substr($element, 0, 1) == 'z');
        }
    }
    return $result;
}

function format_form($survivor_dict, $survivor_list) {
    global $SURVIVOR_MAX_LENGTH;
    
    $result = <<<END
<form action="codeguru_web.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$SURVIVOR_MAX_LENGTH?>" />
<table>
<tr>
    <th>
        Is zombie?
    </th>
    <th>
        Survivor name
    </th>
    <th>
        Survivor code
    </th>
</tr>
END;
            
    $num_of_survivors = count($survivor_list);
    $num_of_zombies = count($zombie_list);
    
    $counter = 0;

    foreach($survivor_list as $survivor) {
        $survivor_number = $survivor->number;
        if($survivor->is_zombie) {
            $zombie_attribute = 'checked="checked"';
        } else {
            $zombie_attribute = '';
        }
                
        if(isset($survivor_dict[$survivor_number])) {
            $survivor_code = $survivor_dict[$survivor_number]->survivor_code;
            $survivor_name = $survivor_dict[$survivor_number]->survivor_name;
        
            $result .= <<<END
<tr>
<td>
    <input name="survivor_zombie$counter" type="checkbox" $zombie_attribute />
</td>
<td>
    $survivor_name
    <input name="survivor_name$counter" value="$survivor_name" type="hidden" />
</td>
<td>
    <i>code provided by us</i>
    <input name="survivor_code$counter" value="$survivor_code" type="hidden" />
</td>
</tr>
END;
        } else {
            $result .= <<<END
<tr>
<td>
    <input name="survivor_zombie$counter" type="checkbox" $zombie_attribute />
</td>
<td>
    <input name="survivor_name$counter" type="text" width="30" />
</td>
<td>
    <input name="survivor_code$counter" type="file" />
</td>
</tr>
END;
        }
        $counter++;
    }

    $result .= <<<END

<tr>
    <td></td>
    <td><input type="submit" value="הצג את השורדים"/></td>
</tr>
</table>
</form>
END;
    return $result;
}

function main($params) {
    do {
        $result = database_connect();        
        if(!$result->success)
            break;
        
        $survivor_list = fetch_int_list($params, 'survivors', 16);
        
        $result = retrieve_relevant_survivors($survivor_list);
        if(!$result->success)
            break;
        
        $survivor_dict = $result->public_payload;
        $result_str = format_form($survivor_dict, $survivor_list, $zombie_list);
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
    <h1 style="text-align:left;">Choose survivors!</h1>
    <br />
    <?php
        $result = main($_GET);
        print($result->public_payload);
    ?>
</body>
</html>
