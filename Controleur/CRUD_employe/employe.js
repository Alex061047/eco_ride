// Chargement des trajets
chargerTrajet();

function chargerTrajet() {
    const noteMax = document.getElementById("noteFilter").value;
    const queryParams = noteMax ? `?noteMax=${noteMax}` : "";

    fetch("../../Controleur_b/CRUD_employe/get_avis_controller.php" + queryParams)
        .then(async response => {
            const text = await response.text();
            let avisList;
            try {
                avisList = JSON.parse(text);
            } catch (e) {
                throw new Error("Réponse invalide: " + text.slice(0, 200));
            }

            if (!response.ok) {
                throw new Error((avisList && avisList.message) ? avisList.message : "Erreur HTTP " + response.status);
            }

            return avisList;
        })
        .then(avisList => {
            const tbody = document.querySelector("#table tbody");
            if (!tbody) return;

            tbody.innerHTML = "";

            if (!Array.isArray(avisList)) {
                throw new Error((avisList && avisList.message) ? avisList.message : "Format inattendu");
            }

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

            if (avisList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted">Aucun avis en attente.</td></tr>`;
            }
        })
        .catch(error => {
            console.error("Erreur lors du chargement des avis :", error);
            const tbody = document.querySelector("#table tbody");
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-danger">Erreur chargement avis : ${error.message}</td></tr>`;
            }
        });
}

// Recherche dynamique
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
    chargerTrajet();
});

// Actions sur les avis
function gererAvis(trajetId, passagerId, action, credit = null) {
    const payload = {
        trajet_id: trajetId,
        passager_id: passagerId,
        action: action
    };

    if (action === "refuser" && credit !== null) {
        payload.credit = parseFloat(credit);
    }

    fetch("../../Controleur_b/CRUD_employe/gestion_avis_controller.php", {
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

// Attribution des crédits en cas de refus
function ouvrirModalRefus(trajetId, passagerId, prix) {
    const montant = prompt(`Crédit à attribuer au chauffeur (sur un total de ${prix - 2} crédits) :`);
    const montantFloat = parseFloat(montant);

    if (!isNaN(montantFloat) && montantFloat >= 0 && montantFloat <= prix - 2) {
        gererAvis(trajetId, passagerId, 'refuser', montantFloat);
    } else {
        alert("Veuillez saisir un montant valide entre 0 et " + (prix - 2));
    }
}
