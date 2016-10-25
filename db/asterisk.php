<?php

class AsteriskMGMT {

    public function Command($cmd) {
	$config = include("config.php");

	$socket = fsockopen($config["asterisk_ip"],$config["manager_port"], $errno, $errstr, 30);

	if (!$socket) {
    		error_log("$errstr ($errno)".PHP_EOL);
		return $errstr;
	} else {
		fputs($socket, "Action: Login\r\n");
		fputs($socket, "UserName: ".$config["manager_login"]."\r\n");
		fputs($socket, "Secret: ".$config["manager_password"]."\r\n\r\n");

		fputs($socket, "Action: Command\r\n");
		fputs($socket, "Command: ".$cmd."\r\n\r\n"); 
		fputs($socket, "Action: Logoff\r\n\r\n");
		$wrets = "";
		while (!feof($socket)) {
			$wrets .= fread($socket, 8192);
		}
		fclose($socket);
		return $wrets;
	}
    }

}


