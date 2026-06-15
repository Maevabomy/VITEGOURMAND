<?php

namespace App\Controllers;

use App\Models\Dish;

class DishController
{
    /* -------------------------------------------------- */
    /* affichage de la photo d'un plat */
    /* -------------------------------------------------- */

    /* Retourne une photo stockée dans MariaDB. */
    public function image(): void
    {
        $dishId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        /* Bloque la requête si l'identifiant est invalide. */
        if (!$dishId) {
            http_response_code(400);

            echo 'Identifiant invalide.';

            return;
        }

        /* Recherche la photo correspondant au plat. */
        $dishImage = Dish::findImageById($dishId);

        /* Retourne une erreur si aucune photo n'est trouvée. */
        if ($dishImage === null) {
            http_response_code(404);

            echo 'Image introuvable.';

            return;
        }

        /* Indique au navigateur le format de la photo. */
        header(
            'Content-Type: '
            . $dishImage['photo_mime_type']
        );

        /* Autorise la mise en cache temporaire de la photo. */
        header('Cache-Control: public, max-age=3600');

        /* Affiche le contenu binaire de la photo. */
        echo $dishImage['photo'];
    }
}