<?php
require '../../vendor/autoload.php'; // Charge l'extension MongoDB

// Connexion à MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");

// Sélection de la base de données et de la collection
$database = $client->eco_ride;
$logsCollection = $database->logs;
?>
