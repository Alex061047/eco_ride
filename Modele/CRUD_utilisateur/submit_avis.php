<?php
// Connexions à la base de données MySQL
require __DIR__ . '/../db_connection.php';
// Connexions à la base de données MongoDB
require __DIR__ . '/../mongodb/mongo_connection.php';

header('Content-Type: application/json');


// Récupération et validation des données
$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['user_id']) ||
    empty($data['trajet_id']) ||
    empty($data['note']) ||
    empty($data['token'])
) {
    echo json_encode(["status" => "error", "message" => "Champs obligatoires manquants"]);
    exit;
}

$user_id = (int) $data['user_id'];
$trajet_id = (int) $data['trajet_id'];
$note = (int) $data['note'];
$commentaire = $data['commentaire'] ?? '';
$token = $data['token'];

// Récupération des collections MongoDB
$avisCollection = $mongo->eco_ride->avis_trajet;
$tokenCollection = $client->eco_ride->avis_tokens;


// Vérifier que le token est encore valide
$tokenDoc = $tokenCollection->findOne([
    'token' => $token,
    'used' => false,
    'user_id' => (int) $user_id,
    'trajet_id' => (int) $trajet_id
]);


if (!$tokenDoc) {
    echo json_encode(["status" => "error", "message" => "Lien expiré ou invalide"]);
    exit;
}


// Vérifie si un avis existe déjà
$avisExistant = $avisCollection->findOne([
    'user_id' => $user_id,
    'trajet_id' => $trajet_id
]);

if ($avisExistant) {
    echo json_encode([
        "status" => "error",
        "message" => "Vous avez déjà laissé un avis pour ce trajet."
    ]);
    exit;
}


// Récupérer l'ID du chauffeur
$stmt = $pdo->prepare("SELECT chauffeur_id FROM covoiturages WHERE id = :trajet_id");
$stmt->execute(['trajet_id' => $trajet_id]);
$chauffeur = $stmt->fetch();

if (!$chauffeur) {
    echo json_encode(["status" => "error", "message" => "Trajet introuvable"]);
    exit;
}

$chauffeurId = (int) $chauffeur['chauffeur_id'];


// Insertion de l'avis dans MongoDB

try {
    $avisCollection->insertOne([
        "user_id" => $user_id,
        "trajet_id" => $trajet_id,
        "note" => $note,
        "commentaire" => $commentaire,
        "chauffeur_id" => $chauffeurId,
        "statut" => "en attente",
        "date_envoi" => date('H:i:s - d/m/Y')
    ]);

    // Marquer le token comme utilisé
    $tokenCollection->updateOne(
        ['token' => $token],
        ['$set' => ['used' => true]]
    );

    
    // Recalcul de la note du chauffeur
    $avisValidés = $avisCollection->find([
        'statut' => 'validé',
        'chauffeur_id' => $chauffeurId
    ]);

    $total = 0;
    $count = 0;
    foreach ($avisValidés as $avis) {
        if (isset($avis['note'])) {
            $total += (int) $avis['note'];
            $count++;
        }
    }

    // Mise à jour de la note dans MySQL
    if ($count > 0) {
        $moyenne = round($total / $count); 
        $update = $pdo->prepare("UPDATE utilisateurs SET note = :note WHERE id = :id");
        $update->execute(['note' => $moyenne, 'id' => $chauffeurId]);
    }

    echo json_encode(["status" => "success", "message" => "Avis enregistré. Il sera publié après validation."]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Erreur serveur : " . $e->getMessage()]);
}

