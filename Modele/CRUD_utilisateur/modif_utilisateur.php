<?php
include '../db_connection.php';

// Fonction pour modifier un utilisateur
function modifierUtilisateur($pdo, $pseudo, $email, $role, $credit) {
    $sql = "UPDATE utilisateurs SET pseudo = :pseudo, email = :email, role = :role, credit = :credit WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':role' => $role,
        ':credit' => $credit
    ]);
    echo json_encode(["status" => "success", "message" => "Utilisateur mis à jour avec succès !"]);
}

// Vérifier si la requête est une requête POST en JSON
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    // Récupérer les données JSON envoyées par Fetch
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['pseudo']) && !empty($data['email']) && !empty($data['role']) && isset($data['credit'])) {
        modifierUtilisateur($pdo, $data['pseudo'], $data['email'], $data['role'], $data['credit']);
    } else {
        echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs."]);
    }
    exit;
}
?>
