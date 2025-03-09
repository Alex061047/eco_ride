<?php

require 'mongo_connection.php';
require __DIR__ . '../../../vendor/autoload.php';

use MongoDB\BSON\UTCDateTime;

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