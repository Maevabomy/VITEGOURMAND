<?php

/** @var array $menuOrderStatistics */
/** @var string|null $statisticsError */
/** @var array $revenueMenus */
/** @var array $revenueStatistics */
/** @var int|null $selectedMenuId */
/** @var string $startDate */
/** @var string $endDate */
/** @var string|null $revenueError */

$maximumOrderCount = 0;

foreach ($menuOrderStatistics as $statistic) {
    $maximumOrderCount = max(
        $maximumOrderCount,
        (int) $statistic['order_count']
    );
}

$revenueTotal = 0.0;

foreach ($revenueStatistics as $statistic) {
    $revenueTotal +=
        (float) $statistic['revenue'];
}

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Administration
        </p>

        <h1 class="employee-dashboard-title">
            Statistiques
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Comparez les commandes par menu et suivez
            le chiffre d’affaires de l’entreprise.
        </p>

    </div>
</section>

<?php

require BASE_PATH
    . '/app/Views/admin/_navigation.php';

?>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <!-- Nombre de commandes MongoDB -->
        <div class="employee-dashboard-card">

            <div class="admin-statistics-heading">

                <div>
                    <p class="section-subtitle mb-2">
                        Commandes
                    </p>

                    <h2 class="employee-dashboard-card-title">
                        Nombre de commandes par menu
                    </h2>
                </div>

                <span class="admin-statistics-source">
                    Source : MongoDB
                </span>

            </div>

            <?php if ($statisticsError !== null): ?>

                <div
                    class="alert alert-danger mt-4"
                    role="alert">
                    <?php
                    echo htmlspecialchars(
                        $statisticsError
                    );
                    ?>
                </div>

            <?php elseif (empty($menuOrderStatistics)): ?>

                <div class="employee-empty-state mt-4">
                    <p class="mb-0">
                        Aucune statistique n’est disponible.
                    </p>
                </div>

            <?php else: ?>

                <div
                    class="admin-statistics-chart"
                    aria-label="Nombre de commandes par menu">

                    <?php foreach (
                        $menuOrderStatistics
                        as $statistic
                    ): ?>

                        <?php

                        $orderCount =
                            (int)
                            $statistic['order_count'];

                        $percentage =
                            $maximumOrderCount > 0
                            ? (
                                $orderCount
                                / $maximumOrderCount
                            ) * 100
                            : 0;

                        ?>

                        <div class="admin-statistics-row">

                            <div
                                class="admin-statistics-label">
                                <?php
                                echo htmlspecialchars(
                                    $statistic[
                                        'menu_title'
                                    ]
                                );
                                ?>
                            </div>

                            <div
                                class="admin-statistics-bar"
                                aria-label="<?php
                                    echo $orderCount;
                                ?> commande<?php
                                    echo $orderCount > 1
                                        ? 's'
                                        : '';
                                ?>">

                                <div
                                    class="admin-statistics-bar-fill"
                                    style="width: <?php
                                        echo number_format(
                                            $percentage,
                                            2,
                                            '.',
                                            ''
                                        );
                                    ?>%;">
                                </div>

                            </div>

                            <strong
                                class="admin-statistics-value">
                                <?php echo $orderCount; ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

        <!-- Chiffre d'affaires MariaDB -->
        <div
            class="employee-dashboard-card mt-4"
            id="revenue">

            <div class="admin-statistics-heading">

                <div>
                    <p class="section-subtitle mb-2">
                        Chiffre d’affaires
                    </p>

                    <h2 class="employee-dashboard-card-title">
                        Chiffre d’affaires par menu
                    </h2>
                </div>

                <span class="admin-statistics-source">
                    Source : MariaDB
                </span>

            </div>

            <p class="admin-revenue-help">
                Les commandes annulées sont exclues.
                La période est basée sur la date de prestation.
            </p>

            <form
                action="<?php
                    echo BASE_URL;
                ?>/admin/statistics#revenue"
                method="get"
                class="admin-revenue-filters">

                <div>
                    <label
                        for="revenue-menu"
                        class="form-label">
                        Menu
                    </label>

                    <select
                        id="revenue-menu"
                        name="menu_id"
                        class="form-select">

                        <option value="">
                            Tous les menus
                        </option>

                        <?php foreach (
                            $revenueMenus
                            as $menu
                        ): ?>

                            <option
                                value="<?php
                                    echo (int) $menu['id'];
                                ?>"
                                <?php
                                echo $selectedMenuId
                                    === (int) $menu['id']
                                    ? 'selected'
                                    : '';
                                ?>>
                                <?php
                                echo htmlspecialchars(
                                    $menu['title']
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div>
                    <label
                        for="revenue-start-date"
                        class="form-label">
                        Du
                    </label>

                    <input
                        type="date"
                        id="revenue-start-date"
                        name="start_date"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $startDate
                            );
                        ?>">
                </div>

                <div>
                    <label
                        for="revenue-end-date"
                        class="form-label">
                        Au
                    </label>

                    <input
                        type="date"
                        id="revenue-end-date"
                        name="end_date"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $endDate
                            );
                        ?>">
                </div>

                <div class="admin-revenue-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-custom">
                        Filtrer
                    </button>

                    <a
                        href="<?php
                            echo BASE_URL;
                        ?>/admin/statistics#revenue"
                        class="btn btn-outline-light">
                        Réinitialiser
                    </a>

                </div>

            </form>

            <?php if ($revenueError !== null): ?>

                <div
                    class="alert alert-danger mt-4"
                    role="alert">
                    <?php
                    echo htmlspecialchars(
                        $revenueError
                    );
                    ?>
                </div>

            <?php else: ?>

                <div class="admin-revenue-summary">

                    <span>
                        Chiffre d’affaires total
                    </span>

                    <strong>
                        <?php
                        echo number_format(
                            $revenueTotal,
                            2,
                            ',',
                            ' '
                        );
                        ?> €
                    </strong>

                </div>

                <?php if (empty($revenueStatistics)): ?>

                    <div class="employee-empty-state mt-4">
                        <p class="mb-0">
                            Aucun résultat pour cette période.
                        </p>
                    </div>

                <?php else: ?>

                    <div class="table-responsive mt-4">

                        <table class="admin-revenue-table">

                            <thead>
                                <tr>
                                    <th scope="col">
                                        Menu
                                    </th>

                                    <th scope="col">
                                        Commandes
                                    </th>

                                    <th scope="col">
                                        Chiffre d’affaires
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach (
                                    $revenueStatistics
                                    as $statistic
                                ): ?>

                                    <tr>
                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $statistic[
                                                    'menu_title'
                                                ]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo (int)
                                                $statistic[
                                                    'order_count'
                                                ];
                                            ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?php
                                                echo number_format(
                                                    (float)
                                                    $statistic[
                                                        'revenue'
                                                    ],
                                                    2,
                                                    ',',
                                                    ' '
                                                );
                                                ?> €
                                            </strong>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>
</section>