document.addEventListener("DOMContentLoaded", function() {
    fetch("./get_trajets.php")
        .then(response => response.json())
        .then(data => {
            const trajetsContainer = document.getElementById("trajets-en-cours");
            if (data.length > 0) {
                trajetsContainer.innerHTML = "";
                data.forEach(trajet => {
                    trajetsContainer.innerHTML += `
                        <div class="card mb-3 p-3">
                            <h5>${trajet.depart} ➝ ${trajet.arrivee}</h5>
                            <p>Jour: ${trajet.jour} | Heure: ${trajet.heure} | Durée: ${trajet.duree}</p>
                            <button class="btn btn-danger">Annuler</button>
                        </div>
                    `;
                });
            } else {
                trajetsContainer.innerHTML = "<div class='alert alert-warning'>Aucun trajet en cours.</div>";
            }
        })
        .catch(error => console.error("Erreur lors du chargement des trajets :", error));
});