<?php
// Liste des avis en attente: reservee employe/admin.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('GET');
securityRequireRole(['employe', 'admin']);

if (isset($_GET['noteMax']) && $_GET['noteMax'] !== '' && !preg_match('/^\d+$/', (string) $_GET['noteMax'])) {
    securityAbort(422, 'noteMax invalide.');
}

require_once __DIR__ . '/../../Modele/CRUD_employe/get_avis.php';