<?php
// Connexion à la base de données MySQL
include('../db_connection.php');
// Inclusion de la classe Covoiturage
require_once('../Classes/Covoiturage.php');

// Réponse attendue en format JSON
header('Content-Type: application/json');
// Démarrage de la session PHP
session_start();

// Requête SQL pour récupérer les covoiturages à venir avec toutes les informations nécessaires
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

// Exécution de la requête SQL
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialisation du tableau
$covoiturages = [];

// Parcours des résultats pour les transformer en objets Covoiturage
foreach ($rows as $row) {
    // Création d’un objet à partir de la classe Covoiturage
    $covoit = new Covoiturage($row);
    // Conversion de l’objet en tableau associatif exploitable pour l’API
    $trajet = $covoit->toArray();


    // Ajout d’informations supplémentaires non incluses dans la classe
    $trajet['pseudo'] = $row['pseudo'];
    $trajet['photo_profil'] = $row['photo_profil'];
    $trajet['note'] = $row['note'];
    $trajet['marque'] = $row['marque'];
    $trajet['modele'] = $row['modele'];
    $trajet['energie'] = $row['energie'];
    $trajet['animaux'] = $row['animaux'];

    // Ajout du trajet au tableau final
    $covoiturages[] = $trajet;
}

// Envoi de la réponse JSON
echo json_encode($covoiturages);
