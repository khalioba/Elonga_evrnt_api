<?php
// Afficher les erreurs PHP
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

// Inclure les fichiers de configuration
include('env.php');
include('db_conf.php');

try {
    // Connexion à la base de données en utilisant PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insérer des données dans la table events
    $eventsData = [
        ['Music Festival', 'A weekend of music and fun', 'music_festival.jpg', '2024-09-01', '789 Music Avenue', 3],
        ['Workshop on Photography', 'Learn photography basics', 'photography_workshop.jpg', '2024-08-25', '101 Photo Lane', 4],
        ['Food Expo', 'Discover new culinary delights', 'food_expo.jpg', '2024-09-10', '456 Food Street', 5],
        ['Tech Meetup', 'Networking and talks on technology', 'tech_meetup.jpg', '2024-08-20', '202 Tech Hub', 2]
    ];

    foreach ($eventsData as $event) {
        $eventStmt = $pdo->prepare("INSERT INTO events (title, description, image, date, address, id_value) VALUES (?, ?, ?, ?, ?, ?)");
        $eventStmt->execute($event);
    }

    // Insérer des données dans la table valu
    $valuData = [
        ['Low'],
        ['High'],
        ['Medium']
    ];

    foreach ($valuData as $value) {
        $valuStmt = $pdo->prepare("INSERT INTO valu (value) VALUES (?)");
        $valuStmt->execute($value);
    }

    // Insérer des données dans la table tickets
    $ticketsData = [
        ['Standard Ticket', 50.00, 'General admission ticket', 3],
        ['VIP Pass', 150.00, 'Access to all areas', 1],
        ['Student Ticket', 30.00, 'Discounted ticket for students', 2]
    ];

    foreach ($ticketsData as $ticket) {
        $ticketStmt = $pdo->prepare("INSERT INTO tickets (name, price, description, id_event) VALUES (?, ?, ?, ?)");
        $ticketStmt->execute($ticket);
    }

    // Insérer des données dans la table users
    $usersData = [
        ['Johnson', 'Mr.', 'Michael', 'michael.johnson@example.com', '123456789', 'Male', 35],
        ['Brown', 'Ms.', 'Emma', 'emma.brown@example.com', '987654321', 'Female', 27],
        ['Williams', 'Dr.', 'David', 'david.williams@example.com', '555555555', 'Male', 42]
    ];

    foreach ($usersData as $user) {
        $userStmt = $pdo->prepare("INSERT INTO users (name, title, first_name, email, tel, genre, age) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $userStmt->execute($user);
    }

    // Insérer des données dans la table achat_ticket
    $achatTicketData = [
        [1, 1],
        [2, 2],
        [3, 3]
    ];

    foreach ($achatTicketData as $achatTicket) {
        $achatTicketStmt = $pdo->prepare("INSERT INTO achat_ticket (id_ticket, id_user) VALUES (?, ?)");
        $achatTicketStmt->execute($achatTicket);
    }

    // Insérer des données dans la table company
    $companiesData = [
        ['Tech Solutions', 'Technology', 'IT solutions provider', '999888777', 'http://maps.example.com', 'info@techsolutions.com', 'http://techsolutions.com'],
        ['Culinary Arts', 'Food', 'Promotes culinary arts', '111222333', 'http://maps.example.com', 'info@culinaryarts.com', 'http://culinaryarts.com']
    ];

    foreach ($companiesData as $company) {
        $companyStmt = $pdo->prepare("INSERT INTO company (name, type, description, tel, maps, email, web_site) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $companyStmt->execute($company);
    }

    // Insérer des données dans la table image_company
    $imageCompaniesData = [
        ['Logo', 'logo_tech.png', 1],
        ['Banner', 'banner_food.png', 2]
    ];

    foreach ($imageCompaniesData as $imageCompany) {
        $imageCompanyStmt = $pdo->prepare("INSERT INTO image_company (title, image, id_company) VALUES (?, ?, ?)");
        $imageCompanyStmt->execute($imageCompany);
    }

    // Insérer des données dans la table forums
    $forumsData = [
        [1, 1, 'Looking forward to the festival!'],
        [2, 2, 'Excited about the photography workshop'],
        [3, 3, 'Can\'t wait to try the new food at the expo'],
        [4, 1, 'Discussing the latest tech trends']
    ];

    foreach ($forumsData as $forum) {
        $forumStmt = $pdo->prepare("INSERT INTO forums (id_event, id_user, message) VALUES (?, ?, ?)");
        $forumStmt->execute($forum);
    }

    echo "Data inserted successfully";
} catch (PDOException $e) {
    echo "Error inserting data: " . $e->getMessage();
}

// Fermer la connexion à la base de données
$pdo = null;
?>
