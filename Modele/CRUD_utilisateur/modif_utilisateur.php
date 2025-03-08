<?php

include ('../db_connection.php');

// Fonction pour modifier un utilisateur
function modifierUtilisateur($pdo, $pseudo, $email, $role, $credit) {
    $sql = "UPDATE utilisateurs SET pseudo = :pseudo, email = :email, role = :role, credit = :credit, WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':role' => $role,
        ':credit' => $credit
    ]);
    echo "Utilisateur mis à jour avec succès !";
}

?>

<!--Importer le formulaire-->
<div id="fichier_importe"></div>

<script>
  fetch('../../Vue/formulaire/modif_utilisateur.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById('fichier_importe').innerHTML = data;
    });
</script>

<?php modifierUtilisateur($pdo, 'UpdatedUser', 'updated@email.com', 'chauffeur', 100); ?> 