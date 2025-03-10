<?php
include '../db_connection.php';
include '../mongodb/mongo_logs.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données JSON envoyées
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["status" => "error", "message" => "ID utilisateur manquant."]);
        exit;
    }

    $sql = "UPDATE utilisateurs SET pseudo = :pseudo, email = :email, role = :role, credit = :credit WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $data['id'],
        ':pseudo' => $data['pseudo'],
        ':email' => $data['email'],
        ':role' => $data['role'],
        ':credit' => $data['credit']
    ]);

    echo json_encode(["status" => "success", "message" => "Utilisateur mis à jour !"]);

    // Enregistrement modification dans le log MongoDB
    enregistrerLog("Modification utilisateur", "Utilisateur modifié : ID " . $data['id']);
} else {
    echo json_encode(["status" => "error", "message" => "Requête invalide"]);
}

?>
