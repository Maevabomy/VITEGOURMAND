<?php

namespace App\Controllers;

use App\Models\OpeningHour;

class LegalController
{
    /* Affiche les mentions légales. */
    public function legalNotice(): void
    {
        $openingHours =
            OpeningHour::getAll();

        $pageTitle =
            'Mentions légales';

        $view =
            BASE_PATH
            . '/app/Views/legal/legal-notice.php';

        require BASE_PATH
            . '/app/Views/layouts/main.php';
    }

    /* Affiche les conditions générales de vente. */
    public function terms(): void
    {
        $openingHours =
            OpeningHour::getAll();

        $pageTitle =
            'Conditions générales de vente';

        $view =
            BASE_PATH
            . '/app/Views/legal/terms.php';

        require BASE_PATH
            . '/app/Views/layouts/main.php';
    }
}