# Vite & Gourmand

Application web réalisée dans le cadre de l'ECF du titre professionnel **Développeur Web et Web Mobile**.

## Présentation

**Vite & Gourmand** est une entreprise fictive de traiteur située à Bordeaux.

L'application permet aux visiteurs de consulter les menus proposés par l'entreprise et aux clients inscrits de commander une prestation en ligne.

Elle comprend également :

- un espace client pour suivre et gérer ses commandes ;
- un espace employé pour gérer les commandes, menus, plats, horaires et avis ;
- un espace administrateur pour gérer les comptes employés et consulter les statistiques de l'entreprise.

Le projet repose sur une architecture **MVC en PHP orienté objet**, sans framework back-end.

---

## Fonctionnalités principales

### Partie publique

- présentation de l'entreprise ;
- affichage des horaires d'ouverture ;
- affichage des avis clients validés ;
- catalogue des menus ;
- filtres dynamiques sans rechargement de page ;
- filtre par prix ;
- filtre par thème ;
- filtre par régime alimentaire ;
- filtre par nombre de personnes ;
- détail complet d'un menu ;
- galerie d'images ;
- affichage des plats et allergènes ;
- affichage du délai minimum de commande ;
- affichage des périodes de disponibilité ;
- affichage du stock disponible ;
- formulaire de contact ;
- mentions légales ;
- conditions générales de vente.

### Authentification

- création d'un compte client ;
- connexion ;
- déconnexion ;
- contrôle de la robustesse des mots de passe ;
- réinitialisation du mot de passe par lien temporaire ;
- gestion des rôles `user`, `employee` et `admin`.

### Commandes

Un client authentifié peut :

- choisir un menu ;
- rechercher une adresse de livraison ;
- sélectionner la date de prestation ;
- sélectionner un créneau de livraison ;
- indiquer le nombre de personnes ;
- consulter le calcul du prix en temps réel ;
- bénéficier d'une réduction de 10 % à partir de 5 personnes supplémentaires par rapport au minimum du menu ;
- consulter les frais de livraison ;
- enregistrer sa commande ;
- recevoir une confirmation de commande.

Les frais de livraison sont calculés selon les règles suivantes :

- livraison à Bordeaux : gratuite ;
- hors Bordeaux : 5 € de frais fixes ;
- supplément de 0,59 € par kilomètre.

La recherche d'adresse utilise **OpenStreetMap / Nominatim** et le calcul de distance routière utilise **OSRM**.

### Espace client

Le client peut :

- consulter ses commandes ;
- consulter le détail d'une commande ;
- suivre l'évolution de son statut ;
- modifier une commande encore en attente ;
- annuler une commande encore en attente ;
- modifier ses informations personnelles ;
- laisser un avis après une commande terminée.

### Espace employé

L'employé peut :

- consulter et rechercher les commandes ;
- filtrer les commandes par statut ;
- consulter leur détail ;
- faire évoluer leur statut ;
- ajouter une note interne ;
- gérer le prêt et le retour de matériel ;
- annuler une commande après contact avec le client ;
- renseigner le moyen de contact et la raison de l'annulation ;
- gérer les horaires d'ouverture ;
- créer et modifier les menus ;
- activer ou désactiver les menus ;
- créer et modifier les plats ;
- gérer les allergènes ;
- activer ou désactiver les plats ;
- modérer les avis clients.

### Espace administrateur

L'administrateur dispose des fonctionnalités de gestion d'un employé et peut également :

- créer des comptes employés ;
- activer ou désactiver un compte employé ;
- consulter le nombre de commandes par menu ;
- consulter le chiffre d'affaires par menu ;
- filtrer le chiffre d'affaires par menu et par période.

Le nombre de commandes par menu est stocké et lu dans **MongoDB**.

Le chiffre d'affaires est calculé à partir des données enregistrées dans **MariaDB**.

---

## Technologies utilisées

### Front-end

- HTML5 ;
- CSS3 ;
- Bootstrap 5 ;
- JavaScript natif.

### Back-end

- PHP 8.4.x, orienté objet ;
- architecture MVC ;
- PDO ;
- sessions PHP ;
- cURL.

La branche PHP 8.4.x est imposée dans Composer avec la contrainte :

