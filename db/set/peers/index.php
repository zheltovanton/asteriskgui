<?php
include "../../../models/set/peers.php";
$config = include("../../config.php");

$db = new PDO($config["db"], $config["username"], $config["password"], $config["options"]);

error_reporting(E_ALL); 

$setpeer = new SetPeerRepository($db);
error_log("ast: call command ".PHP_EOL);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $setpeer->getAll(
            array("clid" => $_GET["clid"],
 		"ip" => $_GET["ip"],
		"port" => $_GET["port"],
		"state" => $_GET["state"]));               
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>


