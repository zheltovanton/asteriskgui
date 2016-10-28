<?php
include "../../../models/report/group.ext.cdr.php";

$config = include("../../config.php");

$db = new PDO($config["db"], $config["username"], $config["password"], $config["options"]);

$report = new ReportGroupExtCdrRepository($db);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $report->getAll(
            array("calldate" => $_GET["calldate"],
 		"src" => $_GET["src"]));              
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>
