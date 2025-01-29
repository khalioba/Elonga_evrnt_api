<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Origin: *");

include_once "../../config/database.php";

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Code pour modifier un étudiant (comme précédemment)
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->id)) {
        echo json_encode([
            "status" => "error",
            "message" => "Student ID is required",
            "data" => null
        ]);
        exit();
    }

    $query = "UPDATE users SET name = :name, number = :number WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $data->id);
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":number", $data->number);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Student updated successfully",
            "data" => null
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Unable to update student",
            "data" => null
        ]);
    }
}
