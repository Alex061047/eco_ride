<?php
// Inscription publique.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');

require_once __DIR__ . '/../../Modele/CRUD_utilisateur/inscription.php';