<?php
include '../db_connection.php'; // Connexion à la BDD

// Récupérer tous les utilisateurs
$query = $pdo->query("SELECT id, pseudo, email, role, credit FROM utilisateurs");
$utilisateurs = $query->fetchAll();

// Retourner la liste en JSON
header('Content-Type: application/json');
echo json_encode($utilisateurs);
?>
