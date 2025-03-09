<?php

include '../db_connection.php';
include '../mongodb/mongo_logs.php';

// Fonction pour ajouter un utilisateur avec vérification de l'email existant
function ajouterUtilisateur($pdo, $pseudo, $email, $mot_de_passe, $role, $credit = 20) {
    // Vérifier si l'email existe déjà
    $check_sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([':email' => $email]);
    $email_exists = $check_stmt->fetchColumn();
    
    if ($email_exists > 0) {
        echo json_encode(["status" => "error", "message" => "Cet email est déjà utilisé."]);
        exit;
    }
    
    // Hash du mot de passe
    $hashed_password = password_hash($mot_de_passe, PASSWORD_BCRYPT);
    
    // Insertion de l'utilisateur
    $sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credit) VALUES (:pseudo, :email, :mot_de_passe, :role, :credit)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':mot_de_passe' => $hashed_password,
        ':role' => $role,
        ':credit' => $credit
    ]);
    
    echo json_encode(["status" => "success", "message" => "Utilisateur ajouté avec succès !"]);



// Enregistrement de l'ajout dans le log Mongodb
enregistrerLog("Ajout utilisateur", "Utilisateur ajouté : ".$pseudo);

}

// Vérifier si la requête est une requête AJAX en POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['mot_de_passe']) && !empty($_POST['role'])) {
        ajouterUtilisateur($pdo, $_POST['pseudo'], $_POST['email'], $_POST['mot_de_passe'], $_POST['role']);
    } else {
        echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs."]);
    }
    exit;
}
?>

<!--Importer le formulaire-->
<div id="fichier_importe"></div>
<div id="message"></div>


  <script src="../../Controleur/ajout_utilisateur.js"></script>

