<?php
require '../../vendor/autoload.php'; 
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Paramètres de connexion
$host = $_ENV['DB_HOST'] ?: 'localhost';
$dbname = $_ENV['DB_NAME'] ?: 'eco_ride';
$username = $_ENV['DB_USER'] ?: 'root';
$password = $_ENV['DB_PASS'] ?: '';


try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les erreurs SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Récupère les données sous forme de tableau associatif
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?> 