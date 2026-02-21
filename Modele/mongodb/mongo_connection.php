<?php
require __DIR__ . '/../../vendor/autoload.php'; 

use Dotenv\Dotenv;
use MongoDB\Client;

// Variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad(); 

// Récupérer URI MongoDB
$mongoUri = $_ENV['MONGO_URI'] ?? getenv('MONGO_URI') ?? 'mongodb://localhost:27017';

try {
    // Connexion au client MongoDB
    $client = new Client($mongoUri);

    // Base de données 
    $database = $client->eco_ride;

    // Permettre un acces des éléments dans les autres fichiers
    $mongo = $client;
    $logsCollection = $database->logs;

} catch (Exception $e) {
    // Ne bloque pas l'application si Mongo est indisponible.
    $mongo = null;
    $logsCollection = null;
}
?>
