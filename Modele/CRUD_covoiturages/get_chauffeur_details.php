<?php
// Connexion à la base de données
include __DIR__ . '/../db_connection.php';
// Connexion MongoDB
require_once __DIR__ . '/../mongodb/mongo_connection.php';

// Réponse au format JSON
header('Content-Type: application/json');

// Vérifie si l'ID du chauffeur est bien passé en paramètre GET
if (!isset($_GET['chauffeur_id'])) {
    echo json_encode(["status" => "error", "message" => "ID chauffeur manquant"]);
    exit;
}

$chauffeur_id = $_GET['chauffeur_id']; // Identifiant du chauffeur
$vehicule_id = $_GET['vehicule_id'] ?? null; // Identifiant du véhicule (ou null si non disponible)

// Récupération des infos utilisateur
$query = "SELECT id, pseudo FROM utilisateurs WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $chauffeur_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur est trouvé
if ($user) {
    // Récupération du véhicule
    if ($vehicule_id) {
        // Si un véhicule spécifique est précisé
        $vehiculeQuery = "SELECT * FROM vehicules WHERE id = :vehicule_id AND utilisateur_id = :user_id LIMIT 1";
        $vehiculeStmt = $pdo->prepare($vehiculeQuery);
        $vehiculeStmt->execute([
            'vehicule_id' => $vehicule_id,
            'user_id' => $chauffeur_id
        ]);
    } else {
        // Sinon, on prend le premier véhicule du chauffeur
        $vehiculeQuery = "SELECT * FROM vehicules WHERE utilisateur_id = :user_id LIMIT 1";
        $vehiculeStmt = $pdo->prepare($vehiculeQuery);
        $vehiculeStmt->execute(['user_id' => $chauffeur_id]);
    }

    $vehicule = $vehiculeStmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des préférences
    $prefQuery = "SELECT * FROM preferences WHERE utilisateur_id = :user_id LIMIT 1";
    $prefStmt = $pdo->prepare($prefQuery);
    $prefStmt->execute(['user_id' => $chauffeur_id]);
    $preferences = $prefStmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des avis validés du chauffeur dans MongoDB
    $avisValides = $mongo->eco_ride->avis_trajet->find([
    'chauffeur_id' => (int)$chauffeur_id,
    'statut' => 'validé'
    ]);

// Liste vide recevra les données commentaire et date_envoi (en jour/mois/année)
$avisListe = [];
foreach ($avisValides as $avis) {
    $avisListe[] = [
        'commentaire' => $avis['commentaire'] ?? '',
        'date_envoi' => isset($avis['date_envoi']) ? explode(' - ', $avis['date_envoi'])[1] : ''
    ];
}

    // Réponse JSON 
    echo json_encode([
        "status" => "success",
        "utilisateur" => [
            "pseudo" => $user['pseudo']
        ],
        "vehicule" => $vehicule ?: [],
        "preferences" => $preferences ?: [],
        "avis_valides" => $avisListe,
    ]);
    
} else {
    echo json_encode(["status" => "error", "message" => "Chauffeur non trouvé"]);
}
?>

