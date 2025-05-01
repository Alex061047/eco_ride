let covoituragesOriginaux = []; // Stockage de tous les covoiturages de la BDD


// Charger les covoiturages

function chargerCovoiturages() {
    fetch("../../Modele/CRUD_covoiturages/get_covoiturages.php")
        .then(response => response.json())
        .then(data => {
            covoituragesOriginaux = data; // Sauvegarde pour permettre les filtres sans recharger
            afficherCovoiturages(covoituragesOriginaux);
        })
        .catch(error => console.error("Erreur lors du chargement des covoiturages :", error));
}


// Affichage des covoiturages
function afficherCovoiturages(covoiturages) {
    const container = document.getElementById("liste-covoiturages");
    container.innerHTML = "";

    if (covoiturages.length === 0) {
        container.innerHTML = "<div class='alert alert-info'>Aucun covoiturage disponible actuellement.</div>";
        return;
    }

    covoiturages.forEach(trajet => {
        if (trajet.nb_places_restantes === 0) return; // On ignore les trajets complets

        const ecologique = (trajet.energie === "électrique") ? "Oui" : "Non";
        const photo = trajet.photo_profil && trajet.photo_profil.trim() !== ''
            ? `../../uploads/photos_utilisateurs/${trajet.photo_profil}`
            : '../../uploads/photos_utilisateurs/default.jpg';
        const etoile = genererEtoilesHTML(trajet.note); // Affichage de la note sous forme d’étoiles

        const card = document.createElement("div");
        card.className = "card rounded col-md-12 p-3 mb-3";

        // Les trajets injecté en HTML
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
                    <p class="mt-5">${trajet.prix}€</p>
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
                 <div class="collapse mt-3" id="details-${trajet.id}">
                    <div class="card card-body">
                        <div id="details-content-${trajet.id}">
                            
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(card);

         // Chargement des détails chauffeur et préférences
       chargerDetailsTrajet(trajet.id, trajet.chauffeur_id);

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

// Details et préférences du chauffeur
async function chargerDetailsTrajet(trajetId, chauffeurId) {
    try {
        const response = await fetch(`../../Modele/CRUD_covoiturages/get_chauffeur_details.php?chauffeur_id=${chauffeurId}`);
        const data = await response.json();

        if (data.status === 'success') {
            const { utilisateur, vehicule, preferences } = data;

            const html = `
                <p><strong>Chauffeur :</strong> ${utilisateur.pseudo || 'Non précisé'}</p>
                <p><strong>Véhicule :</strong> ${vehicule.marque || 'N/A'} ${vehicule.modele || ''} (${vehicule.energie || 'N/A'})</p>
                <p><strong>Fumeurs acceptés :</strong> ${preferences.fumeur ? 'Oui' : 'Non'}</p>
                <p><strong>Animaux acceptés :</strong> ${preferences.animaux ? 'Oui' : 'Non'}</p>
                <p><strong>Discussion :</strong> ${preferences.discussion ? 'Oui' : 'Non'}</p>
                <p><strong>Musique :</strong> ${preferences.musique ? 'Oui' : 'Non'}</p>
                <p><strong>Autres préférences :</strong> ${preferences.autre || 'Aucune'}</p>
            `;

            document.getElementById(`details-content-${trajetId}`).innerHTML = html;
        } else {
            document.getElementById(`details-content-${trajetId}`).innerHTML = `<p>Erreur : ${data.message}</p>`;
        }
    } catch (error) {
        console.error("Erreur lors du chargement des détails du trajet :", error);
        document.getElementById(`details-content-${trajetId}`).innerHTML = "<p>Impossible de charger les détails.</p>";
    }
}




// Chargement des covoiturages au démarrage de la page
chargerCovoiturages();
