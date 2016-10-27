<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class DiagTotalRepository {

    public function getAll() {

        $json = array();
        $asterisk = new AsteriskMGMT();

        $rows = explode(PHP_EOL, $asterisk->Command('core show version'));
	for($i = 10; $i <= 10; $i++) {
	    array_push($json,  array('row' => $i, 'cmd' => 'core ver', 'str' => $rows[$i]));
	}

        $rows = explode(PHP_EOL, $asterisk->Command('core show uptime'));
	for($i = 10; $i <= 11; $i++) {
	    array_push($json,  array('row' => $i, 'cmd' => 'core uptime', 'str' => $rows[$i]));
	}

        $rows = explode(PHP_EOL, $asterisk->Command('core show sysinfo'));
	for($i = 13; $i <= 17; $i++) {
	    array_push($json,  array('row' => $i, 'cmd' => 'core sysinfo', 'str' => $rows[$i]));
	}


        return $json;	
    }


}

?>