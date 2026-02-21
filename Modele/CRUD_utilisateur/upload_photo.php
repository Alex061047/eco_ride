<?php
header("Content-Type: application/json");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include __DIR__ . '/../db_connection.php';
include __DIR__ . '/../mongodb/mongo_logs.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Methode invalide."]);
    exit;
}

if (!isset($_FILES['photo']) || !isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Parametres manquants."]);
    exit;
}

$user_id = (int) $_POST['user_id'];
$sessionUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$sessionRole = $_SESSION['user_role'] ?? null;

if ($sessionUserId <= 0) {
    echo json_encode(["status" => "error", "message" => "Utilisateur non connecte."]);
    exit;
}

if ($sessionRole !== 'admin' && $user_id !== $sessionUserId) {
    echo json_encode(["status" => "error", "message" => "Action interdite."]);
    exit;
}

$file = $_FILES['photo'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'envoi de l'image."]);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes, true)) {
    echo json_encode(["status" => "error", "message" => "Format d'image non autorise."]);
    exit;
}

$targetDir = "../../uploads/photos_utilisateurs/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = $user_id . "." . $extension;
$targetFile = $targetDir . $newFilename;

foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
    $oldFile = $targetDir . $user_id . '.' . $ext;
    if (file_exists($oldFile)) {
        unlink($oldFile);
    }
}

if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode(["status" => "error", "message" => "Impossible d'enregistrer l'image."]);
    exit;
}

$stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo WHERE id = :id");
$stmt->execute([
    ':photo' => $newFilename,
    ':id' => $user_id,
]);

enregistrerLog("Mise a jour photo", "L'utilisateur $user_id a modifie sa photo de profil.");

echo json_encode([
    "status" => "success",
    "message" => "Image enregistree.",
    "newPath" => "../../uploads/photos_utilisateurs/" . $newFilename,
]);
