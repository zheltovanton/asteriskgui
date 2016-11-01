<?php
include "../../../models/queue/status.php";

$report = new QueueStatusRepository();

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
	$f = '';
	if (!empty($_GET["filter"])) $f = $_GET["filter"];
        $result = $report->getAll(
            array("filter" => $f));              
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>
