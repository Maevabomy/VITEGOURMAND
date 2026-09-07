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

L'administrateur dispose des fonctionnalités de gestion et peut également :

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

- PHP 8 orienté objet ;
- architecture MVC ;
- PDO ;
- sessions PHP ;
- cURL.

### Bases de données

- **MariaDB** pour les données relationnelles ;
- **MongoDB Atlas** pour les statistiques non relationnelles.

### Services externes

- OpenStreetMap / Nominatim pour la recherche d'adresses ;
- OSRM pour le calcul des distances routières.

### Outils de développement

- XAMPP ;
- phpMyAdmin ;
- Git ;
- GitHub ;
- Visual Studio Code.

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
├── docs/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── images/
│   │   └── js/
│   ├── .htaccess
│   └── index.php
├── routes/
├── .gitignore
└── README.md
```

### Rôle des principaux dossiers

- `app/Controllers/` : reçoit les requêtes et prépare les données ;
- `app/Models/` : communique avec les bases de données ;
- `app/Services/` : contient les services partagés de l'application ;
- `app/Views/` : contient les interfaces affichées aux utilisateurs ;
- `config/` : contient les paramètres de connexion ;
- `database/` : contient les scripts de création et d'alimentation de la base ;
- `public/` : contient le point d'entrée et les ressources publiques ;
- `routes/` : contient la déclaration des routes ;
- `docs/` : contient les documents associés au projet.

---

# Installation locale

## Prérequis

Le projet a été développé sous macOS avec XAMPP.

Pour l'utiliser en local, il faut disposer de :

- PHP 8 ;
- Apache ;
- MariaDB / MySQL ;
- XAMPP ou environnement équivalent ;
- phpMyAdmin ou un autre client SQL ;
- extension PHP PDO MySQL ;
- extension PHP cURL ;
- extension PHP MongoDB ;
- un navigateur web ;
- une connexion Internet pour MongoDB Atlas, Nominatim et OSRM ;
- Git si le projet est récupéré depuis GitHub.

---

## 1. Récupérer le projet

Cloner le dépôt :

```bash
git clone https://github.com/Maevabomy/VITEGOURMAND.git
```

Ou copier le dossier du projet dans le dossier `htdocs` de XAMPP.

Sous macOS avec l'installation utilisée pendant le développement :

```text
/Applications/XAMPP/xamppfiles/htdocs/VITEGOURMAND
```

---

## 2. Démarrer XAMPP

Démarrer les services :

- Apache ;
- MySQL.

---

## 3. Créer la base MariaDB

Depuis phpMyAdmin, importer :

```text
database/schema.sql
```

Ce fichier :

- supprime la base précédente si elle existe ;
- crée la base `vite_gourmand` ;
- crée les tables ;
- crée les clés étrangères et les contraintes nécessaires.

---

## 4. Importer les données de démonstration

Importer ensuite :

```text
database/seed.sql
```

Ce fichier ajoute notamment :

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

---

## 5. Importer les images des plats

Les photos des plats sont enregistrées directement dans MariaDB sous forme de BLOB.

Depuis la racine du projet :

```bash
/Applications/XAMPP/xamppfiles/bin/php database/import-dish-images.php
```

Le script utilise les images présentes dans :

```text
public/assets/images/menus/
```

et les enregistre dans les colonnes `photo` et `photo_mime_type` de la table `dishes`.

---

## 6. Configurer MongoDB

L'application utilise MongoDB pour les statistiques du nombre de commandes par menu.

Créer un fichier :

```text
.env
```

à la racine du projet.

Le fichier `.env` est ignoré par Git et ne doit jamais être envoyé sur le dépôt public.

Exemple de configuration :

```env
MONGODB_URI=mongodb+srv://UTILISATEUR:MOT_DE_PASSE@CLUSTER/
MONGODB_DATABASE=vite_gourmand
MONGODB_ALLOW_INVALID_CERTIFICATES=false
MONGODB_TLS_CA_FILE=/chemin/vers/cacert.pem
```

La valeur réelle de `MONGODB_URI` doit correspondre au cluster MongoDB Atlas utilisé.

`MONGODB_ALLOW_INVALID_CERTIFICATES` doit rester à `false` en production.

Dans certains environnements XAMPP locaux rencontrant un problème de validation TLS, cette option peut être activée temporairement pour le développement local uniquement.

Les statistiques sont synchronisées depuis MariaDB vers la collection MongoDB :

```text
menu_order_statistics
```

---

## 7. Ouvrir l'application

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

En environnement local, une copie des e-mails générés est enregistrée dans :

```text
storage/logs/emails.log
```

Ce fichier est ignoré par Git.

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
- cookies de session configurés avec `HttpOnly` et `SameSite=Lax` ;
- liens de réinitialisation de mot de passe temporaires ;
- contrôle des fichiers image enregistrés ;
- échappement des données affichées avec `htmlspecialchars()`.

Les secrets de connexion ne sont pas enregistrés sur GitHub.

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

Ces contrôles permettent de prendre en compte plusieurs recommandations d'accessibilité, mais **ne constituent pas un audit complet ni une certification de conformité au RGAA**.n

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

L'application sera déployée sur un hébergement accessible publiquement.

L'URL de production sera ajoutée ici après la mise en ligne.

```text
URL de production : à renseigner
```

---

## Contexte du projet

Vite & Gourmand est un projet pédagogique réalisé dans le cadre d'une évaluation de formation.

Les données, personnes, adresses, commandes, avis et informations commerciales utilisées pour les démonstrations sont fictives.