<?php
session_start();
$role = $_SESSION['user_role'] ?? null;
if ($role === null && isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../Modele/db_connection.php';
    $stmt = $pdo->prepare('SELECT role FROM utilisateurs WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => (int) $_SESSION['user_id']]);
    $role = $stmt->fetchColumn() ?: null;
    if ($role !== null) {
        $_SESSION['user_role'] = $role;
    }
}

if (!isset($_SESSION['user_id']) || !in_array((string) $role, ['employe', 'admin'], true)) {
    http_response_code(403);
    exit('Acces interdit.');
}
?>

<link rel="stylesheet" href="../../assets/styles/app.css">

<section class="container mt-5">

    <h2 class="text-center mb-4">Espace Employé</h2>

    <!-- Champ de recherche et dropdown "Note max" -->
    <div class="mb-4 d-flex justify-content-between">
        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par ID, pseudo, mail, ville...">
        <select id="noteFilter" class="form-control ms-3" style="width: auto;">
            <option value="">Note max</option>
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
    </div>

    <!-- Tableau principal -->
    <div>
        <table class="table table-bordered table-striped" id="table">
            <thead class="table-dark text-center align-middle">
                <tr>
                    <th>ID du trajet</th>
                    <th>Départ</th>
                    <th>Arrivée</th>
                    <th>Chauffeur</th>
                    <th>Prix</th>
                    <th>Date du trajet</th>
                    <th>Passager</th>
                    <th>Commentaire</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rempli dynamiquement par employe.js -->
            </tbody>
        </table>
    </div>
</section>
