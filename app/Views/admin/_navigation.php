<?php

/* -------------------------------------------------- */
/* détection de la page active */
/* -------------------------------------------------- */

/* Récupère le chemin actuellement affiché. */
$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '',
    PHP_URL_PATH
);

/* Construit le chemin de la page d'accueil administrateur. */
$adminHomePath = parse_url(
    BASE_URL . '/admin',
    PHP_URL_PATH
);

/* Construit le chemin de la gestion des employés. */
$adminEmployeesPath = parse_url(
    BASE_URL . '/admin/employees',
    PHP_URL_PATH
);

/* Construit le chemin de la page des statistiques. */
$adminStatisticsPath = parse_url(
    BASE_URL . '/admin/statistics',
    PHP_URL_PATH
);

/* Indique si la page d'accueil administrateur est active. */
$isAdminHomePage =
    $currentPath === $adminHomePath;

/* Indique si la page des employés est active. */
$isAdminEmployeesPage =
    $currentPath === $adminEmployeesPath;

/* Indique si la page des statistiques est active. */
$isAdminStatisticsPage =
    $currentPath === $adminStatisticsPath;
?>

<!-- -------------------------------------------------- -->
<!-- navigation administrateur -->
<!-- -------------------------------------------------- -->

<nav
    class="employee-navigation"
    aria-label="Navigation de l’administration">

    <div class="container">

        <ul class="employee-navigation-list">

            <!-- Accès à l'accueil administrateur. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/admin"
                    class="employee-navigation-link<?php
                                                    echo $isAdminHomePage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isAdminHomePage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Accueil
                </a>
            </li>

            <!-- Accès à la gestion des employés. -->
            <li>
                <a
                    href="<?php
                            echo BASE_URL;
                            ?>/admin/employees"
                    class="employee-navigation-link<?php
                                                    echo $isAdminEmployeesPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isAdminEmployeesPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Employés
                </a>
            </li>

            <!-- Accès aux statistiques administrateur. -->
            <li>
                <a
                    href="<?php
                            echo BASE_URL;
                            ?>/admin/statistics"
                    class="employee-navigation-link<?php
                                                    echo $isAdminStatisticsPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isAdminStatisticsPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Statistiques
                </a>
            </li>

            <!-- Permet à l'administrateur d'accéder à l'espace employé. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee"
                    class="employee-navigation-link">
                    Espace employé
                </a>
            </li>

        </ul>

    </div>

</nav>