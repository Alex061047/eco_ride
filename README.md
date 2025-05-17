# EcoRide, Plateforme de covoiturage écologique

Bienvenue dans EcoRide, une application web écoresponsable qui vise à promouvoir le covoiturage. Le projet a été développé dans le cadre d’un examen de fin de formation en tant que Développeur Web et Web Mobile.

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

  * PHP natif (avec PDO pour requêtes SQL sécurisées)

**Base de données :**

  * **Relationnelle :** MySQL
  * **NoSQL :** MongoDB (logs actions, statistiques, crédits)

**Déploiement :** Heroku

---

## Instructions pour lancer en local

### Prérequis

* XAMPP ou MAMP (Apache + MySQL)
* MongoDB installé localement (ou via Docker)
* PHP ≥ 7.4
* Navigateur moderne (Chrome, Firefox…)

### Étapes

1. **Cloner le dépôt :**

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

* Base de données MySQL
DB_HOST=localhost
DB_PORT=3306
DB_NAME=eco_ride
DB_USER=root
DB_PASS=

* Base de données MongoDB
MONGO_URI=mongodb://localhost:27017

---

## Structure du projet

```
eco_ride/
|-- Assets/               # Feuilles de style SCSS/CSS, images
|   |-- images/           # Images présentes sur le site
|   |-- styles/           # Ensemble des fichiers et dossiers css
|
|-- Controleur/           # Scripts JS pour la logique (fetch, routage, actions)
|   |-- CRUD_*/           # CRUD pour utilisateur, trajets, admin, covoiturages, employe
|   |-- navbar/           # Logique de la barre de navigation (masquer et afficher)
|   |-- Router/           # Système de routage et sécurité
|
|-- Modele/               # Connexion BDD, requêtes SQL & MongoDB
│   |-- CRUD_*/           # CRUD pour utilisateur, trajets, admin, covoiturages, employe, vehicule
│   |-- mongodb/          # Connexion et enregistrements des logs MongoDB
|   |-- db_connection.php # Connexion à MySQL
|
|-- uploads/              # Dossier de stockage des photos de profil
|
|-- Vue/  # Pages front (administrateur, connexion, covoiturage, employé, footer, accueil, utilisateur, 404, navigation )
│   |-- admin/            # Page administrateur 
│   |-- connexion/        # Page de connexion
│   |-- covoit/           # Page des covoiturages
│   |-- employe/          # Page employé
|   |-- footer/           # Page accessible depuis le footer (Mention Legale, Politique de confidentialité)
|   |-- home/             # Page d'accueil
|   |-- user/             # Page espace utilisateur
|   |-- 404.html          # Page de redirection si erreur de chemin d'accès ou pas de droit d'accès
│   |-- navbar.html       # Barre de navigation
|
|-- composer.json         # Bibliothèques et packages nécessaires
|-- composer.lock         # Fixe les versions des dépendances
|-- create_database.sql   # Permet de créer la base de donnée en local
|-- index.php             # Page de base qui "encadre les autres pages" (les autres pages sont injéctées dynamiquement)
|-- README.md             # Fichier actuel
```

---

Pour plus d’infos sur le projet, consulter le manuel utilisateur PDF et la documentation technique.
