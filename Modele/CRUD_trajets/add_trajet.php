<?php
// Connexion à la base de données
include('../db_connection.php');
// Connexion MongoDB (logs)
include('../mongodb/mongo_logs.php');

header('Content-Type: application/json');
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Utilisateur non connecté"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les données envoyées en JSON
$data = json_decode(file_get_contents('php://input'), true);

// Vérification des champs attendus
if (!isset($data['vehicule_id'], $data['depart'], $data['arrivee'], $data['datetime'], $data['duree'], $data['prix'], $data['nb_places_restantes'])) {
    echo json_encode(["status" => "error", "message" => "Données manquantes"]);
    exit;
}

// Récupération des informations du formulaire
$vehicule_id = $data['vehicule_id'];
$depart = $data['depart'];
$arrivee = $data['arrivee'];
$datetime_depart = $data['datetime'];
$duree = $data['duree'];
$prix = $data['prix'];
$nb_places_restantes = $data['nb_places_restantes'];

// Vérifie que le prix n'est pas inférieur à 2 crédits
if ($prix < 2) {
    echo json_encode(["status" => "error", "message" => "Le prix minimum est de 2 crédits."]);
    exit;
}

// Calcul de la date/heure d'arrivée estimée
try {
    $date_depart = new DateTime($datetime_depart);
    $heure_duree = DateTime::createFromFormat('H:i', $duree);

    $interval = new DateInterval('PT' . $heure_duree->format('H') . 'H' . $heure_duree->format('i') . 'M');
    $date_arrivee = clone $date_depart;
    $date_arrivee->add($interval);

    $date_heure_arrivee = $date_arrivee->format('Y-m-d H:i:s');
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Erreur lors du calcul de l'heure d'arrivée"]);
    exit;
}

// Insérer le trajet dans la table "covoiturages"
$sql = "INSERT INTO covoiturages (chauffeur_id, vehicule_id, depart, arrivee, date_heure_depart, date_heure_arrivee, etat, prix, nb_places_restantes)
        VALUES (:chauffeur_id, :vehicule_id, :depart, :arrivee, :date_depart, :date_arrivee, 'à venir', :prix, :nb_places_restantes)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'chauffeur_id' => $user_id,
    'vehicule_id' => $vehicule_id,
    'depart' => $depart,
    'arrivee' => $arrivee,
    'date_depart' => $datetime_depart,
    'date_arrivee' => $date_heure_arrivee,
    'prix' => $prix,
    'nb_places_restantes' => $nb_places_restantes
]);

// Enregistrement des logs dans MongoDB
$details = [
    "chauffeur_id" => $user_id,
    "depart" => $depart,
    "arrivee" => $arrivee,
    "date_depart" => $datetime_depart,
    "date_arrivee" => $date_heure_arrivee,
    "prix" => $prix,
    "places" => $nb_places_restantes
];

enregistrerLog("Ajout trajet", "Trajet proposé", $details);


echo json_encode(["status" => "success", "message" => "Trajet proposé avec succès !"]);
?>
