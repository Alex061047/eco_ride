<?php
include '../db_connection.php';

header('Content-Type: application/json');

session_start();

// Simuler une connexion pour tester
$_SESSION['user_id'] = 5;

$user_id = $_SESSION['user_id'];

// Requête SQL
$sql = "SELECT c.id, c.depart, c.arrivee, 
               DATE_FORMAT(c.date_heure_depart, '%Y-%m-%d') AS jour, 
               TIME_FORMAT(c.date_heure_depart, '%H:%i') AS heure, 
               TIMEDIFF(c.date_heure_arrivee, c.date_heure_depart) AS duree
        FROM covoiturages c
        WHERE c.chauffeur_id = :user_id 
        OR c.id IN (SELECT r.covoiturage_id FROM reservations r WHERE r.passager_id = :user_id)";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$trajets = $stmt->fetchAll();

//  Retourner seulement le JSON
echo json_encode($trajets);

?>
