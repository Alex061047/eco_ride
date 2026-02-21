<?php
// Modification vehicule: utilisateur connecte, proprietaire controle.
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
    securityAbort(403, 'Modification vehicule interdite pour un autre utilisateur.');
}

require_once __DIR__ . '/../../Modele/CRUD_vehicule/update_vehicule.php';
