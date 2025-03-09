<?php
include '../../Modele/mongodb/mongo_connection.php';

// Récupérer les logs
$logs = $logsCollection->find([], ['sort' => ['timestamp' => -1]]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logs des activités</title>
</head>
<body>
    <h2>Logs des activités</h2>
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
                    <td><?= date("Y-m-d H:i:s", $log['timestamp']->toDateTime()->getTimestamp()); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
