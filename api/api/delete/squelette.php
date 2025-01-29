<?php

// Définir les en-têtes HTTP nécessaires
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Origin: *");  // Permet l'accès depuis tous les domaines

// Inclure la configuration et la classe de base de données
include_once "../../config/database.php";

// Créer une instance de la base de données et obtenir la connexion
$database = new Database();
$db = $database->getConnection();

// Vérifier si la méthode HTTP est DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Vérifier si un ID a été passé dans l'URL ou le corps de la requête
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->id)) {
        $id = $data->id; // L'ID de l'utilisateur à supprimer
        
        // Préparer la requête SQL pour supprimer l'utilisateur
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        
        // Lier l'ID à la requête
        $stmt->bindParam(":id", $id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "User deleted successfully",
                "data" => null  // Pas de données spécifiques à renvoyer
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Unable to delete user",
                "data" => null
            ]);
        }
    } else {
        // Si l'ID n'est pas fourni, renvoyer une erreur
        echo json_encode([
            "status" => "error",
            "message" => "User ID is required",
            "data" => null
        ]);
    }
} else {
    // Répondre en cas de méthode HTTP non autorisée
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method",  // Si la méthode n'est pas DELETE
        "data" => null
    ]);
}
?>
