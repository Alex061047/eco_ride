<?php
include '../db_connection.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Utilisateur non authentifié"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) {
    echo json_encode(["status" => "error", "message" => "ID du trajet manquant"]);
    exit;
}

$trajetId = $data['id'];
$userId = $_SESSION['user_id'];

// Vérifier si l'utilisateur est le chauffeur du trajet
$sql = "SELECT chauffeur_id FROM covoiturages WHERE id = :trajetId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['trajetId' => $trajetId]);
$trajet = $stmt->fetch();

if (!$trajet) {
    echo json_encode(["status" => "error", "message" => "Trajet non trouvé"]);
    exit;
}

$isChauffeur = ($trajet['chauffeur_id'] == $userId);

// Supprimer le trajet (si c'est un chauffeur) ou juste la réservation (si c'est un passager)
if ($isChauffeur) {
    // Supprimer les réservations associées
    $sql = "DELETE FROM reservations WHERE covoiturage_id = :trajetId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['trajetId' => $trajetId]);

    // Supprimer le trajet
    $sql = "DELETE FROM covoiturages WHERE id = :trajetId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['trajetId' => $trajetId]);

    // Envoyer un mail aux passagers concernés
    $sql = "SELECT u.email FROM utilisateurs u 
            JOIN reservations r ON u.id = r.passager_id 
            WHERE r.covoiturage_id = :trajetId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['trajetId' => $trajetId]);
    $passagers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($passagers as $passager) {
        $to = $passager['email'];
        $subject = "Annulation de votre trajet";
        $message = "Bonjour,\n\nNous vous informons que votre trajet a été annulé par le chauffeur.\nNous nous excusons pour la gêne occasionnée.\n\nEcoRide";
        $headers = "From: contact@ecoride.fr";
        mail($to, $subject, $message, $headers);
    }

    echo json_encode(["status" => "success", "message" => "Trajet annulé avec succès. Les passagers ont été prévenus."]);
} else {
    // Annuler uniquement la réservation pour un passager
    $sql = "DELETE FROM reservations WHERE covoiturage_id = :trajetId AND passager_id = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['trajetId' => $trajetId, 'userId' => $userId]);

    echo json_encode(["status" => "success", "message" => "Votre réservation a été annulée."]);
}

?>
