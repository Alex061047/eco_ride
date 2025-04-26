<?php
include '../db_connection.php';
header('Content-Type: application/json');

// Récupération de l'ID utilisateur connecté
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT c.id, c.depart, c.arrivee, 
               DATE_FORMAT(c.date_heure_depart, '%Y-%m-%d') AS jour, 
               TIME_FORMAT(c.date_heure_depart, '%H:%i') AS heure, 
               TIMEDIFF(c.date_heure_arrivee, c.date_heure_depart) AS duree,
               c.nb_places_restantes, c.prix, c.etat, c.chauffeur_id,
               v.marque, v.modele
        FROM covoiturages c
        JOIN vehicules v ON c.vehicule_id = v.id
        WHERE c.chauffeur_id = :user_id 
        OR c.id IN (
            SELECT r.covoiturage_id 
            FROM reservations r 
            WHERE r.passager_id = :user_id AND r.statut = 'confirmé'
        )";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajouter le champ "est_chauffeur" pour que le front sache si l'utilisateur est le chauffeur du trajet ou le passager
foreach ($trajets as &$trajet) {
    $trajet["est_chauffeur"] = ($trajet["chauffeur_id"] == $user_id);
    $trajet["est_passager"] = !$trajet["est_chauffeur"];
}

echo json_encode($trajets);
?>