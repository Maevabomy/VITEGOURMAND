<?php

/* -------------------------------------------------- */
/* configuration de la base de données */
/* -------------------------------------------------- */

/*
 * Récupère l'URL de connexion fournie par JawsDB Maria
 * lorsque l'application est exécutée sur Heroku.
 */
$databaseUrl = getenv('JAWSDB_MARIA_URL');

/* -------------------------------------------------- */
/* environnement de production */
/* -------------------------------------------------- */

if (!empty($databaseUrl)) {
    /*
     * Décompose l'URL afin de récupérer séparément
     * l'hôte, le port, le nom de la base et les identifiants.
     */
    $databaseParts = parse_url($databaseUrl);

    return [
        /* Serveur MariaDB fourni par JawsDB. */
        'host' => $databaseParts['host'] ?? '',

        /* Port MariaDB fourni par JawsDB. */
        'port' => isset($databaseParts['port'])
            ? (string) $databaseParts['port']
            : '3306',

        /* Nom de la base MariaDB distante. */
        'dbname' => isset($databaseParts['path'])
            ? ltrim($databaseParts['path'], '/')
            : '',

        /* Encodage utilisé pour les échanges avec la base. */
        'charset' => 'utf8mb4',

        /* Identifiant de connexion MariaDB. */
        'username' => $databaseParts['user'] ?? '',

        /* Mot de passe de connexion MariaDB. */
        'password' => $databaseParts['pass'] ?? '',
    ];
}

/* -------------------------------------------------- */
/* environnement local */
/* -------------------------------------------------- */

return [
    /* Serveur MySQL utilisé par XAMPP. */
    'host' => 'localhost',

    /* Port de connexion à MySQL. */
    'port' => '3306',

    /* Nom de la base de données locale. */
    'dbname' => 'vite_gourmand',

    /* Encodage utilisé pour les échanges avec la base. */
    'charset' => 'utf8mb4',

    /* Identifiant de connexion MySQL local. */
    'username' => 'root',

    /* Mot de passe MySQL de l'environnement local. */
    'password' => '',
];
