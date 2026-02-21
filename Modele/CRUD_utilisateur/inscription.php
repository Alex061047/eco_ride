<?php
// Connexion à la base de données MySQL
include __DIR__ . '/../db_connection.php';

// Inclure le fichier de gestion des logs MongoDB
include __DIR__ . '/../mongodb/mongo_logs.php';

// Fonction pour ajouter un utilisateur à la base de données
function ajouterUtilisateur($pdo, $pseudo, $email, $mot_de_passe, $role, $credit = 20) {
    // Vérifie si l'email existe déjà dans la base de données
    $check_sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([':email' => $email]);
    $email_exists = $check_stmt->fetchColumn();

// Si l'email est déjà utilisé, message d'erreur et arrête l'exécution

    if ($email_exists > 0) {
        echo json_encode(["status" => "error", "message" => "Cet email est déjà utilisé."]);
        exit;
    }

     // Hashage du mot de passe avant de le stocker dans la base de données pour plus de sécurité
    $hashed_password = password_hash($mot_de_passe, PASSWORD_BCRYPT);

     // Prépare la requête SQL pour insérer un nouvel utilisateur dans la base de données
    $sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credit) 
            VALUES (:pseudo, :email, :mot_de_passe, :role, :credit)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':mot_de_passe' => $hashed_password,
        ':role' => $role,
        ':credit' => $credit
    ]);

// Envoie une réponse JSON de succès
    echo json_encode(["status" => "success", "message" => "Utilisateur ajouté avec succès !"]);

    // Enregistre une action dans les logs MongoDB
    enregistrerLog("Ajout utilisateur", "Utilisateur ajouté : " . $pseudo);
}

// Vérifie si la requête HTTP est bien un POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
// Définit le type de contenu de la réponse en JSON
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);

// Vérifie que tous les champs nécessaires sont bien remplis
    if (!empty($data['pseudo']) && !empty($data['email']) && !empty($data['mot_de_passe']) && !empty($data['role'])) {
        ajouterUtilisateur($pdo, $data['pseudo'], $data['email'], $data['mot_de_passe'], $data['role']);
    } else {
        echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs."]);
    }
    exit;
}

