<?php
/* Indique la variable préparée par le contrôleur. */

/** @var array $order */
/** @var array $statusHistory */
/** @var string $csrfToken */
/** @var string|null $success */
/** @var string|null $error */


$eventDate = date_create($order['event_date']);
$deliveryTime = date_create($order['delivery_time']);
$createdAt = date_create($order['created_at']);
?>

<section class="user-dashboard-hero">
    <div class="container py-5">

        <p class="section-subtitle mb-2">
            Espace personnel
        </p>

        <h1 class="user-dashboard-title">
            Détail de la commande
        </h1>

        <p class="user-dashboard-introduction mb-0">
            Référence :
            <strong>
                <?php
                echo htmlspecialchars($order['order_number']);
                ?>
            </strong>
        </p>

    </div>
</section>

<section class="user-dashboard-section py-5">
    <div class="container py-4">

        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success mb-4"
                role="status">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger mb-4"
                role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="user-order-detail-navigation mb-4">
            <a
                href="<?php echo BASE_URL; ?>/user/dashboard"
                class="user-order-back-link">
                ← Retour à mes commandes
            </a>
        </div>

        <div class="row g-4">

            <!-- -------------------------------------------------- -->
            <!-- résumé de la commande -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-8">
                <article class="user-dashboard-card">

                    <div class="user-order-detail-heading">
                        <div>
                            <p class="section-subtitle mb-2">
                                Votre prestation
                            </p>

                            <h2 class="user-dashboard-card-title">
                                <?php
                                echo htmlspecialchars(
                                    $order['menu_title']
                                );
                                ?>
                            </h2>
                        </div>

                        <span class="user-order-status">
                            <?php
                            echo htmlspecialchars(
                                $order['status_name']
                            );
                            ?>
                        </span>
                    </div>

                    <p class="user-order-detail-description">
                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $order['menu_description']
                            )
                        );
                        ?>
                    </p>

                    <div class="user-order-detail-grid">

                        <div class="user-order-detail-item">
                            <span class="user-order-label">
                                Date de la prestation
                            </span>

                            <strong>
                                <?php
                                echo $eventDate
                                    ? $eventDate->format('d/m/Y')
                                    : '';
                                ?>
                            </strong>
                        </div>

                        <div class="user-order-detail-item">
                            <span class="user-order-label">
                                Heure souhaitée
                            </span>

                            <strong>
                                <?php
                                echo $deliveryTime
                                    ? $deliveryTime->format('H\hi')
                                    : '';
                                ?>
                            </strong>
                        </div>

                        <div class="user-order-detail-item">
                            <span class="user-order-label">
                                Nombre de convives
                            </span>

                            <strong>
                                <?php
                                echo (int) $order['people_count'];
                                ?>
                            </strong>
                        </div>

                        <div class="user-order-detail-item">
                            <span class="user-order-label">
                                Commande créée le
                            </span>

                            <strong>
                                <?php
                                echo $createdAt
                                    ? $createdAt->format(
                                        'd/m/Y à H\hi'
                                    )
                                    : '';
                                ?>
                            </strong>
                        </div>

                    </div>

                </article>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- détail du prix -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-4">
                <aside class="user-dashboard-card h-100">

                    <div class="user-dashboard-card-header">
                        <p class="section-subtitle mb-2">
                            Récapitulatif
                        </p>

                        <h2 class="user-dashboard-card-title">
                            Détail du prix
                        </h2>
                    </div>

                    <dl class="user-order-price-list">

                        <div class="user-order-price-row">
                            <dt>Menu</dt>

                            <dd>
                                <?php
                                echo number_format(
                                    (float) $order['menu_price'],
                                    2,
                                    ',',
                                    ' '
                                );
                                ?>
                                €
                            </dd>
                        </div>

                        <div class="user-order-price-row">
                            <dt>Livraison</dt>

                            <dd>
                                <?php
                                echo number_format(
                                    (float) $order['delivery_price'],
                                    2,
                                    ',',
                                    ' '
                                );
                                ?>
                                €
                            </dd>
                        </div>

                        <div class="user-order-price-row is-total">
                            <dt>Total</dt>

                            <dd>
                                <?php
                                echo number_format(
                                    (float) $order['total_price'],
                                    2,
                                    ',',
                                    ' '
                                );
                                ?>
                                €
                            </dd>
                        </div>

                    </dl>

                </aside>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- coordonnées du client -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">
                <article class="user-dashboard-card h-100">

                    <div class="user-dashboard-card-header">
                        <p class="section-subtitle mb-2">
                            Contact
                        </p>

                        <h2 class="user-dashboard-card-title">
                            Coordonnées du client
                        </h2>
                    </div>

                    <dl class="user-profile-list mb-0">

                        <div class="user-profile-row">
                            <dt>Nom</dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_first_name']
                                        . ' '
                                        . $order['customer_last_name']
                                );
                                ?>
                            </dd>
                        </div>

                        <div class="user-profile-row">
                            <dt>Adresse mail</dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_email']
                                );
                                ?>
                            </dd>
                        </div>

                        <div class="user-profile-row">
                            <dt>Téléphone</dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_phone']
                                );
                                ?>
                            </dd>
                        </div>

                    </dl>

                </article>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- lieu de la prestation -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">
                <article class="user-dashboard-card h-100">

                    <div class="user-dashboard-card-header">
                        <p class="section-subtitle mb-2">
                            Livraison
                        </p>

                        <h2 class="user-dashboard-card-title">
                            Lieu de la prestation
                        </h2>
                    </div>

                    <address class="user-order-address mb-0">
                        <?php
                        echo htmlspecialchars(
                            $order['delivery_address']
                        );
                        ?>
                        <br>

                        <?php
                        echo htmlspecialchars(
                            $order['delivery_postal_code']
                                . ' '
                                . $order['delivery_city']
                        );
                        ?>
                    </address>

                </article>
            </div>

            <?php if ($order['status_name'] === 'En attente'): ?>

                <!-- -------------------------------------------------- -->
                <!-- gestion de la commande -->
                <!-- -------------------------------------------------- -->

                <div class="col-12">
                    <section class="user-order-cancellation-card">

                        <div>
                            <p class="section-subtitle mb-2">
                                Gestion de la commande
                            </p>

                            <h2 class="user-order-cancellation-title">
                                Modifier ou annuler
                            </h2>

                            <p class="user-order-cancellation-text mb-0">
                                Vous pouvez encore modifier les informations
                                de la prestation ou annuler cette commande
                                tant que l’équipe ne l’a pas acceptée.
                            </p>
                        </div>

                        <div class="d-flex flex-column gap-3">

                            <a
                                href="<?php
                                        echo BASE_URL
                                            . '/user/order/edit?id='
                                            . (int) $order['id'];
                                        ?>"
                                class="btn btn-custom">
                                Modifier ma commande
                            </a>

                            <form
                                action="<?php
                                        echo BASE_URL
                                            . '/user/order/cancel';
                                        ?>"
                                method="post"
                                class="user-order-cancellation-form"
                                onsubmit="return confirm(
                                    'Confirmez-vous l’annulation définitive '
                                    + 'de cette commande ?'
                                );">

                                <input
                                    type="hidden"
                                    name="order_id"
                                    value="<?php
                                            echo (int) $order['id'];
                                            ?>">

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?php
                                            echo htmlspecialchars(
                                                $csrfToken
                                            );
                                            ?>">

                                <button
                                    type="submit"
                                    class="
                                        btn
                                        user-order-cancel-button
                                        w-100
                                    ">
                                    Annuler ma commande
                                </button>

                            </form>

                        </div>

                    </section>
                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- suivi de la commande -->
            <!-- -------------------------------------------------- -->

            <div class="col-12">
                <section class="user-dashboard-card">

                    <div class="user-dashboard-card-header">
                        <p class="section-subtitle mb-2">
                            Suivi
                        </p>

                        <h2 class="user-dashboard-card-title">
                            Historique de la commande
                        </h2>
                    </div>

                    <?php if (!empty($statusHistory)): ?>

                        <ol class="user-order-timeline">

                            <?php
                            $historyCount = count($statusHistory);
                            ?>

                            <?php
                            foreach ($statusHistory as $index => $history):
                            ?>

                                <?php
                                $statusDate = date_create(
                                    $history['created_at']
                                );

                                $isCurrentStatus =
                                    $index === $historyCount - 1;
                                ?>

                                <li class="<?php
                                            echo $isCurrentStatus
                                                ? 'user-order-timeline-item is-current'
                                                : 'user-order-timeline-item';
                                            ?>">

                                    <div
                                        class="user-order-timeline-marker"
                                        aria-hidden="true">
                                    </div>

                                    <div class="user-order-timeline-content">

                                        <div
                                            class="user-order-timeline-heading">

                                            <h3
                                                class="
                                                    user-order-timeline-title
                                                ">
                                                <?php
                                                echo htmlspecialchars(
                                                    $history['status_name']
                                                );
                                                ?>
                                            </h3>

                                            <?php if ($isCurrentStatus): ?>

                                                <span
                                                    class="
                                                        user-order-current-label
                                                    ">
                                                    Statut actuel
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                        <time
                                            class="user-order-timeline-date"
                                            datetime="<?php
                                                        echo htmlspecialchars(
                                                            $history['created_at']
                                                        );
                                                        ?>">
                                            <?php
                                            echo $statusDate
                                                ? $statusDate->format(
                                                    'd/m/Y à H\hi'
                                                )
                                                : '';
                                            ?>
                                        </time>

                                        <?php
                                        if (!empty($history['note'])):
                                        ?>

                                            <p
                                                class="
                                                    user-order-timeline-note
                                                    mb-0
                                                ">
                                                <?php
                                                echo nl2br(
                                                    htmlspecialchars(
                                                        $history['note']
                                                    )
                                                );
                                                ?>
                                            </p>

                                        <?php endif; ?>

                                    </div>

                                </li>

                            <?php endforeach; ?>

                        </ol>

                    <?php else: ?>

                        <p class="user-order-timeline-empty mb-0">
                            Aucun historique n’est disponible pour cette
                            commande.
                        </p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</section>