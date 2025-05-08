import Route from "./Route.js";


//Définition des routes
export const allRoutes = [
    new Route("/", "Accueil", "../Vue/home/accueil.php", []),
    new Route("/Covoiturage", "Covoiturage", "../Vue/covoit/covoit.php", [], ["../../Controleur/covoit/covoit.js"]),
    new Route("/EspaceUtilisateur", "Espace Utilisateur", "../Vue/user/userSpace.php", [], ["../../Controleur/CRUD_utilisateur/userSpace.js"]),
    new Route("/Trajets", "Historique des trajets", "../Vue/user/userHistory.php", [],
        ["../../Controleur/CRUD_trajets/get_trajets.js", "../../Controleur/CRUD_trajets/historique.js", "../../Controleur/CRUD_trajets/new_trajets.js"]),
    new Route("/Connexion", "Connexion/Inscription", "../Vue/connexion/connexion.php", [], ["../../Controleur/CRUD_utilisateur/auth.js"]),
    new Route("/Avis", "Avis", "../Vue/user/avis.php", [], ["../../Controleur/CRUD_utilisateur/avis.js"]),
    new Route("404", "Page introuvable", "../Vue/404.html", []),
];

//Le titre s'affichera comme ceci : Route.titre - websitename
export const websiteName = "EcoRide";