```json
"php": "~8.4.0"
```

### Bases de données

- **MariaDB** pour les données relationnelles ;
- **MongoDB Atlas** pour les statistiques non relationnelles.

### Services externes

- OpenStreetMap / Nominatim pour la recherche d'adresses ;
- OSRM pour le calcul des distances routières ;
- Brevo pour l'envoi des e-mails transactionnels ;
- MongoDB Atlas pour les statistiques non relationnelles.

### Outils de développement

- Docker ;
- Docker Compose ;
- XAMPP ;
- phpMyAdmin ;
- Git ;
- GitHub ;
- Visual Studio Code ;
- DBeaver pour l'administration et le contrôle de la base de données de production ;
- Trello pour la visualisation du suivi Kanban.

---

## Architecture du projet

```text
VITEGOURMAND/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   └── Views/
│       ├── admin/
│       ├── auth/
│       ├── contact/
│       ├── employee/
│       ├── home/
│       ├── layouts/
│       ├── legal/
│       ├── menus/
│       ├── orders/
│       └── user/
├── config/
├── database/
│   ├── schema.sql
│   ├── seed.sql
│   └── import-dish-images.php
├── docs/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── images/
│   │   └── js/
│   ├── .htaccess
│   └── index.php
├── routes/
├── storage/
│   ├── cache/
│   └── logs/
├── .dockerignore
├── .env.docker.example
├── .gitignore
├── compose.yaml
├── Dockerfile
├── composer.json
├── composer.lock
├── Procfile
└── README.md
```

### Rôle des principaux dossiers

- `app/Controllers/` : reçoit les requêtes et prépare les données ;
- `app/Models/` : communique avec les bases de données ;
- `app/Services/` : contient les services partagés de l'application ;
- `app/Views/` : contient les interfaces affichées aux utilisateurs ;
- `config/` : contient les paramètres de connexion et de configuration ;
- `database/` : contient les scripts de création, d'alimentation et d'import des données ;
- `public/` : contient le point d'entrée et les ressources publiques ;
- `routes/` : contient la déclaration des routes ;
- `storage/` : contient notamment le cache et les journaux locaux ;
- `docs/` : contient les documents associés au projet.

### Fichiers Docker

- `Dockerfile` : construit l'environnement PHP 8.4 / Apache de l'application ;
- `compose.yaml` : définit les services Docker du projet ;
- `.dockerignore` : exclut les fichiers inutiles ou sensibles du contexte de construction Docker ;
- `.env.docker.example` : fournit un modèle de variables d'environnement sans données sensibles.

---

# Installation locale avec Docker

L'utilisation de Docker est la méthode recommandée pour reproduire l'environnement du projet sur une autre machine.

Elle permet de lancer l'application sans devoir installer séparément PHP, Apache, MariaDB et phpMyAdmin.

## Prérequis

Pour utiliser le projet avec Docker :

- Docker Desktop ou Docker Engine avec Docker Compose ;
- Git ;
- un navigateur web ;
- une connexion Internet pour MongoDB Atlas, Brevo, Nominatim et OSRM.

---

## 1. Récupérer le projet

Cloner le dépôt :

```bash
git clone https://github.com/Maevabomy/VITEGOURMAND.git
```

Puis entrer dans le dossier :

```bash
cd VITEGOURMAND
```

---

## 2. Préparer les variables d'environnement

Un fichier d'exemple est fourni :

```text
.env.docker.example
```

Le copier en :

```text
.env.docker
```

Par exemple :

```bash
cp .env.docker.example .env.docker
```

Puis remplacer les valeurs fictives par les informations correspondant à l'environnement utilisé.

Exemple :

```env
# Configuration MariaDB pour Docker uniquement
DB_NAME=vite_gourmand
DB_USER=vitegourmand
DB_PASSWORD=change_me
DB_ROOT_PASSWORD=change_me

# Configuration Brevo pour Docker uniquement
BREVO_API_KEY=your_brevo_api_key
BREVO_SENDER_EMAIL=your_verified_sender@example.com

# Configuration de l'application
APP_URL=http://localhost:8080
CONTACT_RECIPIENT_EMAIL=contact@example.com

# Configuration MongoDB pour Docker uniquement
MONGODB_URI=mongodb+srv://username:password@cluster/
MONGODB_DATABASE=vite_gourmand
MONGODB_ALLOW_INVALID_CERTIFICATES=false
MONGODB_TLS_CA_FILE=
```

