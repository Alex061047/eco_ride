<?php
// Historique de l'utilisateur connecte.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('GET');
securityRequireAuth();

require_once __DIR__ . '/../../Modele/CRUD_trajets/get_historique.php';