<?php

declare(strict_types=1);

use App\Services\Database;

/* -------------------------------------------------- */
/* chargement automatique des classes */
/* -------------------------------------------------- */

/* Définit le chemin principal du projet. */

define('BASE_PATH', dirname(__DIR__));

/* Charge automatiquement les classes du dossier app. */
spl_autoload_register(function (string $className): void {
    $prefix = 'App\\';

    if (strpos($className, $prefix) !== 0) {
        return;
    }

    $relativeClassName = substr($className, strlen($prefix));

    $filePath = BASE_PATH
        . '/app/'
        . str_replace('\\', '/', $relativeClassName)
        . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    }
});


/* -------------------------------------------------- */
/* liste des images à importer */
/* -------------------------------------------------- */

/* Associe chaque plat à son fichier JPG. */
$dishImages = [
    'Tartare de saumon aux agrumes' => 'terroir-entree.jpg',
    'Magret de canard' => 'terroir-plat.jpg',
    'Canelé bordelais revisité' => 'terroir-dessert.jpg',

    'Burrata et légumes rôtis' => 'primeur-entree.jpg',
    'Risotto aux champignons' => 'primeur-plat.jpg',
    'Tarte fine aux pommes' => 'primeur-dessert.jpg',

    'Foie gras mi-cuit' => 'millesime-entree.jpg',
    'Filet de bœuf aux morilles' => 'millesime-minuit-plat.jpg',
    'Entremets chocolat noir' => 'millesime-floraison-dessert.jpg',

    'Houmous de betterave' => 'eclosion-entree.jpg',
    'Parmentier de patate douce' => 'eclosion-plat.jpg',
    'Mousse au chocolat noir' => 'eclosion-dessert.jpg',

    'Velouté de potimarron' => 'minuit-entree.jpg',
    'Bûche chocolat et praliné' => 'minuit-dessert.jpg',

    'Asperges rôties' => 'floraison-entree.jpg',
    'Ravioles printanières' => 'floraison-plat.jpg',
];


/* -------------------------------------------------- */
/* préparation de la requête SQL */
/* -------------------------------------------------- */

/* Récupère la connexion à MariaDB. */
$pdo = Database::getConnection();

/* Prépare la requête utilisée pour enregistrer une image. */
$sql = '
    UPDATE dishes
    SET
        photo = :photo,
        photo_mime_type = :photo_mime_type
    WHERE name = :name
';

$statement = $pdo->prepare($sql);


/* -------------------------------------------------- */
/* import des images dans MariaDB */
/* -------------------------------------------------- */

/* Parcourt chaque plat et importe sa photo. */
foreach ($dishImages as $dishName => $fileName) {
    $filePath = BASE_PATH
        . '/public/assets/images/menus/'
        . $fileName;

    /* Vérifie que le fichier existe. */
    if (!file_exists($filePath)) {
        echo 'Fichier introuvable : ' . $fileName . PHP_EOL;

        continue;
    }

    /* Lit le contenu du fichier JPG. */
    $photoContent = file_get_contents($filePath);

    /* Bloque l'import si la lecture échoue. */
    if ($photoContent === false) {
        echo 'Lecture impossible : ' . $fileName . PHP_EOL;

        continue;
    }

    /* Enregistre la photo dans la ligne du plat. */
    $statement->bindValue(
        ':photo',
        $photoContent,
        \PDO::PARAM_LOB
    );

    /* Enregistre le format de l'image. */
    $statement->bindValue(
        ':photo_mime_type',
        'image/jpeg'
    );

    /* Indique le nom du plat à modifier. */
    $statement->bindValue(
        ':name',
        $dishName
    );

    /* Envoie la requête à MariaDB. */
    $statement->execute();

    /* Affiche le résultat dans le terminal. */
    if ($statement->rowCount() === 0) {
        echo 'Plat introuvable ou image déjà identique : '
            . $dishName
            . PHP_EOL;

        continue;
    }

    echo 'Image importée : ' . $dishName . PHP_EOL;
}
