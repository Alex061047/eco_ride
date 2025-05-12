<?php
// Connexion à la base de données MySQL
include('../db_connection.php');

// Réponse en JSON
header('Content-Type: application/json');

// Nombre de covoiturages par jour sur les 30 derniers jours
$sql = "
    SELECT DATE_FORMAT(date_heure_depart, '%d/%m') AS jour, COUNT(*) AS total
    FROM covoiturages
    WHERE (etat = 'terminé' OR etat = 'en cours')
    AND date_heure_depart >= CURDATE() - INTERVAL 30 DAY
    GROUP BY jour
    ORDER BY date_heure_depart
";

// Execution de la requête
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retour en JSON
echo json_encode($resultats);
?>