Le fichier `.env.docker` contient des informations sensibles.

Il est ignoré par Git et ne doit jamais être envoyé sur le dépôt public.

Le fichier `.env.docker.example`, au contraire, ne contient aucune donnée sensible et sert uniquement de modèle.

---

## 3. Configuration de MongoDB Atlas

L'application utilise MongoDB Atlas pour stocker le nombre de commandes par menu utilisé dans les statistiques administrateur.

Les variables suivantes doivent être renseignées dans `.env.docker` :

```env
MONGODB_URI=...
MONGODB_DATABASE=vite_gourmand
MONGODB_ALLOW_INVALID_CERTIFICATES=false
MONGODB_TLS_CA_FILE=
```

`MONGODB_ALLOW_INVALID_CERTIFICATES` doit rester à `false` dans l'environnement Docker normal.

La connexion nécessite également que le cluster MongoDB Atlas autorise la connexion depuis l'environnement utilisé.

Selon la configuration du cluster, l'adresse IP du poste ou du réseau utilisé peut devoir être ajoutée dans les règles d'accès réseau de MongoDB Atlas.

La collection utilisée par l'application est :

```text
menu_order_statistics
```

---

## 4. Configuration de Brevo

L'application utilise l'API Brevo pour les e-mails transactionnels lorsque les variables suivantes sont configurées :

```env
BREVO_API_KEY=...
BREVO_SENDER_EMAIL=...
```

`BREVO_SENDER_EMAIL` doit correspondre à une adresse expéditrice autorisée dans Brevo.

L'adresse qui reçoit les messages envoyés depuis le formulaire de contact est définie avec :

```env
CONTACT_RECIPIENT_EMAIL=...
```

L'adresse de base utilisée pour générer certains liens présents dans les e-mails est définie avec :

```env
APP_URL=http://localhost:8080
```

Les clés et identifiants Brevo ne sont jamais enregistrés dans le dépôt GitHub.

---

## 5. Démarrer l'environnement Docker

Depuis la racine du projet :

```bash
docker compose --env-file .env.docker up -d --build
```

Cette commande construit et démarre les services nécessaires.

Le projet utilise les services Docker suivants :

### `app`

Application PHP 8.4 avec Apache.

Accessible sur :

```text
http://localhost:8080
```

Le port est exposé uniquement sur l'interface locale :

```text
127.0.0.1:8080
```

### `db`

Base de données :

```text
MariaDB 10.11
```

Le port MariaDB n'est pas exposé directement vers l'extérieur de l'environnement Docker.

### `phpmyadmin`

Interface phpMyAdmin :

```text
phpMyAdmin 5.2.2
```

Accessible sur :

```text
http://localhost:8081
```

L'interface est également limitée à l'adresse locale :

```text
127.0.0.1:8081
```

Le serveur MariaDB à indiquer dans phpMyAdmin est :

```text
db
```

### `import-images`

Service ponctuel chargé d'importer automatiquement les images des plats dans MariaDB.

Il s'exécute après le démarrage de la base de données, puis s'arrête normalement lorsque l'import est terminé.

---

## 6. Initialisation automatique de MariaDB

Lors de la première création du volume MariaDB, Docker exécute automatiquement :

```text
database/schema.sql
database/seed.sql
```

`schema.sql` :

- crée la base `vite_gourmand` ;
- crée les tables ;
- crée les clés étrangères ;
- crée les contraintes nécessaires.

`seed.sql` ajoute notamment :

- les rôles ;
- les horaires ;
- les thèmes ;
- les régimes alimentaires ;
- les statuts de commande ;
- les comptes de démonstration ;
- les menus ;
- les plats ;
- les allergènes ;
- les associations entre menus et plats ;
- les commandes fictives ;
- les avis clients validés.

Le jeu de données de démonstration final contient notamment :

- 4 utilisateurs ;
- 3 commandes ;
- 2 avis validés ;
- 7 menus ;
- 17 plats.

