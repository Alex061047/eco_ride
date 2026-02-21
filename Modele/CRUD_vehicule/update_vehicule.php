<?php
// Connexion à la base de données
include __DIR__ . '/../db_connection.php';
session_start();

// Enregistrement des logs dans MongoDB
include __DIR__ . '/../mongodb/mongo_logs.php';

// Vérifie que la requête est bien de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $GLOBALS['__JSON_BODY'] ?? json_decode(file_get_contents("php://input"), true);
    $sessionUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $sessionRole = $_SESSION['user_role'] ?? null;

    // Sécurité serveur: utilisateur connecté obligatoire
    if ($sessionUserId <= 0) {
        echo json_encode(["status" => "error", "message" => "Utilisateur non connecté."]);
        exit;
    }

     // Vérifie que l'identifiant du véhicule et celui de l'utilisateur sont bien présents
    if (!isset($data["id"]) || !isset($data["utilisateur_id"])) {
        echo json_encode(["status" => "error", "message" => "Identifiants manquants."]);
        exit;
    }

    // Sécurité serveur: modification uniquement sur ses propres véhicules (sauf admin)
    if ($sessionRole !== 'admin' && (int) $data["utilisateur_id"] !== $sessionUserId) {
        echo json_encode(["status" => "error", "message" => "Action interdite."]);
        exit;
    }

     // Initialisation des champs et des paramètres à utiliser dans la requête SQL
    $fields = [];
    $params = [':id' => $data['id'], ':utilisateur_id' => $data['utilisateur_id']];

    // Liste des champs pouvant être mis à jour
    foreach (["immatriculation", "modele", "couleur", "marque", "nb_places", "energie", "date_immatriculation"] as $field) {
        if (!empty($data[$field])) {
            $fields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    // Si aucun champ à modifier n'a été trouvé
    if (empty($fields)) {
        echo json_encode(["status" => "error", "message" => "Aucune donnée à mettre à jour."]);
        exit;
    }

    // Construction de la requête SQL
    $sql = "UPDATE vehicules SET " . implode(", ", $fields) . " WHERE id = :id AND utilisateur_id = :utilisateur_id";
  
    // Préparation et exécution de la requête SQL
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

     // Enregistre un log dans MongoDB après modification du véhicule
     enregistrerLog("Modification véhicule", "Véhicule ID {$data['id']} modifié par utilisateur ID {$data['utilisateur_id']}.");


     // Réponse en cas de succès
    echo json_encode(["status" => "success", "message" => "Véhicule mis à jour avec succès."]);
} 
// Si la méthode HTTP n'est pas POST
else {
    echo json_encode(["status" => "error", "message" => "Requête invalide."]);
}
?>

