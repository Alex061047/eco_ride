<?php

// Connexions à la base de données MySQL
require '../db_connection.php';
// Connexions à la base de données MongoDB
require '../mongodb/mongo_connection.php';

header('Content-Type: application/json');

// Récupération du token depuis l'URL
$token = $_GET['token'] ?? null;

// Si aucun token n'est fourni, on retourne une erreur
if (!$token) {
    echo json_encode(["status" => "error", "message" => "Token manquant"]);
    exit;
}

// Récupération des collections MongoDB
$avisTokensCollection = $client->eco_ride->avis_tokens;
$avisCollection = $mongo->eco_ride->avis_trajet;

// Recherche du token
$entry = $avisTokensCollection->findOne(['token' => $token]);

// Si aucun token correspondant n'est trouvé
if (!$entry) {
    echo json_encode(["status" => "invalid_token", "message" => "Token invalide"]);
    exit;
}

$userId = $entry['user_id'];
$trajetId = $entry['trajet_id'];
$tokenUsed = $entry['used'] ?? false;

// Vérifie que l'utilisateur a bien participé au trajet
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations 
                           WHERE passager_id = :user_id AND covoiturage_id = :trajet_id AND statut = 'réalisé'");
    $stmt->execute([
        'user_id' => $userId,
        'trajet_id' => $trajetId
    ]);
    $estPassager = $stmt->fetchColumn();

    // Si l'utilisateur n'a pas effectué ce trajet, on refuse l'accès
    if (!$estPassager) {
        echo json_encode(["status" => "forbidden", "message" => "Vous n'avez pas participé à ce trajet."]);
        exit;
    }

    // Vérifie si un avis a déjà été laissé ou si le token est marqué "used"
    $avisExistant = $avisCollection->findOne([
        'utilisateur_id' => $userId,
        'trajet_id' => $trajetId
    ]);

    if ($avisExistant || $tokenUsed) {
        echo json_encode(["status" => "already", "message" => "Vous avez déjà laissé un avis pour ce trajet."]);
        exit;
    }

    // Envoi de l'avis
    echo json_encode([
        "status" => "authorized",
        "user_id" => $userId,
        "trajet_id" => $trajetId
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Erreur serveur : " . $e->getMessage()]);
    exit;
}
