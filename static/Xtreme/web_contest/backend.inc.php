<?php
// Constants.
$SQL_SERVER = "p50mysql43.secureserver.net";
$SQL_USER = "cguser";
$SQL_PASS = "doctor1guru2D";
$SQL_SURVIVORS_TABLE = "devel_Xtreme2009";
$SURVIVOR_MAX_LENGTH = 2048;

class Result {
    function Result($success, $public_payload = '', $internal_payload = '') {
	$this->success = $success;
	$this->public_payload = $public_payload;
	$this->internal_payload = $internal_payload;
    }
}

class Survivor {
    function Survivor($survivor_name, $author_name, $survivor_code, $survivor_ID=0, $timestamp='') {
        $this->survivor_name = $survivor_name;
        $this->survivor_code = $survivor_code;
        $this->author_name = $author_name;
        $this->survivor_ID = $survivor_ID;
        $this->timestamp = $timestamp;
    }
}

function database_connect() {
    global $SQL_SERVER, $SQL_USER, $SQL_PASS;
    
    do {
	if (!mysql_connect($SQL_SERVER, $SQL_USER, $SQL_PASS)) {
	    $result = new Result(false,
                                 'כשל בחיבור למסד הנתונים (1)',
				 mysql_error());
	    break;
	}
	
	if (!mysql_select_db("cguser")) {
	    $result = new Result(false,
                                 'כשל בחיבור למסד הנתונים (2)',
				 mysql_error());
	    break;
	}
	
	$result = new Result(true);
    } while(false);
    return $result;
}

$UPLOAD_ERR_OK = 0;

?>