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

function saveImage($imageData) {
    if (strpos($imageData, 'data:image/') !== 0) {
        throw new Exception("Format d'image non valide");
    }

    $extension = explode('/', mime_content_type($imageData))[1];
    $fileName = uniqid('services_') . '.' . $extension;
    $targetDir = "image/";
    $targetFile = $targetDir . $fileName;

    if (file_put_contents($targetFile, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)))) {
        return $targetFile;
    } else {
        throw new Exception("Erreur lors de l'enregistrement du fichier");
    }
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
}


//----------------------------------users----------------------
function generateOTP($length = 6) {
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




// function upUser($data) {
//     $pdo = getcom();

//     try {
//         // Loguer les données reçues dans la fonction
//         error_log("Données reçues pour upUsers: " . print_r($data, true));

//         // Vérifiez que les données nécessaires sont présentes
//         if (!isset($data['tel']) || !isset($data['otp'])) {
//             throw new Exception("Informations requises manquantes");
//         }

//         // Vérifiez d'abord si l'utilisateur existe
//         $req = "SELECT * FROM `users` WHERE tel = :tel AND otp = :otp";
//         $stmt = $pdo->prepare($req);
//         $stmt->bindValue(":tel", $data['tel'], PDO::PARAM_STR);
//         $stmt->bindValue(":otp", $data['otp'], PDO::PARAM_STR);
//         $stmt->execute();
//         $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         $stmt->closeCursor();

//         if (empty($results)) {
//             throw new Exception("Utilisateur non trouvé ou OTP incorrect");
//         }

//         // Construction dynamique de la requête UPDATE
//         $updateFields = [];
//         $updateValues = [];

//         if (!empty($data['name'])) {
//             $updateFields[] = "name = :name";
//             $updateValues[':name'] = $data['name'];
//         }
//         if (!empty($data['first_name'])) {
//             $updateFields[] = "first_name = :first_name";
//             $updateValues[':first_name'] = $data['first_name'];
//         }
//         if (!empty($data['title'])) {
//             $updateFields[] = "title = :title";
//             $updateValues[':title'] = $data['title'];
//         }
//         if (!empty($data['email'])) {
//             $updateFields[] = "email = :email";
//             $updateValues[':email'] = $data['email'];
//         }
//         if (!empty($data['age'])) {
//             $updateFields[] = "age = :age";
//             $updateValues[':age'] = $data['age'];
//         }
//         if (!empty($data['gender'])) {
//             $updateFields[] = "genre = :gender";
//             $updateValues[':gender'] = $data['gender'];
//         }
//         if (!empty($data['ville'])) {
//             $updateFields[] = "ville = :ville";
//             $updateValues[':ville'] = $data['ville'];
//         }

//         // Gestion de la photo de profil
//         if (!empty($data['profile_picture'])) {
//             // Décoder l'image base64
//             $imageParts = explode(";base64,", $data['profile_picture']);
//             if (count($imageParts) === 2) {
//                 $imageTypeAux = explode("image/", $imageParts[0]);
//                 $imageType = $imageTypeAux[1];
//                 $imageBase64 = base64_decode($imageParts[1]);
//                 if ($imageBase64 === false) {
//                     throw new Exception("Erreur lors du décodage de l'image");
//                 }

//                 // Générer un nom de fichier unique et sauvegarder l'image
//                 $filePath = 'image/' . uniqid() . '.' . $imageType;
//                 if (file_put_contents($filePath, $imageBase64) === false) {
//                     throw new Exception("Erreur lors de la sauvegarde de l'image");
//                 }
//                 $updateFields[] = "profil = :profile_picture";
//                 $updateValues[':profile_picture'] = $filePath;
//             } else {
//                 throw new Exception("Format d'image base64 invalide");
//             }
//         }

//         if (!empty($updateFields)) {
//             $updateReq = "UPDATE `users` SET " . implode(", ", $updateFields) . " WHERE tel = :tel AND otp = :otp";
//             $updateStmt = $pdo->prepare($updateReq);
//             foreach ($updateValues as $placeholder => $value) {
//                 $updateStmt->bindValue($placeholder, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
//             }
//             $updateStmt->bindValue(":tel", $data['tel'], PDO::PARAM_STR);
//             $updateStmt->bindValue(":otp", $data['otp'], PDO::PARAM_STR);
//             $updateStmt->execute();
//             $updateStmt->closeCursor();

//             $response = [
//                 "status" => "success",
//                 "message" => "Informations de l'utilisateur mises à jour avec succès"
//             ];
//             return $response;
//         } else {
//             throw new Exception("Aucune donnée à mettre à jour");
//         }
//     } catch (Exception $e) {
//         error_log("Erreur dans upUsers: " . $e->getMessage());
//         $response = [
//             "status" => "error",
//             "message" => $e->getMessage()
//         ];
//         return $response;
//     }
// }



// Fonction pour enregistrer l'image à partir des données base64
function saveImageFromBase64($base64Image) {
    // Séparez la base64 de l'en-tête
    $data = explode(',', $base64Image);
    
    if (count($data) != 2) {
        throw new Exception('Format de données base64 invalide');
    }

    // Décoder les données base64
    $imageData = base64_decode($data[1]);
    
    if ($imageData === false) {
        throw new Exception('Échec du décodage des données base64');
    }

    // Déterminer le type de fichier à partir de l'en-tête
    $imageType = '';
    if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
        $imageType = $matches[1]; // Type d'image (jpeg, png, etc.)
    } else {
        throw new Exception('Type d\'image base64 non reconnu');
    }

    // Générer un nom de fichier unique avec extension correspondante
    $extension = strtolower($imageType);
    $targetFile = 'image/' . uniqid() . '.' . $extension;

    // Enregistrer l'image sur le serveur
    if (!file_put_contents($targetFile, $imageData)) {
        throw new Exception('Échec de l\'enregistrement de l\'image sur le serveur');
    }

    return $targetFile; // Retourner le chemin du fichier enregistré
}









// $req = "
//     SELECT e.*, v.id_value, v.value, t.id_ticket, t.name as ticket_name, t.price, t.description as ticket_description, 
//             c.id_company, c.name as company_name, c.type, c.description as company_description, c.tel, c.maps, c.email, c.web_site,
//             u.id_user, u.name as user_name, u.title as user_title, u.first_name, u.email as user_email, u.tel as user_tel, u.genre, u.age
//     FROM events e
//     LEFT JOIN valu v ON e.id_value = v.id_value
//     LEFT JOIN tickets t ON e.id_event = t.id_event
//     LEFT JOIN company c ON e.id_event = c.id_event
//     LEFT JOIN users u ON e.id_event = u.id_event
//     WHERE MONTH(e.date) = :month AND YEAR(e.date) = :year
// ";





