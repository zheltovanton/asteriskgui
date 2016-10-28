<?php
$config = include("config.php");
	$socket = fsockopen($config["asterisk_ip"],$config["manager_port"], $errno, $errstr, 30);
	if (!$socket) {
    		error_log("$errstr ($errno)".PHP_EOL);
	} else {
		fputs($socket, "Action: Login\r\n");
		fputs($socket, "UserName: ".$config["manager_login"]."\r\n");
		fputs($socket, "Secret: ".$config["manager_password"]."\r\n\r\n");

		fputs($socket, "Action: CoreShowChannels\r\n\r\n");
                fputs($socket, "Action: Logoff\r\n\r\n");

		$wrets = "";
		while (!feof($socket)) {
			$wrets .= fread($socket, 8192);
		}
		fclose($socket);
		echo $wrets;
	}
