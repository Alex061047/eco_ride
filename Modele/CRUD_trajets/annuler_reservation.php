<?php
include __DIR__ . '/../db_connection.php';
header('Content-Type: application/json');
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Utilisateur non connecté"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupère les données envoyées
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['covoiturage_id'])) {
    echo json_encode(["success" => false, "message" => "ID du trajet manquant"]);
    exit;
}

$covoiturage_id = $data['covoiturage_id'];

// Vérifie que la réservation existe pour cet utilisateur
$check = $pdo->prepare("SELECT * FROM reservations WHERE covoiturage_id = :covoiturage_id AND passager_id = :user_id AND statut = 'confirmé'");
$check->execute([
    'covoiturage_id' => $covoiturage_id,
    'user_id' => $user_id
]);

$reservation = $check->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo json_encode(["success" => false, "message" => "Réservation non trouvée ou déjà annulée"]);
    exit;
}

// Met à jour la réservation en statut "annulé"
$update = $pdo->prepare("UPDATE reservations SET statut = 'annulé' WHERE id = :reservation_id");
$update->execute([
    'reservation_id' => $reservation['id']
]);

// Remet une place disponible dans le trajet
$updatePlaces = $pdo->prepare("UPDATE covoiturages SET nb_places_restantes = nb_places_restantes + 1 WHERE id = :covoiturage_id");
$updatePlaces->execute([
    'covoiturage_id' => $covoiturage_id
]);

echo json_encode(["success" => true, "message" => "Réservation annulée avec succès"]);
?>

