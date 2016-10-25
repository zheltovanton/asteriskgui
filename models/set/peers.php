<?php

include dirname(__FILE__)."/../../db/asterisk.php";

class SetPeers {
	public $clid;
	public $name;
	public $ip;
	public $secret;
	public $useragent;
	public $context;
}

class SetPeerRepository {

    protected $db;


    public function __construct(PDO $db) {
        $this->db = $db;
    }

     private function read($row) {
        $result = new SetPeers();

        $result->clid = $row["clid"];
        $result->name = $row["name"];
        $result->ip = $row["ip"];
    	$result->secret = $row["secret"];
    	$result->useragent = $row["useragent"];
    	$result->context = $row["context"];

	return $result;
    }

    public function getAll($filter) {
	// filter vars
	$fclid = $filter["clid"];
	$fip = $filter["ip"];
	$fport = $filter["port"];
	$fstate = $filter["state"];

	//send asterisk management command
        $asterisk = new AsteriskMGMT();
        $rows = $asterisk->Command('sip show peers');
        
        $json = array();

	//search 
        $arr_rows = explode(PHP_EOL, $rows);
	for($first = 1; $first < count($arr_rows); $first++) {
		if (stripos($arr_rows[$first],"/username")>0) break; 
	}

	for($i = $first+1; $i < count($arr_rows); $i++) {

	    $find = true;

	    // Parse string from asterisk response
  	    if (stripos($arr_rows[$i],"[Monitored")>0) break; 
	    $str = $arr_rows[$i];
     	    $clid = substr($str, 0, 26); // 
     	    $ip = substr($str, 26, 30); //
	    if (trim($ip)=='(Unspecified)') $ip='';	 
     	    $port = substr($str, 95, 9); // 
     	    $state = substr($str, 105, 15); // 
	    if (trim($state)=='UNKNOWN') $state='';	 
	    
	    //Search
	    if ( ($fip) && (stripos($ip,$fip) === false) ) { $find=false; }
	    if ( ($fport) && (stripos($port,$fport) === false) ) { $find=false; }
	    if ( ($fstate) && (stripos($state,$fstate) === false) ) { $find=false; }
	    if ( ($fclid) && (stripos($clid,$fclid) === false) ) { $find=false; }
 
	    //$str = explode(" ", $str); 
	    if ($find) array_push($json,  array(
 	      'clid' => $clid,
     	      'ip' =>  $ip,
     	      'port' =>  $port,
     	      'state' =>  $state
   	     ));
	}
	//error_log("ast: ".$row.PHP_EOL);
        return $json;	
    }


}

?>