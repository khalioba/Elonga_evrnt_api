<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include('env.php');
include('db_conf.php');


try {
    // SQL statements to create the tables
    $event = "CREATE TABLE events (
    id_event INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    date DATE,
    address VARCHAR(255),
    id_value INT,
    statu INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

    $valu = "CREATE TABLE valu (
    id_value INT AUTO_INCREMENT PRIMARY KEY,
    value VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

    $tickets = "CREATE TABLE tickets (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price INT,
    description TEXT,
    statu INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";


    $company = "CREATE TABLE company (
    id_company INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    logo VARCHAR(255),
    tel VARCHAR(20),
    maps VARCHAR(255),
    email VARCHAR(255),
    web_site VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

    $image_company = "CREATE TABLE image_company (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    image VARCHAR(255),
    id_company INT,
    FOREIGN KEY (id_company) REFERENCES company(id_company),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

    $forums = "CREATE TABLE forums (
    id_forum INT AUTO_INCREMENT PRIMARY KEY,
    id_event INT,
    id_user INT,
    message TEXT,
    FOREIGN KEY (id_event) REFERENCES events(id_event),
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

    $users = "CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    first_name VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    tel VARCHAR(20),
    genre VARCHAR(50),
    age INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$forum = "CREATE TABLE forum (
    id_forum INT AUTO_INCREMENT PRIMARY KEY,
    id_event INT,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_event) REFERENCES events(id_event),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$ticket = "CREATE TABLE ticket (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    id_tickets INT,
    id_user INT,
    statu INT,
    FOREIGN KEY (id_tickets) REFERENCES tickets(id_tickets),
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

    // Execute the SQL statements
    $pdo->exec($event);
    $pdo->exec($valu);
    $pdo->exec($tickets);
    $pdo->exec($users);
    $pdo->exec($company);
    $pdo->exec($image_company);
    $pdo->exec($forums);
    $pdo->exec($forum);
    $pdo->exec($ticket);
    

    echo "Tables created successfully";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>
