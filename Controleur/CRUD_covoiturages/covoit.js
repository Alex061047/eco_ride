let covoituragesOriginaux = []; // Stockage de tous les covoiturages de la BDD

// Charger les covoiturages
function chargerCovoiturages() {
    fetch("../../Controleur_b/CRUD_covoiturages/get_covoiturages_controller.php")
        .then(response => response.json())
        .then(data => {
            covoituragesOriginaux = data;
            afficherCovoiturages(covoituragesOriginaux);
        })
        .catch(error => console.error("Erreur lors du chargement des covoiturages :", error));
}

// Appliquer les filtres
function appliquerFiltres(filtresPersonnalises = {}) {
    let covoituragesFiltres = [...covoituragesOriginaux];

    const villeDepart = filtresPersonnalises.depart !== undefined ? filtresPersonnalises.depart : document.getElementById('depart').value.trim();
    const villeArrivee = filtresPersonnalises.arrivee !== undefined ? filtresPersonnalises.arrivee : document.getElementById('arrivee').value.trim();
    const dateRecherchee = filtresPersonnalises.jour !== undefined ? filtresPersonnalises.jour : document.getElementById('jour').value;
    const mention = filtresPersonnalises.mention !== undefined ? filtresPersonnalises.mention : document.getElementById('mention').checked;
    const prixMax = filtresPersonnalises.prix !== undefined ? filtresPersonnalises.prix : parseFloat(document.getElementById('prix').value);
    const dureeMax = filtresPersonnalises.duree !== undefined ? filtresPersonnalises.duree : document.getElementById('duree').value;
    const animaux = filtresPersonnalises.animaux !== undefined ? filtresPersonnalises.animaux : document.getElementById('animaux').value;
    const noteMin = filtresPersonnalises.note !== undefined ? filtresPersonnalises.note : parseInt(document.getElementById('note').value, 10);

    if (villeDepart) {
        covoituragesFiltres = covoituragesFiltres.filter(trajet =>
            trajet.depart.toLowerCase().includes(villeDepart.toLowerCase())
        );
    }

    if (villeArrivee) {
        covoituragesFiltres = covoituragesFiltres.filter(trajet =>
            trajet.arrivee.toLowerCase().includes(villeArrivee.toLowerCase())
        );
    }

    if (dateRecherchee) {
        const jourRecherche = new Date(dateRecherchee).toISOString().split('T')[0];
        covoituragesFiltres = covoituragesFiltres.filter(trajet =>
            trajet.jour.startsWith(jourRecherche)
        );
    }

    // Filtre mention écologique
    if (mention) {
        covoituragesFiltres = covoituragesFiltres.filter(trajet => trajet.energie === "electrique");
    }

    if (!isNaN(prixMax)) {
        covoituragesFiltres = covoituragesFiltres.filter(trajet => trajet.prix <= prixMax);
    }

    if (dureeMax) {
        const [hMax, mMax] = dureeMax.split(':').map(Number);
        const dureeMaxMinutes = hMax * 60 + mMax;

        covoituragesFiltres = covoituragesFiltres.filter(trajet => {
            const [h, m] = trajet.duree.split(':').map(Number);
            return h * 60 + m <= dureeMaxMinutes;
        });
    }

    if (animaux && animaux !== "null") {
        covoituragesFiltres = covoituragesFiltres.filter(trajet => {
            if (animaux === "oui") return trajet.animaux == 1;
            if (animaux === "non") return trajet.animaux != 1;
            return true;
        });
    }

    if (!isNaN(noteMin)) {
        covoituragesFiltres = covoituragesFiltres.filter(trajet => trajet.note && trajet.note >= noteMin);
    }

    afficherCovoiturages(covoituragesFiltres);
}

