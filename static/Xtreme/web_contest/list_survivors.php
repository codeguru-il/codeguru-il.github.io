<?php
require_once('backend.inc.php');

function retrieve_survivors_list($survivor_object) {
    global $SQL_SURVIVORS_TABLE;
    
    do {
        $result_handle = mysql_query("SELECT survivor_ID, survivor_name, " .
                                   "author_name FROM `$SQL_SURVIVORS_TABLE` " .
                                   "ORDER BY survivor_name");
        
        if(!$result_handle) {
            $result = new Result(false, "שליפת הנתונים נכשלה", mysql_error());
            break;
        }
        
        $survivors_list = array();
        while($row = mysql_fetch_assoc($result_handle)) {
            $survivors_list[] = new Survivor($row['survivor_name'],
                                             $row['author_name'],
                                             '',
                                             $row['survivor_ID']);
        }
        
        $result = new Result(true, $survivors_list);
    } while(false);
    return $result;
}

function format_result_list($survivors_list) {
    $result = <<< END
<select name="survivor_ID_list" multiple="multiple" onchange="select_single(this, this.options.selectedIndex)"
    style="height: 20em;">
<option selected="selected" value="0">Upload a survivor (next screen)</option>
END;
    foreach($survivors_list as $survivor) {
        $result .= <<<END
<option value="$survivor->survivor_ID">$survivor->survivor_name ($survivor->author_name)
</option>
END;
    }
    $result .= '</select>';
    return $result;
}

function main() {
    global $UPLOAD_PASSWORD_MD5;
    
    do {
        $result = database_connect();
        if(!$result->success)
            break;
        
        $result = retrieve_survivors_list($received_data, $received_files);
        if(!$result->success)
            break;
        
        $result_list_formatted = format_result_list($result->public_payload);
        return new Result(true, $result_list_formatted);        
    } while(false);
    return $result;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>בחירת שורדים</title>


<style type="text/css">
    select { width:20em; height: 10em; direction: ltr; }
    option { direction: ltr; }
    .mid_td { vertical-align: middle; }
</style>
    
<script type="text/javascript">
<!--
function select_single(box, index) {
    options_length = box.options.length;
    
    for(i = 0; i < options_length; i++) {
        box.options[i].selected = (i == index);   
    }
}

function normalize_selection() {
    select_single(document.forms["main"].elements["survivor_ID_zombie"]);
    select_single(document.forms["main"].elements["survivor_ID_target"]);
    select_single(document.forms["main"].elements["survivor_ID_list"]);   
}

function move_horizontal(source_box_name, destination_box_name) {
    source_box = document.forms["main"].elements[source_box_name];
    destination_box = document.forms["main"].elements[destination_box_name];    
    
    moved_index = source_box.options.selectedIndex;
    if(-1 != moved_index) {
        if(source_box_name == "survivor_ID_list") {
            new_option = duplicate_option(source_box.options[moved_index]);
            box_append(destination_box, new_option);            
        } else {
            source_box.remove(moved_index);            
        }
    }
    normalize_selection();
}

function move_vertical(is_up, box_name) {
    box = document.forms["main"].elements[box_name];
    moved_index = box.options.selectedIndex;
    
    if(-1 != moved_index) {
        if(is_up) {
            new_index = moved_index - 1;
        } else {
            new_index = moved_index + 1;
        }
        
        if((new_index >= 0) && (new_index < box.options.length)) {
            moved_option = box.options[moved_index];
            box.remove(moved_index);           
            box_insert(box, moved_option, new_index);
        }
    }   
}

function box_append(box, option) {
    try {
        box.add(option, null);
    } catch(ex) {
        box.add(option);
    }
}

function box_insert(box, option, index) {
   try {
        box.add(option, box.options[index]);
    } catch(ex) {
        box.add(option, index);
    }    
}

function duplicate_option(old_option) {
    value = old_option.value;
    text = old_option.text;
    return Option(text, value);
}

function concat_box_options(prefix, box_name) {
    box = document.forms["main"].elements[box_name];
    result = new Array();
    
    for(i = 0; i < box.options.length; i++) {
        result.push(prefix + box.options[i].value);
    }
    
    return result;
}

function submit_selection() {
    survivors = concat_box_options("s", "survivor_ID_target");
    zombies = concat_box_options("z", "survivor_ID_zombie");
    concat = survivors.concat(zombies);    
    
    document.forms['submitter'].survivors.value =  concat.join('_');
                                
    document.forms['submitter'].submit();
}

//-->
</script>
    
</head>
<body dir="rtl" onload="normalize_selection()" style="font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10pt;background-color: #072C35;color: #D6E5B0;">
    <div dir="rtl">
    <div style="color: red;">    

    </div>
    
	<h1>בחירת שורד</h1>
        <form name="main">
        <table style="border: none; margin-width: 0;">
        <tr>
            <td>
                <h2>לא משתתפים</h2>
            </td>
            <td></td>
            <td>
                <h2>שורדים</h2>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>              
            <td rowspan="2">

            <?php
                $result = main();
                print $result->public_payload;
            ?>
            </td>
            <td>
                <span dir="ltr">
                    <input type="button" onclick="move_horizontal('survivor_ID_list', 'survivor_ID_target')" value="<--"/><br/>                    
                    <input type="button" onclick="move_horizontal('survivor_ID_target', 'survivor_ID_list')" value="-->"/><br/>
                 </span>
             </td>            
            <td>
                <select name="survivor_ID_target" multiple="multiple" onchange="select_single(this, this.options.selectedIndex)">
                </select>
             </td>
            <td>
                <span dir="ltr" class="mid_td">
                    <input type="button" onclick="move_vertical(true, 'survivor_ID_target')" value="/\"/><br/>
                    <input type="button" onclick="move_vertical(false, 'survivor_ID_target')" value="\/"/><br/>
                 </span>                
            </td>
        </tr>
        <tr>
            <td>
                <span dir="ltr">
                    <input type="button" onclick="move_horizontal('survivor_ID_list', 'survivor_ID_zombie')" value="<--"/><br/>
                    <input type="button" onclick="move_horizontal('survivor_ID_zombie', 'survivor_ID_list')" value="-->"/><br/>
                 </span>
             </td>
            <td>
                <select name="survivor_ID_zombie" multiple="multiple" onchange="select_single(this, this.options.selectedIndex)">
                </select>
             </td>
            <td>
                <span dir="ltr" class="mid_td">
                    <input type="button" onclick="move_vertical(true, 'survivor_ID_zombie')" value="/\"/><br/>
                    <input type="button" onclick="move_vertical(false, 'survivor_ID_zombie')" value="\/"/><br/>
                 </span>                
            </td>
         </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <h2>זומבים</h2>
            </td>
            <td></td>        
        </tr>
        <tr>
            <td rowspan="4">
                <input type="button" value="הצג שורדים" onclick="submit_selection()"/>
            </td>
        </tr>
        </table>
        </form>
    </div>
            
    <form action="custom_upload.php" method="get" name="submitter">
        <input type="hidden" name="survivors"/>
     </form>
</body>
</html>