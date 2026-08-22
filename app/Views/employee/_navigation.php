<?php

$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '',
    PHP_URL_PATH
);

$employeeHomePath = parse_url(
    BASE_URL . '/employee',
    PHP_URL_PATH
);

$employeeDashboardPath = parse_url(
    BASE_URL . '/employee/dashboard',
    PHP_URL_PATH
);

$employeeReviewsPath = parse_url(
    BASE_URL . '/employee/reviews',
    PHP_URL_PATH
);

$employeeMenusPath = parse_url(
    BASE_URL . '/employee/menus',
    PHP_URL_PATH
);

$employeeOpeningHoursPath = parse_url(
    BASE_URL . '/employee/opening-hours',
    PHP_URL_PATH
);

$employeeDishesPath = parse_url(
    BASE_URL . '/employee/dishes',
    PHP_URL_PATH
);

$isHomePage =
    $currentPath === $employeeHomePath;

$isOrdersPage =
    $currentPath === $employeeDashboardPath
    || str_contains(
        $currentPath,
        '/employee/order/'
    );

$isReviewsPage =
    $currentPath === $employeeReviewsPath;

$isMenusPage =
    $currentPath === $employeeMenusPath
    || str_contains(
        $currentPath,
        '/employee/menu/'
    );

$isOpeningHoursPage =
    $currentPath === $employeeOpeningHoursPath;

$isDishesPage =
    $currentPath === $employeeDishesPath
    || str_contains(
        $currentPath,
        '/employee/dish/'
    );

?>

<nav
    class="employee-navigation"
    aria-label="Navigation de l’espace professionnel">

    <div class="container">

        <ul class="employee-navigation-list">

            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee"
                    class="employee-navigation-link<?php
                                                    echo $isHomePage ? ' active' : '';
                                                    ?>"
                    <?php
                    echo $isHomePage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Accueil
                </a>
            </li>

            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/dashboard"
                    class="employee-navigation-link<?php
                                                    echo $isOrdersPage ? ' active' : '';
                                                    ?>"
                    <?php
                    echo $isOrdersPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Commandes
                </a>
            </li>

            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/reviews"
                    class="employee-navigation-link<?php
                                                    echo $isReviewsPage ? ' active' : '';
                                                    ?>"
                    <?php
                    echo $isReviewsPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Avis
                </a>
            </li>

            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/menus"
                    class="employee-navigation-link<?php
                                                    echo $isMenusPage ? ' active' : '';
                                                    ?>"
                    <?php
                    echo $isMenusPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Menus
                </a>
            </li>

            <li>
                <a
                    href="<?php echo BASE_URL; ?>/employee/dishes"
                    class="employee-navigation-link<?php
                                                    echo $isDishesPage ? ' active' : '';
                                                    ?>"
                    <?php
                    echo $isDishesPage
                        ? 'aria-current="page"'
                        : '';
                    ?>>
                    Plats
                </a>
            </li>

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