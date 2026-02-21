<?php
// Connexion à la base de données MySQL
require __DIR__ . '/../db_connection.php';
// Connexion à la base MongoDB
require __DIR__ . '/../mongodb/mongo_connection.php';
session_start();

// Sécurité serveur: accès réservé employé/admin
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), ['employe', 'admin'], true)) {
    echo json_encode(["status" => "error", "message" => "Accès interdit."]);
    exit;
}

// Réponse en JSON
header('Content-Type: application/json');

// Lecture des données JSON
$data = json_decode(file_get_contents('php://input'), true);

// Vérification des données
if (!isset($data['trajet_id'], $data['passager_id'], $data['action'])) {
    echo json_encode(["status" => "error", "message" => "Paramètres manquants"]);
    exit;
}

$trajetId = (int) $data['trajet_id'];
$passagerId = (int) $data['passager_id'];
$action = $data['action'];
$creditAttribue = isset($data['credit']) ? floatval($data['credit']) : null;

// Vérification de l'action valider ou refuser
if (!in_array($action, ['valider', 'refuser'])) {
    echo json_encode(["status" => "error", "message" => "Action invalide"]);
    exit;
}

// Connexion à la collection MongoDB
if (!isset($mongo) || $mongo === null) {
    echo json_encode(["status" => "error", "message" => "Service avis indisponible (MongoDB)."]);
    exit;
}

$avisCollection = $mongo->eco_ride->avis_trajet;

// Recherche de l'avis à partir de trajet_id + passager_id
$avis = $avisCollection->findOne([
    'trajet_id' => $trajetId,
    'user_id' => $passagerId
]);

if (!$avis) {
    echo json_encode(["status" => "error", "message" => "Avis non trouvé"]);
    exit;
}

$chauffeurId = (int) $avis['chauffeur_id'];

// Récupération du prix du trajet
$stmt = $pdo->prepare("SELECT prix FROM covoiturages WHERE id = :id");
$stmt->execute(['id' => $trajetId]);
$trajet = $stmt->fetch();

if (!$trajet) {
    echo json_encode(["status" => "error", "message" => "Trajet introuvable"]);
    exit;
}

$prixTrajet = (int) $trajet['prix'] - 2;

try {
    // Avis validé
    if ($action === 'valider') {
        // Crédit du chauffeur ajouté à son solde
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credit = credit + :credit WHERE id = :id");
        $stmt->execute(['credit' => $prixTrajet, 'id' => $chauffeurId]);

        // Mise à jour de l'état de l'avis
        $avisCollection->updateOne(
            ['trajet_id' => $trajetId, 'user_id' => $passagerId],
            ['$set' => ['statut' => 'validé']]
        );
    }

    // Avis refusé
    if ($action === 'refuser') {
        if (!is_numeric($creditAttribue) || $creditAttribue < 0 || $creditAttribue > $prixTrajet) {
            echo json_encode(["status" => "error", "message" => "Crédit partiel invalide"]);
            exit;
        }

        $remboursement = $prixTrajet - $creditAttribue;

        // Crédit ajouté au chauffeur (même partiel)
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credit = credit + :credit WHERE id = :id");
        $stmt->execute(['credit' => $creditAttribue, 'id' => $chauffeurId]);

        // Remboursement passager (si crédit partiel pour le chauffeur)
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credit = credit + :remboursement WHERE id = :id");
        $stmt->execute(['remboursement' => $remboursement, 'id' => $passagerId]);

        // Mise à jour de l'état de l'avis
        $avisCollection->updateOne(
            ['trajet_id' => $trajetId, 'user_id' => $passagerId],
            ['$set' => ['statut' => 'refusé']]
        );
    }

    // Recalcul de la note du chauffeur
    $avisValidés = $avisCollection->find([
        'statut' => 'validé',
        'chauffeur_id' => $chauffeurId
    ]);


    // Mise à jour de la note du chauffeur
    $somme = 0;
    $nb = 0;

    foreach ($avisValidés as $avisItem) {
        if (isset($avisItem['note'])) {
            $somme += (int) $avisItem['note'];
            $nb++;
        }
    }

    if ($nb > 0) {
        $moyenne = round($somme / $nb);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET note = :note WHERE id = :id");
        $stmt->execute(['note' => $moyenne, 'id' => $chauffeurId]);
    }

// Connexion à la collection logs_employe
$logs = $mongo->eco_ride->logs_employe;

$logs->insertOne([
    'employe_id' => $_SESSION['user_id'] ?? null,  
    'action' => $action,
    'trajet_id' => $trajetId,
    'chauffeur_id' => $chauffeurId,
    'passager_id' => $passagerId,
    'credit_attribue' => ($action === 'valider') ? $prixTrajet : $creditAttribue,
    'remboursement_passager' => ($action === 'refuser') ? $remboursement : 0,
    'timestamp' => date('H:i:s - d/m/Y')
]);
    // Réponse succès
    echo json_encode(["status" => "success", "message" => "Avis $action avec succès"]);

} catch (Exception $e) {
    // Réponse echec
    echo json_encode(["status" => "error", "message" => "Erreur serveur : " . $e->getMessage()]);
}
?>

