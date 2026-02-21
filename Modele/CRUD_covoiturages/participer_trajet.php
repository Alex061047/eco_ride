<?php
// Connexion à la base de donnée
include __DIR__ . '/../db_connection.php';
// Connexion à MongoDB pour les logs
include __DIR__ . '/../mongodb/mongo_logs.php';
// Démarre la session PHP
session_start();
// La réponse sera envoyée au format JSON
header('Content-Type: application/json');

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_connected']);
    exit;
}

$userId = $_SESSION['user_id'];

// Récupère les données JSON envoyées en POST
$input = json_decode(file_get_contents("php://input"), true);
$trajetId = $input['covoiturage_id'] ?? null;
$nbPlacesDemandees = isset($input['nb_places']) ? intval($input['nb_places']) : 1;

// Vérifie la validité des données (ID et nombre de places)
if (!$trajetId || $nbPlacesDemandees <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID du covoiturage ou nombre de places invalide']);
    exit;
}

try {
    // Vérifier que le covoiturage existe et a assez de places restantes
    $stmt = $pdo->prepare("SELECT nb_places_restantes, prix FROM covoiturages WHERE id = :id AND etat = 'à venir'");
    $stmt->execute(['id' => $trajetId]);
    $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si le trajet n'existe pas ou n'est plus ouvert aux réservations
    if (!$trajet) {
        echo json_encode(['status' => 'error', 'message' => 'Trajet introuvable ou plus disponible']);
        exit;
    }

    // Si pas assez de places disponibles
    if ($trajet['nb_places_restantes'] < $nbPlacesDemandees) {
        echo json_encode(['status' => 'error', 'message' => 'Pas assez de places restantes pour ce trajet']);
        exit;
    }

    // Calcul du coût total
    $prixTotal = $trajet['prix'] * $nbPlacesDemandees;

     // Vérifier le crédit de l'utilisateur
     $stmtCredit = $pdo->prepare("SELECT credit FROM utilisateurs WHERE id = :user_id");
     $stmtCredit->execute(['user_id' => $userId]);
     $utilisateur = $stmtCredit->fetch(PDO::FETCH_ASSOC);
 
     if (!$utilisateur || $utilisateur['credit'] < $prixTotal) {
         echo json_encode(['status' => 'error', 'message' => 'Crédit insuffisant pour effectuer cette réservation']);
         exit;
     }

    // Préparation de la requête pour insérer une réservation
    $insert = $pdo->prepare("INSERT INTO reservations (passager_id, covoiturage_id, statut)
                             VALUES (:user_id, :trajet_id, 'confirmé')");

     // Crée une boucle de réservation par place demandée
    for ($i = 0; $i < $nbPlacesDemandees; $i++) {
        $insert->execute([
            'user_id' => $userId,
            'trajet_id' => $trajetId
        ]);
    }

    // Mettre à jour les places restantes
    $update = $pdo->prepare("UPDATE covoiturages 
                             SET nb_places_restantes = nb_places_restantes - :nb_places 
                             WHERE id = :id");
    $update->execute([
        'nb_places' => $nbPlacesDemandees,
        'id' => $trajetId
    ]);

    // Déduire le crédit de l'utilisateur
    $updateCredit = $pdo->prepare("UPDATE utilisateurs 
                                   SET credit = credit - :montant 
                                   WHERE id = :user_id");
    $updateCredit->execute([
        'montant' => $prixTotal,
        'user_id' => $userId
    ]);

// Enregistrement logs MongoDB
    $logDetails = [
        "utilisateur_id" => $userId,
        "covoiturage_id" => $trajetId,
        "nb_places" => $nbPlacesDemandees,
        "montant_paye" => $prixTotal
    ];

    enregistrerLog("Réservation", $logDetails);

    // Enregistrement des crédits retenus pour la plateforme
$logsCredit = $mongo->eco_ride->logs_credit;
$logsCredit->insertOne([
    'trajet_id' => (int) $trajetId,
    'credits_plateforme' => 2 * $nbPlacesDemandees,
    'date' => date('Y-m-d H:i:s')
]);

    echo json_encode(['status' => 'success', 'message' => 'Réservation confirmée avec succès ! Crédit déduit : ' . $prixTotal . '']);

} catch (PDOException $e) {
    // Gestion des erreurs SQL
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>

