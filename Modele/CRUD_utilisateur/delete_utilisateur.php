<?php
include '../db_connection.php'; // Connexion à la BDD
include '../mongodb/mongo_logs.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    echo json_encode(["status" => "success", "message" => "Utilisateur supprimé avec succès !"]);

    //Enregistrement suppression dasn le log MongoDB
    enregistrerLog("Suppression utilisateur", "Utilisateur supprimé : ID ".$id);

} else {
    echo json_encode(["status" => "error", "message" => "Requête invalide"]);
}

?>
