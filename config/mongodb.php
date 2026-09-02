<?php

/*
|--------------------------------------------------------------------------
| configuration MongoDB
|--------------------------------------------------------------------------
|
| En local, les informations sensibles sont lues depuis le fichier .env.
| En production, elles pourront être fournies directement par le serveur.
|
*/

$envPath = BASE_PATH . '/.env';

if (file_exists($envPath)) {
    $lines = file(
        $envPath,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {
        $line = trim($line);

        if (
            $line === ''
            || str_starts_with($line, '#')
            || !str_contains($line, '=')
        ) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value);

        if (getenv($name) === false) {
            putenv($name . '=' . $value);
        }
    }
}

return [
    'uri' => getenv('MONGODB_URI') ?: '',
    'database' => getenv('MONGODB_DATABASE') ?: 'vite_gourmand',

    'allow_invalid_certificates' =>
        getenv('MONGODB_ALLOW_INVALID_CERTIFICATES') === 'true',

    'tls_ca_file' =>
        getenv('MONGODB_TLS_CA_FILE') ?: '',
];