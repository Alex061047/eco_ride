<?php
include '../../Modele/mongodb/mongo_connection.php';


// Récupérer les filtres
$actionFilter = $_GET['action'] ?? '';
$dateDebut = $_GET['date_debut'] ?? '';
$dateFin = $_GET['date_fin'] ?? '';

// Construire la requête MongoDB
$filter = [];

// Filtrer par action
if (!empty($actionFilter)) {
    $filter['action'] = $actionFilter;
}

// Filtrer par date (si les deux dates sont fournies)
if (!empty($dateDebut) && !empty($dateFin)) {
    $filter['timestamp'] = [
        '$gte' => date("Y-m-d\TH:i:s", strtotime($dateDebut)),
        '$lte' => date("Y-m-d\TH:i:s", strtotime($dateFin))
    ];
}

// Récupérer les logs filtrés
$logs = $logsCollection->find($filter, ['sort' => ['timestamp' => -1]]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logs des activités</title>
</head>
<body>
    <h2>Logs des activités</h2>

    <!-- Formulaire de filtrage -->
    <form method="GET">
        <label>Action :</label>
        <select name="action">
            <option value="">Toutes</option>
            <option value="Ajout utilisateur" <?= $actionFilter == 'Ajout utilisateur' ? 'selected' : '' ?>>Ajout utilisateur</option>
            <option value="Modification utilisateur" <?= $actionFilter == 'Modification utilisateur' ? 'selected' : '' ?>>Modification utilisateur</option>
            <option value="Suppression utilisateur" <?= $actionFilter == 'Suppression utilisateur' ? 'selected' : '' ?>>Suppression utilisateur</option>
        </select>

        <label>Date début :</label>
        <input type="date" name="date_debut" value="<?= htmlspecialchars($dateDebut) ?>">

        <label>Date fin :</label>
        <input type="date" name="date_fin" value="<?= htmlspecialchars($dateFin) ?>">

        <button type="submit">Filtrer</button>
    </form>

    <!-- Tableau des logs -->
    <table border="1">
        <thead>
            <tr>
                <th>Action</th>
                <th>Détails</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['action']); ?></td>
                    <td><?= htmlspecialchars($log['details']); ?></td>
                    <td><?= date("Y-m-d H:i:s", strtotime($log['timestamp'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
