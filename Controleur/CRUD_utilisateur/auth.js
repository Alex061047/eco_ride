// Attache le gestionnaire d'événements pour le formulaire
attachFormHandler();

function attachFormHandler() {
    // Sélection des éléments HTML du formulaire et autres éléments associés
    const form = document.getElementById('form'); 
    const toggleButton = document.getElementById('toggle-button'); // Le bouton pour basculer entre inscription et connexion
    const formTitle = document.getElementById('form-title'); 
    const pseudoField = document.getElementById('pseudo-field'); // Le champ pseudo (visible uniquement en inscription)
    const roleField = document.getElementById('role-field'); // Le champ rôle (visible uniquement en inscription)
    const submitButton = document.getElementById('submit-button'); // Le bouton de soumission
    const messageDiv = document.getElementById('message'); // Affichage du succès ou de l'echec

    // Initialisation du mode de formulaire (connexion par défaut)
    let mode = "connexion"; 

    // Si le formulaire ou le bouton de bascule n'existent pas, on quitte la fonction
    if (!form || !toggleButton) return;

    // Gestion du clic sur le bouton de bascule
    toggleButton.addEventListener('click', () => {
        // Si on est en mode "connexion", on passe en mode "inscription"
        if (mode === "connexion") {
            mode = "inscription"; // On change le mode
            formTitle.textContent = "Inscription"; 
            pseudoField.style.display = "block"; // Le champ pseudo devient visible
            roleField.style.display = "block"; // Le champ rôle devient visible
            toggleButton.textContent = "Déjà un compte ? Connectez-vous"; 
            submitButton.textContent = "S'inscrire"; 
        } else {
            // Si on est en mode "inscription", on repasse en mode "connexion"
            mode = "connexion"; // On change le mode
            formTitle.textContent = "Connexion"; 
            pseudoField.style.display = "none"; // Le champ pseudo est masqué
            roleField.style.display = "none"; // Le champ rôle est masqué
            toggleButton.textContent = "Pas encore de compte ? Inscrivez-vous"; 
            submitButton.textContent = "Se connecter";
        }
    });

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function (event) {
        event.preventDefault(); 

        // Récupération des valeurs des champs du formulaire
        const email = form.querySelector('[name="email"]').value;
        const mot_de_passe = form.querySelector('[name="mot_de_passe"]').value;

        // Si on est en mode "connexion"
        if (mode === "connexion") {
            // Envoi de la requête pour la connexion
            fetch('../../Controleur_b/CRUD_utilisateur/connexion_controller.php', {
                method: 'POST', // Méthode POST pour envoyer les données
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, mot_de_passe }) // Envoi des données sous forme de chaîne JSON
            })
            .then(async res => {
                const data = await res.json().catch(() => null);
                if (!res.ok || !data) {
                    throw new Error("Réponse serveur invalide");
                }
                return data;
            }) 
            .then(data => {
                // Affichage du message de retour
                messageDiv.innerHTML = `<p>${data.message}</p>`;
                // Changement de couleur selon le statut (succès ou erreur)
                messageDiv.style.color = data.status === "success" ? "green" : "red";

                // Si la connexion réussit, on effectue une redirection
                if (data.status === "success") {
                // Stockage d'un "token" temporaire dans sessionStorage
                 sessionStorage.setItem("isLoggedIn", "true");
                 sessionStorage.setItem("user", JSON.stringify(data.utilisateur)); // Stockage des infos de l'utilisateur
                 // Enregistrement des informations utilisateurs
                 sessionStorage.setItem("userId", data.utilisateur.id);
                 sessionStorage.setItem("userPseudo", data.utilisateur.pseudo);
                 sessionStorage.setItem("userRole", data.utilisateur.role);
                 
                 window.location.href = "/EspaceUtilisateur"; // Redirection vers Mon Espace
                }
            })
            .catch(err => {
                console.error(err);
                messageDiv.innerHTML = "<p>Erreur serveur lors de la connexion.</p>";
                messageDiv.style.color = "red";
            }); 
        } else {
            // Si on est en mode "inscription"
            const pseudo = form.querySelector('[name="pseudo"]').value; // Récupère le pseudo
            const role = form.querySelector('[name="role"]').value; // Récupère le rôle choisi

            // Envoi de la requête pour l'inscription
            fetch('../../Controleur_b/CRUD_utilisateur/inscription_controller.php', {
                method: 'POST', // Méthode POST pour envoyer les données
                headers: { "Content-Type": "application/json" }, 
                body: JSON.stringify({ pseudo, email, mot_de_passe, role }) // Envoi des données sous forme de chaîne JSON
            })
            .then(async res => {
                const data = await res.json().catch(() => null);
                if (!res.ok || !data) {
                    throw new Error("Réponse serveur invalide");
                }
                return data;
            }) 
            .then(data => {
                // Affichage du message de retour
                messageDiv.innerHTML = `<p>${data.message}</p>`;
                // Changement de couleur selon le statut (succès ou erreur)
                messageDiv.style.color = data.status === "success" ? "green" : "red";

                // Si l'inscription réussit
                if (data.status === "success") {
                    // Réinitialisation du formulaire et passage en mode "connexion"
                    form.reset();
                    mode = "connexion"; // On repasse en mode connexion
                    formTitle.textContent = "Connexion"; 
                    pseudoField.style.display = "none"; // On cache à nouveau le champ pseudo
                    roleField.style.display = "none"; // On cache à nouveau le champ rôle
                    toggleButton.textContent = "Pas encore de compte ? Inscrivez-vous"; 
                    submitButton.textContent = "Se connecter"; 
                }
            })
            .catch(err => {
                console.error(err);
                messageDiv.innerHTML = "<p>Erreur serveur lors de l'inscription.</p>";
                messageDiv.style.color = "red";
            }); 
        }
    });
}

