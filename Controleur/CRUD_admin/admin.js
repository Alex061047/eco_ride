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
