<?php
include "../../../models/diag/database.php";
$config = include("../../config.php");

$total = new DiagDatabaseRepository();
//error_log("ast: call command ".PHP_EOL);

switch($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $result = $total->getAll(            
		array("str" => $_GET["str"]));               
        break;  
}

header("Content-Type: application/json");
echo  json_encode($result);

?>


