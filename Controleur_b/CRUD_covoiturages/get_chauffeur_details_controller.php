<?php
// Strict PHP
declare(strict_types=1);
// Recoit la requete du front et applique un premier niveau de controle.

// Toutes les reponses sont retournees en JSON pour le JavaScript du front.
header('Content-Type: application/json; charset=utf-8');

// Connexions SQL + Mongo utilisees pour les details chauffeur et les avis.
require_once __DIR__ . '/../../Modele/db_connection.php';
require_once __DIR__ . '/../../Modele/mongodb/mongo_connection.php';

function abortRequest(int $status, string $message): void
{
    // Reponse d'erreur JSON homogene pour tout le controleur.
    http_response_code($status);
    echo json_encode(['status' => 'error', 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function getPositiveInt(string $key, bool $required = true): ?int
{
    // Lit un parametre GET et verifie qu'il est entier positif.
    $raw = $_GET[$key] ?? null;

    if ($raw === null || $raw === '') {
        if ($required) {
            abortRequest(422, "Parametre {$key} manquant.");
        }
        return null;
    }

    if (!preg_match('/^\d+$/', (string) $raw)) {
        abortRequest(422, "Parametre {$key} invalide.");
    }

    $value = (int) $raw;
    if ($value <= 0) {
        abortRequest(422, "Parametre {$key} invalide.");
    }

    return $value;
}

// Parametres attendus depuis la vue.
$chauffeurId = getPositiveInt('chauffeur_id', true);
$vehiculeId = getPositiveInt('vehicule_id', false);

try {
    // 1) Infos de base du chauffeur.
    $query = "SELECT id, pseudo FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $chauffeurId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        abortRequest(404, 'Chauffeur non trouve.');
    }

    // 2) Vehicule demande (ou premier vehicule du chauffeur si non precise).
    if ($vehiculeId !== null) {
        $vehiculeQuery = "SELECT id, marque, modele, energie FROM vehicules
                          WHERE id = :vehicule_id AND utilisateur_id = :user_id LIMIT 1";
        $vehiculeStmt = $pdo->prepare($vehiculeQuery);
        $vehiculeStmt->execute([
            'vehicule_id' => $vehiculeId,
            'user_id' => $chauffeurId,
        ]);
    } else {
        $vehiculeQuery = "SELECT id, marque, modele, energie FROM vehicules
                          WHERE utilisateur_id = :user_id LIMIT 1";
        $vehiculeStmt = $pdo->prepare($vehiculeQuery);
        $vehiculeStmt->execute(['user_id' => $chauffeurId]);
    }
    $vehicule = $vehiculeStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // 3) Preferences du chauffeur.
    $prefQuery = "SELECT fumeur, animaux, discussion, musique, autre
                  FROM preferences WHERE utilisateur_id = :user_id LIMIT 1";
    $prefStmt = $pdo->prepare($prefQuery);
    $prefStmt->execute(['user_id' => $chauffeurId]);
    $preferences = $prefStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // 4) Avis valides depuis MongoDB.
    $avisValides = $mongo->eco_ride->avis_trajet->find([
        'chauffeur_id' => $chauffeurId,
        'statut' => 'validé',
    ]);

    $avisListe = [];
    foreach ($avisValides as $avis) {
        $dateEnvoi = '';
        if (isset($avis['date_envoi'])) {
            $parts = explode(' - ', (string) $avis['date_envoi']);
            $dateEnvoi = $parts[count($parts) - 1] ?? '';
        }

        $avisListe[] = [
            'commentaire' => (string) ($avis['commentaire'] ?? ''),
            'date_envoi' => $dateEnvoi,
        ];
    }

    // Reponse finale pour alimenter le bloc "Details" de la carte trajet.
    echo json_encode([
        'status' => 'success',
        'utilisateur' => ['pseudo' => $user['pseudo']],
        'vehicule' => $vehicule,
        'preferences' => $preferences,
        'avis_valides' => $avisListe,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    abortRequest(500, 'Erreur serveur lors du chargement des details.');
}
