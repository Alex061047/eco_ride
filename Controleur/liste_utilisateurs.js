document.addEventListener("DOMContentLoaded", function () {
    fetch("../../Modele/CRUD_utilisateur/get_utilisateurs.php")
        .then(response => response.json())
        .then(utilisateurs => {
            const tableBody = document.getElementById("usersTable");
            tableBody.innerHTML = ""; // Nettoyer le tableau avant d'ajouter les nouvelles données

            utilisateurs.forEach(user => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.pseudo}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>${user.credit}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Erreur lors du chargement des utilisateurs :", error));
});
