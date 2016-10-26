<?php
include "../../../models/diag/sip_registry.php";
$config = include("../../config.php");

$sipregistry = new SipRegistryRepository();
//error_log("ast: call command ".PHP_EOL);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $sipregistry->getAll(
            array("host" => $_GET["host"],
 		"username" => $_GET["username"],
		"state" => $_GET["state"]));               
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>


