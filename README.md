# EcoRide, Plateforme de covoiturage écologique

Bienvenue dans EcoRide, une application web A coresponsable qui vise A promouvoir le covoiturage. Le projet a A tA dA veloppA dans le cadre dA A A ?Tun examen de fin de formation en tant que DA veloppeur Web et Web Mobile.

---

## PrA sentation du projet

EcoRide est une plateforme de covoiturage qui :

* Encourage l'utilisation de véhicules écologiques (mention spA ciale si électrique).
* Permet aux utilisateurs de crA er ou rejoindre des trajets.
* Utilise un systA me de crédits virtuels.
* Fournit une interface pour les passagers, chauffeurs, employA s et administrateurs. il existe A galement un role mixte passager-chauffeur.
* Stocke les données principales dans MySQL et les logs/statistiques dans MongoDB.

---

## FonctionnalitA s principales

### Utilisateur

* CrA er un compte et se connecter (crA dit initial de 20 crédits).
* Rechercher un itinA raire selon la ville, la date, des filtres (véhicule électrique, prix, durA e, note, animaux).
* RA server une place avec un paiement en crédits (2 crédits retenus pour la plateforme).
* Consulter et modifier son profil.
* Proposer un trajet (si chauffeur).
* Voir son historique (trajets, préférences, réservations).

### EmployA 

* Valider ou refuser des avis dA posA s aprA s un trajet (avec gestion des crédits accordA s).
* Voir les avis litigieux (trajets mal passA s) avec les informations concernA es.

### Administrateur

* CrA er des comptes employA s.
* Suspendre des comptes.
* Visualiser des graphiques :

 * Nombre de covoiturages / jour.
 * CrA dits gagnA s / jour.
 * CrA dits cumulA s totaux de la plateforme.

---

## Technologies utilisA es

### Stack technique

**Front-End :**

 * HTML5, CSS3 (avec Bootstrap 5)
 * JavaScript (DOM, `fetch`, Chart.js)

**Back-End :**

 * PHP natif (avec PDO pour requA tes SQL sA curisA es)

**Base de données :**

 * **Relationnelle :** MySQL
 * **NoSQL :** MongoDB (logs actions, statistiques, crédits)

**DA ploiement :** Heroku

---

## Instructions pour lancer en local

### PrA requis

* XAMPP ou MAMP (Apache + MySQL)
* MongoDB installA localement (ou via Docker)
* PHP A A A ?A A A 7.4
* Navigateur moderne (Chrome, FirefoxA A A ?A A A )

### A A A ?tapes

1. **Cloner le dA pA t :**

```bash
git clone https://github.com/Alex061047/eco_ride.git
```

2. **Configurer la base MySQL :**

* Lancer phpMyAdmin
* CrA er la base `eco_ride`
* Importer le fichier `create_database.sql` fourni

3. **Configurer MongoDB :**

* DA marrer le serveur Mongo (port par dA faut : 27017)
* MongoDB crA era automatiquement la base `eco_ride` et les collections via les scripts PHP (logs\_credit, logs\_employe, logs\_suspendu, etc.)

4. **Lancer le serveur Apache :**

* Placer le projet dans `htdocs/`
* AccA der via :

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


### PrA requis
* Docker et Docker Compose installA s (pour Linux)
* Ou Docker Desktop (pour Windows ou Mac)

### A A A ?tapes
1. **Cloner le dA pA t**

```bash
git clone https://github.com/Alex061047/eco_ride.git
cd eco_ride
```

2. **Configuration du fichier `.env` :**

CrA ez un fichier .env A la racine du projet avec les variables suivantes :
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

# URL de base pour lA A A ?Tapplication
BASE_URL=http://localhost:8080


3. **Lancer les conteneurs**

```bash
docker-compose up --build
```

4. **AccA der A l'application**

```
http://localhost:8080
```
### Fichier Docker
Le projet inclut les fichiers suivants pour la conteneurisation :

* `Dockerfile` : image PHP 8.1 avec Apache, extensions pdo_mysql, mysqli et modules Apache rewrite, headers

* `docker-compose.yml` : gA re les services web, mysql, mongo

* `.dockerignore` : exclut les fichiers inutiles du contexte Docker

* `.env`(A crA er avant de lancer docker-compose) : variables dA A A ?Tenvironnement centralisA es

* `.htaccess` : gA re la rA A criture dA A A ?TURL pour le routage SPA et sA curise les headers HTTP

---

## Structure du projet

```
eco_ride/
|-- Assets/ # Feuilles de style SCSS/CSS, images
| |-- images/ # Images prA sentes sur le site
| |-- styles/ # Ensemble des fichiers et dossiers css
|
|-- Controleur/ # Scripts JS pour la logique (fetch, routage, actions)
| |-- CRUD_*/ # CRUD pour utilisateur, trajets, admin, covoiturages, employe
| |-- navbar/ # Logique de la barre de navigation (masquer et afficher)
| |-- Router/ # SystA me de routage et sécurité 
|
|-- Modele/ # Connexion BDD, requA tes SQL & MongoDB
A A A ", |-- CRUD_*/ # CRUD pour utilisateur, trajets, admin, covoiturages, employe, vehicule
A A A ", |-- mongodb/ # Connexion et enregistrements des logs MongoDB
| |-- db_connection.php # Connexion A MySQL
|
|-- uploads/ # Dossier de stockage des photos de profil
|
|-- Vue/ # Pages front (administrateur, connexion, covoiturage, employA , footer, accueil, utilisateur, 404, navigation )
A A A ", |-- admin/ # Page administrateur 
A A A ", |-- connexion/ # Page de connexion
A A A ", |-- covoit/ # Page des covoiturages
A A A ", |-- employe/ # Page employA 
| |-- footer/ # Page accessible depuis le footer (Mention Legale, Politique de confidentialitA )
| |-- home/ # Page d'accueil
| |-- user/ # Page espace utilisateur
| |-- 404.html # Page de redirection si erreur de chemin d'accA s ou pas de droit d'accA s
A A A ", |-- navbar.html # Barre de navigation
|
|-- .dockerignore # Exclusion des fichiers inutiles lors du build Docker
|-- .env # Variables sensibles (connexion BDD, SMTP, URI Mongo, etc.)
|-- .htaccess # RA A criture dA A A ?TURL pour le routeur (gestion par Apache dans le cadre de Docker)
|-- composer.json # BibliothA ques et packages nA cessaires
|-- composer.lock # Fixe les versions des dA pendances
|-- create_database.sql # Permet de crA er la base de donnA e en local (via une importation sur MySQL)
|-- Dockerfile # Configuration de lA A A ?Timage Apache + PHP + modules activA s
|-- docker-compose.yml # DA finition des services (PHP, MySQL, MongoDB)
|-- index.php # Page de base qui "encadre les autres pages" (les autres pages sont injA ctA es dynamiquement)
|-- README.md # Fichier actuel 
```

---

Pour plus dA A A ?Tinfos sur le projet, consulter le manuel utilisateur PDF et la documentation technique.

