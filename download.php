<?php

require_once 'db/config.php';

$config = include("db/config.php");


header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
error_log("PATH: ".$config["logfile"].PHP_EOL,3, "log.log");


if (isset($_GET['audio'])) {
  header("Content-Type: application/$system_audio_format");
  header('Content-Transfer-Encoding: binary');
  header('Content-Length: '.filesize($config["monitor"].$_GET['audio']));
  header("Content-Disposition: attachment; filename=".$_GET['audio']);
  readfile($config["monitor"].$_GET['audio']);
} 

exit();
?>
