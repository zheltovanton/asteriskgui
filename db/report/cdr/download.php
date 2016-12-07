<?php
include "../../../models/report/cdr.php";

$config = include("../../config.php");

$db = new PDO($config["db"], $config["username"], $config["password"], $config["options"]);

$report = new ReportCdrRepository($db);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $report->getAll(
            array("start" => $_GET["start"],
 		"end" => $_GET["end"],
		"text" => $_GET["text"]));              
        break;  
}

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header("Content-Disposition: attachment; filename=cdr.csv");
}

header("Content-Type: text/x-csv");
$csvfile = 'calldate;clid;src;dst;dcontext;duration;billsec;disposition'.PHP_EOL;
foreach ($result as $row) {
    $line = $row->calldate.';'. 
            $row->clid.';'.       
            $row->src.';'.        
            $row->dst.';'.        
            $row->dcontext.';'.   
            $row->duration.';'.   
            $row->billsec.';'.    
            $row->disposition.PHP_EOL;
//dstchannel 
//lastapp    
//lastdata   
    $csvfile .= $line;
}
echo $csvfile;

?>
