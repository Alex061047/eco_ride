<?php
// Inclusion de la connexion à la base de données
include __DIR__ . '/../db_connection.php';
session_start();

// Enregistrement des logs dans MongoDB
include __DIR__ . '/../mongodb/mongo_logs.php';

// Vérifie que la requête est bien de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $GLOBALS['__JSON_BODY'] ?? json_decode(file_get_contents("php://input"), true);
    $sessionUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $sessionRole = $_SESSION['user_role'] ?? null;

    // Sécurité serveur: utilisateur connecté obligatoire
    if ($sessionUserId <= 0) {
        echo json_encode(["status" => "error", "message" => "Utilisateur non connecté."]);
        exit;
    }

    // Vérifie que l'ID utilisateur est bien présent
    if (!isset($data['utilisateur_id'])) {
        echo json_encode(["status" => "error", "message" => "ID utilisateur manquant."]);
        exit;
    }

    // Sécurité serveur: modification uniquement de ses préférences (sauf admin)
    if ($sessionRole !== 'admin' && (int) $data['utilisateur_id'] !== $sessionUserId) {
        echo json_encode(["status" => "error", "message" => "Action interdite."]);
        exit;
    }

    // Initialisation des variables pour la requête SQL
    $fields = [];
    $params = [':utilisateur_id' => $data['utilisateur_id']];

    // Liste des champs modifiables dans la table "preferences"
    $possibleFields = ["fumeur", "animaux", "discussions", "musique", "autre"];

    // Pour chaque champ, s'il est présent dans les données reçues, on le prépare pour la requête
    foreach ($possibleFields as $field) {
         if (isset($data[$field])) {
            // Convertit les réponses "oui"/"non" en 1/0 pour les champs de type booléen
              if (in_array($field, ["fumeur", "animaux", "discussions", "musique"])) {
                    $data[$field] = strtolower($data[$field]) === "oui" ? 1 : 0;
                 }
        $fields[] = "$field = :$field";
        $params[":$field"] = $data[$field];
    }
}

    // Si aucun champ n'a été modifié, on arrête
    if (empty($fields)) {
        echo json_encode(["status" => "error", "message" => "Aucune préférence à modifier."]);
        exit;
    }

    // Vérifie si une ligne existe déjà
    $check = $pdo->prepare("SELECT COUNT(*) FROM preferences WHERE utilisateur_id = :utilisateur_id");
    $check->execute(['utilisateur_id' => $data['utilisateur_id']]);
    $exists = $check->fetchColumn();

    // Si la ligne existe, on effectue un UPDATE
    if ($exists) {
        $sql = "UPDATE preferences SET " . implode(", ", $fields) . " WHERE utilisateur_id = :utilisateur_id";
    } else {
        // Création de la ligne si elle n'existe pas
        $columns = implode(", ", array_keys($params));
        $placeholders = implode(", ", array_keys($params));
        $sql = "INSERT INTO preferences (" . str_replace(":", "", $columns) . ") VALUES ($placeholders)";
    }

    // Exécution de la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Enregistrement dans les logs MongoDB
    $action = "Modification préférence";
    enregistrerLog($action, "Préférences modifiées pour utilisateur ID : " . $data["utilisateur_id"]);


    // Retour d'un message de succès
    echo json_encode(["status" => "success", "message" => "Préférences mises à jour."]);
}
?>

