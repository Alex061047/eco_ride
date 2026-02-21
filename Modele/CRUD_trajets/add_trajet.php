<?php
// Connexion a la base de donnees
include __DIR__ . '/../db_connection.php';
// Connexion MongoDB (logs)
include __DIR__ . '/../mongodb/mongo_logs.php';

header('Content-Type: application/json; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifier que l'utilisateur est connecte
if (!isset($_SESSION['user_id']) || !is_numeric((string) $_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Utilisateur non connecte"], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Recuperer les donnees envoyees en JSON
$data = $GLOBALS['__JSON_BODY'] ?? json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    echo json_encode(["status" => "error", "message" => "Corps JSON invalide"], JSON_UNESCAPED_UNICODE);
    exit;
}

// Verification des champs attendus
if (!isset($data['vehicule_id'], $data['depart'], $data['arrivee'], $data['datetime'], $data['duree'], $data['prix'], $data['nb_places_restantes'])) {
    echo json_encode(["status" => "error", "message" => "Donnees manquantes"], JSON_UNESCAPED_UNICODE);
    exit;
}

// Recuperation des informations du formulaire
$vehicule_id = (int) $data['vehicule_id'];
$depart = trim((string) $data['depart']);
$arrivee = trim((string) $data['arrivee']);
$datetime_depart = (string) $data['datetime'];
$duree = (string) $data['duree'];
$prix = (float) $data['prix'];
$nb_places_restantes = (int) $data['nb_places_restantes'];

if ($vehicule_id <= 0 || $depart === '' || $arrivee === '' || $nb_places_restantes <= 0) {
    echo json_encode(["status" => "error", "message" => "Valeurs invalides"], JSON_UNESCAPED_UNICODE);
    exit;
}

// Verifie que le prix n'est pas inferieur a 2 credits
if ($prix < 2) {
    echo json_encode(["status" => "error", "message" => "Le prix minimum est de 2 credits."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Securite serveur: le vehicule doit appartenir au chauffeur connecte (sauf admin)
$sessionRole = $_SESSION['user_role'] ?? null;
if ($sessionRole !== 'admin') {
    $vehiculeCheck = $pdo->prepare("SELECT id FROM vehicules WHERE id = :vehicule_id AND utilisateur_id = :user_id LIMIT 1");
    $vehiculeCheck->execute([
        'vehicule_id' => $vehicule_id,
        'user_id' => $user_id
    ]);

    if (!$vehiculeCheck->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["status" => "error", "message" => "Vehicule non autorise pour cet utilisateur."], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Calcul de la date/heure d'arrivee estimee
try {
    $date_depart = new DateTime($datetime_depart);
    $heure_duree = DateTime::createFromFormat('H:i', $duree);
    if (!$heure_duree) {
        throw new Exception('Duree invalide');
    }

    $interval = new DateInterval('PT' . $heure_duree->format('H') . 'H' . $heure_duree->format('i') . 'M');
    $date_arrivee = clone $date_depart;
    $date_arrivee->add($interval);

    $date_heure_arrivee = $date_arrivee->format('Y-m-d H:i:s');
    $date_heure_depart = $date_depart->format('Y-m-d H:i:s');
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Erreur lors du calcul de l'heure d'arrivee"], JSON_UNESCAPED_UNICODE);
    exit;
}

// Inserer le trajet dans la table "covoiturages"
$sql = "INSERT INTO covoiturages (chauffeur_id, vehicule_id, depart, arrivee, date_heure_depart, date_heure_arrivee, etat, prix, nb_places_restantes)
        VALUES (:chauffeur_id, :vehicule_id, :depart, :arrivee, :date_depart, :date_arrivee, 'à venir', :prix, :nb_places_restantes)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'chauffeur_id' => $user_id,
    'vehicule_id' => $vehicule_id,
    'depart' => $depart,
    'arrivee' => $arrivee,
    'date_depart' => $date_heure_depart,
    'date_arrivee' => $date_heure_arrivee,
    'prix' => $prix,
    'nb_places_restantes' => $nb_places_restantes
]);

// Enregistrement des logs dans MongoDB
$details = [
    "chauffeur_id" => $user_id,
    "depart" => $depart,
    "arrivee" => $arrivee,
    "date_depart" => $date_heure_depart,
    "date_arrivee" => $date_heure_arrivee,
    "prix" => $prix,
    "places" => $nb_places_restantes
];

enregistrerLog("Ajout trajet", "Trajet propose", $details);

echo json_encode(["status" => "success", "message" => "Trajet propose avec succes !"], JSON_UNESCAPED_UNICODE);
?>

