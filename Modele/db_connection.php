<?php

// Paramètres de connexion
$host = 'localhost';
$dbname = 'eco_ride';
$username = 'root'; // Remplace par ton utilisateur MySQL
$password = ''; // Mets ton mot de passe si nécessaire

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les erreurs SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Récupère les données sous forme de tableau associatif
    ]);
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour ajouter un utilisateur
function ajouterUtilisateur($pdo, $pseudo, $email, $mot_de_passe, $role, $credit = 20) {
    $hashed_password = password_hash($mot_de_passe, PASSWORD_BCRYPT); // Hash du mot de passe
    $sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credit) VALUES (:pseudo, :email, :mot_de_passe, :role, :credit)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':mot_de_passe' => $hashed_password,
        ':role' => $role,
        ':credit' => $credit
    ]);
    echo "Utilisateur ajouté avec succès !";
}

// Test d'ajout d'un utilisateur
ajouterUtilisateur($pdo, 'TestUser', 'testuser@email.com', 'MonSuperMotDePasse', 'passager');

?>
