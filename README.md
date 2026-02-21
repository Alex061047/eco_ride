# EcoRide, Plateforme de covoiturage écologique

Bienvenue dans EcoRide, une application web écoresponsable qui vise à promouvoir le covoiturage. Le projet est de développer dans le cadre d'un examen de fin de formation en tant que Développeur Web et Web Mobile.

---

## Présentation du projet

EcoRide est une plateforme de covoiturage qui :

* Encourage l'utilisation de véhicules écologiques (mention spéciale si électrique).
* Permet aux utilisateurs de créer ou rejoindre des trajets.
* Utilise un système de crédits virtuels.
* Fournit une interface pour les passagers, chauffeurs, employés et administrateurs. il existe également un role mixte passager-chauffeur.
* Stocke les données principales dans MySQL et les logs/statistiques dans MongoDB.

---

## Fonctionnalités principales

### Utilisateur

* Créer un compte et se connecter (crédit initial de 20 crédits).
* Rechercher un itinéraire selon la ville, la date, des filtres (véhicule électrique, prix, durée, note, animaux).
* Réserver une place avec un paiement en crédits (2 crédits retenus pour la plateforme).
* Consulter et modifier son profil.
* Proposer un trajet (si chauffeur).
* Voir son historique (trajets, préférences, réservations).

### Employé

* Valider ou refuser des avis déposés après un trajet (avec gestion des crédits accordés).
* Voir les avis litigieux (trajets mal passés) avec les informations concernées.

### Administrateur

* Créer des comptes employés.
* Suspendre des comptes.
* Visualiser des graphiques :

 * Nombre de covoiturages / jour.
 * Crédits gagnés / jour.
 * Crédits cumulés totaux de la plateforme.

---

## Technologies utilisées

### Stack technique

**Front-End :**

 * HTML5, CSS3 (avec Bootstrap 5)
 * JavaScript (DOM, `fetch`, Chart.js)

**Back-End :**

* **PHP natif (architecture MVC légère)** avec séparation claire :
  * `Controleur_b/` : contrôleurs back-end (points d’entrée API sécurisés)
  * `Modele/` : logique métier + accès aux données MySQL/MongoDB
* **Sécurité serveur centralisée** via `Controleur_b/_security.php` :
  * validation de méthode HTTP (`GET/POST`)
  * validation du corps JSON
  * contrôle d’authentification (session)
  * contrôle d’autorisation par rôle (`admin`, `employe`, `chauffeur`, `passager`)
* **Sessions PHP** utilisées comme source de vérification côté serveur (et non `sessionStorage`)
* **API JSON** pour la communication front ↔ back (`fetch` côté front, `json_encode` côté serveur)
* **Accès MySQL avec PDO** :
  * requêtes préparées (protection injection SQL)
  * validation des paramètres côté serveur
  * contrôles d’intégrité métier (ex. appartenance véhicule/utilisateur)
* **Transactions SQL** sur les opérations critiques (ex. réservation trajet : crédits, places, réservation)
* **Gestion d’erreurs back-end** standardisée (codes HTTP + messages JSON)
* **Intégration MongoDB** pour les données non relationnelles (logs, avis, statistiques), avec fallback contrôlé si indisponible
* **Chargement de configuration via `.env`** (DB, Mongo, SMTP, URL) avec `vlucas/phpdotenv`
* **Dépendances gérées par Composer** (autoload PSR, librairies MongoDB, PHPMailer)


**Base de données :**

 * **Relationnelle :** MySQL
 * **NoSQL :** MongoDB (logs actions, statistiques, crédits)

**Déploiement :** Heroku

---

## Instructions pour lancer en local

### Prérequis

* XAMPP ou MAMP (Apache + MySQL)
* MongoDB installA localement (ou via Docker)
* PHP > 7.4
* Navigateur moderne (Chrome, Firefox... )

### Etapes

1. **Cloner le dépot :**

```bash
git clone https://github.com/Alex061047/eco_ride.git
```

2. **Configurer la base MySQL :**

* Lancer phpMyAdmin
* Créer la base `eco_ride`
* Importer le fichier `create_database.sql` fourni

3. **Configurer MongoDB :**

* Démarrer le serveur Mongo (port par défaut : 27017)
* MongoDB créera automatiquement la base `eco_ride` et les collections via les scripts PHP (logs\_credit, logs\_employe, logs\_suspendu, etc.)

4. **Lancer le serveur Apache :**

* Placer le projet dans `htdocs/`
* Accéder via :

```
http://localhost:8000/
```

5. **Configuration des fichiers de connexion :**

* `Modele/db_connection.php` : configuration MySQL
* `Modele/mongodb/mongo_connection.php` : configuration MongoDB

