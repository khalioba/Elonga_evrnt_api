<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

include_once "../../config/database.php";

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Vérifie si un ID est passé dans l'URL
    if (isset($_GET['id'])) {
        $student_id = intval($_GET['id']);
        $query = "SELECT id, name, number FROM users WHERE id = :id LIMIT 0,1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $student_id);
        $stmt->execute();
        
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Réponse avec données si l'étudiant est trouvé
        if ($student) {
            echo json_encode([
                "status" => "success",
                "message" => "Student found successfully",
                "data" => $student
            ]);
        } else {
            // Réponse d'erreur si l'étudiant n'est pas trouvé
            echo json_encode([
                "status" => "error",
                "message" => "Student not found",
                "data" => null
            ]);
        }
    } else {
        $query = "SELECT id, name, number FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $student = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($student) {
            echo json_encode([
                "status" => URL . "success",
                "message" => "Student found successfully",
                "data" => $student
            ]);
        } else {
            // Réponse d'erreur si l'étudiant n'est pas trouvé
            echo json_encode([
                "status" => "error",
                "message" => "Student not found",
                "data" => null
            ]);
        }
    }

}
hjgirijfk  irkjr rikjkzr ierj rlrjr, rljkj irjirpofjgutnkf 