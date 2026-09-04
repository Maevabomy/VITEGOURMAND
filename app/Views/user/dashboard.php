<?php

/** @var array $user */
/** @var array $orders */
/** @var string|null $success */

?>

<!-- -------------------------------------------------- -->
<!-- présentation de l'espace personnel -->
<!-- -------------------------------------------------- -->

<section class="user-dashboard-hero">
    <div class="container py-5">

        <p class="section-subtitle mb-2">
            Espace personnel
        </p>

        <h1 class="user-dashboard-title">
            Bonjour
            <?php
            echo htmlspecialchars(
                $user['first_name']
            );
            ?>
        </h1>

        <p class="user-dashboard-introduction mb-0">
            Retrouvez vos informations personnelles et le suivi de vos
            commandes Vite & Gourmand.
        </p>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- tableau de bord utilisateur -->
<!-- -------------------------------------------------- -->

<section class="user-dashboard-section py-5">
    <div class="container py-4">

        <!-- -------------------------------------------------- -->
        <!-- message de validation -->
        <!-- -------------------------------------------------- -->

        <!-- Affiche le message de confirmation après une action réussie. -->
        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success mb-4"
                role="alert">
                <?php
                echo htmlspecialchars(
                    $success
                );
                ?>
            </div>

        <?php endif; ?>

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

                        <!-- Nom complet de l'utilisateur. -->
                        <div class="user-profile-row">

                            <dt>
                                Nom
                            </dt>

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

                        <!-- Adresse mail de l'utilisateur. -->
                        <div class="user-profile-row">

                            <dt>
                                Adresse mail
                            </dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $user['email']
                                );
                                ?>
                            </dd>

                        </div>

                        <!-- Numéro de téléphone de l'utilisateur. -->
                        <div class="user-profile-row">

                            <dt>
                                Téléphone
                            </dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $user['phone']
                                );
                                ?>
                            </dd>

                        </div>

                        <!-- Adresse postale de l'utilisateur. -->
                        <div class="user-profile-row">

                            <dt>
                                Adresse
                            </dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $user['address']
                                );
                                ?>

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

                    <!-- Accès à la modification du profil. -->
                    <div class="user-dashboard-card-actions">

                        <a
                            href="<?php
                                    echo BASE_URL
                                        . '/user/profile/edit';
                                    ?>"
                            class="btn user-profile-edit-button">
                            Modifier mes informations
                        </a>

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

                                /* Prépare la date de prestation pour l'affichage. */
                                $eventDate = date_create(
                                    $order['event_date']
                                );

                                /* Prépare l'heure de livraison pour l'affichage. */
                                $deliveryTime = date_create(
                                    $order['delivery_time']
                                );

                                ?>

                                <article class="user-order-card">

                                    <!-- -------------------------------------------------- -->
                                    <!-- référence et statut -->
                                    <!-- -------------------------------------------------- -->

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

                                    <!-- -------------------------------------------------- -->
                                    <!-- informations de la commande -->
                                    <!-- -------------------------------------------------- -->

                                    <div class="user-order-information">

                                        <!-- Date de la prestation. -->
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

                                        <!-- Heure de livraison. -->
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

                                        <!-- Nombre de convives. -->
                                        <div>

                                            <span class="user-order-label">
                                                Convives
                                            </span>

                                            <strong>
                                                <?php
                                                echo (int)
                                                $order['people_count'];
                                                ?>
                                            </strong>

                                        </div>

                                        <!-- Montant total de la commande. -->
                                        <div>

                                            <span class="user-order-label">
                                                Total
                                            </span>

                                            <strong>
                                                <?php
                                                echo number_format(
                                                    (float)
                                                    $order['total_price'],
                                                    2,
                                                    ',',
                                                    ' '
                                                );
                                                ?>
                                                €
                                            </strong>

                                        </div>

                                    </div>

                                    <!-- -------------------------------------------------- -->
                                    <!-- actions de la commande -->
                                    <!-- -------------------------------------------------- -->

                                    <div class="user-order-actions">

                                        <a
                                            href="<?php
                                                    echo BASE_URL
                                                        . '/user/order/detail?id='
                                                        . (int) $order['id'];
                                                    ?>"
                                            class="btn user-order-detail-button">
                                            Voir le détail
                                        </a>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <!-- -------------------------------------------------- -->
                        <!-- absence de commande -->
                        <!-- -------------------------------------------------- -->

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