<?php
// Connexion à la base de données MySQL
include('../db_connection.php');

// Réponse JSON
header('Content-Type: application/json');

// Récupération du JSON
$data = json_decode(file_get_contents("php://input"), true);

// Vérification des données reçues
if (!isset($data['pseudo'], $data['email'], $data['mot_de_passe'], $data['role'])) {
    echo json_encode(["status" => "error", "message" => "Champs manquants."]);
    exit;
}

// Attribution des valeurs avec hash du mot de passe
$pseudo = htmlspecialchars($data['pseudo']);
$email = htmlspecialchars($data['email']);
$mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
$role = $data['role']; // associé à la valeur employe

// Vérifie si l'email existe déjà
$check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$check->execute([$email]);
if ($check->rowCount() > 0) {
    echo json_encode(["status" => "error", "message" => "Email déjà utilisé."]);
    exit;
}

// Insertion du nouvel employé
$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credit, note, photo_profil) 
        VALUES (?, ?, ?, ?, 0, 0, 'default.jpg')";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$pseudo, $email, $mot_de_passe, $role]);

if ($success) {
    echo json_encode(["status" => "success", "message" => "Employé créé avec succès."]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la création."]);
}
?>
