<?php
// Mise a jour d'etat trajet: utilisateur connecte.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
securityRequireAuth();

require_once __DIR__ . '/../../Modele/CRUD_trajets/update_trajet.php';