<?php
// Connexion à la base de données MySQL
require '../db_connection.php';
// Connexion à la base MongoDB
require '../mongodb/mongo_connection.php';

// Réponse en JSON
header('Content-Type: application/json');

// Récupère la valeur de filtre "noteMax" si elle existe dans l'URL, sinon null
$noteMax = isset($_GET['noteMax']) ? (int) $_GET['noteMax'] : null;

// Préparation de la collection Mongo
$avisCollection = $mongo->eco_ride->avis_trajet;

// On ne récupère que les avis "en attente" dans MongoDB
$filtreMongo = ['statut' => 'en attente'];
// Si une note max est fournie, on ajoute cette condition dans le filtre
if ($noteMax !== null) {
    $filtreMongo['note'] = ['$lte' => $noteMax];
}

// Récupération des enregistrements depuis MongoDB selon les filtres
$avisCursor = $avisCollection->find($filtreMongo);

// Tableau de résultat vide
$finalResults = [];

// Parcours de chaque avis récupéré
foreach ($avisCursor as $avis) {
    $trajetId = (int) $avis['trajet_id'];
    $passagerId = (int) $avis['user_id'];
    $note = isset($avis['note']) ? (int) $avis['note'] : null;
    $commentaire = $avis['commentaire'] ?? '';
    $dateCommentaire = $avis['date_envoi'] ?? '';

    // Vérification que le passager a bien réservé le trajet
    $stmtCheck = $pdo->prepare("SELECT * FROM reservations WHERE covoiturage_id = :tid AND passager_id = :pid");
    $stmtCheck->execute(['tid' => $trajetId, 'pid' => $passagerId]);
    if (!$stmtCheck->fetch()) continue; // Si aucune réservation trouvée, on ignore l'avis

    // Récupérer des infos du trajet et du chauffeur
    $stmtTrajet = $pdo->prepare("
        SELECT 
            c.id AS trajet_id,
            c.depart,
            c.arrivee,
            c.prix,
            c.date_heure_depart,
            u.pseudo AS chauffeur_pseudo,
            u.email AS chauffeur_mail
        FROM covoiturages c
        JOIN utilisateurs u ON u.id = c.chauffeur_id
        WHERE c.id = :tid
    ");
    $stmtTrajet->execute(['tid' => $trajetId]);
    $trajet = $stmtTrajet->fetch(PDO::FETCH_ASSOC);
    if (!$trajet) continue; // Si le trajet n'existe pas en base, on ignore

    // Récupération des infos du passager
    $stmtPassager = $pdo->prepare("SELECT pseudo, email FROM utilisateurs WHERE id = :pid");
    $stmtPassager->execute(['pid' => $passagerId]);
    $passager = $stmtPassager->fetch(PDO::FETCH_ASSOC);
    if (!$passager) continue; // Si l'utilisateur n'existe pas, on ignore

    // Fusion des données dans le tableau final
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

// Envoi du tableau de résultats au format JSON
echo json_encode($finalResults);