Le schéma et le jeu de données ont été testés sur une base Docker vierge afin de vérifier la reconstruction complète de l'environnement.

---

## 7. Import automatique des images des plats

Les photos des plats sont stockées dans MariaDB sous forme de BLOB.

Les fichiers sources sont présents dans :

```text
public/assets/images/menus/
```

Lors du démarrage de l'environnement Docker, le service :

```text
import-images
```

exécute automatiquement :

```text
database/import-dish-images.php
```

Les images sont enregistrées dans les colonnes :

```text
photo
photo_mime_type
```

de la table :

```text
dishes
```

L'import a été testé sur une base Docker vierge et permet l'import automatique des 17 images du jeu de démonstration.

---

## 8. Vérifier l'état des conteneurs

Pour consulter l'état des services :

```bash
docker compose --env-file .env.docker ps -a
```

Le service `db` doit apparaître comme sain :

```text
healthy
```

Le service `import-images` termine normalement son exécution avec :

```text
Exited (0)
```

---

## 9. Arrêter Docker

Pour arrêter les conteneurs sans supprimer les données MariaDB :

```bash
docker compose --env-file .env.docker down
```

Les données sont conservées dans le volume Docker.

Pour supprimer également le volume MariaDB et recréer ensuite une base entièrement vierge :

```bash
docker compose --env-file .env.docker down -v
```

Attention : cette dernière commande supprime les données enregistrées dans la base Docker locale.

---

# Installation locale avec XAMPP

Le projet a initialement été développé sous macOS avec XAMPP.

Cette méthode reste utilisable en alternative à Docker.

## Prérequis

Pour utiliser le projet avec XAMPP :

- PHP 8.4.x ;
- Apache ;
- MariaDB / MySQL ;
- XAMPP ou environnement équivalent ;
- phpMyAdmin ou un autre client SQL ;
- extension PHP PDO MySQL (`pdo_mysql`) ;
- extension PHP cURL ;
- extension PHP MongoDB ;
- extension PHP mbstring ;
- un navigateur web ;
- une connexion Internet pour MongoDB Atlas, Nominatim et OSRM.

---

## 1. Installer le projet dans XAMPP

Copier le projet dans le dossier `htdocs`.

Sous macOS avec l'installation utilisée pendant le développement :

```text
/Applications/XAMPP/xamppfiles/htdocs/VITEGOURMAND
```

---

## 2. Démarrer XAMPP

Démarrer :

- Apache ;
- MySQL.

---

## 3. Créer la base MariaDB

Depuis phpMyAdmin, importer :

```text
database/schema.sql
```

Puis importer :

```text
database/seed.sql
```

---

## 4. Importer les images

Depuis la racine du projet :

```bash
/Applications/XAMPP/xamppfiles/bin/php database/import-dish-images.php
```

---

## 5. Configurer MongoDB

Dans l'environnement XAMPP historique, les variables MongoDB peuvent être définies dans un fichier :

```text
.env
```

à la racine du projet.

Exemple :

```env
MONGODB_URI=mongodb+srv://UTILISATEUR:MOT_DE_PASSE@CLUSTER/
MONGODB_DATABASE=vite_gourmand
MONGODB_ALLOW_INVALID_CERTIFICATES=false
MONGODB_TLS_CA_FILE=
```

Le fichier `.env` est ignoré par Git.

Dans certains environnements XAMPP rencontrant un problème de validation TLS, un fichier de certificat CA local peut être défini avec `MONGODB_TLS_CA_FILE`.

Le contournement de la validation des certificats ne doit être utilisé que temporairement pour diagnostiquer un environnement local.

---

## 6. Ouvrir l'application

Dans le navigateur :

```text
http://localhost/VITEGOURMAND/public/
```

---

## Routes principales

### Partie publique

```text
/
```

Accueil.

```text
/menus
```

Catalogue des menus.

```text
/menus/detail?id=1
```

Détail d'un menu.

```text
/contact
```

Formulaire de contact.

```text
/mentions-legales
```

Mentions légales.

```text
/conditions-generales-vente
```

Conditions générales de vente.

### Authentification

```text
/register
/login
/forgot-password
/reset-password
```

### Espace client

```text
/user/dashboard
/user/order/detail
/user/order/edit
/user/profile/edit
```

