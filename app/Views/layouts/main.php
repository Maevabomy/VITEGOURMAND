<?php

/** @var string $view */


/* -------------------------------------------------- */
/* chargement de la vue */
/* -------------------------------------------------- */

/* Charge l'en-tête commun du site. */
require BASE_PATH
    . '/app/Views/layouts/header.php';

/* Charge le contenu de la page demandée. */
require $view;

/* Charge le pied de page commun du site. */
require BASE_PATH
    . '/app/Views/layouts/footer.php';
