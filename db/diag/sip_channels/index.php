<?php
include "../../../models/diag/sip_channels.php";
$config = include("../../config.php");

$sipchannels = new SipChannelsRepository();
//error_log("ast: call command ".PHP_EOL);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $sipchannels->getAll(
            array("Channel" => $_GET["Channel"],
		"Context" => $_GET["Context"], 
		"Extension" => $_GET["Extension"],
		"ChannelStateDesc" => $_GET["ChannelStateDesc"],
		"BridgedChannel" => $_GET["BridgedChannel"],
		"Application" => $_GET["Application"],
		"ApplicationData" => $_GET["ApplicationData"],
		"CallerIDnum" => $_GET["CallerIDnum"],
		"CallerIDname" => $_GET["CallerIDname"],
		"Duration" => $_GET["Duration"]));
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>