// Affichage des covoiturages
function afficherCovoiturages(covoiturages) {
    const container = document.getElementById("liste-covoiturages");
    container.innerHTML = "";

    if (covoiturages.length === 0) {
        const dejaRelance = document.getElementById("liste-covoiturages").dataset.relance === "true";

        if (dejaRelance) {
            container.innerHTML = `
                <div class='alert alert-warning text-center'>
                    <h6>Désolé, aucun trajet n'est actuellement proposé avec ces critères.</h6>
                </div>`;
        } else {
            container.innerHTML = `<div class='d-flex flex-column alert alert-info bg-secondary text-center justify-content-center'>
                <h6>Aucun covoiturage disponible actuellement.</h6>
                <button id="btn-date-proche" class="btn btn-primary mt-2">
                    Rechercher les dates les plus proches pour cet itinéraire
                </button>
            </div>`;

            document.getElementById("btn-date-proche").addEventListener("click", () => {
                const dateInput = document.getElementById("jour");
                if (dateInput) dateInput.value = "";

                container.dataset.relance = "true";

                const depart = document.getElementById("depart").value.trim();
                const arrivee = document.getElementById("arrivee").value.trim();
                const prix = parseFloat(document.getElementById("prix").value);
                const duree = document.getElementById("duree").value;
                const animaux = document.getElementById("animaux").value;
                const note = parseInt(document.getElementById("note").value, 10);
                const mention = document.getElementById("mention").checked;

                appliquerFiltres({
                    depart,
                    arrivee,
                    jour: "",
                    prix,
                    duree,
                    animaux,
                    note,
                    mention
                });
            });
        }
        return;
    }

    // Supprimer les trajets antérieurs à aujourd'hui
    const aujourdHui = new Date();
    covoiturages = covoiturages.filter(trajet => {
        const [jour, mois, annee] = trajet.jour.split('/').map(Number);
        const dateTrajet = new Date(annee, mois - 1, jour);
        const dateRef = new Date(aujourdHui.getFullYear(), aujourdHui.getMonth(), aujourdHui.getDate());
        return dateTrajet >= dateRef;
    });

    covoiturages.forEach(trajet => {
        if (trajet.nb_places_restantes === 0) return;

        const ecologique = (trajet.energie === "electrique") ? "Oui" : "Non";

        const photo = trajet.photo_profil && trajet.photo_profil.trim() !== ''
            ? `../../uploads/photos_utilisateurs/${trajet.photo_profil}`
            : '../../uploads/photos_utilisateurs/default.jpg';

        const etoile = genererEtoilesHTML(trajet.note);

        const card = document.createElement("div");
        card.className = "card rounded col-md-12 p-3 mb-3";

        card.innerHTML = `
            <div class="row text-center">
                <div class="col-2">
                    <h5 class="fw-light"><u>Pseudo</u></h5>
                    <img class="img-fluid profil rounded-circle" src="${photo}" alt="Photo de profil">
                    <div class="mt-1" id="note-${trajet.id}">${etoile}</div>
                </div>
                <div class="col-2">
                    <h5 class="fw-light"><u>Trajet</u></h5>
                    <p class="mt-5">${trajet.depart} <br>→<br> ${trajet.arrivee}</p>
                </div>
                <div class="col-1">
                    <h5 class="fw-light"><u>Places</u></h5>
                    <p class="mt-5">${trajet.nb_places_restantes}</p>
                </div>
                <div class="col-1">
                    <h5 class="fw-light"><u>Prix</u></h5>
                    <p class="mt-5">${trajet.prix}</p>
                </div>
                <div class="col-3">
                    <h5 class="fw-light"><u>Jour | Heure | Durée</u></h5>
                    <p class="mt-5">${trajet.jour} | ${trajet.heure} | ${trajet.duree}</p>
                </div>
                <div class="col-2">
                    <h5 class="fw-light"><u>Mention écologique *</u></h5>
                    <p class="mt-5" id="eco-${trajet.id}">${ecologique}</p>
                </div>
                <div class="col-1 d-flex flex-column align-items-center">
                    <button class="btn btn-success mt-2 mb-2" data-bs-toggle="collapse" data-bs-target="#details-${trajet.id}">Détail</button>
                    <button class="btn btn-primary participer-btn" data-id="${trajet.id}">Participer</button>
                </div>
                <div class="d-flex flex-row-reverse">
                    *Un voyage a la mention écologique s'il est effectué avec une voiture électrique.
                </div>
                <div class="collapse mt-3" id="details-${trajet.id}">
                    <div class="card card-body">
                        <div id="details-content-${trajet.id}"></div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(card);

        // Chargement des détails chauffeur et préférences
        chargerDetailsTrajet(trajet.id, trajet.chauffeur_id, trajet.vehicule_id);
    });

    // Ajout des événements sur les boutons "Participer"
    document.querySelectorAll(".participer-btn").forEach(button => {
        button.addEventListener("click", function () {
            const trajetId = this.getAttribute("data-id");
            confirmerParticipation(trajetId);
        });
    });
}

// Gestion des étoiles pour la notation
function genererEtoilesHTML(note) {
    if (!note || note <= 0) return 'Aucune note disponible';

    return Array.from({ length: Math.floor(note) }, () => `
        <svg xmlns="http://www.w3.org/2000/svg" class="star inline">
            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
        </svg>`).join('');
}

// Details, préférences et avis du chauffeur
async function chargerDetailsTrajet(trajetId, chauffeurId, vehiculeId) {
    try {
        const response = await fetch(`../../Controleur_b/CRUD_covoiturages/get_chauffeur_details_controller.php?chauffeur_id=${chauffeurId}&vehicule_id=${vehiculeId}`);
        const data = await response.json();

        if (data.status === 'success') {
            const { utilisateur, vehicule, preferences, avis_valides } = data;

            let html = `
                <p><strong>Chauffeur :</strong> ${utilisateur.pseudo || 'Non précisé'}</p>
                <p><strong>Véhicule :</strong> ${vehicule.marque || 'N/A'} ${vehicule.modele || ''} (${vehicule.energie || 'N/A'})</p>
                <p><strong>Fumeurs acceptés :</strong> ${preferences.fumeur ? 'Oui' : 'Non'}</p>
                <p><strong>Animaux acceptés :</strong> ${preferences.animaux ? 'Oui' : 'Non'}</p>
                <p><strong>Discussion :</strong> ${preferences.discussion ? 'Oui' : 'Non'}</p>
                <p><strong>Musique :</strong> ${preferences.musique ? 'Oui' : 'Non'}</p>
                <p><strong>Autres préférences :</strong> ${preferences.autre || 'Aucune'}</p>
            `;

            html += `<p><strong><u>Avis :</u></strong></p>`;
            if (Array.isArray(avis_valides) && avis_valides.length > 0) {
                avis_valides.forEach(avis => {
                    html += `<p><em> "${avis.commentaire}" (${avis.date_envoi})</em></p>`;
                });
            } else {
                html += `<p>Aucun avis disponible.</p>`;
            }

            document.getElementById(`details-content-${trajetId}`).innerHTML = html;
        } else {
            document.getElementById(`details-content-${trajetId}`).innerHTML = `<p>Erreur : ${data.message}</p>`;
        }
    } catch (error) {
        console.error("Erreur lors du chargement des détails du trajet :", error);
        document.getElementById(`details-content-${trajetId}`).innerHTML = "<p>Impossible de charger les détails.</p>";
    }
}

// Participer à un trajet
function confirmerParticipation(trajetId) {
    if (confirm("Confirmez-vous vouloir utiliser vos crédits pour participer à ce trajet ?")) {
        if (confirm("Êtes-vous absolument certain de vouloir réserver ? (Action irréversible)")) {
            const nbPlaces = prompt("Combien de places souhaitez-vous réserver ?", "1");
            const nbPlacesInt = parseInt(nbPlaces, 10);
            if (isNaN(nbPlacesInt) || nbPlacesInt <= 0) {
                alert("Nombre de places invalide.");
                return;
            }
            participerAuTrajet(trajetId, nbPlacesInt);
        }
    }
}

// Fonction pour envoyer la réservation au serveur
function participerAuTrajet(trajetId, nbPlaces) {
    fetch("../../Controleur_b/CRUD_covoiturages/participer_trajet_controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ covoiturage_id: trajetId, nb_places: nbPlaces })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "not_connected") {
                alert("Veuillez vous connecter ou vous inscrire pour pouvoir effectuer une réservation.");
                window.location.href = "/Connexion";
                return;
            }

            alert(data.message);
            if (data.status === "success") location.reload();
        })
        .catch(error => console.error("Erreur lors de la participation au trajet :", error));
}

// Événements filtres
['mention', 'prix', 'duree', 'animaux', 'note'].forEach(id => {
    document.getElementById(id).addEventListener('input', appliquerFiltres);
    document.getElementById(id).addEventListener('change', appliquerFiltres);
});

// Gérer la recherche par ville de départ, d'arrivée et jour
document.getElementById('form-recherche').addEventListener('submit', function (e) {
    e.preventDefault();

    const jour = document.getElementById('jour').value;
    const depart = document.getElementById('depart').value.toLowerCase();
    const arrivee = document.getElementById('arrivee').value.toLowerCase();

    let resultats = [...covoituragesOriginaux];

    if (jour) {
        resultats = resultats.filter(trajet => trajet.jour === jour.split('-').reverse().join('/'));
    }

    if (depart) {
        resultats = resultats.filter(trajet => trajet.depart.toLowerCase().includes(depart));
    }

    if (arrivee) {
        resultats = resultats.filter(trajet => trajet.arrivee.toLowerCase().includes(arrivee));
    }

    afficherCovoiturages(resultats);
});

// Chargement des covoiturages au démarrage de la page
chargerCovoiturages();
