<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");

include_once "../../config/database.php";

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Code pour créer un étudiant (comme précédemment)
    $data = json_decode(file_get_contents("php://input"));
    $query = "INSERT INTO users (name, number) VALUES (:name, :number)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":number", $data->number);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Student created successfully",
            "data" => null
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Unable to create student",
            "data" => null
        ]);
    }

}