### Espace employé

```text
/employee
/employee/dashboard
/employee/menus
/employee/dishes
/employee/opening-hours
/employee/reviews
```

### Espace administrateur

```text
/admin
/admin/employees
/admin/statistics
```

Les routes employé, administrateur et client sont protégées selon le rôle de l'utilisateur connecté.

---

## Gestion des e-mails

L'application prévoit notamment l'envoi :

- d'un mail de bienvenue ;
- d'un lien de réinitialisation du mot de passe ;
- d'une confirmation de commande ;
- des informations de suivi de commande ;
- d'une confirmation d'annulation ;
- d'une invitation à laisser un avis ;
- d'un message lors de la création d'un compte employé ;
- des demandes issues du formulaire de contact.

Lorsque les variables suivantes sont disponibles :

```text
BREVO_API_KEY
BREVO_SENDER_EMAIL
```

l'application utilise l'API Brevo pour envoyer les e-mails transactionnels.

Cette configuration peut être utilisée aussi bien dans l'environnement Docker que dans l'environnement de production.

Si Brevo n'est pas configuré, l'application utilise la fonction PHP `mail()` comme mécanisme de secours. Selon l'environnement local, cette fonction peut ne pas disposer d'un serveur d'envoi configuré ; les tentatives sont alors enregistrées dans les journaux locaux.

Une copie des e-mails générés par ce fonctionnement local peut être enregistrée dans :

```text
storage/logs/emails.log
```

Le dossier `storage/logs` est préparé dans l'environnement Docker avec les permissions nécessaires à Apache/PHP.

Les fichiers de logs ne doivent pas contenir de secrets et ne sont pas destinés à être versionnés.

---

## Sécurité

Plusieurs mesures de sécurité sont intégrées :

- requêtes PDO préparées ;
- mots de passe enregistrés avec `password_hash()` ;
- vérification avec `password_verify()` ;
- protection CSRF des formulaires sensibles ;
- contrôle des rôles ;
- contrôle des accès aux espaces privés ;
- régénération de l'identifiant de session lors de la connexion ;
- cookies de session configurés avec `HttpOnly`, `SameSite=Lax` et `Secure` lorsque l'application est servie en HTTPS ;
- liens de réinitialisation de mot de passe temporaires ;
- contrôle des fichiers image enregistrés ;
- échappement des données affichées avec `htmlspecialchars()`.

Les secrets de connexion et clés API ne sont jamais enregistrés dans le dépôt GitHub.

Les fichiers suivants sont notamment ignorés par Git :

```text
.env
.env.docker
```

Le fichier `.dockerignore` empêche également les fichiers d'environnement sensibles d'être intégrés dans l'image Docker.

Les ports HTTP Docker sont liés à `127.0.0.1` afin de ne pas exposer directement l'application ou phpMyAdmin aux autres machines du réseau local pendant le développement.

---

## Accessibilité

Des contrôles d'accessibilité ont été réalisés sur les principales pages de l'application avec **Lighthouse**, en affichage desktop et mobile.

Les pages principales testées ont obtenu un score de **100/100 en accessibilité après correction des anomalies détectées**.

Les contrôles ont notamment porté sur :

- la page d'accueil ;
- le catalogue et le détail des menus ;
- l'inscription et la connexion ;
- le formulaire de contact ;
- le parcours de commande ;
- l'espace client ;
- l'espace employé ;
- l'espace administrateur.

Des vérifications manuelles ont également été réalisées concernant :

- la navigation au clavier avec `Tab` et `Shift + Tab` ;
- l'ordre logique de tabulation ;
- la visibilité du focus ;
- l'utilisation des boutons, liens, filtres et formulaires sans souris ;
- l'association des champs de formulaire avec leurs libellés ;
- la compréhension des messages d'erreur ;
- le contraste des textes et des composants ;
- l'utilisation de la page avec un zoom navigateur à 200 % ;
- l'affichage responsive sur mobile ;
- l'utilisation pertinente des attributs ARIA.

Plusieurs corrections ont été apportées à la suite de ces tests, notamment sur les zones cliquables du carrousel, certains contrastes, le reflow des filtres, les propositions d'adresses et les informations ARIA du graphique de statistiques.

