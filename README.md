# Vite & Gourmand

Application web réalisée dans le cadre de l'ECF du titre professionnel Développeur Web et Web Mobile.

## Présentation

Vite & Gourmand est une entreprise de traiteur située à Bordeaux.

L'application permet de présenter les menus proposés par l'entreprise, de faciliter leur consultation et, à terme, de permettre aux clients de passer leurs commandes en ligne.

Le projet est développé progressivement en respectant une architecture MVC simple et une organisation Git par fonctionnalités.

## Technologies utilisées

### Front-end

- HTML5
- CSS3
- Bootstrap 5
- JavaScript

### Back-end

- PHP natif orienté objet
- Architecture MVC
- PDO

### Bases de données

- MariaDB pour les données relationnelles
- MongoDB pour les statistiques demandées dans l'espace administrateur

> La partie MongoDB sera ajoutée lors du développement de l'espace administrateur.

## Prérequis

Pour utiliser le projet en local, il est nécessaire d'installer :

- XAMPP ;
- PHP ;
- MariaDB ;
- un navigateur web ;
- Git.

Le projet a été développé localement avec XAMPP sur macOS.

## Installation locale avec XAMPP

### 1. Placer le projet dans le dossier XAMPP

Copier le dossier du projet dans :


/Applications/XAMPP/xamppfiles/htdocs/VITEGOURMAND

2. Démarrer les services

Depuis XAMPP, démarrer :

Apache ;
MySQL.
3. Créer la base de données

Depuis phpMyAdmin, importer le fichier :

database/schema.sql

Ce fichier :

supprime l'ancienne base si elle existe ;
crée la base vite_gourmand ;
crée les tables nécessaires.
4. Importer les données fictives

Depuis phpMyAdmin, importer ensuite :

database/seed.sql

Ce fichier ajoute les données utiles pour tester l'application :

rôles ;
horaires ;
thèmes ;
régimes ;
utilisateurs fictifs ;
menus ;
plats ;
allergènes ;
commandes ;
avis clients.
5. Importer les images des plats dans MariaDB

Les images des plats sont stockées dans MariaDB sous forme de BLOB.

Depuis la racine du projet, exécuter :

/Applications/XAMPP/xamppfiles/bin/php database/import-dish-images.php

Le script lit les fichiers JPG présents dans :

public/assets/images/menus/

puis les enregistre dans la colonne photo de la table dishes.

6. Ouvrir l'application

Dans le navigateur, ouvrir :

http://localhost/VITEGOURMAND/public/
Routes disponibles
Page d'accueil
http://localhost/VITEGOURMAND/public/
Catalogue des menus
http://localhost/VITEGOURMAND/public/menus
Exemple d'affichage d'une image enregistrée en BLOB
http://localhost/VITEGOURMAND/public/dish/image?id=1
Architecture du projet
VITEGOURMAND/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   └── Views/
├── config/
├── database/
├── docs/
├── public/
│   ├── assets/
│   └── index.php
├── routes/
├── .gitignore
└── README.md
Dossiers principaux
app/Controllers/ : prépare les données et choisit les vues à afficher ;
app/Models/ : communique avec la base de données ;
app/Services/ : contient les services partagés, comme la connexion PDO et le routeur ;
app/Views/ : contient les pages visibles par l'utilisateur ;
config/ : contient la configuration de la base de données ;
database/ : contient les scripts SQL et l'import des images ;
public/ : contient le point d'entrée de l'application et les fichiers accessibles par le navigateur ;
routes/ : contient les routes de l'application.
Fonctionnalités développées
Socle technique
point d'entrée unique dans public/index.php ;
autoload simple des classes PHP ;
routeur ;
connexion PDO à MariaDB ;
architecture MVC ;
layout commun avec header et footer.
Page d'accueil
présentation de l'entreprise ;
mise en avant du savoir-faire ;
horaires récupérés depuis MariaDB ;
avis clients validés récupérés depuis MariaDB ;
carrousel Bootstrap pour les avis.
Catalogue public des menus
récupération des menus depuis MariaDB ;
affichage des plats associés à chaque menu ;
gestion des plats partagés entre plusieurs menus ;
stockage des images de plats dans MariaDB sous forme de BLOB ;
mini-carrousel de photos pour chaque menu ;
affichage du thème ;
affichage du régime ;
affichage du prix total minimum ;
affichage du prix indicatif par personne ;
affichage du nombre minimum de convives ;
filtres dynamiques sans rechargement de page ;
filtrage par prix minimum et prix maximum ;
filtrage par thème ;
filtrage par régime ;
filtrage selon le nombre de convives ;
compteur dynamique des résultats ;
bouton de réinitialisation.
Organisation Git

Le projet utilise plusieurs branches :

main
develop
feature/menu-catalog
Rôle des branches
main : contient les versions stables principales ;
develop : regroupe les fonctionnalités terminées et testées ;
feature/... : permet de développer une fonctionnalité isolée avant sa fusion dans develop.

Chaque fonctionnalité est développée et testée sur une branche dédiée avant d'être fusionnée dans develop.

# Dépôt GitHub

Le dépôt public du projet est disponible à cette adresse :

https://github.com/Maevabomy/VITEGOURMAND