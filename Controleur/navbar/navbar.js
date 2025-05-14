window.addEventListener('DOMContentLoaded', () => {
    // Récupération des éléments de la barre de navigation par leur ID
    const monEspaceItem = document.getElementById('mon-espace-item');
    const dashboardItem = document.getElementById('dashboard-item');
    const logoutItem = document.getElementById('logout-item');
    const loginItem = document.getElementById('login-item');
    const logoutLink = document.getElementById('logout-link');
    const dashboardAdminItem = document.getElementById('dashboard-admin');


    // Vérifie si l'utilisateur est connecté et récupère son rôle
    const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";
    const userRole = sessionStorage.getItem("userRole");

    // Affiche les bons éléments à l'utilisateur en fonction de son role
    if (isLoggedIn) {
        // Utilisateur connecté
        monEspaceItem.style.display = "block";  // Affiche "Mon espace"
        logoutItem.style.display = "block"; // Affiche "Déconnexion"
        loginItem.style.display = "none"; // Masque "Connexion"

        // Affiche le tableau de bord si l'utilisateur est un employé
        if (userRole === "employe") {
            dashboardItem.style.display = "block";
        } else if (dashboardItem) {
            dashboardItem.style.display = "none";
        }
        // Affiche le tableau de bord si l'utilisateur est un administrateur
        if (userRole === "admin") {
            dashboardAdminItem.style.display = "block";
        } else if (dashboardAdminItem) {
            dashboardAdminItem.style.display = "none";
        }
    } else {
        // Utilisateur non connecté
        monEspaceItem.style.display = "none"; // Masque "Mon espace"
        logoutItem.style.display = "none"; // Masque "Déconnexion"
        loginItem.style.display = "block"; // Affiche "Connexion"

        // Masque le dashboard si besoin (sécurité supplémentaire)
        if (dashboardItem) {
            dashboardItem.style.display = "none";
        }
    }

    // Gestion du clic sur "Déconnexion"
    if (logoutLink) {
        logoutLink.addEventListener("click", (e) => {
            e.preventDefault();
            sessionStorage.removeItem("isLoggedIn");
            sessionStorage.removeItem("userRole");
            sessionStorage.removeItem("userId");
            sessionStorage.removeItem("userPseudo");

            // Déconnexion côté serveur
            fetch("../../Modele/CRUD_utilisateur/logout.php")
                .then(() => {
                    window.location.href = "/"; 
                });
        });
    }
});
