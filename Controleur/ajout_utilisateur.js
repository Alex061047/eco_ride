document.addEventListener("DOMContentLoaded", function () {
    fetch('../../Vue/formulaire/ajout_utilisateur.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('fichier_importe').innerHTML = data;
            attachFormHandler(); // Attacher l'événement après insertion du formulaire
        });

    function attachFormHandler() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Empêche le rechargement de la page

            // Récupérer les valeurs du formulaire
            const formData = {
                pseudo: form.querySelector('[name="pseudo"]').value,
                email: form.querySelector('[name="email"]').value,
                mot_de_passe: form.querySelector('[name="mot_de_passe"]').value,
                role: form.querySelector('[name="role"]').value
            };


            // Envoyer en JSON
            fetch('../../Modele/CRUD_utilisateur/ajout_utilisateur.php', {
                method: 'POST',
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('message');
                messageDiv.innerHTML = `<p>${data.message}</p>`;
                messageDiv.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") form.reset();
            })
            .catch(error => console.error('Erreur:', error));
        });
    }
});
