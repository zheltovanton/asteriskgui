<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class SipPeerRepository {

    public function getAll($filter) {
	// filter vars
	$fclid = $filter["clid"];
	$fip = $filter["ip"];
	$fport = $filter["port"];
	$fstate = $filter["state"];

	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Action('Sippeers');
        
        $json = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"Message: Peer status list will follow")!==false) break; 
	}

	for($i = $first+1; $i < count($arr_rows); $i++) {

	    $find = true;
	    $name = '';
	    $ip = '';
	    $port = '';
            $state = '';
            $desc = '';

	    // Parse string from asterisk response
  	    if (stripos($arr_rows[$i],"Event: PeerlistComplete")!==false) break; //if find end message, then break 

  	    for ($n = $i; $n < count($arr_rows); $n++) {  
	    	$str = $arr_rows[$n];
     	    	if (stripos($str,"ObjectName:")!==false) { $name = trim(str_replace('ObjectName:','',$str)); }  
     	    	if (stripos($str,"IPaddress:")!==false) { $ip = trim(str_replace('IPaddress:','',$str)); }  
     	    	if (stripos($str,"IPport:")!==false) { $port = trim(str_replace('IPport:','',$str)); }  
     	    	if (stripos($str,"Status:")!==false) { $state = trim(str_replace('Status:','',$str)); }  
     	    	if (stripos($str,"Description:")!==false) { $desc = trim(str_replace('Description:','',$str)); }  
  	  	$i++;
		if (stripos($arr_rows[$i],"Event: ")!==false) break; //if find end message, then break 
		
	    } 
	    
	    //Search
	    if ( ($fip) && (stripos($ip,$fip) === false) ) { $find=false; }
	    if ( ($fport) && (stripos($port,$fport) === false) ) { $find=false; }
	    if ( ($fstate) && (stripos($state,$fstate) === false) ) { $find=false; }
	    if ( ($fclid) && (stripos($clid,$fclid) === false) ) { $find=false; }
 
	    //$str = explode(" ", $str);
	    if (!empty($name)) 
	    if ($find) array_push($json,  array(
 	      'name' => $name,
     	      'ip' =>  $ip,
     	      'port' =>  $port,
     	      'state' =>  $state,
 	      'desc' => $desc
   	     ));
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


}

?>