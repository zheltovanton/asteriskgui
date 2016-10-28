<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class SipChannelsRepository {

//Event: CoreShowChannel
//-Channel: SIP/141-00005049
//UniqueID: 1477634487.141041
//-Context: macro-dialout-trunk
//-Extension: s
//Priority: 20
//ChannelState: 4
//-ChannelStateDesc: Ring
//-Application: Dial
//-ApplicationData: SIP/78007328911/79112125438,300,Tt
//-CallerIDnum: 74959990535
//-CallerIDname:
//ConnectedLineNum:
//ConnectedLineName:
//-Duration: 00:00:07
//AccountCode:
//BridgedChannel:
//BridgedUniqueID:

    public function getAll($filter) {

	// filter vars
	$fChannel = $filter["Channel"];
	$fContext = $filter["Context"]; 
	$fExtension = $filter["Extension"];
	$fChannelStateDesc = $filter["ChannelStateDesc"];
	$fBridgedChannel = $filter["BridgedChannel"];
	$fApplication = $filter["Application"];
	$fApplicationData = $filter["ApplicationData"];
	$fCallerIDnum = $filter["CallerIDnum"];
	$fCallerIDname = $filter["CallerIDname"];
	$fDuration = $filter["Duration"];

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

	    $find = true;
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
	    
	    //Search
	    if ( ($fChannel) && (stripos($Channel,$fChannel) === false) ) { $find=false; }
	    if ( ($fContext) && (stripos($Context,$fContext) === false) ) { $find=false; }
	    if ( ($fExtension) && (stripos($Extension,$fExtension) === false) ) { $find=false; }
	    if ( ($fChannelStateDesc) && (stripos($ChannelStateDesc,$fChannelStateDesc) === false) ) { $find=false; }
	    if ( ($fBridgedChannel) && (stripos($BridgedChannel,$fBridgedChannel) === false) ) { $find=false; }
	    if ( ($fApplication) && (stripos($Application,$fApplication) === false) ) { $find=false; }
	    if ( ($fApplicationData) && (stripos($ApplicationData,$fApplicationData) === false) ) { $find=false; }
	    if ( ($fCallerIDnum) && (stripos($CallerIDnum,$fCallerIDnum) === false) ) { $find=false; }
	    if ( ($fCallerIDname) && (stripos($CallerIDname,$fCallerIDname) === false) ) { $find=false; }
	    if ( ($fDuration) && (stripos($Duration,$fDuration) === false) ) { $find=false; }
 
	    //$str = explode(" ", $str);
	    if (!empty($Channel)) 
	    if ($find) array_push($json,  array(
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


}

?>