document.addEventListener("DOMContentLoaded", function() {
    // Charger le formulaire dynamiquement
    fetch('../../Vue/formulaire/ajout_utilisateur.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('fichier_importe').innerHTML = data;
            attachFormHandler(); // Attacher l'événement après insertion du formulaire
        });

    function attachFormHandler() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche le rechargement de la page
            
            const formData = new FormData(form);
            formData.append('ajax', true);

            fetch('../../Modele/CRUD_utilisateur/ajout_utilisateur.php', {
                method: 'POST',
                body: formData
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
