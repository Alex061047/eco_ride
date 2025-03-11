import Route from "./Route.js";


//Définition des routes
export const allRoutes = [
    new Route("/", "Accueil", "../Vue/home/accueil.php", []),
    new Route("404", "Page introuvable", "../Vue/404.html", []),
];

//Le titre s'affichera comme ceci : Route.titre - websitename
export const websiteName = "EcoRide";