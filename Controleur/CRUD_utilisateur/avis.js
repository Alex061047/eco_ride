// Récupération du token présent dans l'URL
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get("token");

// Variables de contrôle
let dejaVote = false; 
let userId = null;
let trajetId = null;


// Vérification de l'autorisation via le token

fetch(`../../Controleur_b/CRUD_utilisateur/avis_auth_controller.php?token=${token}`)
    .then(res => res.json())
    .then(data => {
        const resultDiv = document.getElementById("resultat");

        // Cas où le token est invalide ou déjà utilisé
        if (data.status === "forbidden" || data.status === "error" || data.status === "invalid_token") {
            resultDiv.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            masquerEtDesactiverFormulaire();
        } 
        // Cas où l'utilisateur a déjà voté
        else if (data.status === "already") {
            resultDiv.innerHTML = `<div class='alert alert-info'>${data.message}</div>`;
            dejaVote = true;
            masquerEtDesactiverFormulaire();
        } 
        // Cas où le token est valide et l'utilisateur peut voter
        else if (data.status === "authorized") {
            
            userId = data.user_id;
            trajetId = data.trajet_id;

            // Pré-remplissage des champs cachés du formulaire
            document.getElementById("user_id").value = userId;
            document.getElementById("trajet_id").value = trajetId;
            document.getElementById("token").value = token;
        }
    })
    .catch(err => {
        console.error("Erreur auth avis:", err);
        document.getElementById("resultat").innerHTML = `<div class='alert alert-danger'>Erreur de vérification.</div>`;
        masquerEtDesactiverFormulaire();
    });

// Fonction pour désactiver et masquer le formulaire 
function masquerEtDesactiverFormulaire() {
    const form = document.getElementById("form-avis");
    form.style.display = "none";
    form.querySelectorAll("input, textarea, button").forEach(el => el.disabled = true);
}


// Système de notation par étoiles
const starsContainer = document.getElementById("etoile");
const noteInput = document.getElementById("note");

const starSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" class="star" viewBox="0 0 16 16" width="24" height="24" fill="gray">
        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
    </svg>
`;

// Création de 5 étoiles
for (let i = 1; i <= 5; i++) {
    const span = document.createElement("span");
    span.classList.add("etoile");
    span.setAttribute("data-value", i);
    span.innerHTML = starSVG;
    starsContainer.appendChild(span);
}

// Enregistre la note et applique le style "checked"
const etoiles = starsContainer.querySelectorAll(".etoile");
etoiles.forEach(etoile => {
    etoile.addEventListener("click", () => {
        const valeur = etoile.getAttribute("data-value");
        noteInput.value = valeur;

        // Mise à jour visuelle des étoiles sélectionnées
        etoiles.forEach(e => {
            const eVal = e.getAttribute("data-value");
            e.classList.toggle("checked", eVal <= valeur);
        });
    });
});


// Envoi du formulaire
document.getElementById("form-avis").addEventListener("submit", function (e) {
    e.preventDefault();

    // Si l'utilisateur a déjà voté
    if (dejaVote) {
        document.getElementById("resultat").innerHTML = "<div class='alert alert-warning'>Vous avez déjà soumis un avis pour ce trajet.</div>";
        masquerEtDesactiverFormulaire();
        return;
    }

    const formData = new FormData(this);
    const objet = Object.fromEntries(formData.entries());

    // Vérifier que user_id et trajet_id ont bien été chargés
    if (!objet.user_id || !objet.trajet_id || !objet.token) {
        document.getElementById("resultat").innerHTML = "<div class='alert alert-danger'>Erreur de vérification du lien.</div>";
        masquerEtDesactiverFormulaire();
        return;
    }

    // Conversion des valeurs numériques
    objet.user_id = parseInt(objet.user_id, 10);
    objet.trajet_id = parseInt(objet.trajet_id, 10);
    objet.note = parseInt(objet.note, 10);

 

    // Envoi des données au serveur en JSON
    fetch("../../Controleur_b/CRUD_utilisateur/submit_avis_controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(objet)
    })
    .then(res => res.json())
    .then(data => {
        const div = document.getElementById("resultat");
        // Si réussi: message + désactivation du formulaire
        if (data.status === "success") {
            div.innerHTML = "<div class='alert alert-success'>Merci pour votre retour !</div>";
            dejaVote = true;
            this.reset();
            masquerEtDesactiverFormulaire();
        } else {
            // Sinon: erreur retournée par le serveur
            div.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error("Erreur lors de l'envoi de l'avis :", error);
        document.getElementById("resultat").innerHTML = "<div class='alert alert-danger'>Erreur lors de l'envoi de l'avis.</div>";
    });
});

