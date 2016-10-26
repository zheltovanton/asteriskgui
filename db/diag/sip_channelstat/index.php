<?php
include "../../../models/diag/sip_channelstat.php";
$config = include("../../config.php");

$sipchannelstat = new SipChannelstatRepository();
//error_log("ast: call command ".PHP_EOL);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $sipchannelstat->getAll(
            array("peer" => $_GET["peer"],
		"callid" => $_GET["callid"], 
		"duration" => $_GET["duration"],
		"receive" => $_GET["receive"],
		"lostp" => $_GET["lostp"],
		"procentp" => $_GET["procentp"],
		"jitterp" => $_GET["jitterp"],
		"send" => $_GET["send"],
		"losts" => $_GET["losts"],
		"procents" => $_GET["procents"],
		"jitters" => $_GET["jitters"]));
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>


