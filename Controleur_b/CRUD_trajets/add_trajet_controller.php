<?php
// Creation de trajet: utilisateur connecte obligatoire.
require_once __DIR__ . '/../_security.php';
securityJsonHeader();
securityRequireMethod('POST');
securityRequireRole(['chauffeur', 'passager-chauffeur', 'admin']);

// Lit le JSON une seule fois et le transmet au modele.
$payload = securityReadJsonBody();
$GLOBALS['__JSON_BODY'] = $payload;

require_once __DIR__ . '/../../Modele/CRUD_trajets/add_trajet.php';
