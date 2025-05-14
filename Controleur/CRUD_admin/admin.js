// Sélection du bouton pour créer un employé
const btnCreerEmploye = document.getElementById("btn-creer-employe");

// Création dynamique du formulaire avec switch afficher/masquer
btnCreerEmploye.addEventListener("click", () => {
    const existingForm = document.getElementById("form-employe");

    // Si le formulaire existe déjà, on le supprime
    if (existingForm) {
        existingForm.closest(".card").remove();
        return;
    }

    // Création du formulaire
    const container = document.querySelector(".container");

    const formWrapper = document.createElement("div");
    formWrapper.classList.add("card", "shadow", "p-4", "mb-4");
    formWrapper.style.maxWidth = "30%";
    formWrapper.style.margin = "0 auto";
    formWrapper.innerHTML = `
        <h4 class="mb-3 text-center">Créer un compte employé</h4>
        <form id="form-employe">
            <div class="mb-3">
                <input type="text" name="pseudo" class="form-control" placeholder="Pseudo" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Adresse e-mail" required>
            </div>
            <div class="mb-3">
                <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" required>
            </div>
            <input type="hidden" name="role" value="employe">
            <button type="submit" class="btn btn-success w-100">Créer le compte</button>
        </form>
        <div id="message-employe" class="mt-3 text-center"></div>
    `;

    container.prepend(formWrapper);

    // Gestion de la soumission
    const form = document.getElementById("form-employe");
    const message = document.getElementById("message-employe");

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const pseudo = form.pseudo.value.trim();
        const email = form.email.value.trim();
        const mot_de_passe = form.mot_de_passe.value;
        const role = form.role.value;

        // Envoie les données au fichier creer_employe.php
        fetch("../../Modele/CRUD_admin/creer_employe.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ pseudo, email, mot_de_passe, role })
        })
        .then(res => res.json())
        .then(data => {
            message.textContent = data.message;
            message.style.color = data.status === "success" ? "green" : "red";
            if (data.status === "success") form.reset();
        })
        .catch(err => {
            message.textContent = "Erreur lors de la requête.";
            message.style.color = "red";
        });
    });
});




// Bouton Suspendre un compte

// Sélection du bouton "Suspendre un compte"
const btnSuspendre = document.getElementById("btn-suspendre-compte");

// Affichage/Masquage du tableau
btnSuspendre.addEventListener("click", () => {
    const existingTable = document.getElementById("table-utilisateurs");

    // Si le tableau existe déjà, on le retire
    if (existingTable) {
        existingTable.closest(".card").remove();
        return;
    }

    // Création de la structure du tableau
    const container = document.querySelector(".container");

    const tableWrapper = document.createElement("div");
    tableWrapper.classList.add("card", "shadow", "p-4", "mb-4", "bg-secondary");
    tableWrapper.innerHTML = `
        <h4 class="mb-3 text-center">Gérer les comptes utilisateurs</h4>
        <input type="text" id="search-user" class="form-control mb-3" placeholder="Recherche...">
        <div class="table-responsive table-dark">
            <table id="table-utilisateurs" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Crédit</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    `;
    container.appendChild(tableWrapper);

    // Chargement des utilisateurs via PHP
    fetch("../../Modele/CRUD_admin/get_utilisateurs.php")
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#table-utilisateurs tbody");
            tbody.innerHTML = "";

            data.forEach(user => {
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.pseudo}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>${user.credit}</td>
                    <td>${user.note}</td>
                    <td>
                        <button class="btn btn-${user.note == -1 ? "primary" : "danger"} btn-sm suspend-btn">
                            ${user.note == -1 ? "Rétablir" : "Suspendre"}
                        </button>
                    </td>
                `;

                // Ajout de l'écouteur sur le bouton "Suspendre"
                tr.querySelector(".suspend-btn").addEventListener("click", () => {
                    fetch("../../Modele/CRUD_admin/suspendre_utilisateur.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: user.id })
                    })
                    .then(res => res.json())
                    .then(resp => {
                        alert(resp.message);
                        btnSuspendre.click(); // Alterne masquer et afficher le tableau
                        btnSuspendre.click();
                    })
                    .catch(() => alert("Erreur lors de la suspension."));
                });

                tbody.appendChild(tr);
            });
        });

    // Filtrage avec la barre de recherche
    document.getElementById("search-user").addEventListener("input", function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll("#table-utilisateurs tbody tr");

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchValue) ? "" : "none";
        });
    });
});

