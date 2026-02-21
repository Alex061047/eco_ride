// Charger les vehicules dans le select
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
        const vehicules = Array.isArray(data.vehicules) ? data.vehicules : [];
        if (data.status === "success" && vehicules.length > 0) {
            const vehiculeSelect = document.getElementById("vehicule");
            vehicules.forEach(vehicule => {
                const option = document.createElement("option");
                option.value = vehicule.id;
                option.textContent = `${vehicule.marque} ${vehicule.modele} - ${vehicule.energie}`;
                vehiculeSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error("Erreur lors du chargement des vehicules :", error);
        alert("Impossible de charger les vehicules : " + (error.message || "serveur"));
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
    // Verifie que le prix n'est pas inferieur a 2 credits
    if (parseFloat(prix) < 2) {
        alert("Le prix ne peut pas etre inferieur a 2 credits.");
        return;
    }


    // Verification rapide que tous les champs sont remplis
    if (!vehicule || !depart || !arrivee || !datetime || !duree || !prix || !nbPlacesRestantes) {
        alert("Merci de remplir tous les champs !");
        return;
    }

    fetch("../../Controleur_b/CRUD_trajets/add_trajet_controller.php", {
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
            // Reinitialiser le formulaire et recharger la page
            document.getElementById('form-trajet').reset();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Erreur lors de l'ajout du trajet :", error);
        alert("Erreur lors de l'ajout du trajet");
    });
});
