<?php
// Connexion à la base de données
include('../db_connection.php');
// Réponse au format JSON
header('Content-Type: application/json');

// Démarre la session PHP
session_start();

// Récupère tous les trajets disponibles
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
        WHERE c.etat = 'à venir' AND c.nb_places_restantes > 0
        ORDER BY c.date_heure_depart ASC";

$stmt = $pdo->query($sql);
$covoiturages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gestion pour ajouter "mention écologique"
// Si le véhicule est électrique, on ajoute "mention_ecologique" = "Oui"
foreach ($covoiturages as &$trajet) {
    if (isset($trajet['energie']) && strtolower($trajet['energie']) === 'électrique') {
        $trajet['mention_ecologique'] = 'Oui';
    } else {
        $trajet['mention_ecologique'] = 'Non';
    }
}

// Envoi de la réponse en JSON
echo json_encode($covoiturages);
?>
