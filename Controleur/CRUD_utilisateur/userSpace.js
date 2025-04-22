// Variables globales pour la gestion des véhicules
let vehiculeList = [];
let currentVehiculeIndex = 0;

// Récupération des informations utilisateur et véhicules
fetch("../../Modele/CRUD_vehicule/get_user.php")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            const user = data.user;
            const preferences = data.preferences || {};

            // Affichage des infos utilisateur
            document.getElementById("user-firstname").textContent = user.pseudo;
            document.getElementById("user-email").textContent = user.email;
            document.getElementById("user-role").textContent = user.role;
            document.getElementById("user-credits").textContent = user.credit;

            const vehicleSection = document.getElementById("vehicle-card");

            // Si l'utilisateur n'est pas chauffeur, on cache
            if (user.role !== "chauffeur" && user.role !== "passager-chauffeur") {
                vehicleSection.style.display = "none";
                return;
            }

            // On stocke les véhicules
            vehiculeList = Array.isArray(data.vehicules) ? data.vehicules : [];

if (vehiculeList.length > 1) {
    document.getElementById("vehicule-next-btn").style.display = "inline-block";
}

if (vehiculeList.length > 0) {
    afficherVehicule(0);
} else {
    document.getElementById("vehicle-card").innerHTML += `
        <div class="alert alert-warning mt-3">Aucun véhicule enregistré.</div>`;
}


            // Préférences utilisateur (non liées à un véhicule)
            const boolToText = (val) => {
                if (val === null || val === undefined) return "Non renseigné";
                return val == 1 ? "Oui" : "Non";
            };

            document.getElementById("vehicle-pref-fumeur").textContent = boolToText(preferences.fumeur);
            document.getElementById("vehicle-pref-animaux").textContent = boolToText(preferences.animaux);
            document.getElementById("vehicle-pref-discussions").textContent = boolToText(preferences.discussions);
            document.getElementById("vehicle-pref-musique").textContent = boolToText(preferences.musique);
            document.getElementById("vehicle-pref-autre").textContent = preferences.autre || "Non renseigné";
        } else {
            alert(data.message || "Erreur lors du chargement des données utilisateur");
        }
    })
    .catch(error => {
        console.error("Erreur lors de la récupération des données :", error);
    });


    // Fonction pour convertir une date "YYYY-MM-DD" en "DD/MM/YYYY"
function formatDateFR(dateStr) {
    if (!dateStr || !dateStr.includes("-")) return dateStr; // sécurité
    const [year, month, day] = dateStr.split("-");
    return `${day}/${month}/${year}`;
}

// Fonction pour afficher les véhicules
function afficherVehicule(index) {
    const vehicule = vehiculeList[index];
    if (!vehicule) return;

    document.getElementById("vehicle-plate").textContent = vehicule.immatriculation;
    document.getElementById("vehicle-energy").textContent = vehicule.energie;
    document.getElementById("vehicle-model").textContent = vehicule.modele;
    document.getElementById("vehicle-color").textContent = vehicule.couleur;
    document.getElementById("vehicle-brand").textContent = vehicule.marque;
    document.getElementById("vehicle-seats").textContent = vehicule.nb_places;
    document.getElementById("vehicle-date").textContent = vehicule.date_immatriculation
        ? formatDateFR(vehicule.date_immatriculation)
        : "";

    sessionStorage.setItem("vehiculeId", vehicule.id);
}

