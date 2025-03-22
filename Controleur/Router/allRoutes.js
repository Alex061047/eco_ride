import Route from "./Route.js";


//Définition des routes
export const allRoutes = [
    new Route("/", "Accueil", "../Vue/home/accueil.php", []),
    new Route("/Covoiturage", "Covoiturage", "../Vue/covoit/covoit.php", []),
    new Route("/EspaceUtilisateur", "Espace Utilisateur", "../Vue/user/userSpace.php", []),
    new Route("/Trajets", "Historique des trajets", "../Vue/user/userHistory.php", [], "../../Controleur/CRUD_trajets/get_trajets.js"),

    new Route("404", "Page introuvable", "../Vue/404.html", []),
];

//Le titre s'affichera comme ceci : Route.titre - websitename
export const websiteName = "EcoRide";