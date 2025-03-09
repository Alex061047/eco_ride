<?php
require 'mongo_connection.php';

function enregistrerLog($action, $details) {
    global $logsCollection;
    
    $log = [
        'action' => $action,
        'details' => $details,
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ];

    $logsCollection->insertOne($log);
}
?>
