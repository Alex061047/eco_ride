fetch("../../Modele/CRUD_trajets/get_trajets.php")
    .then(response => {
        return response.json();
    })
    .then(data => {
        const trajetsContainer = document.getElementById("trajets-en-cours");

        if (data.length > 0) {
            trajetsContainer.innerHTML = "";
            data.forEach(trajet => {
                const trajetDiv = document.createElement("div");
                trajetDiv.classList.add("card", "mb-3", "p-3");
                trajetDiv.innerHTML = `
                    <h5>${trajet.depart} ➝ ${trajet.arrivee}</h5>
                    <p>Jour: ${trajet.jour} | Heure: ${trajet.heure} | Durée: ${trajet.duree}</p>
                    <button class="btn btn-danger annuler-btn" data-id="${trajet.id}">Annuler</button>
                `;
                trajetsContainer.appendChild(trajetDiv);
            });

            // Ajouter des événements sur les boutons "Annuler"
            document.querySelectorAll(".annuler-btn").forEach(button => {
                button.addEventListener("click", function() {
                    const trajetId = this.getAttribute("data-id");
                    annulerTrajet(trajetId);
                });
            });

        } else {
            trajetsContainer.innerHTML = "<div class='alert alert-warning'>Aucun trajet en cours.</div>";
        }
    })
    .catch(error => console.error("Erreur lors du chargement des trajets :", error));

// Fonction pour annuler un trajet
function annulerTrajet(trajetId) {
    if (confirm("Voulez-vous vraiment annuler ce trajet ?")) {
        fetch("../../Modele/CRUD_trajets/delete_trajet.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: trajetId })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => console.error("Erreur lors de l'annulation :", error));
    }
}
