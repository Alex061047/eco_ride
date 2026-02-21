<?php
// Graphique admin des covoiturages.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('GET');
securityRequireRole(['admin']);

require_once __DIR__ . '/../../Modele/CRUD_admin/graph_covoit.php';