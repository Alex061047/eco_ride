<?php
// Connexion à la base de données MySQL
require_once('../db_connection.php');
// Connexion à MongoDB
require_once('../mongodb/mongo_connection.php');

// Réponse en JSON
header('Content-Type: application/json');

// Récupère les données envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

// Vérifie si l'ID est présent
if (!isset($data['id'])) {
    echo json_encode(["status" => "error", "message" => "ID manquant."]);
    exit;
}

$id = (int)$data['id'];

// Récupérer la note actuelle
$stmt = $pdo->prepare("SELECT note FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si utilisateur non trouvé
if (!$user) {
    echo json_encode(["status" => "error", "message" => "Utilisateur introuvable."]);
    exit;
}

$note_actuelle = (int)$user['note'];

if ($note_actuelle != -1) {
    // Suspension
    $pdo->beginTransaction();
    try {
        // Met la note à -1 pour bloquer la connexion
        $stmt = $pdo->prepare("UPDATE utilisateurs SET note = -1 WHERE id = ?");
        $stmt->execute([$id]);

        // Annule les réservations du passager
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'annulé' WHERE passager_id = ?");
        $stmt->execute([$id]);

        // Récupère les covoiturages liés aux réservations de l'utilisateur
        $stmt = $pdo->prepare("SELECT covoiturage_id FROM reservations WHERE passager_id = ?");
        $stmt->execute([$id]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque réservations, incrémentation de la place du covoiturage de 1
        foreach ($reservations as $reservation) {
        $covoiturage_id = $reservation['covoiturage_id'];

        $stmtUpdate = $pdo->prepare("UPDATE covoiturages SET nb_places_restantes = nb_places_restantes + 1 WHERE id = ?");
        $stmtUpdate->execute([$covoiturage_id]);
        }

        // Annulation des covoiturages (en tant que chauffeur)
        $stmt = $pdo->prepare("UPDATE covoiturages SET etat = 'annulé' WHERE chauffeur_id = ?");
        $stmt->execute([$id]);

        // Enregistrement du log MongoDB (utile pour rétablir la note)
        $collection = $mongo->eco_ride->logs_suspendu;
        $collection->insertOne([
            "user_id" => $id,
            "note_initiale" => $note_actuelle,
            "date" => date("Y-m-d H:i:s")
        ]);

        // Valide les actions
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Utilisateur suspendu avec succès."]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Erreur lors de la suspension."]);
    }
} else {

    // Rétablir le compte

// Chercher le dernier log pour cet utilisateur
$collection = $mongo->eco_ride->logs_suspendu;
$log = $collection->findOne(
    ['user_id' => $id],
    ['sort' => ['date' => -1]] // le plus récent
);

// Si aucune note initiale trouvée
if (!$log || !isset($log['note_initiale'])) {
    echo json_encode(["status" => "error", "message" => "Impossible de retrouver la note initiale."]);
    exit;
}

// Rétabli l'ancienne note dans MySQL (et permet la connexion)
$noteInitiale = (int)$log['note_initiale'];

$stmt = $pdo->prepare("UPDATE utilisateurs SET note = ? WHERE id = ?");
$stmt->execute([$noteInitiale, $id]);

echo json_encode(["status" => "success", "message" => "Utilisateur rétabli avec succès."]);

}
?>
