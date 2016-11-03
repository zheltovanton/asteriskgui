<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class QueueStatusRepository {

//      Feofanova (Local/111@from-queue/n from hint:111@ext-local) with penalty 1 (ringinuse disabled) (Busy) has taken 16 calls (last was 380 secs ago)
//      Ushakova (Local/110@from-queue/n from hint:110@ext-local) with penalty 1 (ringinuse disabled) (Not in use) has taken no calls yet
//   Callers:
//      1. SIP/mts9164738575-0000217d (wait: 0:30, prio: 0)


    private function SearchBusyInChannelList($channels,$number,$state){
	for($i = 1; $i < count($channels); $i++) {
		if ($channels[$i]["CallerIDnum"] == $number) {
			$state = 'busy';
			break; 
		}
	}
	return $state;
    }

    private function SearchStatusInPeersList($peers,$number){
	$state = 'na';
	for($i = 1; $i < count($peers); $i++) {
		if ($peers[$i]["name"] == $number) {
			if (stripos($peers[$i]["state"],"OK")!==false) {
				$state = 'aviable';
				break; 
			}
		}
	}
	return $state;
    }

    private function getChannels() {

	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Action('CoreShowChannels');
        
        $json = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"Message: Channels will follow")!==false) break; 
	}

	for($i = $first+1; $i < count($arr_rows); $i++) {

	    $Channel = '';
	    $Context = '';
	    $Extension = '';
	    $ChannelStateDesc = '';
	    $BridgedChannel = '';
	    $Application = '';
	    $ApplicationData = '';
	    $CallerIDnum = '';
	    $CallerIDname = '';
	    $Duration = '';

	    
	    // Parse string from asterisk response
  	    if (stripos($arr_rows[$i],"Event: CoreShowChannelsComplete")!==false) break; //if find end message, then break 

  	    for ($n = $i; $n < count($arr_rows); $n++) {  
	    	$str = $arr_rows[$n];
     	    	if (stripos($str,"Channel:")!==false) { $Channel = trim(str_replace('Channel:','',$str)); }  
     	    	if (stripos($str,"Context:")!==false) { $Context = trim(str_replace('Context:','',$str)); }  
     	    	if (stripos($str,"Extension:")!==false) { $Extension = trim(str_replace('Extension:','',$str)); }  
     	    	if (stripos($str,"ChannelStateDesc:")!==false) { $ChannelStateDesc = trim(str_replace('ChannelStateDesc:','',$str)); }  
     	    	if (stripos($str,"BridgedChannel:")!==false) { $BridgedChannel = trim(str_replace('BridgedChannel:','',$str)); }  
     	    	if (stripos($str,"Application:")!==false) { $Application = trim(str_replace('Application:','',$str)); }  
     	    	if (stripos($str,"ApplicationData:")!==false) { $ApplicationData = trim(str_replace('ApplicationData:','',$str)); }  
     	    	if (stripos($str,"CallerIDnum:")!==false) { $CallerIDnum = trim(str_replace('CallerIDnum:','',$str)); }  
     	    	if (stripos($str,"CallerIDname:")!==false) { $CallerIDname = trim(str_replace('CallerIDname:','',$str)); }  
     	    	if (stripos($str,"Duration:")!==false) { $Duration = trim(str_replace('Duration:','',$str)); }  
  	  	$i++;
		if (stripos($arr_rows[$i],"Event: ")!==false) break; //if find end message, then break 
		
	    } 
	    
	    //$str = explode(" ", $str);
	    if (!empty($Channel)) 
	    array_push($json,  array(
     	      'Channel' =>  $Channel,
     	      'Context' =>  $Context,
     	      'Extension' =>  $Extension,
 	      'Duration' => $Duration,
	      'BridgedChannel' => $BridgedChannel,
     	      'ChannelStateDesc' =>  $ChannelStateDesc,
     	      'Application' =>  $Application,
     	      'ApplicationData' =>  $ApplicationData,
     	      'CallerIDnum' =>  $CallerIDnum,
     	      'CallerIDname' =>  $CallerIDname
   	     ));
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


    private function SearchNumberInQueuestring($str){
	$step1 = explode('(',trim($str));
	$step2 = explode(' ',trim($step1[1]));
	$step3 = substr($step2[0], 0, strpos($step2[0],'@'));
	$step4 = substr($step3, strpos($step2[0],'/')+1,15);
	return $step4;	
    }

    private function getPeers() {
	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Action('Sippeers');
        
        $json2 = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"Message: Peer status list will follow")!==false) break; 
	}

	for($i = $first+1; $i < count($arr_rows); $i++) {
	    $name = '';
	    $ip = '';
 	    $state = '';
	    // Parse string from asterisk response
  	    if (stripos($arr_rows[$i],"Event: PeerlistComplete")!==false) break; //if find end message, then break 

  	    for ($n = $i; $n < count($arr_rows); $n++) {  
	    	$str = $arr_rows[$n];
     	    	if (stripos($str,"ObjectName:")!==false) { $name = trim(str_replace('ObjectName:','',$str)); }  
     	    	if (stripos($str,"IPaddress:")!==false) { $ip = trim(str_replace('IPaddress:','',$str)); }  
     	    	if (stripos($str,"Status:")!==false) { $state = trim(str_replace('Status:','',$str)); }  
  	  	$i++;
		if (stripos($arr_rows[$i],"Event: ")!==false) break; //if find end message, then break 
	    } 

	    if (!empty($name)) 
	    array_push($json2,  array(
 	      'name' => $name,
     	      'ip' =>  $ip,
     	      'state' =>  $state
   	     ));
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json2;	
    }

    public function getAll($filter) {
	// filter vars
	if ($filter["filter"]) {
		$ffilter = $filter["filter"];
	} else {
		$ffilter = '';
	}

	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Command('queue show');
        
        $json = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"Response: Follows")!==false) break; 
	}

	$peers = $this->getPeers();	
	$channels = $this->getChannels();	

	for($i = $first; $i < count($arr_rows); $i++) {

	    	$find = true;
	    	$queue_num = '';
            	$queue = array();
            	$callers = array();
	    	$W = '';
	    
	    	// Parse string from asterisk response
  	    	if (stripos($arr_rows[$i],"--END COMMAND--")!==false) break; //if find end message, then break 
	    	if (stripos($arr_rows[$i],"Privilege: Command")!==false) continue;	
	    	if (stripos($arr_rows[$i],"Response: Follows")!==false) continue; 
  	    	$str=$arr_rows[$i];
  	    	if (empty($str)) continue; //if find end message, then break 
  	    	
            	$q = explode(" ", trim($str));
  	    	$queue_num = $q[0]; //
  	    	
	    	$i++;
	    	$i++;
		
		$callersmode = false;

		//Parse queue members until find empty string
  	    	for ($n = $i; $n < count($arr_rows); $n++, $i++) {  
		    	$find = true;
	    		$str = $arr_rows[$n];
			if ((stripos($str,"Privilege: Command")!==false)) continue;	
			if ((stripos($str,"No Members")!==false)) {
				$i++;
				continue;
			}
			if ((stripos($str,"Callers:")!==false)) {
				$i++;
				$callersmode = true;
				continue;
			}
			if ((stripos($str,"No Callers")!==false)) {
				$i++;
				break;
			}	
                        if (!$callersmode) { //search callers if true and members if false
				$number='';
				if (empty($str)) break; //if find empty message, then break 
				//Search
				if ( ($ffilter) && (stripos($str,$ffilter) === false) ) { $find=false; }
		        
				// Parse member string
              	  	  	$q = explode(" ", trim($str));
                		$member = $q[0];
				
				$number = $this->SearchNumberInQueuestring($str);				

				$state = 'na';
				if (stripos($str,'(Unavailable)') !== false)  $state = 'na';
				if (stripos($str,'(Not in use)') !== false)  $state = 'na';
				if (stripos($str,'(In use)') !== false)  $state = 'busy';
				if (stripos($str,'(Busy)') !== false)  $state = 'busy';
				if (stripos($str,'(Ringing)') !== false)  $state = 'aviable';

				$state = $this->SearchStatusInPeersList($peers, $number);				
				$state = $this->SearchBusyInChannelList($channels, $number, $state);				
		        
				//made array of members
				if (!empty($str)) 
				if ($find) array_push($queue,  array(
 	 				'member' => $member,
 	 				'number' => $number,
					'state' => $state
   	   			));
			} else { // Looking for callers
				if (empty($str)) break; //if find end message, then break 

				if ((stripos($arr_rows[$n],"No Members")!==false)) {
					$i++;
					continue;
				}

				//Search
				if ( ($ffilter) && (stripos($str,$ffilter) === false) ) { $find=false; }
		        
				// Parse member string
              	  	  	$q = explode(" ", trim($str));
                		$caller = $q[0];
				$time = $q[1];

				//made array of callers
				if (!empty($str)) 
				if ($find) array_push($queue,  array(
 	 				'caller' => $caller,
					'time' => $time
   	   			));
			}
	    	} 
	    	// push final record to array
	    	array_push($json,  array(
			'queue' => $queue_num,
 		 	'callers' => $callers,
 		 	'members' => $queue
   		));

	    
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


}

?>