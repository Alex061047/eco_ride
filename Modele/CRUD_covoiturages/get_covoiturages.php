<?php
include __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Classes/Covoiturage.php';

header('Content-Type: application/json');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$sql = "SELECT
            c.id,
            c.depart,
            c.arrivee,
            DATE_FORMAT(c.date_heure_depart, '%d/%m/%Y') AS jour,
            TIME_FORMAT(c.date_heure_depart, '%H:%i') AS heure,
            TIME_FORMAT(TIMEDIFF(c.date_heure_arrivee, c.date_heure_depart), '%H:%i') AS duree,
            c.nb_places_restantes,
            c.prix,
            c.etat,
            c.chauffeur_id,
            c.vehicule_id,
            u.pseudo,
            u.photo_profil,
            u.note,
            v.marque,
            v.modele,
            v.energie,
            p.animaux
        FROM covoiturages c
        JOIN utilisateurs u ON c.chauffeur_id = u.id
        JOIN vehicules v ON c.vehicule_id = v.id
        LEFT JOIN preferences p ON u.id = p.utilisateur_id
        WHERE (c.etat LIKE '%venir' OR c.etat = 'a venir')
          AND c.nb_places_restantes > 0
        ORDER BY c.date_heure_depart ASC";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$covoiturages = [];
foreach ($rows as $row) {
    $covoit = new Covoiturage($row);
    $trajet = $covoit->toArray();

    $trajet['pseudo'] = $row['pseudo'];
    $trajet['photo_profil'] = $row['photo_profil'];
    $trajet['note'] = $row['note'];
    $trajet['marque'] = $row['marque'];
    $trajet['modele'] = $row['modele'];
    $trajet['energie'] = $row['energie'];
    $trajet['animaux'] = $row['animaux'];

    $covoiturages[] = $trajet;
}

echo json_encode($covoiturages);
