<?php
// Soumission avis via token.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');

require_once __DIR__ . '/../../Modele/CRUD_utilisateur/submit_avis.php';