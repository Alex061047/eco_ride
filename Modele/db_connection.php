<?php

// Paramètres de connexion
$host = 'localhost';
$dbname = 'eco_ride';
$username = 'root'; 
$password = ''; 

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
    ]);
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Requête test : récupérer tous les utilisateurs
$query = $pdo->query("SELECT * FROM utilisateurs");
$users = $query->fetchAll();

// Affichage des utilisateurs
echo "<pre>";
print_r($users);
echo "</pre>";

?>
