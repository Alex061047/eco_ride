window.addEventListener('DOMContentLoaded', () => {
    const monEspaceItem = document.getElementById('mon-espace-item');
    const logoutItem = document.getElementById('logout-item');
    const loginItem = document.getElementById('login-item');
    const logoutLink = document.getElementById('logout-link');

    const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";

    if (isLoggedIn) {
        monEspaceItem.style.display = "block";
        logoutItem.style.display = "block";
        loginItem.style.display = "none";
    } else {
        monEspaceItem.style.display = "none";
        logoutItem.style.display = "none";
        loginItem.style.display = "block";
    }

    // Gestion du clic sur "Déconnexion"
    if (logoutLink) {
        logoutLink.addEventListener("click", (e) => {
            e.preventDefault();
            sessionStorage.removeItem("isLoggedIn");
    
            // Déconnexion serveur
            fetch("../../Modele/CRUD_utilisateur/logout.php")
                .then(() => {
                    window.location.href = "/"; 
                });
        });
    }
});
