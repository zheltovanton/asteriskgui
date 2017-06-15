<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class DiagDatabaseRepository {

    public function getAll($filter) {
	$str = $filter["str"];

        $json = array();
        $asterisk = new AsteriskMGMT();

        $rows = explode(PHP_EOL, $asterisk->Command('database show'));
	for($i = 10; $i <= count($rows); $i++) {
	    $find = true;
	    if (($str) && (stripos($rows[$i],$str) === false)) { 
		$find = false; 
	    }
	    if ($find) 
	    	array_push($json,  array('row' => $i, 'cmd' => 'database', 'str' => $rows[$i]));
	}

        return $json;	
    }


}

?>