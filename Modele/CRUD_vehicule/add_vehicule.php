<?php
// La réponse renvoyée sera au format JSON
header("Content-Type: application/json");

// Connexion à la base de données + import du système de logs MongoDB
include '../db_connection.php';
include '../mongodb/mongo_logs.php';

// Vérifie que la requête est bien une requête POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données JSON envoyées par le client
    $data = json_decode(file_get_contents("php://input"), true);

    // Vérifie que les données ont bien été reçues
    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Format JSON invalide."]);
        exit;
    }

    // Vérifie que tous les champs obligatoires sont présents et non vides
    if (
        empty($data["utilisateur_id"]) || empty($data["marque"]) || empty($data["modele"]) ||
        empty($data["immatriculation"]) || empty($data["couleur"]) ||
        empty($data["energie"]) || empty($data["nb_places"])
    ) {
        echo json_encode(["status" => "error", "message" => "Tous les champs sont requis."]);
        exit;
    }

    try {
        // Prépare la requête SQL pour insérer le véhicule en base
        $stmt = $pdo->prepare("INSERT INTO vehicules (utilisateur_id, marque, modele, immatriculation, couleur, energie, nb_places, date_immatriculation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
       
        // Exécute la requête avec les données fournies
        $stmt->execute([
            $data["utilisateur_id"],
            $data["marque"],
            $data["modele"],
            $data["immatriculation"],
            $data["couleur"],
            $data["energie"],
            $data["nb_places"],
            $data["date_immatriculation"]
        ]);

        // Retourne un message de succès
        echo json_encode(["status" => "success", "message" => "Véhicule ajouté avec succès."]);
        // Log de l'ajout dans MongoDB
        enregistrerLog("Ajout véhicule", "Véhicule ajouté pour utilisateur ID : " . $data["utilisateur_id"] . " (" . $data["marque"] . " " . $data["modele"] . ")");

    } 
    // Gestion des erreurs SQL
    catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} 
// Si la méthode n'est pas POST
else {
    echo json_encode(["status" => "error", "message" => "Requête invalide."]);
}
