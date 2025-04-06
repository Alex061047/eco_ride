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
                    <td>
                        <button class="edit-btn" data-id="${user.id}" data-pseudo="${user.pseudo}" data-email="${user.email}" data-role="${user.role}" data-credit="${user.credit}">Modifier</button>
                        <button class="delete-btn" data-id="${user.id}">Supprimer</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Ajouter les événements aux boutons
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", openEditModal);
            });

            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", deleteUser);
            });
        })
        .catch(error => console.error("Erreur lors du chargement des utilisateurs :", error));
});

//Ajout de la suppression utilisateur
function deleteUser(event) {
    const userId = event.target.getAttribute("data-id");

    if (confirm("Voulez-vous vraiment supprimer cet utilisateur ?")) {
        fetch("../../Modele/CRUD_utilisateur/delete_utilisateur.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: userId }) // Envoi en JSON
        })
        .then(response => response.json()) // Conversion de la réponse en JSON
        .then(data => {
            alert(data.message);
            location.reload(); // Recharger la liste après suppression
        })
        .catch(error => console.error("Erreur lors de la suppression :", error));
    }
};

//Ajout de la modification utilisateur
function openEditModal(event) {
    const button = event.target;
    const userId = button.getAttribute("data-id");

    document.getElementById("editId").value = userId;
    document.getElementById("editPseudo").value = button.getAttribute("data-pseudo");
    document.getElementById("editEmail").value = button.getAttribute("data-email");
    document.getElementById("editRole").value = button.getAttribute("data-role");
    document.getElementById("editCredit").value = button.getAttribute("data-credit");

    document.getElementById("editModal").style.display = "block";
}


document.getElementById("editForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const formData = {
        id: document.getElementById("editId").value,
        pseudo: document.getElementById("editPseudo").value,
        email: document.getElementById("editEmail").value,
        role: document.getElementById("editRole").value,
        credit: document.getElementById("editCredit").value
    };


    fetch("../../Modele/CRUD_utilisateur/update_utilisateur.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    })
    .catch(error => console.error("Erreur lors de la modification :", error));
});


function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}
