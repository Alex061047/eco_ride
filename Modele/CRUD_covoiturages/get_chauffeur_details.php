<?php
// Connexion à la base de données
include('../db_connection.php');
// Réponse au format JSON
header('Content-Type: application/json');

// Vérifie si l'ID du chauffeur est bien passé en paramètre GET
if (!isset($_GET['chauffeur_id'])) {
    echo json_encode(["status" => "error", "message" => "ID chauffeur manquant"]);
    exit;
}

$chauffeur_id = $_GET['chauffeur_id'];

// Récupération des infos utilisateur
$query = "SELECT id, pseudo FROM utilisateurs WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $chauffeur_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Récupération des infos véhicule lié au chauffeur
    $vehiculeQuery = "SELECT * FROM vehicules WHERE utilisateur_id = :user_id LIMIT 1";
    $vehiculeStmt = $pdo->prepare($vehiculeQuery);
    $vehiculeStmt->execute(['user_id' => $chauffeur_id]);
    $vehicule = $vehiculeStmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des préférences
    $prefQuery = "SELECT * FROM preferences WHERE utilisateur_id = :user_id LIMIT 1";
    $prefStmt = $pdo->prepare($prefQuery);
    $prefStmt->execute(['user_id' => $chauffeur_id]);
    $preferences = $prefStmt->fetch(PDO::FETCH_ASSOC);

    // Réponse JSON avec les données trouvées
    echo json_encode([
        "status" => "success",
        "utilisateur" => [
            "pseudo" => $user['pseudo']
        ],
        "vehicule" => $vehicule ?: [],
        "preferences" => $preferences ?: [],
    ]);
    
    
} else {
    echo json_encode(["status" => "error", "message" => "Chauffeur non trouvé"]);
}
?>
