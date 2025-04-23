<?php
// Connexion à la base de données
include('../db_connection.php');
// Démarrage de la session pour accéder à l'ID utilisateur
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Récupération des infos utilisateur
    $query = "SELECT id, pseudo, email, photo_profil, role, credit FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Récupération des infos véhicule
        $vehiculeQuery = "SELECT * FROM vehicules WHERE utilisateur_id = :user_id";
        $vehiculeStmt = $pdo->prepare($vehiculeQuery);
        $vehiculeStmt->execute(['user_id' => $user_id]);
        $vehicules = $vehiculeStmt->fetchAll(PDO::FETCH_ASSOC);

        // Préférences
        $prefQuery = "SELECT * FROM preferences WHERE utilisateur_id = :user_id LIMIT 1";
        $prefStmt = $pdo->prepare($prefQuery);
        $prefStmt->execute(['user_id' => $user_id]);
        $preferences = $prefStmt->fetch(PDO::FETCH_ASSOC);

        // Envoie des données au format JSON
        echo json_encode([
            'status' => 'success',
            'user' => $user,
            'vehicules' => $vehicules,
            'preferences' => $preferences ? $preferences : null
        ]);        
    } 
    // Utilisateur introuvable
    else {
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non trouvé']);
    }
} 
// L'utilisateur n'est pas connecté
else {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
}
?>
