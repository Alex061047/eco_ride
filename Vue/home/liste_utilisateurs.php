<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <link rel="stylesheet" href="../../assets/styles/app.css">
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTable">
            <?php
            include '../../Modele/db_connection.php';

            $sql = "SELECT * FROM utilisateurs";
            $stmt = $pdo->query($sql);
            $utilisateurs = $stmt->fetchAll();

            foreach ($utilisateurs as $user) {
                echo "<tr>
                    <td>" . htmlspecialchars($user['id']) . "</td>
                    <td>" . htmlspecialchars($user['pseudo']) . "</td>
                    <td>" . htmlspecialchars($user['email']) . "</td>
                    <td>" . htmlspecialchars($user['role']) . "</td>
                    <td>" . htmlspecialchars($user['credit']) . "</td>
                    <td>
                        <button class='edit-btn' 
                            data-id='" . htmlspecialchars($user['id']) . "' 
                            data-pseudo='" . htmlspecialchars($user['pseudo']) . "' 
                            data-email='" . htmlspecialchars($user['email']) . "' 
                            data-role='" . htmlspecialchars($user['role']) . "' 
                            data-credit='" . htmlspecialchars($user['credit']) . "'>
                             Modifier
                        </button>
                        <button class='delete-btn' data-id='" . htmlspecialchars($user['id']) . "'>Supprimer</button>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Formulaire de modification -->
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
                <option value="passager-chauffeur">Passager-Chauffeur</option>
                <option value="employe">Employé</option>
                <option value="admin">Admin</option>
            </select><br>

            <label>Crédit :</label>
            <input type="number" name="credit" id="editCredit"><br>

            <button type="submit">Modifier</button>
            <button type="button" onclick="closeEditModal()">Annuler</button>
        </form>
    </div>

    <script src="../../Controleur/CRUD_utilisateur/liste_utilisateurs.js"></script>
</body>
</html>
