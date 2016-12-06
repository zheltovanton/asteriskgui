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

header("Content-Type: application/json");
echo  json_encode($result);

?>
