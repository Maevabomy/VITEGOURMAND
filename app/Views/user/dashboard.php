<?php
/* Indique les variables préparées par le contrôleur. */

/** @var array $user */
/** @var array $orders */
?>

<section class="user-dashboard-hero">
    <div class="container py-5">

        <p class="section-subtitle mb-2">
            Espace personnel
        </p>

        <h1 class="user-dashboard-title">
            Bonjour
            <?php echo htmlspecialchars($user['first_name']); ?>
        </h1>

        <p class="user-dashboard-introduction mb-0">
            Retrouvez vos informations personnelles et le suivi de vos
            commandes Vite & Gourmand.
        </p>

    </div>
</section>

<section class="user-dashboard-section py-5">
    <div class="container py-4">

        <div class="row g-4">

            <!-- -------------------------------------------------- -->
            <!-- informations du compte -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-4">
                <article class="user-dashboard-card h-100">

                    <div class="user-dashboard-card-header">
                        <p class="section-subtitle mb-2">
                            Votre compte
                        </p>

                        <h2 class="user-dashboard-card-title">
                            Informations personnelles
                        </h2>
                    </div>

                    <dl class="user-profile-list mb-0">

                        <div class="user-profile-row">
                            <dt>Nom</dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $user['first_name']
                                        . ' '
                                        . $user['last_name']
                                );
                                ?>
                            </dd>
                        </div>

                        <div class="user-profile-row">
                            <dt>Adresse mail</dt>

                            <dd>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </dd>
                        </div>

                        <div class="user-profile-row">
                            <dt>Téléphone</dt>

                            <dd>
                                <?php echo htmlspecialchars($user['phone']); ?>
                            </dd>
                        </div>

                        <div class="user-profile-row">
                            <dt>Adresse</dt>

                            <dd>
                                <?php echo htmlspecialchars($user['address']); ?>
                                <br>

                                <?php
                                echo htmlspecialchars(
                                    $user['postal_code']
                                        . ' '
                                        . $user['city']
                                );
                                ?>
                            </dd>
                        </div>

                    </dl>

                    <div class="user-dashboard-card-actions">
                        <button
                            type="button"
                            class="btn btn-outline-light"
                            disabled>
                            Modifier mes informations
                        </button>
                    </div>

                </article>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- commandes de l'utilisateur -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-8">
                <section class="user-dashboard-card h-100">

                    <div class="user-dashboard-card-header">
                        <p class="section-subtitle mb-2">
                            Vos prestations
                        </p>

                        <h2 class="user-dashboard-card-title">
                            Mes commandes
                        </h2>
                    </div>

                    <?php if (!empty($orders)): ?>

                        <div class="user-orders-list">

                            <?php foreach ($orders as $order): ?>

                                <?php
                                $eventDate = date_create(
                                    $order['event_date']
                                );

                                $deliveryTime = date_create(
                                    $order['delivery_time']
                                );
                                ?>

                                <article class="user-order-card">

                                    <div class="user-order-card-header">

                                        <div>
                                            <p class="user-order-reference mb-1">
                                                <?php
                                                echo htmlspecialchars(
                                                    $order['order_number']
                                                );
                                                ?>
                                            </p>

                                            <h3 class="user-order-menu-title">
                                                <?php
                                                echo htmlspecialchars(
                                                    $order['menu_title']
                                                );
                                                ?>
                                            </h3>
                                        </div>

                                        <span class="user-order-status">
                                            <?php
                                            echo htmlspecialchars(
                                                $order['status_name']
                                            );
                                            ?>
                                        </span>

                                    </div>

                                    <div class="user-order-information">

                                        <div>
                                            <span class="user-order-label">
                                                Date de la prestation
                                            </span>

                                            <strong>
                                                <?php
                                                echo $eventDate
                                                    ? $eventDate->format(
                                                        'd/m/Y'
                                                    )
                                                    : '';
                                                ?>
                                            </strong>
                                        </div>

                                        <div>
                                            <span class="user-order-label">
                                                Heure
                                            </span>

                                            <strong>
                                                <?php
                                                echo $deliveryTime
                                                    ? $deliveryTime->format(
                                                        'H\hi'
                                                    )
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

                                    <div class="user-order-actions">
                                        <button
                                            type="button"
                                            class="btn user-order-detail-button"
                                            disabled>
                                            Voir le détail
                                        </button>
                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="user-orders-empty">
                            <h3 class="user-orders-empty-title">
                                Aucune commande pour le moment
                            </h3>

                            <p>
                                Vous pourrez retrouver ici les commandes
                                réalisées depuis votre compte.
                            </p>

                            <a
                                href="<?php echo BASE_URL; ?>/menus"
                                class="btn btn-primary">
                                Découvrir les menus
                            </a>
                        </div>

                    <?php endif; ?>

                </section>
            </div>

        </div>
    </div>
</section>