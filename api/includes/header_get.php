<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET ");
header("Access-Control-Allow-Origin: *");

include_once "../../config/database.php";
include_once "../../function/function.php";

$database = new Database();
$db = $database->getConnection();

?>