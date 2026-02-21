<?php
// Total credits: reserve a l'admin.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('GET');
securityRequireRole(['admin']);

require_once __DIR__ . '/../../Modele/CRUD_admin/get_credit_total.php';