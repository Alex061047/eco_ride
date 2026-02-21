window.addEventListener('DOMContentLoaded', async () => {
    // Elements de navigation
    const monEspaceItem = document.getElementById('mon-espace-item');
    const dashboardItem = document.getElementById('dashboard-item');
    const logoutItem = document.getElementById('logout-item');
    const loginItem = document.getElementById('login-item');
    const logoutLink = document.getElementById('logout-link');
    const dashboardAdminItem = document.getElementById('dashboard-admin');

    const normaliserRole = (role) => {
        if (!role) return '';
        return String(role)
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    };

    const afficherConnecte = (role) => {
        const roleNormalise = normaliserRole(role);
        if (monEspaceItem) monEspaceItem.style.display = 'block';
        if (logoutItem) logoutItem.style.display = 'block';
        if (loginItem) loginItem.style.display = 'none';

        if (dashboardItem) {
            dashboardItem.style.display = roleNormalise === 'employe' ? 'block' : 'none';
        }
        if (dashboardAdminItem) {
            dashboardAdminItem.style.display = roleNormalise === 'admin' ? 'block' : 'none';
        }
    };

    const afficherDeconnecte = () => {
        if (monEspaceItem) monEspaceItem.style.display = 'none';
        if (logoutItem) logoutItem.style.display = 'none';
        if (loginItem) loginItem.style.display = 'block';
        if (dashboardItem) dashboardItem.style.display = 'none';
        if (dashboardAdminItem) dashboardAdminItem.style.display = 'none';
    };

    // Verification session + role cote serveur
    let user = null;
    try {
        const response = await fetch('../../Controleur_b/CRUD_vehicule/get_user_controller.php');
        const data = await response.json();

        if (data && data.status === 'success' && data.user) {
            user = data.user;
        }
    } catch (error) {
        console.error('Erreur verification utilisateur navbar :', error);
    }

    if (user) {
        sessionStorage.setItem('isLoggedIn', 'true');
        sessionStorage.setItem('userId', user.id);
        sessionStorage.setItem('userRole', normaliserRole(user.role));
        sessionStorage.setItem('userPseudo', user.pseudo || '');
        afficherConnecte(user.role);
    } else {
        // Fallback UX: si l'appel serveur echoue, on garde l'affichage base sur sessionStorage.
        const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
        const savedRole = normaliserRole(sessionStorage.getItem('userRole') || '');
        if (isLoggedIn && savedRole) {
            afficherConnecte(savedRole);
        } else {
            sessionStorage.removeItem('isLoggedIn');
            sessionStorage.removeItem('userRole');
            sessionStorage.removeItem('userId');
            sessionStorage.removeItem('userPseudo');
            afficherDeconnecte();
        }
    }

    // Deconnexion
    if (logoutLink) {
        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            sessionStorage.removeItem('isLoggedIn');
            sessionStorage.removeItem('userRole');
            sessionStorage.removeItem('userId');
            sessionStorage.removeItem('userPseudo');

            fetch('../../Controleur_b/CRUD_utilisateur/logout_controller.php')
                .then(() => {
                    window.location.href = '/';
                });
        });
    }
});
