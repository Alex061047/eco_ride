// Charger les véhicules dans le select
fetch("../../Modele/CRUD_vehicule/get_user.php")
    .then(response => response.json())
    .then(data => {
        if (data.status === "success" && data.vehicules.length > 0) {
            const vehiculeSelect = document.getElementById("vehicule");
            data.vehicules.forEach(vehicule => {
                const option = document.createElement("option");
                option.value = vehicule.id; // on stocke l'ID du véhicule !
                option.textContent = `${vehicule.marque} ${vehicule.modele} - ${vehicule.energie}`;
                vehiculeSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error("Erreur lors du chargement des véhicules :", error);
    });


// Gestion proposition de trajet
document.getElementById('btn-proposer-trajet').addEventListener('click', () => {
    const vehicule = document.getElementById('vehicule').value;
    const depart = document.getElementById('depart').value;
    const arrivee = document.getElementById('arrivee').value;
    const datetime = document.getElementById('datetime').value;
    const duree = document.getElementById('duree').value;
    const prix = document.getElementById("prix").value;
    const nbPlacesRestantes = document.getElementById("places").value;
    // Vérifie que le prix n'est pas inférieur à 2 crédits
    if (parseFloat(prix) < 2) {
        alert("Le prix ne peut pas être inférieur à 2 crédits.");
        return;
    }    
    

    // Vérification rapide que tous les champs sont remplis
    if (!vehicule || !depart || !arrivee || !datetime || !duree || !prix || !nbPlacesRestantes) {
        alert("Merci de remplir tous les champs !");
        return;
    }

    fetch("../../Modele/CRUD_trajets/add_trajet.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            vehicule_id: vehicule,
            depart: depart,
            arrivee: arrivee,
            datetime: datetime,
            duree: duree,
            prix: prix,
            nb_places_restantes: nbPlacesRestantes
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            // Réinitialiser le formulaire et recharger la page
            document.getElementById('form-trajet').reset();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Erreur lors de l'ajout du trajet :", error);
        alert("Erreur lors de l'ajout du trajet");
    });
});
