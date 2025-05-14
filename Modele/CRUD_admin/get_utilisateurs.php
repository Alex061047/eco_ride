<?php
// Connexion à la base de données MySQL
require_once('../db_connection.php');

// Réponse en JSON
header('Content-Type: application/json');

// Préparation de la requête SQL pour récupérer les utilisateurs
$stmt = $pdo->prepare("SELECT id, pseudo, email, role, credit, note FROM utilisateurs ORDER BY pseudo ASC");
$stmt->execute();

$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($utilisateurs);
