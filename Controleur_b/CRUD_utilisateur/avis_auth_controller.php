<?php
// Verification de token avis (publique via lien mail).
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('GET');

if (!isset($_GET['token']) || trim((string) $_GET['token']) === '') {
    securityAbort(422, 'Token manquant.', 'invalid_token');
}

require_once __DIR__ . '/../../Modele/CRUD_utilisateur/avis_auth.php';