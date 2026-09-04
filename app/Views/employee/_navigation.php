<?php

/* -------------------------------------------------- */
/* détection de la page active */
/* -------------------------------------------------- */

/* Récupère le chemin actuellement affiché. */
$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '',
    PHP_URL_PATH
);

/* Construit le chemin de l'accueil employé. */
$employeeHomePath = parse_url(
    BASE_URL . '/employee',
    PHP_URL_PATH
);

/* Construit le chemin du tableau de bord des commandes. */
$employeeDashboardPath = parse_url(
    BASE_URL . '/employee/dashboard',
    PHP_URL_PATH
);

/* Construit le chemin de la gestion des avis. */
$employeeReviewsPath = parse_url(
    BASE_URL . '/employee/reviews',
    PHP_URL_PATH
);

/* Construit le chemin de la gestion des menus. */
$employeeMenusPath = parse_url(
    BASE_URL . '/employee/menus',
    PHP_URL_PATH
);

/* Construit le chemin de la gestion des horaires. */
$employeeOpeningHoursPath = parse_url(
    BASE_URL . '/employee/opening-hours',
    PHP_URL_PATH
);

/* Construit le chemin de la gestion des plats. */
$employeeDishesPath = parse_url(
    BASE_URL . '/employee/dishes',
    PHP_URL_PATH
);

/* Indique si la page d'accueil employé est active. */
$isHomePage =
    $currentPath === $employeeHomePath;

/* Indique si une page liée aux commandes est active. */
$isOrdersPage =
    $currentPath === $employeeDashboardPath
    || str_contains(
        $currentPath,
        '/employee/order/'
    );

/* Indique si la page des avis est active. */
$isReviewsPage =
    $currentPath === $employeeReviewsPath;

/* Indique si une page liée aux menus est active. */
$isMenusPage =
    $currentPath === $employeeMenusPath
    || str_contains(
        $currentPath,
        '/employee/menu/'
    );

/* Indique si la page des horaires est active. */
$isOpeningHoursPage =
    $currentPath === $employeeOpeningHoursPath;

/* Indique si une page liée aux plats est active. */
$isDishesPage =
    $currentPath === $employeeDishesPath
    || str_contains(
        $currentPath,
        '/employee/dish/'
    );

?>

<!-- -------------------------------------------------- -->
<!-- navigation de l'espace employé -->
<!-- -------------------------------------------------- -->

<nav
    class="employee-navigation"
    aria-label="Navigation de l’espace professionnel">

    <div class="container">

        <ul class="employee-navigation-list">

            <!-- Accès à l'accueil employé. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee"
                    class="employee-navigation-link<?php
                                                    echo $isHomePage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isHomePage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Accueil
                </a>
            </li>

            <!-- Accès à la gestion des commandes. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/dashboard"
                    class="employee-navigation-link<?php
                                                    echo $isOrdersPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isOrdersPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Commandes
                </a>
            </li>

            <!-- Accès à la gestion des avis. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/reviews"
                    class="employee-navigation-link<?php
                                                    echo $isReviewsPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isReviewsPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Avis
                </a>
            </li>

            <!-- Accès à la gestion des menus. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/menus"
                    class="employee-navigation-link<?php
                                                    echo $isMenusPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isMenusPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Menus
                </a>
            </li>

            <!-- Accès à la gestion des plats. -->
            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/dishes"
                    class="employee-navigation-link<?php
                                                    echo $isDishesPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isDishesPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Plats
                </a>
            </li>

            <!-- Accès à la gestion des horaires. -->
            <li>
                <a
                    href="<?php
                            echo BASE_URL;
                            ?>/employee/opening-hours"
                    class="employee-navigation-link<?php
                                                    echo $isOpeningHoursPage
                                                        ? ' active'
                                                        : '';
                                                    ?>"
                    <?php
                    echo $isOpeningHoursPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Horaires
                </a>
            </li>

        </ul>

    </div>

</nav>