6. **Configuration du fichier `.env` :**
* SMTP (envoi d'emails)
SMTP_HOST=smtp.gmail.com
SMTP_USER=email@email.com
SMTP_PASS=motdepasse_application
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_FROM=email@email.com
SMTP_FROM_NAME=EcoRide

* URL pour l'avis suite A l'email
BASE_URL=http://localhost:8000

* Base de données MySQL
DB_HOST=localhost
DB_PORT=3306
DB_NAME=eco_ride
DB_USER=root
DB_PASS=

* Base de données MongoDB
MONGO_URI=mongodb://localhost:27017

---

## Lancer avec Docker


### Prérequis
* Docker et Docker Compose installés (pour Linux)
* Ou Docker Desktop (pour Windows ou Mac)

### Etapes
1. **Cloner le dépot**

```bash
git clone https://github.com/Alex061047/eco_ride.git
cd eco_ride
```

2. **Configuration du fichier `.env` :**

Créez un fichier .env à la racine du projet avec les variables suivantes :
# Configuration MySQL
DB_HOST=localhost
DB_PORT=3306
DB_NAME=eco_ride
DB_USER=root
DB_PASS=

# Configuration MongoDB
MONGO_URI=mongodb://localhost:27017

# Configuration SMTP (envoi d'e-mails)
SMTP_HOST=smtp.gmail.com
SMTP_USER=your_email@gmail.com
SMTP_PASS=motdepasse_application
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_FROM=your_email@gmail.com
SMTP_FROM_NAME=EcoRide

# URL de base pour l'application
BASE_URL=http://localhost:8080


3. **Lancer les conteneurs**

```bash
docker-compose up --build
```

4. **Accéder à l'application**

```
http://localhost:8080
```
### Fichier Docker
Le projet inclut les fichiers suivants pour la conteneurisation :

* `Dockerfile` : image PHP 8.1 avec Apache, extensions pdo_mysql, mysqli et modules Apache rewrite, headers

* `docker-compose.yml` : gère les services web, mysql, mongo

* `.dockerignore` : exclut les fichiers inutiles du contexte Docker

* `.env`(Crée avant de lancer docker-compose) : variables d'environnement centralisées

* `.htaccess` : gère la récriture d'URL pour le routage SPA et sécurise les headers HTTP

---

## Structure du projet

```
eco_ride/
|-- Assets/ # Feuilles de style SCSS/CSS, images
| |-- images/ # Images présentes sur le site
| |-- styles/ # Ensemble des fichiers et dossiers css
|
|-- Controleur/ # Scripts JS pour la logique (fetch, routage, actions)
| |-- CRUD_*/ # CRUD pour utilisateur, trajets, admin, covoiturages, employe
| |-- navbar/ # Logique de la barre de navigation (masquer et afficher)
| |-- Router/ # Système de routage et sécurité 
|
|-- Controleur_b/ # Contrôleurs back-end: validation, autorisation, sécurité, routage vers les modèles (indépendant du front)
| |-- _security.php # Fonctions communes de sécurité (auth, rôle, méthode, JSON, erreurs)
| |-- CRUD_admin/ # Endpoints admin (création employé, suspension, statistiques, crédits)
| |-- CRUD_covoiturages/ # Endpoints covoiturages (liste, détails chauffeur, participation)
| |-- CRUD_employe/ # Endpoints employé (lecture/traitement des avis)
| |-- CRUD_trajets/ # Endpoints trajets (ajout, mise à jour état, historique, annulation)
| |-- CRUD_utilisateur/ # Endpoints utilisateur (connexion, profil, photo, avis, etc.)
| |-- CRUD_vehicule/ # Endpoints véhicule et préférences (CRUD + récupération profil)
|
|
|-- Modele/ # Connexion BDD, requètes SQL et MongoDB
| |-- CRUD_*/ # CRUD pour utilisateur, trajets, admin, covoiturages, employe, vehicule
| |-- mongodb/ # Connexion et enregistrements des logs MongoDB
| |-- db_connection.php # Connexion à MySQL
|
|-- uploads/ # Dossier de stockage des photos de profil
|
|-- Vue/ # Pages front (administrateur, connexion, covoiturage, employA , footer, accueil, utilisateur, 404, navigation )
| |-- admin/ # Page administrateur 
| |-- connexion/ # Page de connexion
| |-- covoit/ # Page des covoiturages
| |-- employe/ # Page employé
| |-- footer/ # Page accessible depuis le footer (Mention Legale, Politique de confidentialité )
| |-- home/ # Page d'accueil
| |-- user/ # Page espace utilisateur
| |-- 404.html # Page de redirection si erreur de chemin d'accès ou pas de droit d'accès
| |-- navbar.html # Barre de navigation
|
|-- .dockerignore # Exclusion des fichiers inutiles lors du build Docker
|-- .env # Variables sensibles (connexion BDD, SMTP, URI Mongo, etc.)
|-- .htaccess # Récriture d'URL pour le routeur (gestion par Apache dans le cadre de Docker)
|-- composer.json # Bibliothèques et packages nécessaires
|-- composer.lock # Fixe les versions des dépendances
|-- create_database.sql # Permet de créer la base de donnée en local (via une importation sur MySQL)
|-- Dockerfile # Configuration de l'image Apache + PHP + modules activés
|-- docker-compose.yml # Définition des services (PHP, MySQL, MongoDB)
|-- index.php # Page de base qui "encadre les autres pages" (les autres pages sont injectées dynamiquement)
|-- README.md # Fichier actuel 
```

---

Pour plus d'informations sur le projet, consulter le manuel utilisateur PDF et la documentation technique.

