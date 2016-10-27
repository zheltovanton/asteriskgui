<?php
include "../../../models/diag/total.php";
$config = include("../../config.php");

$total = new DiagTotalRepository();
//error_log("ast: call command ".PHP_EOL);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $total->getAll();               
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>


