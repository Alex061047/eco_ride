<?php
// Définit le type de contenu de la réponse en JSON
header("Content-Type: application/json");

// Connexion à la base de données
include '../db_connection.php';

// Enregistrement des logs dans MongoDB
include '../mongodb/mongo_logs.php';

// Vérifie que la requête est une méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Vérifie que les identifiants nécessaires sont présents
    if (!isset($data["id"]) || !isset($data["utilisateur_id"])) {
        echo json_encode(["status" => "error", "message" => "Données incomplètes."]);
        exit;
    }

     // Prépare et exécute la requête SQL pour supprimer le véhicule correspondant
    $stmt = $pdo->prepare("DELETE FROM vehicules WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$data["id"], $data["utilisateur_id"]]);

     // Vérifie si une suppression a bien été effectuée
    if ($stmt->rowCount() > 0) {
        // Enregistre l'action de suppression dans les logs MongoDB
        enregistrerLog("Suppression véhicule", "Véhicule ID : " . $data["id"] . " supprimé pour utilisateur ID : " . $data["utilisateur_id"]);
        // Retourne une réponse JSON de succès
        echo json_encode(["status" => "success", "message" => "Véhicule supprimé avec succès."]);
    } 
    // Retourne une erreur si aucun véhicule n'a été supprimé
    else {
        echo json_encode(["status" => "error", "message" => "Aucun véhicule supprimé."]);
    }
} 
// Retourne une erreur si la requête n'est pas de type POST
else {
    echo json_encode(["status" => "error", "message" => "Requête invalide."]);
}
?>
