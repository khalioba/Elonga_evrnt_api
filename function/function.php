<?php
// le lien des image 
define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS'])? "https" : "http").
"://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]));

// cconnexion à la basse de donner
function getcom(){
    return new PDO("mysql:host=localhost;dbname=matsuri;charset=utf8","root","");
}

// la sortie en json
function sendJSON($info){
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    
    echo json_encode($info,JSON_UNESCAPED_UNICODE);
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


//----------------------------------les evenement----------------------

function getEvents() {
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

    // Réstructurer le tableau pour inclure les informations de valu
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_event" => $row["id_event"],
            "title" => $row["title"],
            "description" => $row["description"],
            "image" => URL."".$row["image"],
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

    sendJSON($formattedResults);
}

function getEventById($id_event){
    $pdo = getcom();
    $req = "
        SELECT e.*, v.id_value, v.value
        FROM events e
        LEFT JOIN valu v ON e.id_value = v.id_value
    WHERE  e.id_event = :id";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id",$id_event,PDO::PARAM_STR);
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
            "image" => $row["image"],
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

    sendJSON($formattedResults);
}

function getEventsByMonth($mois) {
    $pdo = getcom();

    // Obtenez le mois et l'année actuels
    $currentMonth = $mois;
    $currentYear = date('Y');

    // Requête SQL avec filtrage par mois et année
    $req = "
        SELECT e.*, v.id_value, v.value
        FROM events e
        LEFT JOIN valu v ON e.id_value = v.id_value
        WHERE MONTH(e.date) = :month AND YEAR(e.date) = :year
    ";

    $stmt = $pdo->prepare($req);
    $stmt->bindParam(':month', $currentMonth, PDO::PARAM_INT);
    $stmt->bindParam(':year', $currentYear, PDO::PARAM_INT);
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
            "image" => $row["image"],
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

    sendJSON($formattedResults);
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
            "image" => $row["image"],
            "logo" => $row["logo"],
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
        $formattedResults[] = [
            "id_company" => $row["id_company"],
            "name" => $row["name"],
            "type" => $row["type"],
            "description" => $row["description"],
            "image" => $row["image"],
            "logo" => $row["logo"],
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
        $formattedResults[] = [
            "id_company" => $row["id_company"],
            "name" => $row["name"],
            "type" => $row["type"],
            "description" => $row["description"],
            "image" => $row["image"],
            "logo" => $row["logo"],
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
        SELECT e.*, v.id_value, v.value, f.id_forum, f.name as forum_name
        FROM events e
        LEFT JOIN valu v ON e.id_value = v.id_value
        LEFT JOIN forum f ON e.id_event = f.id_event
    ";
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu et forum
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_event" => $row["id_event"],
            "title" => $row["title"],
            "description" => $row["description"],
            "image" => $row["image"],
            "date" => $row["date"],
            "address" => $row["address"],
            "value" => [
                "id_value" => $row["id_value"],
                "value" => $row["value"]
            ],
            "forum" => [
                "id_forum" => $row["id_forum"],
                "forum_name" => $row["forum_name"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }

    sendJSON($formattedResults);
}

function getForumById($id_forum){
    $pdo = getcom();
    $req = "
        SELECT fs.*, f.id_forum, f.name as forum_name
        FROM forums fs
        LEFT JOIN forum f ON fs.id_forum = f.id_forum
        LEFT JOIN users u ON fs.id_user = u.id_user
        WHERE  fs.id_forum = :id
    ";
    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id",$id_forum,PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Réstructurer le tableau pour inclure les informations de valu et forum
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            "id_forums" => $row["id_forums"],
            "message" => $row["message"],
           "user" => [
                "id_user" => $row["id_user"]
            ],
            "forum" => [
                "id_forum" => $row["id_forum"],
                "forum_name" => $row["forum_name"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }

    sendJSON($formattedResults);
}



//----------------------------------users----------------------
function generateOTP($length = 4) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

function sendSMS($to, $otp) {
    $url = 'https://sms.mtncongo.net/api/sms/';
    $token = '8565e8e2316d99ce983df5ac73054c8ab15555c9'; // Remplacez par votre token personnel

    // Les données du SMS
    $data = [
        "msg" => "Votre code est : " . $otp,
        "receivers" => $to,
        "sender" => "Elonga Even",
        "date_envois" => date('c'), // Date actuelle au format ISO 8601
        "externalId" => 10,
        "callback_url" => "https://www.example.com/api/callback"
    ];

    // Initialiser cURL
    $ch = curl_init($url);

    // Configurer les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Token ' . $token,
        'Content-Type: application/json; charset=utf-8'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Exécuter la requête cURL et obtenir la réponse
    $response = curl_exec($ch);
    
    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        throw new Exception("Erreur cURL: " . $error_msg);
    }

    // Fermer cURL
    curl_close($ch);

    // Décoder la réponse JSON
    $response_data = json_decode($response, true);

    // Vérifier si l'envoi a réussi
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



//----------------------------------tickets----------------------
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
                "image" => $row["image"],
                "date" => $row["date"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

function getTicketById($id_event) {
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
                "image" => $row["image"],
                "date" => $row["date"]
            ],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    sendJSON($formattedResults);
}

function getTicket($user) {
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