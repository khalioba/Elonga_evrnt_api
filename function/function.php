<?php
// le lien des image 
define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS'])? "https" : "http").
"://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]));



// cconnexion à la basse de donner en ligne
function getcom(){
    return new PDO("mysql:host=localhost;dbname=u246153201_db_elonga;charset=utf8","u246153201_db_elonga","Mot2paSSe");

    // return new PDO("mysql:host=localhost;dbname=elonga;charset=utf8","root","");
}

function sendJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit; // Assurez-vous que rien d'autre n'est envoyé après la réponse JSON
}

// Count
function getCount($tab) {
    $pdo = getcom();
    $req = "SELECT COUNT(*) AS count FROM $tab";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'];
    $stmt->closeCursor();

    sendJSON(['count' => $count]);
}


function saveProfileImage($imageData) {
    if (strpos($imageData, 'data:image/') !== 0 || !preg_match('#^data:image/\w+;base64,#i', $imageData)) {
        throw new Exception("Format d'image non valide");
    }

    $extension = explode('/', mime_content_type($imageData))[1];
    $fileName = uniqid('profile_') . '.' . $extension;
    $targetDir = "image/";
    $targetFile = $targetDir . $fileName;

    $imageData = str_replace(' ', '+', $imageData);
    $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
    $decodedData = base64_decode($base64Str);

    if ($decodedData === false) {
        throw new Exception("Décodage de l'image échoué");
    }

    if (file_put_contents($targetFile, $decodedData) === false) {
        throw new Exception("Échec de la sauvegarde de l'image");
    }

    return $targetFile;
}

function saveImage($imageData) {
    if (strpos($imageData, 'data:image/') !== 0) {
        throw new Exception("Format d'image non valide");
    }

    $extension = explode('/', mime_content_type($imageData))[1];
    $fileName = uniqid('services_') . '.' . $extension;
    $targetDir = "images/";
    $targetFile = $targetDir . $fileName;

    if (file_put_contents($targetFile, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)))) {
        return $targetFile;
    } else {
        throw new Exception("Erreur lors de l'enregistrement du fichier");
    }
}
//----------------------------------les evenement----------------------

//------GET

function getEvents() {
        $pdo = getcom();
        $req = "
            SELECT e.*, v.id_value, v.value, c.id_company, c.name AS name_company, c.logo AS logo_company, ca.id_categorie, ca.name as name_categorie, ca.color

            FROM events e
            LEFT JOIN valu v ON e.id_value = v.id_value
            LEFT JOIN categories ca ON e.id_categorie = ca.id_categorie
            LEFT JOIN company c ON e.id_company = c.id_company
        ";
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Réstructurer le tableau pour inclure les informations de valu
        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                "id_event" => $row["id_event"],
                "title" => $row["title"],
                "description" => $row["description"],
                "image" => URL . "image/" . $row["image"],
                "date" => $row["date"],
                "address" => $row["address"],
                "statu" => $row["statu"],
                "categorie" => [
                    "name" => $row["name_categorie"],
                    "color" => $row["color"]
                ],
                "value" => [
                    "id_value" => $row["id_value"],
                    "value" => $row["value"]
                ],
                "company" => [
                    "id_company" => $row["id_company"],
                    "name_company" => $row["name_company"],
                    "logo_company" => URL . "" . $row["logo_company"],
                ],
                "created_at" => $row["created_at"],
                "updated_at" => $row["updated_at"]
            ];
        }

        sendJSON($formattedResults);
}

