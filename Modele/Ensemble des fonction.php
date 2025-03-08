<?php

// Paramètres de connexion
$host = 'localhost';
$dbname = 'eco_ride';
$username = 'root'; 
$password = ''; 

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

// Fonction pour modifier un utilisateur
function modifierUtilisateur($pdo, $id, $pseudo, $email, $role, $credit) {
    $sql = "UPDATE utilisateurs SET pseudo = :pseudo, email = :email, role = :role, credit = :credit WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':role' => $role,
        ':credit' => $credit
    ]);
    echo "Utilisateur mis à jour avec succès !";
}

// Fonction pour supprimer un utilisateur
function supprimerUtilisateur($pdo, $id) {
    $sql = "DELETE FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    echo "Utilisateur supprimé avec succès !";
}


?>
