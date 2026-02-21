<?php
// Upload photo: utilisateur connecte et id protege.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
$userId = securityRequireAuth();
$userRole = securityGetUserRole();

if (!isset($_POST['user_id']) || !is_numeric((string) $_POST['user_id'])) {
    securityAbort(422, 'user_id manquant.');
}
$targetId = (int) $_POST['user_id'];
if ($userRole !== 'admin' && $targetId !== $userId) {
    securityAbort(403, 'Upload photo interdit pour un autre utilisateur.');
}

require_once __DIR__ . '/../../Modele/CRUD_utilisateur/upload_photo.php';