<?php
// Connexion à la base de données + MongoDB pour enregistrer les logs
include __DIR__ . '/../db_connection.php';
include __DIR__ . '/../mongodb/mongo_logs.php';
session_start();

// Vérifie que la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données envoyées en JSON
    $data = $GLOBALS['__JSON_BODY'] ?? json_decode(file_get_contents("php://input"), true);
    $sessionUserId = $_SESSION['user_id'] ?? null;
    $sessionRole = $_SESSION['user_role'] ?? null;

    // Sécurité serveur: utilisateur connecté obligatoire
    if (!$sessionUserId) {
        echo json_encode(["status" => "error", "message" => "Utilisateur non connecté."]);
        exit;
    }

     // Vérifie que l'ID utilisateur est bien fourni
    if (!isset($data['id'])) {
        echo json_encode(["status" => "error", "message" => "ID utilisateur manquant."]);
        exit;
    }

    // Sécurité serveur: interdit de modifier un autre compte (sauf admin)
    if ($sessionRole !== 'admin' && (int) $data['id'] !== (int) $sessionUserId) {
        echo json_encode(["status" => "error", "message" => "Action interdite."]);
        exit;
    }

    // Prépare les champs à mettre à jour dynamiquement
    $fields = [];
    $params = [':id' => $data['id']];

    // Mise à jour du pseudo si présent
    if (!empty($data['pseudo'])) {
        $fields[] = "pseudo = :pseudo";
        $params[':pseudo'] = $data['pseudo'];
    }

    // Mise à jour de l'email si présent
    if (!empty($data['email'])) {
        $fields[] = "email = :email";
        $params[':email'] = $data['email'];
    }

    // Mise à jour du rôle si présent
    if (!empty($data['role'])) {
        if ($sessionRole !== 'admin') {
            echo json_encode(["status" => "error", "message" => "Seul un admin peut modifier le rôle."]);
            exit;
        }
        $fields[] = "role = :role";
        $params[':role'] = $data['role'];
    }

    // Mise à jour du mot de passe (haché avec Bcrypt) si présent
    if (!empty($data['mot_de_passe'])) {
        $fields[] = "mot_de_passe = :mot_de_passe";
        $params[':mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
    }

    // Si aucun champ n'est à mettre à jour, on arrête ici
    if (empty($fields)) {
        echo json_encode(["status" => "error", "message" => "Aucune donnée à mettre à jour."]);
        exit;
    }

    // Construction de la requête SQL avec uniquement les champs à mettre à jour
    $sql = "UPDATE utilisateurs SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Enregistrement du log dans MongoDB
    enregistrerLog("Modification utilisateur", "Utilisateur modifié : ID " . $data['id']);
    // Réponse de succès au format JSON
    echo json_encode(["status" => "success", "message" => "Utilisateur mis à jour avec succès."]);
}
?>

