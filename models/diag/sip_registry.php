<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class SipRegistryRepository {

    public function getAll($filter) {
	// filter vars
	$fhost = $filter["host"];
	$fusername = $filter["username"];
	$fstate = $filter["state"];

	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Command('sip show registry');
        
        $json = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"Host") !== false) break; 
	}

	for($i = $first+1; $i < count($arr_rows); $i++) {

	    $find = true;
		
	    // Sample 
	    //sip1.uiscom.ru:9060                     N      082944             105 Registered           Tue, 25 Oct 2016 20:56:28
	    // Parse string from asterisk response
  	    if (stripos($arr_rows[$i],"SIP registrations") !== false) break; 
	    $str = $arr_rows[$i];
     	    $host = substr($str, 0, 40); // 
     	    $username = substr($str, 42, 19); //
     	    $state = substr($str, 70, 21); // 
	    
	    //Search
	    if ( ($fhost) && (stripos($host,$fhost) === false) ) { $find=false; }
	    if ( ($fusername) && (stripos($username,$fusername) === false) ) { $find=false; }
	    if ( ($fstate) && (stripos($state,$fstate) === false) ) { $find=false; }
 
	    //$str = explode(" ", $str); 
	    if ($find) array_push($json,  array(
 	      'host' => $host,
     	      'username' =>  $username,
     	      'state' =>  $state
   	     ));
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


}

?>