<?php

/** @var array $admin */

?>

<!-- -------------------------------------------------- -->
<!-- présentation du tableau de bord administrateur -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Administration
        </p>

        <h1 class="employee-dashboard-title">
            Tableau de bord administrateur
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Gérez les comptes employés et accédez aux outils
            d’administration de Vite & Gourmand.
        </p>

    </div>
</section>

<?php

/* Charge la navigation de l'espace administrateur. */
require BASE_PATH
    . '/app/Views/admin/_navigation.php';

?>

<!-- -------------------------------------------------- -->
<!-- accès aux outils d'administration -->
<!-- -------------------------------------------------- -->

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

            <!-- -------------------------------------------------- -->
            <!-- gestion des employés -->
            <!-- -------------------------------------------------- -->

            <article class="employee-management-card">

                <div>

                    <p class="employee-management-card-label">
                        Équipe
                    </p>

                    <h3 class="employee-management-card-title">
                        Comptes employés
                    </h3>

                    <p class="employee-management-card-text">
                        Créez les accès des employés et désactivez
                        les comptes qui ne doivent plus être utilisés.
                    </p>

                </div>

                <a
                    href="<?php
                            echo BASE_URL;
                            ?>/admin/employees"
                    class="btn btn-custom">
                    Gérer les employés
                </a>

            </article>

            <!-- -------------------------------------------------- -->
            <!-- statistiques -->
            <!-- -------------------------------------------------- -->

            <article class="employee-management-card">

                <div>

                    <p class="employee-management-card-label">
                        Analyse
                    </p>

                    <h3 class="employee-management-card-title">
                        Statistiques
                    </h3>

                    <p class="employee-management-card-text">
                        Comparez les commandes et le chiffre
                        d’affaires des différents menus.
                    </p>

                </div>

                <a
                    href="<?php
                            echo BASE_URL;
                            ?>/admin/statistics"
                    class="btn btn-custom">
                    Voir les statistiques
                </a>

            </article>


            <!-- -------------------------------------------------- -->
            <!-- accès à l'espace employé -->
            <!-- -------------------------------------------------- -->

            <article class="employee-management-card">

                <div>

                    <p class="employee-management-card-label">
                        Activité
                    </p>

                    <h3 class="employee-management-card-title">
                        Espace employé
                    </h3>

                    <p class="employee-management-card-text">
                        Accédez aux commandes, avis, menus,
                        plats et horaires de l’entreprise.
                    </p>

                </div>

                <a
                    href="<?php echo BASE_URL; ?>/employee"
                    class="btn btn-custom">
                    Accéder à l’espace employé
                </a>

            </article>

        </div>

    </div>
</section>