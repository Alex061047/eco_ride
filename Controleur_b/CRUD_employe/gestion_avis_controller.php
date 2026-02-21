<?php
// Validation/refus des avis: reserve employe/admin.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
securityRequireRole(['employe', 'admin']);

require_once __DIR__ . '/../../Modele/CRUD_employe/gestion_avis.php';