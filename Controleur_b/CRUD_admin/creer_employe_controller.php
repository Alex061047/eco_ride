<?php
// Controle des acces admin avant creation d'employe.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
securityRequireRole(['admin']);

require_once __DIR__ . '/../../Modele/CRUD_admin/creer_employe.php';