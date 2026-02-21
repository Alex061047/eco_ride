// Variables globales pour la gestion des véhicules
let vehiculeList = [];
let currentVehiculeIndex = 0;
let currentUserId = null;

function getCurrentUserId() {
 return currentUserId || sessionStorage.getItem("userId");
}

// Récupération des informations utilisateur et véhicules
fetch("../../Controleur_b/CRUD_vehicule/get_user_controller.php", { cache: "no-store" })
 .then(async response => {
 const text = await response.text();
 let data;
 try {
 data = JSON.parse(text);
 } catch (e) {
 throw new Error("Reponse serveur invalide: " + text.slice(0, 200));
 }

 if (!response.ok) {
 throw new Error(data.message || "Erreur HTTP " + response.status);
 }

 return data;
 })
 .then(data => {
 if (data.status === "success") {
 const user = data.user;
 const preferences = data.preferences || {};
 currentUserId = user.id;
 sessionStorage.setItem("userId", user.id);
 sessionStorage.setItem("userRole", user.role);
 sessionStorage.setItem("userPseudo", user.pseudo || "");

 // Affichage des infos utilisateur
 document.getElementById("user-firstname").textContent = user.pseudo;
 document.getElementById("user-email").textContent = user.email;
 document.getElementById("user-role").textContent = user.role;
 document.getElementById("user-credits").textContent = user.credit;

 // Affichage de la photo de profil
 if (user.photo_profil) {
 document.getElementById("profil-photo").src = `../../uploads/photos_utilisateurs/${user.photo_profil}`;
 }


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
 <div class="alert alert-warning mt-3">Aucun véhicule enregistrA .</div>`;
}


 // Préférences utilisateur 
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
 console.error("Erreur lors de la recuperation des donnees :", error);
 alert("Erreur chargement profil/vehicules : " + (error.message || "serveur"));
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

 const userId = getCurrentUserId();

 if (field === "role") {
 // Affichage d'un dropdown pour le changement de rôle
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
 fetch("../../Controleur_b/CRUD_utilisateur/update_utilisateur_controller.php", {
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
 alert("Rôle modifié avec succés !");
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

 fetch("../../Controleur_b/CRUD_utilisateur/update_utilisateur_controller.php", {
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

 const userId = getCurrentUserId();

 fetch("../../Controleur_b/CRUD_utilisateur/update_utilisateur_controller.php", {
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
 immatriculation: { label: "Nouvelle plaque d'immatriculation", id: "vehicle-plate" },
 modele: { label: "Nouveau modA le", id: "vehicle-model" },
 couleur: { label: "Nouvelle couleur", id: "vehicle-color" },
 marque: { label: "Nouvelle marque", id: "vehicle-brand" },
 nb_places: { label: "Nouveau nombre de places", id: "vehicle-seats" },
 energie: { label: "Type d'A nergie", id: "vehicle-energy" },
 date_immatriculation: { label: "Nouvelle date d'immatriculation", id: "vehicle-date" }
 };

 const config = fieldConfigMap[field];
 if (!config) return;

 const fieldElement = document.getElementById(config.id);
 if (!fieldElement) {
 console.error(`Element avec id="${config.id}" introuvable.`);
 return;
 }

 const parent = fieldElement.parentElement;

 const userId = getCurrentUserId();
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
 <option value="electrique">Electrique</option>
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

 fetch("../../Controleur_b/CRUD_vehicule/update_vehicule_controller.php", {
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
 alert("Energie mise à jour !");
 wrapper.remove();
 } else {
 alert(data.message);
 }
 })
 .catch(err => console.error("Erreur A nergie :", err));
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

 fetch("../../Controleur_b/CRUD_vehicule/update_vehicule_controller.php", {
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

 fetch("../../Controleur_b/CRUD_vehicule/update_vehicule_controller.php", {
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

 const userId = getCurrentUserId();
 const fieldElement = document.getElementById(`vehicle-pref-${pref}`);
 if (!fieldElement) {
 console.error(`Element #vehicle-pref-${pref} introuvable`);
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
 fetch("../../Controleur_b/CRUD_vehicule/update_preference_controller.php", {
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
 .catch(err => console.error("Erreur mise à jour prA fA rence :", err));
}



// Fonction pour ajouter un nouveau véhicule à l'utilisateur
function addVehicle() {
 const utilisateur_id = getCurrentUserId();
 if (!utilisateur_id) {
 alert("Utilisateur non connectA .");
 return;
 }

 const overlay = document.createElement("div");
 overlay.style.position = "fixed";
 overlay.style.top = "0";
 overlay.style.left = "0";
 overlay.style.width = "100%";
 overlay.style.height = "100%";
 overlay.style.backgroundColor = "rgba(0,0,0,0.6)";
 overlay.style.zIndex = "9999";

 overlay.innerHTML = `
 <div style="background:white; padding:20px; margin:5% auto; width:360px; border-radius:8px;">
 <h5 class="mb-3">Ajouter un véhicule</h5>
 <label class="form-label">Marque</label>
 <input type="text" id="add-marque" class="form-control mb-2">

 <label class="form-label">Modèle</label>
 <input type="text" id="add-modele" class="form-control mb-2">

 <label class="form-label">Plaque d'immatriculation</label>
 <input type="text" id="add-immatriculation" class="form-control mb-2">

 <label class="form-label">Couleur</label>
 <input type="text" id="add-couleur" class="form-control mb-2">

 <label class="form-label">Nombre de places</label>
 <input type="number" id="add-places" min="1" class="form-control mb-2">

 <label class="form-label">Energie</label>
 <select id="add-energie" class="form-select mb-2">
 <option value="essence">Essence</option>
 <option value="diesel">Diesel</option>
 <option value="electrique">Electrique</option>
 <option value="hybride">Hybride</option>
 </select>

 <label class="form-label">Date de première immatriculation</label>
 <input type="date" id="add-date" class="form-control mb-3">

 <div class="d-flex justify-content-end gap-2">
 <button id="add-cancel" class="btn btn-secondary btn-sm">Annuler</button>
 <button id="add-submit" class="btn btn-success btn-sm">Ajouter</button>
 </div>
 </div>
 `;

 document.body.appendChild(overlay);

 document.getElementById("add-cancel").addEventListener("click", () => {
 document.body.removeChild(overlay);
 });

 document.getElementById("add-submit").addEventListener("click", () => {
 const marque = document.getElementById("add-marque").value.trim();
 const modele = document.getElementById("add-modele").value.trim();
 const immatriculation = document.getElementById("add-immatriculation").value.trim();
 const couleur = document.getElementById("add-couleur").value.trim();
 const nb_places = parseInt(document.getElementById("add-places").value, 10);
 const energie = document.getElementById("add-energie").value;
 const date_immatriculation = document.getElementById("add-date").value;

 if (!marque || !modele || !immatriculation || !couleur || !date_immatriculation || !nb_places || nb_places <= 0) {
 alert("Merci de remplir correctement tous les champs.");
 return;
 }

 fetch("../../Controleur_b/CRUD_vehicule/add_vehicule_controller.php", {
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
 document.body.removeChild(overlay);
 location.reload();
 }
 })
 .catch(err => {
 console.error("Erreur ajout véhicule :", err);
 alert("Erreur serveur pendant l'ajout du véhicule.");
 });
 });
}


// Fonction pour supprimer un véhicule existant
function deleteVehicle() {
 const utilisateur_id = getCurrentUserId();
 const vehicule_id = sessionStorage.getItem("vehiculeId");

 if (!vehicule_id) {
 alert("Aucun véhicule à supprimer.");
 return;
 }

 const confirmation = confirm("Es-tu sûr de vouloir supprimer ce véhicule ?");
 if (!confirmation) return;

 fetch("../../Controleur_b/CRUD_vehicule/delete_vehicule_controller.php", {
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


// Gestion de la photo de profil
function uploadPhoto() {
 const input = document.getElementById("photo-input");
 const file = input.files[0];
 const userId = getCurrentUserId();

 if (!file) {
 alert("Veuillez choisir une image.");
 return;
 }

 const formData = new FormData();
 formData.append("photo", file);
 formData.append("user_id", userId);

 fetch("../../Controleur_b/CRUD_utilisateur/upload_photo_controller.php", {
 method: "POST",
 body: formData,
 })
 .then(res => res.json())
 .then(data => {
 if (data.status === "success") {
 document.getElementById("profil-photo").src = data.newPath + "?t=" + new Date().getTime(); 
 alert("Photo mise à jour !");
 } else {
 alert("Erreur : " + data.message);
 }
 })
 .catch(err => console.error("Erreur envoi photo :", err));
}






