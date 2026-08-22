<?php

/** @var array $user */

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Gestion de l’activité
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Bonjour
            <?php echo htmlspecialchars($user['first_name']); ?>.
            Accédez aux différents outils de gestion de
            Vite & Gourmand.
        </p>

    </div>
</section>

<?php

require BASE_PATH
    . '/app/Views/employee/_navigation.php';

?>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <div class="employee-management-heading mb-4">

            <p class="section-subtitle mb-2">
                Administration
            </p>

            <h2 class="employee-dashboard-card-title">
                Que souhaitez-vous gérer ?
            </h2>

        </div>

        <div class="employee-management-grid">

            <article class="employee-management-card">

                <div>
                    <p class="employee-management-card-label">
                        Prestations
                    </p>

                    <h3 class="employee-management-card-title">
                        Commandes
                    </h3>

                    <p class="employee-management-card-text">
                        Recherchez les commandes, consultez leur détail
                        et mettez à jour leur statut.
                    </p>
                </div>

                <a
                    href="<?php
                            echo BASE_URL
                                . '/employee/dashboard';
                            ?>"
                    class="btn btn-custom">
                    Gérer les commandes
                </a>

            </article>

            <article class="employee-management-card">

                <div>
                    <p class="employee-management-card-label">
                        Satisfaction client
                    </p>

                    <h3 class="employee-management-card-title">
                        Avis
                    </h3>

                    <p class="employee-management-card-text">
                        Consultez les avis en attente et choisissez
                        de les approuver ou de les refuser.
                    </p>
                </div>

                <a
                    href="<?php
                            echo BASE_URL
                                . '/employee/reviews';
                            ?>"
                    class="btn btn-custom">
                    Modérer les avis
                </a>

            </article>

            <article class="employee-management-card">

                <div>
                    <p class="employee-management-card-label">
                        Catalogue
                    </p>

                    <h3 class="employee-management-card-title">
                        Menus
                    </h3>

                    <p class="employee-management-card-text">
                        Gérez les menus proposés, leurs informations,
                        leurs plats et leur disponibilité.
                    </p>
                </div>

                <a
                    href="<?php echo BASE_URL; ?>/employee/menus"
                    class="btn btn-custom">
                    Gérer les menus
                </a>

            </article>

            <article class="employee-management-card">

                <div>
                    <p class="employee-management-card-label">
                        Cuisine
                    </p>

                    <h3 class="employee-management-card-title">
                        Plats
                    </h3>

                    <p class="employee-management-card-text">
                        Gérez les plats, leurs photos et les allergènes
                        associés.
                    </p>
                </div>

                <a
                    href="<?php echo BASE_URL; ?>/employee/dishes"
                    class="btn btn-custom">
                    Gérer les plats
                </a>

            </article>

            <article class="employee-management-card">

                <div>
                    <p class="employee-management-card-label">
                        Informations publiques
                    </p>

                    <h3 class="employee-management-card-title">
                        Horaires
                    </h3>

                    <p class="employee-management-card-text">
                        Modifiez les horaires visibles dans le pied
                        de page du site.
                    </p>
                </div>

                <a
                    href="<?php
                            echo BASE_URL
                                . '/employee/opening-hours';
                            ?>"
                    class="btn btn-custom">
                    Gérer les horaires
                </a>

            </article>

        </div>

    </div>
</section>