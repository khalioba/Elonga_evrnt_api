<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("function/function.php");

try {
    if (!empty($_GET['api'])) {
        // Traitement des requêtes GET
        $url = explode("/", filter_var($_GET['api'],FILTER_SANITIZE_URL));
        switch ($url[0]) {
            //---------------
            case 'events':
                if (empty($url[1])) {
                    getEvents();
                } else {
                    getEventsByMonth($url[1]);
                }
                break;
            //---------------
            case 'event':
                if (!empty($url[1])) {
                    getEventById($url[1]);
                } else {
                    throw new Exception("Pas d'id d'objet fourni");
                }
                break;
            //---------------
            case 'companys':
                if (empty($url[1])) {
                    getConpany();
                } else {
                    getConpanyByNumber($url[1]);
                }
                break;
            //---------------
            case 'company':
                if (!empty($url[1])) {
                    getConpanyById($url[1]);
                } else {
                    throw new Exception("Pas d'id");
                }
                break;
            //---------------
            case 'forum':
                if (empty($url[1])) {
                    getForum();
                } else {
                    throw new Exception("Pas d'id");
                }
                break;
            //---------------
            case 'getuser':
                if (!empty($url[1])) {
                    app($url[1],$url[2]);
                } else {
                    throw new Exception("Pas d'id");
                }
                break;
            //---------------
            default:
                throw new Exception("La demande n'est pas valide");
                break;
        }
    } 
    
    
    
    
    
 elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handling POST requests
    $inputJSON = file_get_contents('php://input');
    $postData = json_decode($inputJSON, true);

    if (!empty($postData['api'])) {
        $url = explode("/", filter_var($postData['api'], FILTER_SANITIZE_URL));
        switch ($url[0]) {
            case 'createuser':
                if (!empty($postData['data'])) {
                    $result = createUser($postData['data']); // Call to createUser function
                    sendJSON($result); // Send response to client
                } else {
                    throw new Exception("Données manquantes pour la création d'un utilisateur");
                }
                break;
            //---------------
            //---------------UPDATE 
            case 'updateusers':
                if (!empty($postData['data'])) {
                    $result = upUsers($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                }
                break;
            //---------------
            case 'updateuser':
                if (!empty($postData['data'])) {
                    $result = upUser($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                }
                break;
            //---------------
            default:
                throw new Exception("La demande POST n'est pas valide");
        }
    } else {
        throw new Exception("Problème de récupération de l'API");
    }
    } else {
        throw new Exception("Méthode non autorisée");
    }
} catch(Exception $e) {
    $erreur = [
        "message" => $e->getMessage(),
        "code" => $e->getCode()
    ];
    print_r($erreur);
}
?>