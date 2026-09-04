<?php

/* -------------------------------------------------- */
/* configuration de MongoDB */
/* -------------------------------------------------- */

/* Définit le chemin du fichier d'environnement local. */
$envPath = BASE_PATH . '/.env';

/* Charge les variables du fichier .env lorsqu'il existe. */
if (file_exists($envPath)) {
    $lines = file(
        $envPath,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {
        $line = trim($line);

        /* Ignore les lignes vides, les commentaires et les lignes invalides. */
        if (
            $line === ''
            || str_starts_with($line, '#')
            || !str_contains($line, '=')
        ) {
            continue;
        }

        /* Sépare le nom de la variable de sa valeur. */
        [$name, $value] = explode(
            '=',
            $line,
            2
        );

        $name = trim($name);
        $value = trim($value);

        /* Ajoute la variable uniquement si elle n'existe pas déjà. */
        if (getenv($name) === false) {
            putenv(
                $name . '=' . $value
            );
        }
    }
}

/* -------------------------------------------------- */
/* paramètres de connexion */
/* -------------------------------------------------- */

return [
    /* URI de connexion au cluster MongoDB. */
    'uri' =>
    getenv('MONGODB_URI') ?: '',

    /* Nom de la base de données MongoDB. */
    'database' =>
    getenv('MONGODB_DATABASE')
        ?: 'vite_gourmand',

    /* Autorise le contournement TLS uniquement lorsqu'il est activé. */
    'allow_invalid_certificates' =>
    getenv('MONGODB_ALLOW_INVALID_CERTIFICATES')
        === 'true',

    /* Chemin vers le certificat utilisé pour la connexion TLS. */
    'tls_ca_file' =>
    getenv('MONGODB_TLS_CA_FILE') ?: '',
];
