<?php

$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '',
    PHP_URL_PATH
);

$adminHomePath = parse_url(
    BASE_URL . '/admin',
    PHP_URL_PATH
);

$adminEmployeesPath = parse_url(
    BASE_URL . '/admin/employees',
    PHP_URL_PATH
);

$adminStatisticsPath = parse_url(
    BASE_URL . '/admin/statistics',
    PHP_URL_PATH
);

$isAdminHomePage =
    $currentPath === $adminHomePath;

$isAdminEmployeesPage =
    $currentPath === $adminEmployeesPath;

$isAdminStatisticsPage =
    $currentPath === $adminStatisticsPath;
?>

<nav
    class="employee-navigation"
    aria-label="Navigation de l’administration">

    <div class="container">

        <ul class="employee-navigation-list">

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

            <li>
    <a
        href="<?php echo BASE_URL; ?>/admin/statistics"
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