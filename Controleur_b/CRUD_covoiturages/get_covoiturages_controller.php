<?php
declare(strict_types=1);
// Ce controleur recoit la requete du front et applique un premier niveau de controle.

// Toutes les reponses sont retournees en JSON pour le JavaScript du front.
header('Content-Type: application/json; charset=utf-8');

// Connexion a la base SQL.
require_once __DIR__ . '/../../Modele/db_connection.php';

function abortRequest(int $status, string $message): void
{
    // Reponse d'erreur standard.
    http_response_code($status);
    echo json_encode(['status' => 'error', 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function normalizeString(?string $value, int $maxLength = 80): ?string
{
    // Nettoie un texte libre (trim + longueur max).
    if ($value === null) {
        return null;
    }

    $value = trim($value);
    if ($value === '') {
        return null;
    }

    if (mb_strlen($value) > $maxLength) {
        abortRequest(422, 'Valeur texte trop longue.');
    }

    return $value;
}

function normalizeDate(?string $value): ?string
{
    // Valide une date au format YYYY-MM-DD.
    if ($value === null || trim($value) === '') {
        return null;
    }

    $date = DateTime::createFromFormat('Y-m-d', $value);
    $errors = DateTime::getLastErrors();
    if (!$date || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
        abortRequest(422, 'Format de date invalide.');
    }

    return $date->format('Y-m-d');
}

function normalizeBool(?string $value): bool
{
    // Convertit vers booleen.
    if ($value === null || $value === '') {
        return false;
    }
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

function normalizeFloat(?string $value, float $min, float $max): ?float
{
    // Valide un nombre decimal avec bornes.
    if ($value === null || trim($value) === '') {
        return null;
    }

    if (!is_numeric($value)) {
        abortRequest(422, 'Valeur numerique invalide.');
    }

    $floatValue = (float) $value;
    if ($floatValue < $min || $floatValue > $max) {
        abortRequest(422, 'Valeur numerique hors limites.');
    }

    return $floatValue;
}

function normalizeInt(?string $value, int $min, int $max): ?int
{
    // Valide un entier avec bornes.
    if ($value === null || trim($value) === '') {
        return null;
    }

    if (!preg_match('/^-?\d+$/', $value)) {
        abortRequest(422, 'Valeur entiere invalide.');
    }

    $intValue = (int) $value;
    if ($intValue < $min || $intValue > $max) {
        abortRequest(422, 'Valeur entiere hors limites.');
    }

    return $intValue;
}

function normalizeDuration(?string $value): ?int
{
    // Convertit HH:MM en minutes pour filtrer en SQL.
    if ($value === null || trim($value) === '') {
        return null;
    }

    if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value)) {
        abortRequest(422, 'Duree invalide. Format attendu HH:MM');
    }

    [$hours, $minutes] = array_map('intval', explode(':', $value));
    return ($hours * 60) + $minutes;
}

// Lecture + validation des filtres envoyes par la vue.
$depart = normalizeString($_GET['depart'] ?? null);
$arrivee = normalizeString($_GET['arrivee'] ?? null);
$jour = normalizeDate($_GET['jour'] ?? null);
$mention = normalizeBool($_GET['mention'] ?? null);
$prixMax = normalizeFloat($_GET['prix_max'] ?? null, 0, 9999);
$dureeMaxMinutes = normalizeDuration($_GET['duree_max'] ?? null);
$animaux = $_GET['animaux'] ?? null;
$noteMin = normalizeInt($_GET['note_min'] ?? null, 0, 5);

if ($animaux !== null && $animaux !== '' && !in_array($animaux, ['oui', 'non'], true)) {
    abortRequest(422, 'Filtre animaux invalide.');
}

// Requete de base: trajets a venir, non complets, non passes.
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
        WHERE c.etat = 'à venir'
          AND c.nb_places_restantes > 0
          AND DATE(c.date_heure_depart) >= CURDATE()";

$params = [];

// Ajout progressif des filtres valides.
if ($depart !== null) {
    $sql .= " AND c.depart LIKE :depart";
    $params['depart'] = '%' . $depart . '%';
}

if ($arrivee !== null) {
    $sql .= " AND c.arrivee LIKE :arrivee";
    $params['arrivee'] = '%' . $arrivee . '%';
}

if ($jour !== null) {
    $sql .= " AND DATE(c.date_heure_depart) = :jour";
    $params['jour'] = $jour;
}

if ($mention) {
    $sql .= " AND v.energie = 'electrique'";
}

if ($prixMax !== null) {
    $sql .= " AND c.prix <= :prix_max";
    $params['prix_max'] = $prixMax;
}

if ($dureeMaxMinutes !== null) {
    $sql .= " AND TIMESTAMPDIFF(MINUTE, c.date_heure_depart, c.date_heure_arrivee) <= :duree_max";
    $params['duree_max'] = $dureeMaxMinutes;
}

if ($animaux === 'oui') {
    $sql .= " AND p.animaux = 1";
} elseif ($animaux === 'non') {
    $sql .= " AND (p.animaux IS NULL OR p.animaux <> 1)";
}

if ($noteMin !== null) {
    $sql .= " AND u.note >= :note_min";
    $params['note_min'] = $noteMin;
}

$sql .= " ORDER BY c.date_heure_depart ASC";

try {
    // Requete preparee pour eviter les injections SQL.
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    abortRequest(500, 'Erreur serveur lors du chargement des covoiturages.');
}