// Modification des champs utilisateur
function editField(field) {
    const fieldMap = {
        prenom: {
            label: "Nouveau pseudo",
            key: "pseudo",
            elementId: "user-firstname"
        },
        email: {
            label: "Nouvel email",
            key: "email",
            elementId: "user-email"
        },
        role: {
            label: "Nouveau rôle",
            key: "role",
            elementId: "user-role"
        }
    };

    const fieldConfig = fieldMap[field];
    if (!fieldConfig) return;

    const userId = sessionStorage.getItem("userId");

    if (field === "role") {
        // Affichage d’un dropdown pour le changement de rôle
        const dropdown = document.createElement("select");
        dropdown.innerHTML = `
            <option value="passager">Passager</option>
            <option value="chauffeur">Chauffeur</option>
            <option value="passager-chauffeur">Passager-Chauffeur</option>
        `;
        dropdown.classList.add("form-select", "mt-2");

        const confirmBtn = document.createElement("button");
        confirmBtn.textContent = "Confirmer";
        confirmBtn.classList.add("btn", "btn-success", "btn-sm", "ms-2", "mt-2");

        const wrapper = document.createElement("div");
        wrapper.classList.add("text-end");
        wrapper.appendChild(dropdown);
        wrapper.appendChild(confirmBtn);

        const parent = document.getElementById(fieldConfig.elementId).parentElement;
        parent.appendChild(wrapper);

        confirmBtn.addEventListener("click", () => {
            const selectedRole = dropdown.value;
            fetch("../../Modele/CRUD_utilisateur/update_utilisateur.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id: userId,
                    role: selectedRole
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById(fieldConfig.elementId).textContent = selectedRole;
                    alert("Rôle modifié avec succès !");
                    wrapper.remove();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error("Erreur lors de la mise à jour du rôle :", err));
        });

    } 
    // Prompt classique pour les autres champs
    else {
        const newValue = prompt(fieldConfig.label);
        if (!newValue) return;

        fetch("../../Modele/CRUD_utilisateur/update_utilisateur.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: userId,
                [fieldConfig.key]: newValue
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                document.getElementById(fieldConfig.elementId).textContent = newValue;
                alert("Modification réussie !");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error("Erreur de mise à jour :", err));
    }
}


