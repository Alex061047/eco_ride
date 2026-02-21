<?php
require __DIR__ . '/../db_connection.php';
require __DIR__ . '/../mongodb/mongo_connection.php';
require __DIR__ . '/../mailer/sendMail.php';

use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

// Recuperer l'URL de base depuis l'environnement
$baseUrl = $_ENV['BASE_URL'] ?? getenv('BASE_URL') ?? 'http://localhost:8000';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Utilisateur non authentifie"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id']) || !isset($data['etat'])) {
    echo json_encode(["success" => false, "message" => "Donnees manquantes"]);
    exit;
}

$trajetId = intval($data['id']);
$nouvelEtat = $data['etat'];

// Verifier que le trajet existe et recuperer son chauffeur
$sqlCheck = "SELECT chauffeur_id FROM covoiturages WHERE id = :trajet_id";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute(['trajet_id' => $trajetId]);
$trajet = $stmtCheck->fetch();

if (!$trajet) {
    echo json_encode(["success" => false, "message" => "Trajet introuvable"]);
    exit;
}

// Verifier que l'utilisateur est bien le chauffeur pour en cours/termine
if (($nouvelEtat === "en cours" || $nouvelEtat === "terminé") && $trajet['chauffeur_id'] != $_SESSION['user_id']) {
    echo json_encode(["success" => false, "message" => "Acces refuse"]);
    exit;
}

// Mettre a jour l'etat du trajet
$sqlUpdate = "UPDATE covoiturages SET etat = :etat WHERE id = :trajet_id";
$stmtUpdate = $pdo->prepare($sqlUpdate);
$stmtUpdate->execute(['etat' => $nouvelEtat, 'trajet_id' => $trajetId]);

// Si termine, passer les reservations en realise
if ($nouvelEtat === "terminé") {
    $sqlUpdateReservations = "UPDATE reservations SET statut = 'réalisé' WHERE covoiturage_id = :trajet_id";
    $stmtUpdateReservations = $pdo->prepare($sqlUpdateReservations);
    $stmtUpdateReservations->execute(['trajet_id' => $trajetId]);
}

if ($nouvelEtat === "terminé") {
    // Recuperer emails + ids des passagers
    $sqlPassagers = "SELECT u.email, u.id FROM utilisateurs u
                     JOIN reservations r ON u.id = r.passager_id
                     WHERE r.covoiturage_id = :trajet_id AND r.statut = 'réalisé'";
    $stmtPassagers = $pdo->prepare($sqlPassagers);
    $stmtPassagers->execute(['trajet_id' => $trajetId]);
    $passagers = $stmtPassagers->fetchAll(PDO::FETCH_ASSOC);

    // Collection Mongo pour stocker les tokens de lien avis
    $avisTokensCollection = $client->eco_ride->avis_tokens;

    foreach ($passagers as $passager) {
        $email = $passager['email'];
        $userId = $passager['id'];

        // Generer un token unique
        $token = bin2hex(random_bytes(32));

        // Stocker le token dans MongoDB
        $avisTokensCollection->insertOne([
            'token' => $token,
            'user_id' => (int) $userId,
            'trajet_id' => (int) $trajetId,
            'cree_le' => date('H:i:s - d/m/Y'),
            'used' => false
        ]);

        // Construire le lien avec token
        $lienAvis = "{$baseUrl}/Avis?token={$token}";

        // Envoi du mail
        $sujet = "Merci d'avoir voyage avec EcoRide !";
        $message = "
            <p>Bonjour,<br><br>
            Votre trajet vient de se terminer.<br>
            Merci de prendre un instant pour confirmer que tout s'est bien deroule :<br><br>
            <a href='{$lienAvis}'
            style='background-color:#A16D24; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>
            Valider le bon deroulement du trajet
            </a><br><br>
            Merci de contribuer a la qualite de notre communaute.<br><br>
            L'equipe EcoRide
            </p>
        ";
        sendMail($email, $sujet, $message);
    }
}

// Si annule, passer les reservations en annule
if ($nouvelEtat === "annulé") {
    $sqlUpdateReservations = "UPDATE reservations SET statut = 'annulé' WHERE covoiturage_id = :trajet_id";
    $stmtUpdateReservations = $pdo->prepare($sqlUpdateReservations);
    $stmtUpdateReservations->execute(['trajet_id' => $trajetId]);
}

// Envoi de mail apres annulation chauffeur
if ($nouvelEtat === "annulé") {
    $sql = "UPDATE reservations SET statut = 'annulé' WHERE covoiturage_id = :trajet_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['trajet_id' => $trajetId]);

    $sqlPassagers = "SELECT u.email FROM utilisateurs u
                     JOIN reservations r ON u.id = r.passager_id
                     WHERE r.covoiturage_id = :trajet_id";
    $stmtPassagers = $pdo->prepare($sqlPassagers);
    $stmtPassagers->execute(['trajet_id' => $trajetId]);
    $passagers = $stmtPassagers->fetchAll(PDO::FETCH_ASSOC);

    foreach ($passagers as $passager) {
        $email = $passager['email'];
        $sujet = "Annulation de votre trajet EcoRide";
        $message = "<p>Bonjour,<br><br>Votre trajet a ete annule par le chauffeur.<br>
                     Nous nous excusons pour la gene occasionnee.<br><br>
                     L'equipe EcoRide</p>";
        sendMail($email, $sujet, $message);
    }
}

echo json_encode(["success" => true, "message" => "Mise a jour effectuee avec succes"]);
?>
