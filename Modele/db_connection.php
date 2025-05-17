<?php
require '../../vendor/autoload.php'; 
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Paramètres de connexion
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'eco_ride';
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$password = $_ENV['DB_PASS'] ??  getenv('DB_PASS') ?? '';


try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les erreurs SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Récupère les données sous forme de tableau associatif
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?> 