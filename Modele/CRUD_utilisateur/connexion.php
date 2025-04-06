<?php
// Définit le type de contenu de la réponse HTTP comme étant du JSON
header("Content-Type: application/json");

// Connexion à la base de données
require_once("../db_connection.php");

// Récupération des données envoyées en JSON via la méthode POST
$data = json_decode(file_get_contents("php://input"), true);

// Vérifie si l'email et le mot de passe ne sont pas vides
if (!empty($data["email"]) && !empty($data["mot_de_passe"])) {
    // Assigne l'email et le mot de passe à des variables locales
    $email = $data["email"];
    $mot_de_passe = $data["mot_de_passe"];

    // Prépare la requête SQL pour sélectionner l'id et le mot de passe de l'utilisateur en fonction de l'email
    $stmt = $pdo->prepare("SELECT id, mot_de_passe FROM utilisateurs WHERE email = ?");
    // Exécute la requête en passant l'email comme paramètre
    $stmt->execute([$email]);
    
    // Récupère l'utilisateur correspondant à l'email dans la base de données
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifie si un utilisateur est trouvé et si le mot de passe correspond
    if ($user && password_verify($mot_de_passe, $user["mot_de_passe"])) {
        // Si l'authentification réussit, retourne un message de succès au format JSON
        echo json_encode(["status" => "success", "message" => "Connexion réussie."]);
    } else {
        // Si l'email ou le mot de passe est incorrect, retourne un message d'erreur au format JSON
        echo json_encode(["status" => "error", "message" => "Email ou mot de passe incorrect."]);
    }
} else {
    // Si l'email ou le mot de passe n'est pas fourni, retourne un message d'erreur au format JSON
    echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs."]);
}
?>
