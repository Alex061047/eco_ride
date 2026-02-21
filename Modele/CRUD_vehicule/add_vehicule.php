<?php
// La réponse renvoyée sera au format JSON
header("Content-Type: application/json");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Connexion à la base de données + import du système de logs MongoDB
include __DIR__ . '/../db_connection.php';
include __DIR__ . '/../mongodb/mongo_logs.php';

// Vérifie que la requête est bien une requête POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données JSON envoyées par le client
    $data = $GLOBALS['__JSON_BODY'] ?? json_decode(file_get_contents("php://input"), true);
    $sessionUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $sessionRole = $_SESSION['user_role'] ?? null;

    // Sécurité serveur: utilisateur connecté obligatoire
    if ($sessionUserId <= 0) {
        echo json_encode(["status" => "error", "message" => "Utilisateur non connecté."]);
        exit;
    }

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

    // Sécurité serveur: ajout uniquement sur son propre compte (sauf admin)
    if ($sessionRole !== 'admin' && (int) $data["utilisateur_id"] !== $sessionUserId) {
        echo json_encode(["status" => "error", "message" => "Action interdite."]);
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

