<?php
include '../db_connection.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Utilisateur non authentifié"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id']) || !isset($data['etat'])) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

$trajetId = $data['id'];
$nouvelEtat = $data['etat'];

// Vérifier que le trajet existe et récupérer son chauffeur
$sqlCheck = "SELECT chauffeur_id FROM covoiturages WHERE id = :trajet_id";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute(['trajet_id' => $trajetId]);
$trajet = $stmtCheck->fetch();

if (!$trajet) {
    echo json_encode(["success" => false, "message" => "Trajet introuvable"]);
    exit;
}

// Vérifier si l'utilisateur est bien le chauffeur du trajet pour "en cours" et "terminé"
if (($nouvelEtat === "en cours" || $nouvelEtat === "terminé") && $trajet['chauffeur_id'] != $_SESSION['user_id']) {
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    exit;
}

//Mettre à jour l'état du trajet
$sqlUpdate = "UPDATE covoiturages SET etat = :etat WHERE id = :trajet_id";
$stmtUpdate = $pdo->prepare($sqlUpdate);
$stmtUpdate->execute(['etat' => $nouvelEtat, 'trajet_id' => $trajetId]);

//Si le trajet est terminé, mettre les réservations en "réalisé"
if ($nouvelEtat === "terminé") {
    $sqlUpdateReservations = "UPDATE reservations SET statut = 'réalisé' WHERE covoiturage_id = :trajet_id";
    $stmtUpdateReservations = $pdo->prepare($sqlUpdateReservations);
    $stmtUpdateReservations->execute(['trajet_id' => $trajetId]);
}

//Si le trajet est annulé, mettre les réservations en "annulé"
if ($nouvelEtat === "annulé") {
    $sqlUpdateReservations = "UPDATE reservations SET statut = 'annulé' WHERE covoiturage_id = :trajet_id";
    $stmtUpdateReservations = $pdo->prepare($sqlUpdateReservations);
    $stmtUpdateReservations->execute(['trajet_id' => $trajetId]);
}

echo json_encode(["success" => true, "message" => "Trajet mis à jour avec succès"]);
?>
