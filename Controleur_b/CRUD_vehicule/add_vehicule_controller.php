<?php
// Ajout vehicule: utilisateur connecte, id verrouille.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
$userId = securityRequireAuth();
$userRole = securityGetUserRole();

$payload = securityReadJsonBody();
$GLOBALS['__JSON_BODY'] = $payload;
if (!isset($payload['utilisateur_id']) || !is_numeric((string) $payload['utilisateur_id'])) {
    securityAbort(422, 'utilisateur_id manquant.');
}
$targetId = (int) $payload['utilisateur_id'];
if ($userRole !== 'admin' && $targetId !== $userId) {
    securityAbort(403, 'Ajout vehicule interdit pour un autre utilisateur.');
}

require_once __DIR__ . '/../../Modele/CRUD_vehicule/add_vehicule.php';
