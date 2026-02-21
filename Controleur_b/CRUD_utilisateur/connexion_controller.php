<?php
// Connexion publique (sans session prealable).
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');

require_once __DIR__ . '/../../Modele/CRUD_utilisateur/connexion.php';