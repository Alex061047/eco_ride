<?php
// Connexion à MongoDB
require_once('../mongodb/mongo_connection.php');

// Réponse en JSON
header('Content-Type: application/json');

// On va récupérer les infos dans la bonne collection
$collection = $mongo->eco_ride->logs_credit;

// Pipeline d'agrégation pour additionner tous les crédits
$pipeline = [
    [
        '$group' => [
            '_id' => null,
            'totalCredits' => ['$sum' => '$credits_plateforme']
        ]
    ]
];

$cursor = $collection->aggregate($pipeline);

// Récupération du total depuis le curseur
$total = 0;
foreach ($cursor as $doc) {
    $total = $doc['totalCredits'];
}

// Retour en JSON
echo json_encode(['total' => $total]);
?>
