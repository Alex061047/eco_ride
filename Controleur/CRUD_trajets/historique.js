fetch("../../Modele/CRUD_trajets/get_historique.php")
    .then(response => response.json())
    .then(data => {
        const historiqueContainer = document.getElementById("historique-trajets");
        historiqueContainer.innerHTML = "";

        if (data.length > 0) {
            data.forEach(trajet => {
                historiqueContainer.innerHTML += `
                    <div class="card mb-3 p-3 shadow-sm border-0">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center">
                                <h5><i class="bi bi-geo-alt-fill text-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
</svg></i> ${trajet.depart} ➝ ${trajet.arrivee} <i class="bi bi-flag-fill text-danger"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-flag-fill" viewBox="0 0 16 16">
  <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001"/>
</svg></i></h5>
                                <p class="text-muted">Jour: ${trajet.jour} | Heure: ${trajet.heure} | Durée: ${trajet.duree}</p>
                            </div>
                            
                            <div class="col-md-6 text-center">
                                <p><strong>Prix :</strong> ${trajet.prix}€</p>
                                <p><strong>Véhicule :</strong> ${trajet.marque} ${trajet.modele}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            historiqueContainer.innerHTML = "<div class='alert alert-warning'>Aucun trajet effectué.</div>";
        }
    })
    .catch(error => console.error("Erreur lors du chargement de l'historique :", error));
