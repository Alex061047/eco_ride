<?php
declare(strict_types=1);
// Ce controleur recoit la requete du front et applique un premier niveau de controle.

// Toutes les reponses sont retournees en JSON pour le JavaScript du front.
header('Content-Type: application/json; charset=utf-8');

// Connexions SQL + Mongo pour la reservation et les logs.
require_once __DIR__ . '/../../Modele/db_connection.php';
require_once __DIR__ . '/../../Modele/mongodb/mongo_logs.php';

// Session obligatoire pour identifier l'utilisateur connecte.
session_start();

function abortRequest(int $status, string $message, string $code = 'error'): void
{
    // Reponse d'erreur JSON centralisee.
    http_response_code($status);
    echo json_encode(['status' => $code, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// Refus immediat si utilisateur non connecte.
if (!isset($_SESSION['user_id']) || !is_numeric((string) $_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_connected'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = (int) $_SESSION['user_id'];
if ($userId <= 0) {
    echo json_encode(['status' => 'not_connected'], JSON_UNESCAPED_UNICODE);
    exit;
}

// L'API de reservation accepte uniquement POST JSON.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    abortRequest(405, 'Methode non autorisee.');
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') === false) {
    abortRequest(415, 'Content-Type invalide.');
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    abortRequest(400, 'Corps JSON invalide.');
}

// Validation des champs envoyes par le front.
$trajetIdRaw = $input['covoiturage_id'] ?? null;
$nbPlacesRaw = $input['nb_places'] ?? null;
if (!is_numeric((string) $trajetIdRaw) || !is_numeric((string) $nbPlacesRaw)) {
    abortRequest(422, 'Parametres invalides.');
}

$trajetId = (int) $trajetIdRaw;
$nbPlacesDemandees = (int) $nbPlacesRaw;
if ($trajetId <= 0 || $nbPlacesDemandees <= 0 || $nbPlacesDemandees > 8) {
    abortRequest(422, 'ID covoiturage ou nombre de places invalide.');
}

try {
    // Transaction pour eviter les incoherences (credits/places) en cas d'acces simultane.
    $pdo->beginTransaction();

    // Verrouillage du trajet cible pendant la reservation.
    $stmt = $pdo->prepare(
        "SELECT id, chauffeur_id, nb_places_restantes, prix
         FROM covoiturages
         WHERE id = :id AND etat = 'à venir'
         FOR UPDATE"
    );
    $stmt->execute(['id' => $trajetId]);
    $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trajet) {
        $pdo->rollBack();
        abortRequest(404, 'Trajet introuvable ou plus disponible.');
    }

    // Interdit de reserver son propre trajet.
    if ((int) $trajet['chauffeur_id'] === $userId) {
        $pdo->rollBack();
        abortRequest(422, 'Vous ne pouvez pas reserver votre propre trajet.');
    }

    // Verifie le stock de places.
    if ((int) $trajet['nb_places_restantes'] < $nbPlacesDemandees) {
        $pdo->rollBack();
        abortRequest(409, 'Pas assez de places restantes pour ce trajet.');
    }

    // Empeche un doublon de reservation active.
    $alreadyReservedStmt = $pdo->prepare(
        "SELECT COUNT(*) FROM reservations
         WHERE passager_id = :user_id
           AND covoiturage_id = :trajet_id
           AND statut IN ('confirmé', 'en attente')"
    );
    $alreadyReservedStmt->execute([
        'user_id' => $userId,
        'trajet_id' => $trajetId,
    ]);
    $alreadyReserved = (int) $alreadyReservedStmt->fetchColumn();
    if ($alreadyReserved > 0) {
        $pdo->rollBack();
        abortRequest(409, 'Vous avez deja une reservation active pour ce trajet.');
    }

    // Calcul du total a payer.
    $prixTotal = ((float) $trajet['prix']) * $nbPlacesDemandees;

    // Verifie les credits avec verrouillage de la ligne utilisateur.
    $stmtCredit = $pdo->prepare("SELECT credit FROM utilisateurs WHERE id = :user_id FOR UPDATE");
    $stmtCredit->execute(['user_id' => $userId]);
    $utilisateur = $stmtCredit->fetch(PDO::FETCH_ASSOC);
    if (!$utilisateur || (float) $utilisateur['credit'] < $prixTotal) {
        $pdo->rollBack();
        abortRequest(409, 'Credit insuffisant pour effectuer cette reservation.');
    }

    // Creation d'une ligne de reservation par place.
    $insert = $pdo->prepare(
        "INSERT INTO reservations (passager_id, covoiturage_id, statut)
         VALUES (:user_id, :trajet_id, 'confirmé')"
    );
    for ($i = 0; $i < $nbPlacesDemandees; $i++) {
        $insert->execute([
            'user_id' => $userId,
            'trajet_id' => $trajetId,
        ]);
    }

    // Mise a jour des places restantes.
    $update = $pdo->prepare(
        "UPDATE covoiturages
         SET nb_places_restantes = nb_places_restantes - :nb_places
         WHERE id = :id"
    );
    $update->execute([
        'nb_places' => $nbPlacesDemandees,
        'id' => $trajetId,
    ]);

    // Deduction du credit utilisateur.
    $updateCredit = $pdo->prepare(
        "UPDATE utilisateurs
         SET credit = credit - :montant
         WHERE id = :user_id"
    );
    $updateCredit->execute([
        'montant' => $prixTotal,
        'user_id' => $userId,
    ]);

    // Validation definitive des changements SQL.
    $pdo->commit();

    // Logs metier MongoDB.
    $logDetails = [
        'utilisateur_id' => $userId,
        'covoiturage_id' => $trajetId,
        'nb_places' => $nbPlacesDemandees,
        'montant_paye' => $prixTotal,
    ];
    enregistrerLog('Réservation', $logDetails);

    // Log des credits preleves par la plateforme.
    $logsCredit = $mongo->eco_ride->logs_credit;
    $logsCredit->insertOne([
        'trajet_id' => $trajetId,
        'credits_plateforme' => 2 * $nbPlacesDemandees,
        'date' => date('Y-m-d H:i:s'),
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Reservation confirmee avec succes. Credit deduit : ' . $prixTotal,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    abortRequest(500, 'Erreur serveur lors de la reservation.');
}
