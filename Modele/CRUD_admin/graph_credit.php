<?php
// Connexion à MongoDB
require_once('../mongodb/mongo_connection.php');

// Réponse en JSON
header('Content-Type: application/json');

// On va récupérer le infos dans la bonne collection
$collection = $mongo->eco_ride->logs_credit;

// Date limite (30 derniers jours)
$dateLimite = (new DateTime('-30 days'))->format('Y-m-d H:i:s');

// Pipeline d'agrégation
$pipeline = [
    // On filtre les documents récents
    ['$match' => [
        'date' => ['$gte' => $dateLimite]
    ]],
    
    // On regroupe par jour extrait depuis la chaîne de date
    [
        '$group' => [
            '_id' => [
                '$substr' => ['$date', 0, 10] // extrait "Année/mois/jours"
            ],
            'totalCredits' => ['$sum' => '$credits_plateforme']
        ]
    ],

    // Reformater la date pour l'affichage (format jour/mois)
    [
        '$project' => [
            'jour' => [
                '$dateToString' => [
                    'format' => "%d/%m",
                    'date' => [
                        '$dateFromString' => ['dateString' => '$_id']
                    ]
                ]
            ],
            'total' => '$totalCredits'
        ]
    ],

    // Trier par date
    [
        '$sort' => ['_id' => 1]
    ]
];

$cursor = $collection->aggregate($pipeline);

// Construire le tableau final
$resultats = [];
foreach ($cursor as $doc) {
    $resultats[] = [
        'jour' => $doc['jour'],
        'total' => $doc['total']
    ];
}

// Retour JSON
echo json_encode($resultats);
?>
