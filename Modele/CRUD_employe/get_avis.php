<?php
require __DIR__ . '/../db_connection.php';
require __DIR__ . '/../mongodb/mongo_connection.php';
header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !is_numeric((string) $_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Acces interdit."]);
    exit;
}

$sessionRole = $_SESSION['user_role'] ?? null;
if ($sessionRole === null) {
    $stmtRole = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = :id LIMIT 1");
    $stmtRole->execute(['id' => (int) $_SESSION['user_id']]);
    $sessionRole = $stmtRole->fetchColumn() ?: null;
    if ($sessionRole !== null) {
        $_SESSION['user_role'] = $sessionRole;
    }
}

if (!in_array((string) $sessionRole, ['employe', 'admin'], true)) {
    echo json_encode(["status" => "error", "message" => "Acces interdit."]);
    exit;
}

$noteMax = isset($_GET['noteMax']) ? (int) $_GET['noteMax'] : null;

if (!isset($mongo) || $mongo === null) {
    // Mongo indisponible: ne casse pas le front, renvoie une liste vide.
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}

$avisCollection = $mongo->eco_ride->avis_trajet;
$filtreMongo = ['statut' => 'en attente'];
if ($noteMax !== null) {
    $filtreMongo['note'] = ['$lte' => $noteMax];
}

$avisCursor = $avisCollection->find($filtreMongo);
$finalResults = [];

foreach ($avisCursor as $avis) {
    $trajetId = (int) $avis['trajet_id'];
    $passagerId = (int) $avis['user_id'];
    $note = isset($avis['note']) ? (int) $avis['note'] : null;
    $commentaire = $avis['commentaire'] ?? '';
    $dateCommentaire = $avis['date_envoi'] ?? '';

    $stmtCheck = $pdo->prepare("SELECT 1 FROM reservations WHERE covoiturage_id = :tid AND passager_id = :pid");
    $stmtCheck->execute(['tid' => $trajetId, 'pid' => $passagerId]);
    if (!$stmtCheck->fetchColumn()) {
        continue;
    }

    $stmtTrajet = $pdo->prepare(
        "SELECT c.id AS trajet_id, c.depart, c.arrivee, c.prix, c.date_heure_depart,
                u.pseudo AS chauffeur_pseudo, u.email AS chauffeur_mail
         FROM covoiturages c
         JOIN utilisateurs u ON u.id = c.chauffeur_id
         WHERE c.id = :tid"
    );
    $stmtTrajet->execute(['tid' => $trajetId]);
    $trajet = $stmtTrajet->fetch(PDO::FETCH_ASSOC);
    if (!$trajet) {
        continue;
    }

    $stmtPassager = $pdo->prepare("SELECT pseudo, email FROM utilisateurs WHERE id = :pid");
    $stmtPassager->execute(['pid' => $passagerId]);
    $passager = $stmtPassager->fetch(PDO::FETCH_ASSOC);
    if (!$passager) {
        continue;
    }

    $finalResults[] = [
        'trajet_id' => $trajetId,
        'depart' => $trajet['depart'],
        'arrivee' => $trajet['arrivee'],
        'chauffeur_pseudo' => $trajet['chauffeur_pseudo'],
        'chauffeur_mail' => $trajet['chauffeur_mail'],
        'prix' => $trajet['prix'],
        'date_trajet' => $trajet['date_heure_depart'],
        'passager_pseudo' => $passager['pseudo'],
        'passager_mail' => $passager['email'],
        'passager_id' => $passagerId,
        'commentaire' => $commentaire,
        'note' => $note,
        'date_commentaire' => $dateCommentaire,
        '_id' => (string) $avis['_id'],
    ];
}

echo json_encode($finalResults, JSON_UNESCAPED_UNICODE);