function getEventById($id_event){
    $pdo = getcom();
    $req = "
        SELECT e.*, v.id_value, v.value, c.id_company, c.name AS name_company, c.logo AS logo_company

        FROM events e
        LEFT JOIN valu v ON e.id_value = v.id_value
        LEFT JOIN company c ON e.id_company = c.id_company
    WHERE  e.id_event = :id";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id",$id_event,PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults = [
            "id_event" => $row["id_event"],
            "title" => $row["title"],
            "description" => $row["description"],
            "image" => URL . "image/" . $row["image"],
            "date" => $row["date"],
            "address" => $row["address"],
            "statu" => $row["statu"],
            "value" => [
                "id_value" => $row["id_value"],
                "value" => $row["value"]
            ],
            "company" => [
                "id_company" => $row["id_company"],
                "name_company" => $row["name_company"],
                "logo_company" => URL . "" . $row["logo_company"],
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }

    sendJSON($formattedResults);
}

function getEventsByMonths($mois) {
    $pdo = getcom();

    // Obtenez le mois actuel (le mois est fourni en argument)
    $currentMonth = $mois;

    // Requête SQL avec filtrage par mois uniquement
    $req = "
        SELECT e.*, v.id_value, v.value
        FROM events e
        LEFT JOIN valu v ON e.id_value = v.id_value
        WHERE MONTH(e.date) = :month
    ";

    // Préparer la requête
    $stmt = $pdo->prepare($req);
    $stmt->bindParam(':month', $currentMonth, PDO::PARAM_INT);
    
    // Exécuter la requête
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_event" => $row["id_event"],
            "title" => $row["title"],
            "description" => $row["description"],
            "image" => URL . "image/" . $row["image"],
            "date" => $row["date"],
            "address" => $row["address"],
            "value" => [
                "id_value" => $row["id_value"],
                "value" => $row["value"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }

    // Envoyer les résultats sous format JSON
    sendJSON($formattedResults);
}

function getEventsByCurrentMonth() {
    $pdo = getcom();
    $req = "
        SELECT e.*, v.id_value, v.value
        FROM events e
        LEFT JOIN valu v ON e.id_value = v.id_value
    ";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Obtenir la date actuelle
    $currentDate = new DateTime();

    // Structure pour regrouper les événements par mois
    $eventsByMonth = [];

    foreach ($results as $row) {
        $eventDate = new DateTime($row["date"]);
        
        // Ne conserver que les événements dans le futur
        if ($eventDate > $currentDate) {
            $eventMonth = $eventDate->format('F'); // Récupère le nom du mois en lettres (ex: January, February)

            // Regrouper les événements par mois
            if (!isset($eventsByMonth[$eventMonth])) {
                $eventsByMonth[$eventMonth] = []; // Crée un tableau pour chaque mois
            }

            // Ajouter l'événement à son mois correspondant
            $eventsByMonth[$eventMonth][] = [
                "id_event" => $row["id_event"],
                "title" => $row["title"],
                "description" => $row["description"],
                "image" => URL."image/".$row["image"],
                "date" => $row["date"],
                "address" => $row["address"],
                "value" => [
                    "id_value" => $row["id_value"],
                    "value" => $row["value"]
                ],
                "created_at" => $row["created_at"],
                "updated_at" => $row["updated_at"]
            ];
        }
    }

    // Boucler sur chaque mois et afficher les événements correspondants
    $formattedResults = [];
    foreach ($eventsByMonth as $month => $events) {
        $formattedResults[] = [
            "month" => $month,
            "events" => $events
        ];
    }

    sendJSON($formattedResults);
}

//------POST

function postEvent($data) {
        $pdo = getcom(); // Connexion à la base de données
    
        try {
            error_log("Données reçues pour postEvent: " . print_r($data, true));
    
            // Vérifiez que les données nécessaires sont présentes
            if (
                !isset($data['title']) || 
                !isset($data['description']) || 
                !isset($data['date']) || 
                !isset($data['address']) || 
                !isset($data['id_categorie']) || 
                !isset($data['id_company'])
            ) {
                throw new Exception("Informations requises manquantes");
            }
    
            if (!isset($_FILES['image'])) {
                throw new Exception("Image non fournie");
            }
    
            // Enregistrez l'image
            $targetFile = saveImage($_FILES['image']);
            if (!$targetFile) {
                throw new Exception("L'enregistrement de l'image a échoué");
            }
    
            // Préparez la requête d'insertion
            $sql = "INSERT INTO events (title, description, date, address, id_categorie, id_company, image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(1, $data['title']);
            $stmt->bindParam(2, $data['description']);
            $stmt->bindParam(3, $data['date']);
            $stmt->bindParam(4, $data['address']);
            $stmt->bindParam(5, $data['id_categorie']);
            $stmt->bindParam(6, $data['id_company']);
            $stmt->bindParam(7, $targetFile);
    
            // Exécutez la requête
            $stmt->execute();
    
            // Retournez une réponse JSON en cas de succès
            $response = [
                "status" => "success",
                "message" => "Événement créé avec succès"
            ];
            return $response;
    
        } catch (Exception $e) {
            // En cas d'erreur, enregistrez le message d'erreur dans les logs et retournez une réponse d'erreur
            error_log("Erreur dans postEvent: " . $e->getMessage());
            $response = [
                "status" => "error",
                "message" => $e->getMessage()
            ];
            return $response;
        }
    }



//----------------------------------les entreprise----------------------

function getConpany() {
    $pdo = getcom();
    $req = "SELECT * FROM `company` WHERE 1";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_company" => $row["id_company"],
            "name" => $row["name"],
            "type" => $row["type"],
            "description" => $row["description"],
            "image" => URL."image/".$row["image"],
            "logo" => URL."image/".$row["logo"],
            "tel" => $row["tel"],
            "maps" => $row["maps"],
            "email" => $row["email"],
            "web_site" => $row["web_site"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

function getConpanyByNumber($numbre) {
    $pdo = getcom();
    $req = "SELECT * FROM `company` WHERE tel = :number";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":number",$numbre,PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults = [
            "id_company" => $row["id_company"],
            "name" => $row["name"],
            "type" => $row["type"],
            "description" => $row["description"],
            "image" => URL."image/".$row["image"],
            "logo" => URL."image/".$row["logo"],
            "tel" => $row["tel"],
            "maps" => $row["maps"],
            "email" => $row["email"],
            "web_site" => $row["web_site"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

function getConpanyById($id_company) {
    $pdo = getcom();
    $req = "SELECT * FROM `company` WHERE id_company = :id_company";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id_company",$id_company,PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults = [
            "id_company" => $row["id_company"],
            "name" => $row["name"],
            "type" => $row["type"],
            "description" => $row["description"],
            "image" => URL."image/".$row["image"],
            "logo" => URL."image/".$row["logo"],
            "tel" => $row["tel"],
            "maps" => $row["maps"],
            "email" => $row["email"],
            "web_site" => $row["web_site"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

//----------------------------------les forum----------------------
    
function getForum() {
    $pdo = getcom();
    $req = "
        SELECT fs.*, 
               u.id_user, u.name as user_name, u.first_name, u.profil,
               e.id_event, e.title as event_title, e.description as event_description, 
               e.image as event_image, e.date as event_date
        FROM forums fs
        LEFT JOIN users u ON fs.id_user = u.id_user
        LEFT JOIN events e ON fs.id_event = e.id_event
    ";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $formattedResults = [];
    $forumsMap = [];

    foreach ($results as $row) {
        // Vérifier si ce forum a déjà été ajouté pour cet id_forum
        if (!isset($forumsMap[$row["id_forum"]])) {
            // Initialiser les informations du forum avec un tableau de forums vide
            $forumsMap[$row["id_forum"]] = [
                "id_forum" => $row["id_forum"],
                "event" => [
                    "id_event" => $row["id_event"],
                    "event_title" => $row["event_title"],
                    "event_description" => $row["event_description"],
                    "event_image" => URL . "image/" . $row["event_image"],
                    "event_date" => $row["event_date"]
                ],
                "forums" => [],
                "created_at" => $row["created_at"],
                "updated_at" => $row["updated_at"]
            ];
        }

        // Ajouter le message forum au tableau des forums pour ce forum
        $forumsMap[$row["id_forum"]]["forums"][] = [
            "id_forums" => $row["id_forum"], // Il semble que tu veuilles utiliser "id_forum" ici
            "message" => $row["message"],
            "created_at" => $row["created_at"], // Ajouter les informations de la date
            "updated_at" => $row["updated_at"]  // Ajouter les informations de la date
        ];
    }

    // Convertir le forum map en tableau final
    foreach ($forumsMap as $forum) {
        $formattedResults[] = $forum;
    }

    // Envoyer les résultats au format JSON
    sendJSON([
        "forums" => $formattedResults
    ]);
}


function getForumById($id_forum) {
    $pdo = getcom();
    $req = "
        SELECT fs.created_at,fs.id_forums, fs.message, f.id_forum, f.name as forum_name, 
               u.id_user, u.name as user_name, u.first_name, u.profil,
               e.id_event, e.title as event_title, e.description as event_description, 
               e.image as event_image, e.date as event_date
        FROM forums fs
        LEFT JOIN forum f ON fs.id_forums = f.id_forum
        LEFT JOIN users u ON fs.id_user = u.id_user
        LEFT JOIN events e ON f.id_event = e.id_event
        WHERE fs.id_forum = :id
    ";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id", $id_forum, PDO::PARAM_INT); // Correction: PDO::PARAM_INT pour un ID
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $formattedResults = [];
    $forums = [];

    foreach ($results as $row) {
        // Regroupe les forums dans un tableau
        $forums[] = [
            "id_forums" => $row["id_forums"],
            "message" => $row["message"],
            "date" => $row["created_at"],
            "user" => [
                "id_user" => $row["id_user"],
                "user_name" => $row["user_name"],
                "first_name" => $row["first_name"],
                "profil" => URL . $row["profil"],
            ]
        ];
    }

    // Ajouter les informations de l'événement et les forums associés
    $formattedResults = [
        "id_forum" => $results[0]["id_forum"], // On suppose que c'est le même pour toutes les lignes
        "event" => [
            "id_event" => $results[0]["id_event"],
            "event_title" => $results[0]["event_title"],
            "event_description" => $results[0]["event_description"],
            "event_image" => URL . "image/" . $results[0]["event_image"],
            "event_date" => $results[0]["event_date"]
        ],
        "forums" => $forums // Insère le tableau de forums regroupés
    ];

    sendJSON($formattedResults);
}
function getForumByIdEvent($id_event) {
    $pdo = getcom();
    $req = "
    SELECT fs.*, 
           u.id_user, u.name as user_name, u.first_name, u.profil,
           e.id_event, e.title as event_title, e.description as event_description, 
           e.image as event_image, e.date as event_date
    FROM forums fs
    LEFT JOIN users u ON fs.id_user = u.id_user
    LEFT JOIN events e ON fs.id_event = e.id_event
    WHERE fs.id_event = :id
    ";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id", $id_event, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Vérifiez si des résultats existent
    if (empty($results)) {
        // Si aucun forum n'est trouvé pour cet événement
        sendJSON([
            "id_forum" => null,
            "event" => [
                "id_event" => null,
                "event_title" => null,
                "event_description" => null,
                "event_image" => URL . "image/",
                "event_date" => null
            ],
            "forums" => []
        ]);
        return;
    }

    $forums = [];

    foreach ($results as $row) {
        $forums[] = [
            "forums" => $row["id_forums"],
            "message" => htmlspecialchars($row["message"]),
            "date" => $row["created_at"],
            "user" => [
                "id_user" => $row["id_user"],
                "user_name" => $row["user_name"],
                "first_name" => $row["first_name"],
                "profil" => URL . $row["profil"],
            ]
        ];
    }

    // Ajouter les informations de l'événement et les forums associés
    $formattedResults = [
        "forum" => $results[0]["id_forums"], // Utilisation des premiers résultats
        "event" => [
            "id_event" => $results[0]["id_event"],
            "event_title" => $results[0]["event_title"],
            "event_description" => $results[0]["event_description"],
            "event_image" => URL . "image/" . $results[0]["event_image"],
            "event_date" => $results[0]["event_date"]
        ],
        "forums" => $forums
    ];

    sendJSON($formattedResults);
}



//------POST

function postForum($data) {
    $pdo = getcom(); // Connexion à la base de données

    try {
        error_log("Données reçues pour createServices: " . print_r($data, true));
        
        // Vérifiez que les données nécessaires sont présentes
        if (!isset($data['id_event']) || !isset($data['name'])) {
            throw new Exception("Informations requises manquantes");
        }

        // Préparez la requête d'insertion
        $sql = "INSERT INTO forum (id_event, name) VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $data['id_event']);
        $stmt->bindParam(2, $data['name']);

        // Exécutez la requête
        $stmt->execute();

        // Retournez une réponse JSON en cas de succès
        $response = [
            "status" => "success",
            "message" => "forum créé avec succès"
        ];
        return $response;

    } catch (Exception $e) {
        // En cas d'erreur, enregistrez le message d'erreur dans les logs et retournez une réponse d'erreur
        error_log("Erreur dans createEvents: " . $e->getMessage());
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    }
}

function postForums($data) {
    $pdo = getcom(); // Connexion à la base de données

    try {
        error_log("Données reçues pour createServices: " . print_r($data, true));
        
        // Vérifiez que les données nécessaires sont présentes
        if (!isset($data['id_event']) || !isset($data['id_user']) || !isset($data['message'])) {
            throw new Exception("Informations requises manquantes");
        }

        // Préparez la requête d'insertion
        $sql = "INSERT INTO forums (id_event, id_user, message) VALUES (?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $data['id_event']);
        $stmt->bindParam(2, $data['id_user']);
        $stmt->bindParam(3, $data['message']);

        // Exécutez la requête
        $stmt->execute();

        // Retournez une réponse JSON en cas de succès
        $response = [
            "status" => "success",
            "message" => "forum créé avec succès"
        ];
        return $response;

    } catch (Exception $e) {
        // En cas d'erreur, enregistrez le message d'erreur dans les logs et retournez une réponse d'erreur
        error_log("Erreur dans createEvents: " . $e->getMessage());
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    }
}
//---------------------------------- users ----------------------
function getUsers() {
    $pdo = getcom();
    $req = "SELECT * FROM `users`";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_user" => $row["id_user"],
            "name" => $row["name"],
            "first_name" => $row["first_name"],
            "ville" => $row["ville"],
            "email" => $row["email"],
            "genre" => $row["genre"],
            "age" => $row["age"],
            "tel" => $row["tel"],
            "profil" => URL."".$row["profil"],
        ];
    }
    sendJSON($formattedResults);
}

function generateOTP($length = 4) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

function sendSMS($to, $otp) {
    $url = 'https://sms.mtncongo.net/api/sms/';
    $token = '8565e8e2316d99ce983df5ac73054c8ab15555c9';

    $data = [
        "msg" => "Votre code est : " . $otp,
        "receivers" => $to,
        "sender" => "Elonga Even",
        "date_envois" => date('c'),
        "externalId" => 10,
        "callback_url" => "https://www.example.com/api/callback"
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Token ' . $token,
        'Content-Type: application/json; charset=utf-8'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/function/cacert.pem'); 
    // Ajoutez le fichier cacert.pem

    // Pour déboguer temporairement (désactiver en production)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        throw new Exception("Erreur cURL: " . $error_msg);
    }

    curl_close($ch);

    $response_data = json_decode($response, true);

    if (isset($response_data['statut']) && $response_data['statut'] === '200') {
        return [
            "status" => "success",
            "message" => "SMS envoyé avec succès"
        ];
    } else {
        return [
            "status" => "error",
            "message" => isset($response_data['resultat']) ? $response_data['resultat'] : "Erreur inconnue"
        ];
    }
}

function createUser($data) {
    try {
        $pdo = getcom();
        $otp = generateOTP();

        // Assurez-vous que les données nécessaires sont présentes dans le tableau $data
        if (!isset($data['tel']) || empty($data['tel'])) {
            throw new Exception("Le numéro de l'utilisateur est requis");
        }

        // Valider le numéro (exemple : s'assurer qu'il est numérique)
        if (!is_numeric($data['tel'])) {
            throw new Exception("Le numéro de l'utilisateur doit être numérique");
        }

        // Vérifiez si le numéro existe déjà dans la base de données
        $sqlCheck = "SELECT COUNT(*) FROM users WHERE tel = :tel";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':tel', $data['tel']);
        $stmtCheck->execute();
        $numberExists = $stmtCheck->fetchColumn() > 0;

        if ($numberExists) {
            // Si le numéro existe, mettre à jour l'OTP
            $sqlUpdate = "UPDATE users SET otp = :otp WHERE tel = :tel";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':otp', $otp);
            $stmtUpdate->bindParam(':tel', $data['tel']);
            $stmtUpdate->execute();
            $smsResponse = sendSMS($data['tel'], $otp);
            // Renvoyer une réponse indiquant que l'OTP a été mis à jour
            $response = [
                "status" => "success",
                "message" => "OTP mis à jour avec succès",
                "smsResponse" => $smsResponse
            ];
        } else {
            // Si le numéro n'existe pas, insérer un nouvel enregistrement
            $sqlInsert = "INSERT INTO users (otp, tel) VALUES (:otp, :tel)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':otp', $otp);
            $stmtInsert->bindParam(':tel', $data['tel']);
            $stmtInsert->execute();
            $smsResponse = sendSMS($data['tel'], $otp);

            // Renvoyer une réponse indiquant que l'utilisateur a été créé avec succès
            $response = [
                "status" => "success",
                "message" => "Utilisateur créé avec succès",
                "smsResponse" => $smsResponse
            ];
        }
        return $response;

    } catch (PDOException $e) {
        // Gérer les erreurs spécifiques à la base de données
        $response = [
            "status" => "error",
            "message" => "Erreur de base de données : " . $e->getMessage()
        ];
        return $response;

    } catch (Exception $e) {
        // Gérer les erreurs générales
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    }
}

function app($tel, $otp){
    $pdo = getcom();
    $req = "SELECT * FROM `users` WHERE tel = :tel AND otp = :otp";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":tel", $tel, PDO::PARAM_STR);
    $stmt->bindValue(":otp", $otp, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Restructure the array to include valu information
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_user" => $row["id_user"],
            "name" => $row["name"],
            "first_name" => $row["first_name"],
            "ville" => $row["ville"],
            "email" => $row["email"],
            "genre" => $row["genre"],
            "age" => $row["age"],
            "tel" => $row["tel"],
            "profil" => URL."".$row["profil"],
        ];
    }

    sendJSON($formattedResults);
}

function upUsers($data) {
    $pdo = getcom();

    try {
        // Loguer les données reçues dans la fonction
        error_log("Données reçues pour upUsers: " . print_r($data, true));

        // Vérifiez que les données nécessaires sont présentes
        if (!isset($data['tel']) || !isset($data['otp'])) {
            throw new Exception("Informations requises manquantes");
        }

        // Vérifiez d'abord si l'utilisateur existe
        $req = "SELECT * FROM `users` WHERE tel = :tel AND otp = :otp";
        $stmt = $pdo->prepare($req);
        $stmt->bindValue(":tel", $data['tel'], PDO::PARAM_STR);
        $stmt->bindValue(":otp", $data['otp'], PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($results)) {
            throw new Exception("Utilisateur non trouvé ou OTP incorrect");
        }

        // Construction dynamique de la requête UPDATE
        $updateFields = [];
        $updateValues = [];

        if (!empty($data['name'])) {
            $updateFields[] = "name = :name";
            $updateValues[':name'] = $data['name'];
        }
        if (!empty($data['first_name'])) {
            $updateFields[] = "first_name = :first_name";
            $updateValues[':first_name'] = $data['first_name'];
        }
        if (!empty($data['title'])) {
            $updateFields[] = "title = :title";
            $updateValues[':title'] = $data['title'];
        }
        if (!empty($data['email'])) {
            $updateFields[] = "email = :email";
            $updateValues[':email'] = $data['email'];
        }
        if (!empty($data['age'])) {
            $updateFields[] = "age = :age";
            $updateValues[':age'] = $data['age'];
        }
        if (!empty($data['gender'])) {
            $updateFields[] = "genre = :gender";
            $updateValues[':gender'] = $data['gender'];
        }
        if (!empty($data['ville'])) {
            $updateFields[] = "ville = :ville";
            $updateValues[':ville'] = $data['ville'];
        }

        if (!empty($updateFields)) {
            $updateReq = "UPDATE `users` SET " . implode(", ", $updateFields) . " WHERE tel = :tel AND otp = :otp";
            $updateStmt = $pdo->prepare($updateReq);
            foreach ($updateValues as $placeholder => $value) {
                $updateStmt->bindValue($placeholder, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $updateStmt->bindValue(":tel", $data['tel'], PDO::PARAM_STR);
            $updateStmt->bindValue(":otp", $data['otp'], PDO::PARAM_STR);
            $updateStmt->execute();
            $updateStmt->closeCursor();

            $response = [
                "status" => "success",
                "message" => "Informations de l'utilisateur mises à jour avec succès"
            ];
            return $response;
        } else {
            throw new Exception("Aucune donnée à mettre à jour");
        }
    } catch (Exception $e) {
        error_log("Erreur dans upUsers: " . $e->getMessage());
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    }
}

function upUser($data) {
    // Connexion à la base de données
    $pdo = getcom();

    try {
        // Loguer les données reçues dans la fonction
        error_log("Données reçues pour upUsers: " . print_r($data, true));

        // Vérifiez que les données nécessaires sont présentes
        if (!isset($data['tel']) || !isset($data['otp'])) {
            throw new Exception("Informations requises manquantes");
        }

        // Vérifiez si l'utilisateur existe
        $req = "SELECT * FROM `users` WHERE tel = :tel AND otp = :otp";
        $stmt = $pdo->prepare($req);
        $stmt->bindValue(":tel", $data['tel'], PDO::PARAM_STR);
        $stmt->bindValue(":otp", $data['otp'], PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($results)) {
            throw new Exception("Utilisateur non trouvé ou OTP incorrect");
        }

        // Construction dynamique de la requête UPDATE
        $updateFields = [];
        $updateValues = [];

        $fields = ['name', 'first_name', 'title', 'email', 'age', 'gender', 'ville', 'profil'];

        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                if ($field === 'profil') {
                    // Gestion de la photo de profil
                    $imageData = $data['profil'];
                    if (strpos($imageData, 'data:image/') !== 0 || !preg_match('#^data:image/\w+;base64,#i', $imageData)) {
                        throw new Exception("Format d'image non valide");
                    }

                    $extension = explode('/', mime_content_type($imageData))[1];
                    $fileName = uniqid('profile_') . '.' . $extension;
                    $targetDir = "image/";
                    $targetFile = $targetDir . $fileName;

                    $imageData = str_replace(' ', '+', $imageData);
                    $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
                    $decodedData = base64_decode($base64Str);

                    if ($decodedData === false) {
                        throw new Exception("Décodage de l'image échoué");
                    }

                    if (file_put_contents($targetFile, $decodedData) === false) {
                        throw new Exception("Échec de la sauvegarde de l'image");
                    }

                    $updateFields[] = "profil = :profile_picture";
                    $updateValues[':profile_picture'] = $targetFile;
                } else {
                    $updateFields[] = "$field = :$field";
                    $updateValues[":$field"] = $data[$field];
                }
            }
        }

        if (!empty($updateFields)) {
            $updateReq = "UPDATE `users` SET " . implode(", ", $updateFields) . " WHERE tel = :tel AND otp = :otp";
            $updateStmt = $pdo->prepare($updateReq);
            foreach ($updateValues as $placeholder => $value) {
                $updateStmt->bindValue($placeholder, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $updateStmt->bindValue(":tel", $data['tel'], PDO::PARAM_STR);
            $updateStmt->bindValue(":otp", $data['otp'], PDO::PARAM_STR);
            $updateStmt->execute();
            $updateStmt->closeCursor();

            $response = [
                "status" => "success",
                "message" => "Informations de l'utilisateur mises à jour avec succès"
            ];
            return $response;
        } else {
            throw new Exception("Aucune donnée à mettre à jour");
        }
    } catch (Exception $e) {
        error_log("Erreur dans upUsers: " . $e->getMessage());
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    } finally {
        $pdo = null;  // Assurer la fermeture de la connexion
    }
}

//---------------------------------- tickets ----------------------
function getTickets() {
    $pdo = getcom();
    $req = "
        SELECT t.*, e.id_event, e.title, e.image, e.description as description_event, e.date 
        FROM tickets t
        LEFT JOIN events e ON t.id_event = e.id_event
        ";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_tickets" => $row["id_tickets"],
            "name" => $row["name"],
            "price" => $row["price"],
            "description" => $row["description"],
            "statu" => $row["statu"],
            "events" => [
                "id_event" => $row["id_event"],
                "title" => $row["title"],
                "description_event" => $row["description_event"],
                "image" =>  URL . "image/" .$row["image"],
                "date" => $row["date"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

function getTicketByIdEvent($id_event) {
    $pdo = getcom();
    $req = "
        SELECT t.*, e.id_event, e.title, e.image, e.description as description_event, e.date 
        FROM tickets t
        LEFT JOIN events e ON t.id_event = e.id_event
        WHERE e.id_event = :id
        ";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id",$id_event,PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_tickets" => $row["id_tickets"],
            "name" => $row["name"],
            "price" => $row["price"],
            "description" => $row["description"],
            "statu" => $row["statu"],
            "events" => [
                "id_event" => $row["id_event"],
                "title" => $row["title"],
                "description_event" => $row["description_event"],
                "image" =>  URL . "image/" .$row["image"],
                "date" => $row["date"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

function getTicketByUsers($user) {
    $pdo = getcom();
    $req = "
        SELECT t.*, u.id_user, e.id_event, e.title, e.image, e.description as description_event, e.date, ti.statu as statu_tickrt, ti.id_ticket
        FROM tickets t
        LEFT JOIN events e ON t.id_event = e.id_event
        LEFT JOIN ticket ti ON ti.id_tickets = t.id_tickets
        LEFT JOIN users u ON ti.id_user = u.id_user
        WHERE u.id_user = :id
        ";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id",$user,PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_tickets" => $row["id_tickets"],
            "name" => $row["name"],
            "price" => $row["price"],
            "description" => $row["description"],
            "statu" => $row["statu"],
            "events" => [
                "id_event" => $row["id_event"],
                "title" => $row["title"],
                "description_event" => $row["description_event"],
                "image" => $row["image"],
                "date" => $row["date"]
            ],
            "ticket" => [
                "id_ticket" => $row["id_tickets"],
                "statu_tickrt" => $row["statu_tickrt"]
                
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

//------POST

function postTicketsUser($data) {
    $pdo = getcom(); // Connexion à la base de données

    try {
        error_log("Données reçues pour createServices: " . print_r($data, true));
        
        // Vérifiez que les données nécessaires sont présentes
        if (!isset($data['id_tickets']) || !isset($data['id_user'])) {
            throw new Exception("Informations requises manquantes");
        }

        // Préparez la requête d'insertion
        $sql = "INSERT INTO ticket (id_tickets, id_user) VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $data['id_tickets']);
        $stmt->bindParam(2, $data['id_user']);

        // Exécutez la requête
        $stmt->execute();

        // Retournez une réponse JSON en cas de succès
        $response = [
            "status" => "success",
            "message" => "Service créé avec succès"
        ];
        return $response;

    } catch (Exception $e) {
        // En cas d'erreur, enregistrez le message d'erreur dans les logs et retournez une réponse d'erreur
        error_log("Erreur dans createEvents: " . $e->getMessage());
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    }
}

function postTicketsEvent($data) {
    $pdo = getcom(); // Connexion à la base de données

    try {
        error_log("Données reçues pour createServices: " . print_r($data, true));
        
        // Vérifiez que les données nécessaires sont présentes
        if (!isset($data['name']) || !isset($data['price'])|| !isset($data['description'])|| !isset($data['id_event'])) {
            throw new Exception("Informations requises manquantes");
        }

        // Préparez la requête d'insertion
        $sql = "INSERT INTO tickets (name, price, description, id_event) VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $data['name']);
        $stmt->bindParam(2, $data['price']);
        $stmt->bindParam(3, $data['description']);
        $stmt->bindParam(4, $data['id_event']);

        // Exécutez la requête
        $stmt->execute();

        // Retournez une réponse JSON en cas de succès
        $response = [
            "status" => "success",
            "message" => "ticket créé avec succès"
        ];
        return $response;

    } catch (Exception $e) {
        // En cas d'erreur, enregistrez le message d'erreur dans les logs et retournez une réponse d'erreur
        error_log("Erreur dans createEvents: " . $e->getMessage());
        $response = [
            "status" => "error",
            "message" => $e->getMessage()
        ];
        return $response;
    }
}
//--------------------------------------------------------