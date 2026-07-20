<?php

/** @var array $user */
/** @var array $orders */
/** @var array $statuses */
/** @var int|null $statusId */
/** @var string $search */

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Gestion des commandes
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Bonjour
            <?php echo htmlspecialchars($user['first_name']); ?>.
            Retrouvez les commandes à préparer et utilisez les filtres
            pour accéder rapidement à une prestation.
        </p>

        <div class="mt-4">
            <a
                href="<?php echo BASE_URL; ?>/employee/reviews"
                class="btn btn-custom">
                Modérer les avis
            </a>
        </div>

    </div>
</section>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <!-- -------------------------------------------------- -->
        <!-- filtres -->
        <!-- -------------------------------------------------- -->

        <section class="employee-dashboard-card mb-4">

            <div class="employee-dashboard-card-header">
                <p class="section-subtitle mb-2">
                    Recherche
                </p>

                <h2 class="employee-dashboard-card-title">
                    Filtrer les commandes
                </h2>
            </div>

            <form
                action="<?php echo BASE_URL; ?>/employee/dashboard"
                method="get"
                class="employee-order-filters">

                <div>
                    <label
                        for="employee-order-status"
                        class="form-label">
                        Statut
                    </label>

                    <select
                        class="form-select"
                        id="employee-order-status"
                        name="status">

                        <option value="">
                            Tous les statuts
                        </option>

                        <?php foreach ($statuses as $status): ?>

                            <option
                                value="<?php echo (int) $status['id']; ?>"
                                <?php
                                echo $statusId === (int) $status['id']
                                    ? 'selected'
                                    : '';
                                ?>>
                                <?php
                                echo htmlspecialchars(
                                    $status['name']
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div>
                    <label
                        for="employee-order-search"
                        class="form-label">
                        Client ou commande
                    </label>

                    <input
                        type="search"
                        class="form-control"
                        id="employee-order-search"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        maxlength="100"
                        placeholder="
                            Nom, e-mail ou numéro de commande
                        ">
                </div>

                <div class="employee-order-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-custom">
                        Rechercher
                    </button>

                    <?php if ($statusId !== null || $search !== ''): ?>

                        <a
                            href="<?php
                                    echo BASE_URL . '/employee/dashboard';
                                    ?>"
                            class="btn employee-filter-reset-button">
                            Réinitialiser
                        </a>

                    <?php endif; ?>

                </div>

            </form>

        </section>

        <!-- -------------------------------------------------- -->
        <!-- commandes -->
        <!-- -------------------------------------------------- -->

        <section class="employee-dashboard-card">

            <div class="employee-orders-heading">

                <div class="employee-dashboard-card-header mb-0">
                    <p class="section-subtitle mb-2">
                        Prestations
                    </p>

                    <h2 class="employee-dashboard-card-title">
                        Commandes trouvées
                    </h2>
                </div>

                <span class="employee-order-count">
                    <?php echo count($orders); ?>
                    commande<?php
                            echo count($orders) > 1 ? 's' : '';
                            ?>
                </span>

            </div>

            <?php if (!empty($orders)): ?>

                <div class="employee-orders-list">

                    <?php foreach ($orders as $order): ?>

                        <?php
                        $eventDate = date_create(
                            $order['event_date']
                        );

                        $deliveryTime = date_create(
                            $order['delivery_time']
                        );
                        ?>

                        <article class="employee-order-card">

                            <div class="employee-order-card-header">

                                <div>
                                    <p class="employee-order-reference mb-1">
                                        <?php
                                        echo htmlspecialchars(
                                            $order['order_number']
                                        );
                                        ?>
                                    </p>

                                    <h3 class="employee-order-client">
                                        <?php
                                        echo htmlspecialchars(
                                            $order['customer_first_name']
                                                . ' '
                                                . $order['customer_last_name']
                                        );
                                        ?>
                                    </h3>

                                    <p class="employee-order-email mb-0">
                                        <?php
                                        echo htmlspecialchars(
                                            $order['customer_email']
                                        );
                                        ?>
                                    </p>
                                </div>

                                <span class="user-order-status">
                                    <?php
                                    echo htmlspecialchars(
                                        $order['status_name']
                                    );
                                    ?>
                                </span>

                            </div>

                            <div class="employee-order-information">

                                <div>
                                    <span class="user-order-label">
                                        Menu
                                    </span>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order['menu_title']
                                        );
                                        ?>
                                    </strong>
                                </div>

                                <div>
                                    <span class="user-order-label">
                                        Prestation
                                    </span>

                                    <strong>
                                        <?php
                                        echo $eventDate
                                            ? $eventDate->format('d/m/Y')
                                            : '';
                                        ?>
                                        à
                                        <?php
                                        echo $deliveryTime
                                            ? $deliveryTime->format('H\hi')
                                            : '';
                                        ?>
                                    </strong>
                                </div>

                                <div>
                                    <span class="user-order-label">
                                        Convives
                                    </span>

                                    <strong>
                                        <?php
                                        echo (int) $order['people_count'];
                                        ?>
                                    </strong>
                                </div>

                                <div>
                                    <span class="user-order-label">
                                        Total
                                    </span>

                                    <strong>
                                        <?php
                                        echo number_format(
                                            (float) $order['total_price'],
                                            2,
                                            ',',
                                            ' '
                                        );
                                        ?>
                                        €
                                    </strong>
                                </div>

                            </div>

                            <div class="employee-order-actions">

                                <a
                                    href="<?php
                                            echo BASE_URL
                                                . '/employee/order/detail?id='
                                                . (int) $order['id'];
                                            ?>"
                                    class="
        btn
        user-order-detail-button
    ">
                                    Voir le détail
                                </a>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="user-orders-empty">

                    <h3 class="user-orders-empty-title">
                        Aucune commande trouvée
                    </h3>

                    <p class="mb-0">
                        Aucun résultat ne correspond aux filtres
                        actuellement sélectionnés.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </div>
</section>