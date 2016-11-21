<?php
include "../../../models/report/noanswer.php";

$config = include("../../config.php");

$db = new PDO($config["db"], $config["username"], $config["password"], $config["options"]);

$report = new ReportNoAnswerRepository($db);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $report->getAll(
            array("calldate" => $_GET["calldate"],
 		"clid" => $_GET["clid"],
		"src" => $_GET["src"],
		"dst" => $_GET["dst"],
		"dcontext" => $_GET["dcontext"]));              
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>
