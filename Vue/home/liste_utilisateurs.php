<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <link rel="stylesheet" href="../../Assets/styles/app.css">
</head>
<body>
    <h2>Liste des utilisateurs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pseudo</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Crédit</th>
            </tr>
        </thead>
        <tbody id="usersTable">
            <!-- Ajout des utilisateurs -->
        </tbody>
    </table>


    <!-- Formulaire de modification caché -->
<div id="editModal" style="display:none;">
    <h2>Modifier l'utilisateur</h2>
    <form id="editForm">
        <input type="hidden" name="id" id="editId">
        <label>Pseudo :</label>
        <input type="text" name="pseudo" id="editPseudo"><br>

        <label>Email :</label>
        <input type="email" name="email" id="editEmail"><br>

        <label>Rôle :</label>
        <select name="role" id="editRole">
            <option value="passager">Passager</option>
            <option value="chauffeur">Chauffeur</option>
            <option value="employe">Employé</option>
            <option value="admin">Admin</option>
        </select><br>

        <label>Crédit :</label>
        <input type="number" name="credit" id="editCredit"><br>

        <button type="submit">Modifier</button>
        <button type="button" onclick="closeEditModal()">Annuler</button>
    </form>
</div>

    <script src="../../Controleur/liste_utilisateurs.js"></script>
</body>
</html>
