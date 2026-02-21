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

if (!isset($_SESSION['user_id']) || $role !== 'admin') {
    http_response_code(403);
    exit('Acces interdit.');
}
?>

<link rel="stylesheet" href="../../assets/styles/admin/admin.css">

<!-- Header -->
<header>
  <div class="hero-scene text-center text-black">
    <div class="hero-scene-content">
      <h1>Espace Administrateur</h1>
    </div>
  </div>
</header>

<!-- Contenu principal -->
<div class="container mt-5">

  <!-- Boutons -->
  <div class="d-flex justify-content-evenly mb-4 flex-wrap">
    <button class="btn btn-success" id="btn-creer-employe">Créer un compte employé</button>
    <button class="btn btn-success" id="btn-suspendre-compte">Suspendre un compte</button>
  </div>

  <!-- Graphiques -->
  <div class="row">
    <!-- Graphique covoiturage par jour -->
    <div class="col-md-6 mb-4">
      <h5 class="text-center">Covoiturages par jour</h5>
      <div class="card p-3 shadow-sm">
        <canvas id="graphCovoiturages"></canvas>
      </div>
    </div>

    <!-- Graphique crédit gagné par jour -->
    <div class="col-md-6 mb-4">
      <h5 class="text-center">Crédits gagnés par jour</h5>
      <div class="card p-3 shadow-sm">
        <canvas id="graphCredits"></canvas>
      </div>
      <p class="mt-3 text-end pe-3">
        <strong>Crédit total perçu par la plateforme :</strong>
        <span id="creditTotal">0</span> crédits
      </p>
    </div>
  </div>

</div>

