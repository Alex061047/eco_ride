import Route from "./Route.js";
import { allRoutes, websiteName } from "./allRoutes.js";

// Création d'une route pour la page 404 (page introuvable)
const route404 = new Route("404", "Page introuvable", "../../Vue/404.html", []);

const normalizeRole = (role) => {
  if (!role) return "";
  return String(role)
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .trim();
};

// Récupère l'utilisateur connecté depuis le serveur.
const getServerUser = async () => {
  try {
    const response = await fetch("../../Controleur_b/CRUD_vehicule/get_user_controller.php");
    const data = await response.json();

    if (data && data.status === "success" && data.user) {
      return data.user;
    }
  } catch (error) {
    console.error("Erreur lors de la vérification serveur du rôle :", error);
  }

  return null;
};

const render404 = async () => {
  window.history.pushState({}, "", "/404");
  const html = await fetch("../../Vue/404.html").then(res => res.text());
  document.getElementById("main-page").innerHTML = html;
  document.title = "Page introuvable - " + websiteName;
};

// Fonction pour récupérer la route correspondant à une URL donnée
const getRouteByUrl = (url) => {
  let currentRoute = null;
  // Parcours de toutes les routes pour trouver la correspondance
  allRoutes.forEach((element) => {
    if (element.url == url) {
      currentRoute = element;
    }
  });
  // Si aucune correspondance n'est trouvée, on retourne la route 404
  if (currentRoute != null) {
    return currentRoute;
  } else {
    return route404;
  }
};

// Fonction pour charger le contenu de la page
const LoadContentPage = async () => {
  const path = window.location.pathname;
  // Récupération de l'URL actuelle
  const actualRoute = getRouteByUrl(path);

  // Vérifie si l'utilisateur a des droits d'accès aux pages
  if (actualRoute.authorize.length > 0) {
    const user = await getServerUser();
    const role = normalizeRole(user?.role ?? "");
    const allowedRoles = actualRoute.authorize.map(normalizeRole);

    if (!role || !allowedRoles.includes(role)) {
      // Redirige vers une page d'erreur si rôle non autorisé côté serveur
      await render404();
      return;
    }
  }
  
  // Récupération du contenu HTML de la route
  const html = await fetch(actualRoute.pathHtml).then((data) => data.text());
  // Ajout du contenu HTML à l'élément avec l'ID "main-page"
  document.getElementById("main-page").innerHTML = html;

// Suppression des anciens scripts de la page précédente
document.querySelectorAll("script.dynamic-script").forEach(script => script.remove());

  // Ajout du contenu JavaScript
  actualRoute.pathJS.forEach(jsFile => {
    // Création d'une balise script
    var scriptTag = document.createElement("script");
    scriptTag.setAttribute("type", "text/javascript");
    scriptTag.setAttribute("src", jsFile);
    scriptTag.classList.add("dynamic-script");
    
    // Ajout de la balise script au corps du document
    document.querySelector("body").appendChild(scriptTag);
  });

  // Changement du titre de la page
  document.title = actualRoute.title + " - " + websiteName;
};



// Fonction pour gérer les événements de routage (clic sur les liens)
const routeEvent = (event) => {
    event.preventDefault();
    const newUrl = new URL(event.target.href);
    // Mise à jour de l'URL dans l'historique du navigateur
    window.history.pushState({}, "", newUrl.pathname);
    // Chargement du contenu de la nouvelle page
    LoadContentPage();
  };

// Gestion de l'événement de retour en arrière dans l'historique du navigateur
window.onpopstate = LoadContentPage;
// Assignation de la fonction routeEvent à la propriété route de la fenêtre
window.route = routeEvent;
// Chargement du contenu de la page au chargement initial
LoadContentPage();
