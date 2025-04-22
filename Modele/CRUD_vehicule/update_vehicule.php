<?php
// Connexion à la base de données
include '../db_connection.php';

// Enregistrement des logs dans MongoDB
include '../mongodb/mongo_logs.php';

// Vérifie que la requête est bien de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

     // Vérifie que l'identifiant du véhicule et celui de l'utilisateur sont bien présents
    if (!isset($data["id"]) || !isset($data["utilisateur_id"])) {
        echo json_encode(["status" => "error", "message" => "Identifiants manquants."]);
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
