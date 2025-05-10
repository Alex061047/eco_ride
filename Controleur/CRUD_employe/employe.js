// Chargement des trajets
chargerTrajet();

function chargerTrajet() {
    // Récupérer la valeur du filtre "Note max"
    const noteMax = document.getElementById("noteFilter").value;
    const queryParams = noteMax ? `?noteMax=${noteMax}` : "";

    // Récupérer les avis depuis le serveur
    fetch("../../Modele/CRUD_employe/get_avis.php" + queryParams)
        .then(response => response.json())
        .then(avisList => {
            const tbody = document.querySelector("#table tbody");
            if (!tbody) return; 

            // Réinitialise le contenu du tableau
            tbody.innerHTML = "";

             // Parcours chaque avis et insère une ligne dans le tableau
            avisList.forEach(avis => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${avis.trajet_id}</td>
                    <td>${avis.depart}</td>
                    <td>${avis.arrivee}</td>
                    <td>${avis.chauffeur_pseudo} (${avis.chauffeur_mail})</td>
                    <td>${avis.prix}</td>
                    <td>${avis.date_trajet}</td>
                    <td>${avis.passager_pseudo} (${avis.passager_mail}, ID: ${avis.passager_id})</td>
                    <td>${avis.commentaire} (${avis.date_commentaire})</td>
                    <td>${avis.note ? avis.note : 'Aucune'}</td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="gererAvis(${avis.trajet_id}, ${avis.passager_id}, 'valider')">Valider</button>
                       <button class="btn btn-danger btn-sm" onclick="ouvrirModalRefus(${avis.trajet_id}, ${avis.passager_id}, ${avis.prix})">Refuser</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            // Affiche les erreurs dans la console en cas d'échec
            console.error("Erreur lors du chargement des avis :", error);
        });
}


// Recherche dynamique dans le tableau (barre de recherche)
document.getElementById("searchInput").addEventListener("input", function () {
    const query = this.value.toLowerCase();
    const rows = document.querySelectorAll("#table tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
});


// Filtrer par note max
document.getElementById("noteFilter").addEventListener("change", function () {
    chargerTrajet(); // Recharge les trajets
});

// Actions sur les avis
function gererAvis(trajetId, passagerId, action, credit = null) {
    const payload = {
        trajet_id: trajetId,
        passager_id: passagerId,
        action: action
    };

    // Si l'action est un refus, ajoute le crédit à accorder
    if (action === "refuser" && credit !== null) {
        payload.credit = parseFloat(credit);
    }

    // Envoi de payload au serveur
    fetch("../../Modele/CRUD_employe/gestion_avis.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        chargerTrajet();
    });
}



// Fonction d'attribution des crédits en cas de refus
function ouvrirModalRefus(trajetId, passagerId, prix) {
    // Demande à l'employé combien de crédits accorder au chauffeur
    const montant = prompt(`Crédit à attribuer au chauffeur (sur un total de ${prix} crédits) :`);
    const montantFloat = parseFloat(montant);

    // Vérifie que la valeur est bien numérique et comprise entre 0 et le prix
    if (!isNaN(montantFloat) && montantFloat >= 0 && montantFloat <= prix) {
        gererAvis(trajetId, passagerId, 'refuser', montantFloat);
    } else {
        alert("Veuillez saisir un montant valide entre 0 et " + prix);
    }
}
