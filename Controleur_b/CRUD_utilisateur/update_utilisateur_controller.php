<?php
// Mise a jour utilisateur: force controle d'identite serveur.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
$userId = securityRequireAuth();
$userRole = securityGetUserRole();

$payload = securityReadJsonBody();
$GLOBALS['__JSON_BODY'] = $payload;
if (!isset($payload['id']) || !is_numeric((string) $payload['id'])) {
    securityAbort(422, 'ID utilisateur manquant.');
}
$targetId = (int) $payload['id'];

// Hors admin, interdit de modifier un autre compte.
if ($userRole !== 'admin' && $targetId !== $userId) {
    securityAbort(403, 'Modification d\'un autre utilisateur interdite.');
}

// Hors admin, interdit de changer le role.
if ($userRole !== 'admin' && array_key_exists('role', $payload)) {
    securityAbort(403, 'Changement de role reserve a l\'administrateur.');
}

require_once __DIR__ . '/../../Modele/CRUD_utilisateur/update_utilisateur.php';
