<?php
include '../db_connection.php'; // Connexion à la BDD

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $sql = "UPDATE utilisateurs SET pseudo = :pseudo, email = :email, role = :role, credit = :credit WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':pseudo' => $_POST['pseudo'],
        ':email' => $_POST['email'],
        ':role' => $_POST['role'],
        ':credit' => $_POST['credit']
    ]);

    echo json_encode(["status" => "success", "message" => "Utilisateur mis à jour !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Requête invalide"]);
}
?>