// Modification du mot de passe
function editPassword() {
    const newPassword = prompt("Entrez votre nouveau mot de passe :");
    if (!newPassword) return;

    const userId = sessionStorage.getItem("userId");

    fetch("../../Modele/CRUD_utilisateur/update_utilisateur.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: userId,
            mot_de_passe: newPassword
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(err => console.error("Erreur de mise à jour du mot de passe :", err));
}

// Fonction pour modifier un véhicule
function editVehicle(field) {
     // Configuration des champs disponibles
    const fieldConfigMap = {
        immatriculation:       { label: "Nouvelle plaque d’immatriculation", id: "vehicle-plate" },
        modele:                { label: "Nouveau modèle", id: "vehicle-model" },
        couleur:               { label: "Nouvelle couleur", id: "vehicle-color" },
        marque:                { label: "Nouvelle marque", id: "vehicle-brand" },
        nb_places:             { label: "Nouveau nombre de places", id: "vehicle-seats" },
        energie:               { label: "Type d’énergie", id: "vehicle-energy" },
        date_immatriculation:  { label: "Nouvelle date d'immatriculation", id: "vehicle-date" }
    };

    const config = fieldConfigMap[field];
    if (!config) return;

    const fieldElement = document.getElementById(config.id);
    if (!fieldElement) {
        console.error(`Élément avec id="${config.id}" introuvable.`);
        return;
    }

    const parent = fieldElement.parentElement;

    const userId = sessionStorage.getItem("userId");
    const vehiculeId = sessionStorage.getItem("vehiculeId");
    if (!vehiculeId) {
        alert("Aucun véhicule à modifier.");
        return;
    }

    // Energie sous forme de dropdown
    if (field === "energie") {
        const dropdown = document.createElement("select");
        dropdown.innerHTML = `
            <option value="diesel">Diesel</option>
            <option value="essence">Essence</option>
            <option value="electrique">Électrique</option>
            <option value="hybride">Hybride</option>
        `;
        dropdown.classList.add("form-select", "mt-2");

        const confirmBtn = document.createElement("button");
        confirmBtn.textContent = "Confirmer";
        confirmBtn.classList.add("btn", "btn-success", "btn-sm", "ms-2", "mt-2");

        const wrapper = document.createElement("div");
        wrapper.classList.add("text-end");
        wrapper.appendChild(dropdown);
        wrapper.appendChild(confirmBtn);
        parent.appendChild(wrapper);

        confirmBtn.addEventListener("click", () => {
            const newValue = dropdown.value;

            fetch("../../Modele/CRUD_vehicule/update_vehicule.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id: vehiculeId,
                    utilisateur_id: userId,
                    energie: newValue
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    fieldElement.textContent = newValue;
                    alert("Énergie mise à jour !");
                    wrapper.remove();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error("Erreur énergie :", err));
        });

    }

    // Date d'immatriculation sous forme d'input type date
    else if (field === "date_immatriculation") {
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = "0";
        overlay.style.left = "0";
        overlay.style.width = "100%";
        overlay.style.height = "100%";
        overlay.style.backgroundColor = "rgba(0,0,0,0.6)";
        overlay.style.zIndex = "9999";

        overlay.innerHTML = `
            <div style="background:white; padding:20px; margin:15% auto; width:300px; border-radius:8px; text-align:center;">
                <label for="new-date"><strong>${config.label} :</strong></label><br>
                <input type="date" id="new-date" class="form-control mt-2"><br><br>
                <button class="btn btn-success btn-sm" id="confirm-date">Confirmer</button>
            </div>
        `;

        document.body.appendChild(overlay);

        document.getElementById("confirm-date").addEventListener("click", () => {
            const newDate = document.getElementById("new-date").value;
            if (!newDate) {
                alert("Veuillez sélectionner une date.");
                return;
            }

            fetch("../../Modele/CRUD_vehicule/update_vehicule.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id: vehiculeId,
                    utilisateur_id: userId,
                    date_immatriculation: newDate
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    fieldElement.textContent = formatDateFR(newDate); 
                    alert("Date mise à jour !");
                    document.body.removeChild(overlay);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error("Erreur date immatriculation :", err));
        });
    }

    // Tous les autres champs sous forme de prompt de texte simple
    else {
        const newValue = prompt(config.label);
        if (!newValue) return;

        fetch("../../Modele/CRUD_vehicule/update_vehicule.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: vehiculeId,
                utilisateur_id: userId,
                [field]: newValue
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                fieldElement.textContent = newValue;
                alert("Véhicule mis à jour !");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error("Erreur de mise à jour du véhicule :", err));
    }
}

// Fonction pour modifier les préférences utilisateur
function editPreference(pref) {
    const labelMap = {
        fumeur: "Acceptez-vous les fumeurs ?",
        animaux: "Acceptez-vous les animaux ?",
        discussions: "Souhaitez-vous discuter pendant le trajet ?",
        musique: "Souhaitez-vous écouter de la musique ?",
        autre: "Autre préférence :"
    };

    const label = labelMap[pref];
    if (!label) return;

    const userId = sessionStorage.getItem("userId");
    const fieldElement = document.getElementById(`vehicle-pref-${pref}`);
    if (!fieldElement) {
        console.error(`Élément #vehicle-pref-${pref} introuvable`);
        return;
    }

    const parent = fieldElement.parentElement;

    // Pour "autre", on garde un prompt
    if (pref === "autre") {
        const newValue = prompt(label);
        if (!newValue) return;
        return updatePreference(pref, newValue, fieldElement, userId);
    }

    // Pour les autres, on crée un dropdown (oui / non)
    const dropdown = document.createElement("select");
    dropdown.innerHTML = `
        <option value="oui">Oui</option>
        <option value="non">Non</option>
    `;
    dropdown.classList.add("form-select", "mt-2");

    const confirmBtn = document.createElement("button");
    confirmBtn.textContent = "Confirmer";
    confirmBtn.classList.add("btn", "btn-success", "btn-sm", "ms-2", "mt-2");

    const wrapper = document.createElement("div");
    wrapper.classList.add("text-end");
    wrapper.appendChild(dropdown);
    wrapper.appendChild(confirmBtn);
    parent.appendChild(wrapper);

    confirmBtn.addEventListener("click", () => {
        const newValue = dropdown.value;
        updatePreference(pref, newValue, fieldElement, userId);
        wrapper.remove();
    });
}

// Fonction de mise à jour des préférences
function updatePreference(field, value, fieldElement, userId) {
    fetch("../../Modele/CRUD_vehicule/update_preference.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            utilisateur_id: userId,
            [field]: value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            fieldElement.textContent = value;
            alert("Préférence mise à jour !");
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error("Erreur mise à jour préférence :", err));
}



// Fonction pour ajouter un nouveau véhicule à l'utilisateur
function addVehicle() {
    const utilisateur_id = sessionStorage.getItem("userId");

    // Prompts initiaux (saisie libre)
    const marque = prompt("Marque du véhicule :");
    if (!marque) return;

    const modele = prompt("Modèle du véhicule :");
    if (!modele) return;

    const immatriculation = prompt("Plaque d'immatriculation :");
    if (!immatriculation) return;

    const couleur = prompt("Couleur :");
    if (!couleur) return;

    const nb_places = prompt("Nombre de places :");
    if (!nb_places || isNaN(nb_places)) {
        alert("Nombre de places invalide !");
        return;
    }

    //Sélecteur dropdown pour l’énergie
    const energieWrapper = document.createElement("div");
    energieWrapper.style.position = "fixed";
    energieWrapper.style.top = "50%";
    energieWrapper.style.left = "50%";
    energieWrapper.style.transform = "translate(-50%, -50%)";
    energieWrapper.style.background = "#fff";
    energieWrapper.style.padding = "20px";
    energieWrapper.style.border = "1px solid #ccc";
    energieWrapper.style.zIndex = 10000;
    energieWrapper.style.borderRadius = "8px";
    energieWrapper.style.boxShadow = "0 0 10px rgba(0,0,0,0.3)";

    const energieSelect = document.createElement("select");
    ["essence", "diesel", "electrique", "hybride"].forEach(val => {
        const opt = document.createElement("option");
        opt.value = val;
        opt.textContent = val.charAt(0).toUpperCase() + val.slice(1);
        energieSelect.appendChild(opt);
    });

    energieWrapper.innerHTML = `<h5>Choisissez le type d'énergie :</h5>`;
    energieWrapper.appendChild(energieSelect);

    const energieBtn = document.createElement("button");
    energieBtn.textContent = "Suivant";
    energieBtn.className = "btn btn-success btn-sm mt-2 ms-2";
    energieWrapper.appendChild(energieBtn);

    document.body.appendChild(energieWrapper);

    energieBtn.onclick = () => {
        const energie = energieSelect.value;
        document.body.removeChild(energieWrapper);

        // Affiche ensuite le sélecteur de date d’immatriculation
        const dateWrapper = document.createElement("div");
        dateWrapper.style.position = "fixed";
        dateWrapper.style.top = "0";
        dateWrapper.style.left = "0";
        dateWrapper.style.width = "100%";
        dateWrapper.style.height = "100%";
        dateWrapper.style.backgroundColor = "rgba(0,0,0,0.6)";
        dateWrapper.style.zIndex = "9999";

        dateWrapper.innerHTML = `
            <div style="background:white; padding:20px; margin:15% auto; width:300px; border-radius:8px; text-align:center;">
                <label for="date-immatriculation"><strong>Date de première immatriculation :</strong></label><br>
                <input type="date" id="date-immatriculation" class="form-control mt-2"><br><br>
                <button id="valider-date" class="btn btn-success btn-sm">Ajouter le véhicule</button>
            </div>
        `;
        document.body.appendChild(dateWrapper);

        // Lors du clic sur "Valider"
        document.getElementById("valider-date").addEventListener("click", () => {
            const date_immatriculation = document.getElementById("date-immatriculation").value;
            if (!date_immatriculation) {
                alert("Merci de renseigner une date.");
                return;
            }

            // Envoi des données vers le serveur
            fetch("../../Modele/CRUD_vehicule/add_vehicule.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    utilisateur_id,
                    marque,
                    modele,
                    immatriculation,
                    couleur,
                    energie,
                    nb_places,
                    date_immatriculation
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    document.body.removeChild(dateWrapper);
                    location.reload();
                }
            })
            .catch(err => console.error("Erreur ajout véhicule :", err));
        });
    };
}


// Fonction pour supprimer un véhicule existant
function deleteVehicle() {
    const utilisateur_id = sessionStorage.getItem("userId");
    const vehicule_id = sessionStorage.getItem("vehiculeId");

    if (!vehicule_id) {
        alert("Aucun véhicule à supprimer.");
        return;
    }

    const confirmation = confirm("Es-tu sûr de vouloir supprimer ce véhicule ?");
    if (!confirmation) return;

    fetch("../../Modele/CRUD_vehicule/delete_vehicule.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: vehicule_id,
            utilisateur_id: utilisateur_id
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            sessionStorage.removeItem("vehiculeId");
            location.reload(); // Recharger la page pour actualiser l'affichage
        }
    })
    .catch(err => console.error("Erreur lors de la suppression du véhicule :", err));
}




// Affichage du bouton Suivant
document.getElementById("vehicule-next-btn").addEventListener("click", () => {
    currentVehiculeIndex = (currentVehiculeIndex + 1) % vehiculeList.length;
    afficherVehicule(currentVehiculeIndex);
});