Ces contrôles permettent de prendre en compte plusieurs recommandations d'accessibilité, mais **ne constituent pas un audit complet ni une certification de conformité au RGAA**.

---

## Gestion du stock

Le stock d'un menu représente le nombre de portions / personnes encore disponibles.

Lors d'une commande :

- le nombre de portions commandées est retiré du stock ;
- une modification recalcule la différence de stock ;
- une annulation restitue les portions correspondantes.

---

## Gestion du matériel

Lorsqu'une commande comprend un prêt de matériel :

- le retour du matériel est suivi dans l'espace professionnel ;
- le délai maximal de restitution est fixé à 10 jours ouvrés après la prestation ;
- les conditions générales de vente prévoient une pénalité forfaitaire de 600 € en cas de non-restitution dans le délai prévu.

---

## Organisation Git

Le projet utilise une organisation basée sur plusieurs branches :

```text
main
develop
feature/...
```

### Rôle des branches

- `main` : version stable du projet ;
- `develop` : intégration des fonctionnalités terminées et testées ;
- `feature/...` : développement isolé d'une fonctionnalité.

Le flux de travail utilisé est :

```text
feature/... → develop → main
```

Les fonctionnalités sont développées et vérifiées avant leur intégration dans la branche suivante.

---

## Base de données relationnelle

MariaDB contient notamment les tables :

```text
roles
users
password_reset_tokens
opening_hours
themes
dietary_types
menus
dishes
allergens
dish_allergen
menu_dish
order_statuses
orders
order_status_history
reviews
contact_requests
```

---

## Base de données non relationnelle

MongoDB contient la collection :

```text
menu_order_statistics
```

Elle permet de stocker le nombre de commandes par menu utilisé dans les statistiques administrateur.

---

## Dépôt GitHub

Le dépôt public du projet est disponible ici :

https://github.com/Maevabomy/VITEGOURMAND

---

## Déploiement

L'application est déployée sur **Heroku**.

URL de production :

https://vite-gourmand-ecf-ad74809e59a7.herokuapp.com/

L'environnement de production utilise :

- **Heroku** pour l'hébergement de l'application PHP ;
- **JawsDB Maria** pour la base de données relationnelle MariaDB ;
- **MongoDB Atlas** pour la base de données non relationnelle ;
- **Brevo** pour l'envoi des e-mails transactionnels ;
- des variables d'environnement Heroku pour stocker les informations sensibles de connexion.

Le fichier :

```text
Procfile
```

indique à Heroku de servir l'application depuis le dossier :

```text
public/
```

Le déploiement Heroku est indépendant de l'environnement Docker local.

Docker est utilisé afin de fournir un environnement local reproductible pour le développement, les tests et la démonstration du projet.

La branche PHP utilisée par le projet est verrouillée sur PHP 8.4.x avec Composer afin d'éviter un passage automatique vers PHP 8.5 ou une version ultérieure avant validation du projet.

Les identifiants, mots de passe, clés API et chaînes de connexion ne sont jamais enregistrés dans le dépôt GitHub.

---

## Liens utiles

- [Application Vite & Gourmand en production](https://vite-gourmand-ecf-ad74809e59a7.herokuapp.com/)
- [Dépôt GitHub](https://github.com/Maevabomy/VITEGOURMAND)
- [Tableau Trello - Gestion de projet](https://trello.com/b/UX1OnscC/vite-gourmand-gestion-de-projet)
- [Documentation technique](docs/Documentation_technique/Documentation_technique.pdf)
- [Manuel utilisateur](docs/Manuel_utilisateur/Manuel_utilisateur.pdf)
- [Documentation de gestion de projet](docs/documentation_gestion_de_projet/Documentation_gestion_de_projet.pdf)
- [Charte graphique](docs/Charte_graphique/Charte_graphique.pdf)
- [Wireframes et mockups](docs/Mockups_et_Wireframe/)

Les identifiants des comptes de démonstration sont disponibles dans le manuel utilisateur.

---

## Contexte du projet

Vite & Gourmand est un projet pédagogique réalisé dans le cadre d'une évaluation de formation.

Les données, personnes, adresses, commandes, avis et informations commerciales utilisées pour les démonstrations sont fictives.
