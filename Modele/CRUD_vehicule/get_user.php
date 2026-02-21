<?php
// Connexion a la base de donnees
include __DIR__ . '/../db_connection.php';

// Demarrage de la session pour acceder a l'ID utilisateur
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifie si l'utilisateur est connecte
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Recuperation des infos utilisateur
    $query = "SELECT id, pseudo, email, photo_profil, role, credit FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Recuperation des infos vehicule
        $vehiculeQuery = "SELECT * FROM vehicules WHERE utilisateur_id = :user_id";
        $vehiculeStmt = $pdo->prepare($vehiculeQuery);
        $vehiculeStmt->execute(['user_id' => $user_id]);
        $vehicules = $vehiculeStmt->fetchAll(PDO::FETCH_ASSOC);

        // Preferences
        $prefQuery = "SELECT * FROM preferences WHERE utilisateur_id = :user_id LIMIT 1";
        $prefStmt = $pdo->prepare($prefQuery);
        $prefStmt->execute(['user_id' => $user_id]);
        $preferences = $prefStmt->fetch(PDO::FETCH_ASSOC);

        // Envoie des donnees au format JSON
        echo json_encode([
            'status' => 'success',
            'user' => $user,
            'vehicules' => $vehicules,
            'preferences' => $preferences ? $preferences : null
        ], JSON_UNESCAPED_UNICODE);
    }
    // Utilisateur introuvable
    else {
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non trouve'], JSON_UNESCAPED_UNICODE);
    }
}
// L'utilisateur n'est pas connecte
else {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecte'], JSON_UNESCAPED_UNICODE);
}
?>
