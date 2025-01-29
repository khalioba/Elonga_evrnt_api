<?php
include_once "../../includes/header_post.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification si une image a été envoyée
    $responseImage = isset($_FILES['image']) ? uploadImage($_FILES['image']) : null;

    // Validation si le fichier image est obligatoire
    if ($responseImage && $responseImage['status'] === 'error') {
        echo json_encode([
            "status" => "error",
            "message" => "Image upload failed",
            "data" => $responseImage
        ]);
        exit;
    }

    // Lecture des données envoyées via form-data
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $id_categorie = $_POST['id_categorie'] ?? null;
    $id_company = $_POST['id_company'] ?? null;
    $date = $_POST['date'] ?? null;
    $address = $_POST['address'] ?? null;
    $id_value = $_POST['id_value'] ?? null;
    $statu = $_POST['statu'] ?? null;

    // Validation des champs obligatoires
    if (!$title || !$id_categorie || !$id_company) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields: title, id_categorie, id_company.",
            "data" => null
        ]);
        exit;
    }

    // Requête SQL pour insérer un nouvel événement
    $query = "INSERT INTO events (title, description, id_categorie, id_company, image, date, address, id_value, statu) 
              VALUES (:title, :description, :id_categorie, :id_company, :image, :date, :address, :id_value, :statu)";
    
    $stmt = $db->prepare($query);

    // Liaisons des paramètres
    $stmt->bindValue(":title", $title);
    $stmt->bindValue(":description", $description);
    $stmt->bindValue(":id_categorie", $id_categorie);
    $stmt->bindValue(":id_company", $id_company);
    $stmt->bindValue(":image", $responseImage ? $responseImage['data'] : null);
    $stmt->bindValue(":date", $date);
    $stmt->bindValue(":address", $address);
    $stmt->bindValue(":id_value", $id_value);
    $stmt->bindValue(":statu", $statu);


    // Exécution de la requête
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Event created successfully",
            "data" => [
                "title" => $title,
                "description" => $description,
                "image_path" => $responseImage ? $responseImage['data'] : null
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Unable to create event",
            "data" => null
        ]);
    }
}
?>
