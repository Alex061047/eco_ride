<?php
// Type JSON pour la réponse
header("Content-Type: application/json");

// Connexion à la base de donnée et à Mongo log
include '../db_connection.php';
include '../mongodb/mongo_logs.php';

// Vérifie que la requête est bien en POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Méthode invalide."]);
    exit;
}

// Vérifie les champs obligatoires
if (!isset($_FILES['photo']) || !isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Paramètres manquants."]);
    exit;
}

$user_id = intval($_POST['user_id']);
$file = $_FILES['photo'];

// Vérifie qu'il n'y a pas d'erreur dans l'envoi
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'envoi de l'image."]);
    exit;
}

// Autoriser uniquement certains types d'images
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(["status" => "error", "message" => "Format d'image non autorisé."]);
    exit;
}

// Crée le dossier s’il n’existe pas (on sait jamais)
$targetDir = "../../uploads/photos_utilisateurs/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Crée un nom de fichier basé sur l’ID utilisateur
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = $user_id . "." . $extension;
$targetFile = $targetDir . $newFilename;

// Supprime les anciennes photos s’il y en a
foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
    $oldFile = $targetDir . $user_id . '.' . $ext;
    if (file_exists($oldFile)) {
        unlink($oldFile);
    }
}

// Enregistre la nouvelle photo
if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode(["status" => "error", "message" => "Impossible d'enregistrer l'image."]);
    exit;
}

// Met à jour la base de données avec le nouveau nom de fichier
$stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo WHERE id = :id");
$stmt->execute([
    ':photo' => $newFilename,
    ':id' => $user_id
]);

// Enregistre un log Mongo
enregistrerLog("Mise à jour photo", "L'utilisateur $user_id a modifié sa photo de profil.");

// Réponse au frontend
echo json_encode([
    "status" => "success",
    "message" => "Image enregistrée.",
    "newPath" => "../../uploads/photos_utilisateurs/" . $newFilename
]);
