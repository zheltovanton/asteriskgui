<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class QueueStatusRepository {

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

	for($i = $first; $i < count($arr_rows); $i++) {

	    $find = true;

	    // Parse string from asterisk response
  	    if (stripos($arr_rows[$i],"--END COMMAND--")!==false) break; //if find end message, then break 
	    if (stripos($arr_rows[$i],"Privilege: Command")!==false) continue;	
	    if (stripos($arr_rows[$i],"Response: Follows")!==false) continue; 
  	    $str=$arr_rows[$i];
  	    if (empty($str)) continue; //if find end message, then break 

            $q = explode(" ", trim($str));
  	    $queue_num = $q[0];

            $queue = array();
	    $i++;
	    $i++;

  	    for ($n = $i; $n < count($arr_rows); $n++) {  
	    	$str = $arr_rows[$n];
		if ((stripos($arr_rows[$n],"Privilege: Command")!==false)) continue;	
		if ((stripos($arr_rows[$n],"No Members")!==false)) {
			$i++;
			continue;
		}
		if ((stripos($arr_rows[$n],"No Callers")!==false)) {
			$i++;
			break;
		}	
                       
		if (empty($str)) break; //if find end message, then break 
		//Search
		if ( ($ffilter) && (stripos($str,$ffilter) === false) ) { $find=false; }

		// Parse member string
                $q = explode(" ", trim($str));
                $member = $q[0];

		//$str = explode(" ", $str);
		if (!empty($str)) 
		if ($find) array_push($queue,  array(
 	 	  'member' => $member
   	   	 ));
		 $i++;
		
	    } 
	    array_push($json,  array(
		'queue' => $queue_num,
 	 	'members' => $queue
   	    ));

	    
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


}

?>