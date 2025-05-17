<?php
require '../../vendor/autoload.php'; // Charge l'extension MongoDB
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Connexion à MongoDB
$mongoUri = $_ENV['MONGO_URI'] ?? 'mongodb://localhost:27017';
$client = new MongoDB\Client($mongoUri);

// Pour que $mongo soit accessible dans les autres fichiers
$mongo = $client;

// Sélection de la base de données et de la collection
$database = $client->eco_ride;
$logsCollection = $database->logs;
?>
