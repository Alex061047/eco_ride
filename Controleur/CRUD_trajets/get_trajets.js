// On récupére le rôle utilisateur pour gérer l'affichage des onglets
fetch("../../Modele/CRUD_vehicule/get_user.php")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            const role = data.user.role;
            if (role === "passager") {
                // Masquer l'onglet "Mes futurs trajets" pour les passagers
                document.getElementById("futurs-tab").style.display = "none";
            }
        } else {
            console.error("Erreur lors de la récupération de l'utilisateur :", data.message);
        }
    })
    .catch(error => console.error("Erreur lors de la récupération de l'utilisateur :", error));

// Gestion des trajets
fetch("../../Modele/CRUD_trajets/get_trajets.php")
    .then(response => response.json())
    .then(data => {
        const trajetsContainer = document.getElementById("trajets-en-cours");
        trajetsContainer.innerHTML = "";

        //Ne garder que les trajets "en cours" ou "à venir"
        let trajetsEnCours = data.filter(trajet => trajet.etat !== "terminé" && trajet.etat !== "annulé");


        if (trajetsEnCours.length > 0) {
            trajetsEnCours.forEach(trajet => {
                let buttonHTML = "";
                
                //Vérifier si l'utilisateur est bien le chauffeur
                if (trajet.est_chauffeur) {
                    //Affichage conditionnel des boutons selon l'état du trajet
                    if (trajet.etat === "à venir") {
                        buttonHTML = `<button class="btn btn-primary demarrer-trajet" data-id="${trajet.id}">Démarrer</button>`;
                    } else if (trajet.etat === "en cours") {
                        buttonHTML = `<button class="btn btn-success arriver-trajet" data-id="${trajet.id}">Arrivée</button>`;
                    }
                }

                else if (trajet.est_passager) {
                    // Affichage passager
                    buttonHTML += `<button class="btn btn-danger btn-sm annuler-reservation" data-id="${trajet.id}">Annuler ma réservation</button>`;
                }

                trajetsContainer.innerHTML += `
                    <div class="card mb-3 p-3 shadow-sm border-0 trajet-card" data-id="${trajet.id}">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center">
                                <h5><i class="bi bi-geo-alt-fill text-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
</svg></i> ${trajet.depart} ➝ ${trajet.arrivee} <i class="bi bi-flag-fill text-danger"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-flag-fill" viewBox="0 0 16 16">
  <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001"/>
</svg></i></h5>
                                <p class="text-muted">Jour: ${trajet.jour} | Heure: ${trajet.heure} | Durée: ${trajet.duree}</p>
                            </div>
                            
                            <div class="col-md-4 text-center">
                                <p><strong>Places restantes :</strong> ${trajet.nb_places_restantes}</p>
                                <p><strong>Prix :</strong> ${trajet.prix}€</p>
                                <p><strong>Véhicule :</strong> ${trajet.marque} ${trajet.modele}</p>
                            </div>

                            <div class="col-md-4 text-center">
                            ${buttonHTML}
                             ${trajet.est_chauffeur ? `<button class="btn btn-danger btn-lg annuler-trajet" data-id="${trajet.id}">Annuler</button>` : ""}
                            </div>
                        </div>
                    </div>
                `;
            });

            //Événements pour les boutons "Démarrer"
            document.querySelectorAll(".demarrer-trajet").forEach(button => {
                button.addEventListener("click", function () {
                    const trajetId = this.getAttribute("data-id");
                    mettreAJourEtatTrajet(trajetId, "en cours");
                });
            });

            //Événements pour les boutons "Arrivée"
            document.querySelectorAll(".arriver-trajet").forEach(button => {
                button.addEventListener("click", function () {
                    const trajetId = this.getAttribute("data-id");
                    mettreAJourEtatTrajet(trajetId, "terminé");
                });
            });

            // Événements pour les boutons "Annuler ma réservation"
            document.querySelectorAll(".annuler-reservation").forEach(button => {
                 button.addEventListener("click", function () {
                     const trajetId = this.getAttribute("data-id");
                     annulerReservation(trajetId);
                 });
            });


        } else {
            trajetsContainer.innerHTML = "<div class='alert alert-warning'>Aucun trajet en cours.</div>";
        }
    
    

    //Événements pour les boutons "Annuler"
document.querySelectorAll(".annuler-trajet").forEach(button => {
    button.addEventListener("click", function () {
        const trajetId = this.getAttribute("data-id");
        annulerTrajet(trajetId);
    });
});
})
.catch(error => console.error("Erreur lors du chargement des trajets :", error));

//Fonction pour annuler un trajet
function annulerTrajet(trajetId) {
    if (confirm("Voulez-vous vraiment annuler ce trajet ?")) {
        fetch("../../Modele/CRUD_trajets/update_trajet.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: trajetId, etat: "annulé" })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);

            //Supprimer le trajet annulé de l'affichage
            document.querySelector(`.trajet-card[data-id="${trajetId}"]`).remove();
        })
        .catch(error => console.error("Erreur lors de l'annulation du trajet :", error));
    }
}

//Fonction pour mettre à jour l'état du trajet
function mettreAJourEtatTrajet(trajetId, nouvelEtat) {
    fetch("../../Modele/CRUD_trajets/update_trajet.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: trajetId, etat: nouvelEtat })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);

        if (nouvelEtat === "terminé") {
            //Supprimer le trajet de l'affichage
            document.querySelector(`.trajet-card[data-id="${trajetId}"]`).remove();
        }
        location.reload();
    })
    .catch(error => console.error("Erreur lors de la mise à jour du trajet :", error));
}


// Événement pour le bouton "Annuler ma réservation"
document.querySelectorAll(".annuler-reservation").forEach(button => {
    button.addEventListener("click", function () {
        const trajetId = this.getAttribute("data-id");
        if (confirm("Voulez-vous vraiment annuler votre réservation ?")) {
            fetch("../../Modele/CRUD_trajets/annuler_reservation.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ covoiturage_id: trajetId })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                document.querySelector(`.trajet-card[data-id="${trajetId}"]`).remove();
            })
            .catch(error => console.error("Erreur lors de l'annulation de la réservation :", error));
        }
    });
});


// Fonction pour annuler une réservation en tant que passager
function annulerReservation(trajetId) {
    if (confirm("Voulez-vous vraiment annuler votre réservation ?")) {
        fetch("../../Modele/CRUD_trajets/annuler_reservation.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ covoiturage_id: trajetId })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => console.error("Erreur lors de l'annulation de la réservation :", error));
    }
}

