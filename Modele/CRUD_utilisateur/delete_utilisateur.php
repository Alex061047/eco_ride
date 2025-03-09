<?php
include '../db_connection.php'; // Connexion à la BDD

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    echo json_encode(["status" => "success", "message" => "Utilisateur supprimé avec succès !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Requête invalide"]);
}
?>
