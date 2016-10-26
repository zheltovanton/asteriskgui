<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class SipChannelstatRepository {

    public function getAll($filter) {

	// filter vars
	$fpeer = $filter["peer"];
	$fcallid = $filter["callid"]; 
	$fduration = $filter["duration"];
	$freceive = $filter["receive"];
	$flostp = $filter["lostp"];
	$fprocentp = $filter["procentp"];
	$fjitterp = $filter["jitterp"];
	$fsend = $filter["send"];
	$flosts = $filter["losts"];
	$fprocents = $filter["procents"];
	$fjitters = $filter["jitters"];

	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Command('sip show channelstats');
        
        $json = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"Peer") !== false) break; 
	}

	for($i = $first+1; $i < count($arr_rows); $i++) {

	    $find = true;
		
	    // Sample 
	   //Peer             Call ID      Duration Recv: Pack  Lost       (     %) Jitter Send: Pack  Lost       (     %) Jitter
	   //095.011.120.9    40F2D8B14C0  00:02:00 0000006016  0000000000 ( 0.00%) 0.0000 0000005512  0000000000 ( 0.00%) 0.0007
            $str = preg_replace('/[\s]{2,}/', ' ', $arr_rows[$i]);
	    $str = str_replace('( ','',$str); 
	    $str = str_replace(')','',$str); 
  	    if (stripos($str,"SIP chann") !== false) break; 
	    $rows = explode(" ", $str);
	    
	    //Search
	    if ( ($peer) && (stripos($peer,$fpeer) === false) ) { $find=false; }
 
	    //$str = explode(" ", $str); 
	    if ($find) array_push($json,  array(
 	      'peer' => $rows[0],
     	      'callid' =>  $rows[1], 
     	      'duration' =>  $rows[2], 
     	      'receive' =>  $rows[3],
     	      'lostp' =>  $rows[4],
     	      'procentp' =>  $rows[5], 
     	      'jitterp' =>  $rows[6],
     	      'send' =>  $rows[7],
     	      'losts' =>  $rows[8],
     	      'procents' =>  $rows[9], 
     	      'jitters' =>  $rows[10]

   	     ));
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


}

?>