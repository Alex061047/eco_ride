<?php
// Connexion à MongoDB
require_once __DIR__ . '/../mongodb/mongo_connection.php';
session_start();

// Réponse en JSON
header('Content-Type: application/json');

// Sécurité serveur: accès réservé admin
if (!isset($_SESSION['user_id']) || (($_SESSION['user_role'] ?? '') !== 'admin')) {
    echo json_encode(["status" => "error", "message" => "Accès interdit."]);
    exit;
}

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

