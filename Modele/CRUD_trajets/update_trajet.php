<?php
require '../db_connection.php';
require '../mongodb/mongo_connection.php';
require '../mailer/sendMail.php';

use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

// Récupérer l'URL de base depuis l'environnement
$baseUrl = $_ENV['BASE_URL'] ?? getenv('BASE_URL') ?? 'http://localhost:8000';

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

$trajetId = intval($data['id']);
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

if ($nouvelEtat === "terminé") {
    // Récupérer les emails et les identifiants des passagers
    $sqlPassagers = "SELECT u.email, u.id FROM utilisateurs u
                     JOIN reservations r ON u.id = r.passager_id
                     WHERE r.covoiturage_id = :trajet_id AND r.statut = 'réalisé'";
    $stmtPassagers = $pdo->prepare($sqlPassagers);
    $stmtPassagers->execute(['trajet_id' => $trajetId]);
    $passagers = $stmtPassagers->fetchAll(PDO::FETCH_ASSOC);


    // Mail avis utilisateur
    // Collection pour le token des liens 
    $avisTokensCollection = $client->eco_ride->avis_tokens;

    foreach ($passagers as $passager) {
        $email = $passager['email'];
        $userId = $passager['id'];
    
        // Générer un jeton unique
        $token = bin2hex(random_bytes(32));
    
        // Stocker le token dans MongoDB
        $avisTokensCollection->insertOne([
            'token' => $token,
            'user_id' => (int) $userId,
            'trajet_id' => (int) $trajetId,
            'crée_le' => date('H:i:s - d/m/Y'),
            'used' => false
        ]);
    
        // Construire le lien avec token
        $lienAvis = "{$baseUrl}/Avis?token={$token}";
    
        // Envoi du mail
        $sujet = "Merci d'avoir voyagé avec EcoRide !";
        $message = "
            <p>Bonjour,<br><br>
            Votre trajet vient de se terminer.<br>
            Merci de prendre un instant pour confirmer que tout s'est bien déroulé :<br><br>
            <a href='{$lienAvis}'
            style='background-color:#A16D24; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>
            Valider le bon déroulement du trajet
            </a><br><br>
            Merci de contribuer à la qualité de notre communauté.<br><br>
            — L'équipe EcoRide
            </p>
        ";
        sendMail($email, $sujet, $message);  
    }
}
    


//Si le trajet est annulé, mettre les réservations en "annulé"
if ($nouvelEtat === "annulé") {
    $sqlUpdateReservations = "UPDATE reservations SET statut = 'annulé' WHERE covoiturage_id = :trajet_id";
    $stmtUpdateReservations = $pdo->prepare($sqlUpdateReservations);
    $stmtUpdateReservations->execute(['trajet_id' => $trajetId]);
};



//Envoi mail après annulation chauffeur

if ($nouvelEtat === "annulé") {
    // Mettre à jour les réservations
    $sql = "UPDATE reservations SET statut = 'annulé' WHERE covoiturage_id = :trajet_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['trajet_id' => $trajetId]);

    // Récupérer les emails des passagers
    $sqlPassagers = "SELECT u.email FROM utilisateurs u 
                     JOIN reservations r ON u.id = r.passager_id 
                     WHERE r.covoiturage_id = :trajet_id";
    $stmtPassagers = $pdo->prepare($sqlPassagers);
    $stmtPassagers->execute(['trajet_id' => $trajetId]);
    $passagers = $stmtPassagers->fetchAll(PDO::FETCH_ASSOC);

    // Envoyer un mail aux passagers
    foreach ($passagers as $passager) {
        $email = $passager['email'];
        $sujet = "Annulation de votre trajet EcoRide";
        $message = "<p>Bonjour,<br><br>Votre trajet a été annulé par le chauffeur.<br>
                     Nous nous excusons pour la gêne occasionnée.<br><br>
                     L'équipe EcoRide</p>";
        sendMail($email, $sujet, $message);
    }
}

echo json_encode(["success" => true, "message" => "Mise à jour effectuée avec succès"]);
?>