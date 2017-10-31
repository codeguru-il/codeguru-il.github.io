<?php
require_once('backend.inc.php');

$UPLOAD_PASSWORD_MD5 = "65f954808c5da329f00248340565188d";

function verify_input($received_data, $received_files) {
    global $UPLOAD_ERR_OK, $SURVIVOR_MAX_LENGTH;
    
    do {
        if(!isset($received_data['survivor_name'])) {
            $result = new Result(false, 'שם השורד לא תקין');
            break;
        }
        $survivor_name = mysql_real_escape_string($received_data['survivor_name']);
        
        if(!isset($received_data['author_name'])) {
            $result = new Result(false, 'שם היוצרים אינו תקין');
            break;
        }
        $author_name = mysql_real_escape_string($received_data['author_name']);
        
        if((!isset($received_files['survivor'])) ||
                    ($received_files['survivor']['error'] != $UPLOAD_ERR_OK)) {
            $result = new Result(false, 'השורד לא הועלה');
            break;
        }
        
        $concat_survivor_code = substr(file_get_contents($received_files['survivor']['tmp_name']),
                                       0, $SURVIVOR_MAX_LENGTH);
        
        $survivor_code = mysql_real_escape_string($concat_survivor_code);
        
        $result = new Result(true, new Survivor($survivor_name,
                                                $author_name, $survivor_code));
    } while(false);
    
    return $result;
}

function submit_survivor($survivor_object) {
    global $SQL_SURVIVORS_TABLE;
    
    do {
        $result_flag = mysql_query("INSERT INTO `$SQL_SURVIVORS_TABLE` " .
                        "(survivor_name, survivor_code, author_name) VALUES " .
                        "('$survivor_object->survivor_name', " .
                        "'$survivor_object->survivor_code', " .
                        "'$survivor_object->author_name')");
        
        if(!$result_flag) {
            $result = new Result(false, "העלאת השורד נכשלה", mysql_error());
            break;
        }
        
        $result = new Result(true, 'השורד הועלה בהצלחה');        
    } while(false);
    return $result;
}

function perform_submission($received_data, $received_files) {
    global $UPLOAD_PASSWORD_MD5;
    
    do {
        if(!isset($received_data['submit'])) {
            $result = new Result(true, 'אנא מלא את הטופס');
            break;
        }
        
        if($UPLOAD_PASSWORD_MD5 != md5($received_data['password'])) {
            $result = new Result(false, 'אימות הסיסמא נכשל');
            break;
        }
        
        $result = database_connect();
        if(!$result->success)
            break;
        
        $result = verify_input($received_data, $received_files);
        if(!$result->success)
            break;
        
        $result = submit_survivor($result->public_payload); 
    } while(false);
    return $result;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>העלאת שורד</title>
</head>
<body dir="rtl" style="font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10pt;background-color: #072C35;color: #D6E5B0;">
    <div style="color: red;">    
    <?php
        $result = perform_submission($_POST, $_FILES);
        print $result->public_payload;
    ?>
    </div>
    
	<h1>העלאת שורד</h1>
        <form action="#" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <td>
                        סיסמא:
                     </td>
                    <td>
                        <input name="password" type="password" size="60 "/>
                    </td>
                 </tr>            
                <tr>
                    <td>
                        שם השורד:
                    </td>
                    <td>
                        <input name="survivor_name" type="text" size="30"/><br/>
                     </td>
                </tr>
                <tr>
                    <td>
                        שם היוצרים:
                     </td>
                    <td>
                        <input name="author_name" type="text" size="30"/>
                    </td>
                 </tr>
                <tr>
                    <td>
                        העלאת השורד:
                     </td>
                    <td>
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=$SURVIVOR_MAX_LENGTH?>" />
                        <input name="survivor" type="file" size="30"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input name="submit" type="submit" value="העלה את השורד"/>
                     </td>
                 </tr>
            </table>
        </form>
</body>
</html>