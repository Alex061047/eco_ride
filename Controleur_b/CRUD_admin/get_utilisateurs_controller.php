<?php
// Liste utilisateurs: reservee a l'administrateur.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('GET');
securityRequireRole(['admin']);

require_once __DIR__ . '/../../Modele/CRUD_admin/get_utilisateurs.php';