<?php
// Suspension/retablissement: reserve a l'administrateur.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
securityRequireRole(['admin']);

require_once __DIR__ . '/../../Modele/CRUD_admin/suspendre_utilisateur.php';