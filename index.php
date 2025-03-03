<?php

error_log("Méthode HTTP utilisée: " . $_SERVER['REQUEST_METHOD']);
error_log("Données GET: " . print_r($_GET, true));
error_log("Données POST: " . file_get_contents('php://input'));

// Initialisation cURL
$ch = curl_init(); // Crée un handle cURL

// Chemin vers le fichier cacert.pem
curl_setopt($ch, CURLOPT_CAINFO, 'cacert.pem');

include("function/function.php");

try {
    if (!empty($_GET['api'])) {
        // Traitement des requêtes GET
        $url = explode("/", filter_var($_GET['api'],FILTER_SANITIZE_URL));

        switch ($url[0]) {
            //---------------//
            case 'events':
                if (empty($url[1])) {
                    getEvents();
                } else {
                    getEventById($url[1]);
                }
                break;
            //---------------
            case 'eventsCurrent':
                if (empty($url[1])) {
                    getEventsByCurrentMonth();
                } else {
                    getEventsByMonths($url[1]);
                }
                break;
            //---------------//
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
            //---------------//
            case 'forums':
                if (empty($url[1])) {
                    getForum();
                } else {
                    getForumByIdEvent($url[1]);
                }
                break;
            //---------------
            case 'forum':
                if (!empty($url[1])) {
                    getForumById($url[1]);
                } else {
                    throw new Exception("Pas d'id");
                }
                break;
            //---------------//
            case 'getuser':
                if (!empty($url[1])) {
                    app($url[1],$url[2]);
                } else {
                    throw new Exception("Pas d'id");
                }
                break;
            //---------------//
            case 'getusers':
                if (empty($url[1])) {
                    getUsers();
                } else {
                    throw new Exception("");
                }
                break;
            //---------------//
            case 'tickets':
                if (empty($url[1])) {
                    getTickets();
                } else {
                    getTicketByIdEvent($url[1]);
                }
                break;
            //---------------//
            case 'ticket':
                if (!empty($url[1])) {
                    getTicketByUsers($url[1]);
                } else {
                    throw new Exception("Pas d'id");
                }
                break;
            //---------------//
            default:
                throw new Exception("La demande n'est pas valide");
                break;
        }
    } 
    
    
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputJSON = file_get_contents('php://input');
        $postData = json_decode($inputJSON, true);

        // Loguer les données reçues
        error_log("POST data: " . print_r($postData, true));

        if (!empty($postData['api'])) {
            $url = explode("/", filter_var($postData['api'], FILTER_SANITIZE_URL));
        switch ($url[0]) {
            case 'createuser':
                if (!empty($postData['data'])) {
                    $result = createUser($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la création d'un utilisateur");
                }
                break;
                  //---------------//
            case 'createvent':
                if (!empty($postData['data'])) {
                    $result = postEvent($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la création d'un événement");
                }
                break;
                 //---------------//
            case 'updateusers':
                if (!empty($postData['data'])) {
                    $result = upUsers($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                }
                break;
                 //---------------//
            case 'updateuser':
                if (!empty($postData['data'])) {
                    $result = upUser($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                }
                break;
                //---------------//
            case 'tickets_user':
                if (!empty($postData['data'])) {
                    $result = postTicketsUser($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                }
                break;
                //---------------//
            case 'ticketsEvent':
                if (!empty($postData['data'])) {
                    $result = postTicketsEvent($postData['data']);
                    sendJSON($result);
                } else {
                    throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                }
                break;
                //---------------//
                case 'postforum':
                    if (!empty($postData['data'])) {
                        $result = postForum($postData['data']);
                        sendJSON($result);
                    } else {
                        throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                    }
                    break;
                    //---------------//
                case 'postforums':
                    if (!empty($postData['data'])) {
                        $result = postForums($postData['data']);
                        sendJSON($result);
                    } else {
                        throw new Exception("Données manquantes pour la mise à jour de l'utilisateur");
                    }
                    break;
                    //---------------//
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