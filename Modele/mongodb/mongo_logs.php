<?php

require __DIR__ . '/mongo_connection.php';

function enregistrerLog($action, $details) {
    global $logsCollection;

    // Si MongoDB est indisponible, on ignore le log sans casser l'exécution.
    if (!$logsCollection) {
        return;
    }
    
    $log = [
        'action' => $action,
        'details' => $details,
        'timestamp' => (new DateTime())->format(DATE_ATOM)
    ];

    $logsCollection->insertOne($log);
}
?>
