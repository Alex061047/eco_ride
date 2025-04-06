// Attache le gestionnaire d'événements pour le formulaire
attachFormHandler();

function attachFormHandler() {
    // Sélection des éléments HTML du formulaire et autres éléments associés
    const form = document.getElementById('form'); 
    const toggleButton = document.getElementById('toggle-button'); // Le bouton pour basculer entre inscription et connexion
    const formTitle = document.getElementById('form-title'); 
    const pseudoField = document.getElementById('pseudo-field'); // Le champ pseudo (visible uniquement en inscription)
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
            toggleButton.textContent = "Déjà un compte ? Connectez-vous"; 
            submitButton.textContent = "S'inscrire"; 
        } else {
            // Si on est en mode "inscription", on repasse en mode "connexion"
            mode = "connexion"; // On change le mode
            formTitle.textContent = "Connexion"; 
            pseudoField.style.display = "none"; // Le champ pseudo est masqué
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
            fetch('../../Modele/CRUD_utilisateur/connexion.php', {
                method: 'POST', // Méthode POST pour envoyer les données
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, mot_de_passe }) // Envoi des données sous forme de chaîne JSON
            })
            .then(res => res.json()) 
            .then(data => {
                // Affichage du message de retour
                messageDiv.innerHTML = `<p>${data.message}</p>`;
                // Changement de couleur selon le statut (succès ou erreur)
                messageDiv.style.color = data.status === "success" ? "green" : "red";

                // Si la connexion réussit, on effectue une redirection (stockage du token à rajouter plus tard)
                if (data.status === "success") {
                    window.location.href = "/EspaceUtilisateur"; // Redirection vers Mon Espace
                }
            })
            .catch(err => console.error(err)); 
        } else {
            // Si on est en mode "inscription"
            const pseudo = form.querySelector('[name="pseudo"]').value;
            const role = "passager"; // Rôle par défaut

            // Envoi de la requête pour l'inscription
            fetch('../../Modele/CRUD_utilisateur/inscription.php', {
                method: 'POST', // Méthode POST pour envoyer les données
                headers: { "Content-Type": "application/json" }, 
                body: JSON.stringify({ pseudo, email, mot_de_passe, role }) // Envoi des données sous forme de chaîne JSON
            })
            .then(res => res.json()) 
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
                    toggleButton.textContent = "Pas encore de compte ? Inscrivez-vous"; 
                    submitButton.textContent = "Se connecter"; 
                }
            })
            .catch(err => console.error(err)); 
        }
    });
}
