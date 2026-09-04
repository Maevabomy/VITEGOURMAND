<?php

/** @var array $order */
/** @var array $statusHistory */
/** @var array|null $review */
/** @var string $reviewCsrfToken */
/** @var string $csrfToken */
/** @var string|null $success */
/** @var string|null $error */


/* -------------------------------------------------- */
/* préparation de l'affichage */
/* -------------------------------------------------- */

/* Prépare la date de la prestation. */
$eventDate = date_create(
    $order['event_date']
);

/* Prépare l'heure de livraison. */
$deliveryTime = date_create(
    $order['delivery_time']
);

/* Prépare la date de création de la commande. */
$createdAt = date_create(
    $order['created_at']
);

?>

<!-- -------------------------------------------------- -->
<!-- présentation de la commande -->
<!-- -------------------------------------------------- -->

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
                echo htmlspecialchars(
                    $order['order_number']
                );
                ?>
            </strong>
        </p>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- détail de la commande -->
<!-- -------------------------------------------------- -->

<section class="user-dashboard-section py-5">
    <div class="container py-4">

        <!-- -------------------------------------------------- -->
        <!-- messages de validation -->
        <!-- -------------------------------------------------- -->

        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success mb-4"
                role="status">
                <?php
                echo htmlspecialchars(
                    $success
                );
                ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger mb-4"
                role="alert">
                <?php
                echo htmlspecialchars(
                    $error
                );
                ?>
            </div>

        <?php endif; ?>

        <!-- -------------------------------------------------- -->
        <!-- navigation -->
        <!-- -------------------------------------------------- -->

        <div class="user-order-detail-navigation mb-4">

            <a
                href="<?php
                        echo BASE_URL;
                        ?>/user/dashboard"
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

                        <!-- Affiche le statut actuel de la commande. -->
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

                    <!-- -------------------------------------------------- -->
                    <!-- informations de la prestation -->
                    <!-- -------------------------------------------------- -->

                    <div class="user-order-detail-grid">

                        <div class="user-order-detail-item">

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

                        <div class="user-order-detail-item">

                            <span class="user-order-label">
                                Heure souhaitée
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

                        <div class="user-order-detail-item">

                            <span class="user-order-label">
                                Nombre de convives
                            </span>

                            <strong>
                                <?php
                                echo (int)
                                $order['people_count'];
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

                        <!-- Prix du menu. -->
                        <div class="user-order-price-row">

                            <dt>
                                Menu
                            </dt>

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

                        <!-- Frais de livraison. -->
                        <div class="user-order-price-row">

                            <dt>
                                Livraison
                            </dt>

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

                        <!-- Montant total de la commande. -->
                        <div class="user-order-price-row is-total">

                            <dt>
                                Total
                            </dt>

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

                        <!-- Nom complet du client. -->
                        <div class="user-profile-row">

                            <dt>
                                Nom
                            </dt>

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

                        <!-- Adresse mail du client. -->
                        <div class="user-profile-row">

                            <dt>
                                Adresse mail
                            </dt>

                            <dd>
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_email']
                                );
                                ?>
                            </dd>

                        </div>

                        <!-- Numéro de téléphone du client. -->
                        <div class="user-profile-row">

                            <dt>
                                Téléphone
                            </dt>

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

            <!-- -------------------------------------------------- -->
            <!-- gestion de la commande -->
            <!-- -------------------------------------------------- -->

            <?php if ($order['status_name'] === 'En attente'): ?>

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

                            <!-- Accès à la modification de la commande. -->
                            <a
                                href="<?php
                                        echo BASE_URL
                                            . '/user/order/edit?id='
                                            . (int) $order['id'];
                                        ?>"
                                class="btn btn-custom">
                                Modifier ma commande
                            </a>

                            <!-- Permet l'annulation de la commande. -->
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
                                            echo (int)
                                            $order['id'];
                                            ?>">

                                <!-- Protège l'annulation contre les requêtes CSRF. -->
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
            <!-- avis client -->
            <!-- -------------------------------------------------- -->

            <?php if ($order['status_name'] === 'Terminée'): ?>

                <div class="col-12">

                    <section class="user-dashboard-card">

                        <div class="user-dashboard-card-header">

                            <p class="section-subtitle mb-2">
                                Votre expérience
                            </p>

                            <h2 class="user-dashboard-card-title">
                                Votre avis
                            </h2>

                        </div>

                        <?php if ($review === null): ?>

                            <!-- -------------------------------------------------- -->
                            <!-- création d'un avis -->
                            <!-- -------------------------------------------------- -->

                            <p>
                                Votre commande est terminée. Vous pouvez
                                maintenant partager votre expérience avec
                                Vite & Gourmand.
                            </p>

                            <form
                                action="<?php
                                        echo BASE_URL
                                            . '/user/order/review';
                                        ?>"
                                method="post">

                                <input
                                    type="hidden"
                                    name="order_id"
                                    value="<?php
                                            echo (int)
                                            $order['id'];
                                            ?>">

                                <!-- Protège l'envoi de l'avis contre les requêtes CSRF. -->
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?php
                                            echo htmlspecialchars(
                                                $reviewCsrfToken
                                            );
                                            ?>">

                                <!-- -------------------------------------------------- -->
                                <!-- note de l'avis -->
                                <!-- -------------------------------------------------- -->

                                <fieldset class="mb-3">

                                    <legend class="form-label">
                                        Note
                                    </legend>

                                    <div class="d-flex flex-wrap gap-3">

                                        <?php for (
                                            $rating = 1;
                                            $rating <= 5;
                                            $rating++
                                        ): ?>

                                            <div class="form-check">

                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="rating"
                                                    id="rating-<?php
                                                                echo $rating;
                                                                ?>"
                                                    value="<?php
                                                            echo $rating;
                                                            ?>"
                                                    required>

                                                <label
                                                    class="form-check-label"
                                                    for="rating-<?php
                                                                echo $rating;
                                                                ?>">

                                                    <?php
                                                    echo $rating;
                                                    ?>

                                                    étoile<?php
                                                            echo $rating > 1
                                                                ? 's'
                                                                : '';
                                                            ?>

                                                </label>

                                            </div>

                                        <?php endfor; ?>

                                    </div>

                                </fieldset>

                                <!-- -------------------------------------------------- -->
                                <!-- commentaire de l'avis -->
                                <!-- -------------------------------------------------- -->

                                <div class="mb-3">

                                    <label
                                        for="review-comment"
                                        class="form-label">
                                        Commentaire
                                    </label>

                                    <textarea
                                        class="form-control"
                                        id="review-comment"
                                        name="comment"
                                        rows="5"
                                        minlength="10"
                                        maxlength="1000"
                                        required></textarea>

                                    <small class="form-text">
                                        Entre 10 et 1 000 caractères.
                                    </small>

                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-custom">
                                    Envoyer mon avis
                                </button>

                            </form>

                        <?php else: ?>

                            <?php

                            /* -------------------------------------------------- */
                            /* préparation de l'avis existant */
                            /* -------------------------------------------------- */

                            /* Traduit le statut technique de modération. */
                            $reviewStatusLabels = [
                                'pending' =>
                                'En attente de validation',
                                'approved' =>
                                'Avis validé',
                                'refused' =>
                                'Avis refusé',
                            ];

                            /* Récupère le libellé correspondant au statut. */
                            $reviewStatus =
                                $reviewStatusLabels[$review['moderation_status']]
                                ?? 'Statut inconnu';

                            ?>

                            <!-- -------------------------------------------------- -->
                            <!-- avis déjà envoyé -->
                            <!-- -------------------------------------------------- -->

                            <div class="user-order-detail-grid">

                                <div class="user-order-detail-item">

                                    <span class="user-order-label">
                                        Note
                                    </span>

                                    <strong>
                                        <?php
                                        echo (int)
                                        $review['rating'];
                                        ?>
                                        / 5
                                    </strong>

                                </div>

                                <div class="user-order-detail-item">

                                    <span class="user-order-label">
                                        Statut
                                    </span>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $reviewStatus
                                        );
                                        ?>
                                    </strong>

                                </div>

                            </div>

                            <!-- Affiche le commentaire envoyé par le client. -->
                            <div class="mt-4">

                                <span class="user-order-label">
                                    Votre commentaire
                                </span>

                                <p class="mb-0 mt-2">
                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $review['comment']
                                        )
                                    );
                                    ?>
                                </p>

                            </div>

                            <!-- Informe le client lorsque son avis attend une validation. -->
                            <?php if (
                                $review['moderation_status']
                                === 'pending'
                            ): ?>

                                <p class="form-text mt-3 mb-0">
                                    Votre avis sera publié après validation
                                    par notre équipe.
                                </p>

                            <?php endif; ?>

                        <?php endif; ?>

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

                        <?php

                        /* Compte les étapes de l'historique. */
                        $historyCount =
                            count($statusHistory);

                        ?>

                        <ol class="user-order-timeline">

                            <?php foreach (
                                $statusHistory
                                as $index => $history
                            ): ?>

                                <?php

                                /* Prépare la date du changement de statut. */
                                $statusDate = date_create(
                                    $history['created_at']
                                );

                                /* Identifie le dernier statut de l'historique. */
                                $isCurrentStatus =
                                    $index
                                    === $historyCount - 1;

                                ?>

                                <li
                                    class="<?php
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
                                            class="
                                                user-order-timeline-heading
                                            ">

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

                                            <!-- Identifie visuellement le statut actuel. -->
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
                                            class="
                                                user-order-timeline-date
                                            "
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

                                        <!-- Affiche la note associée au changement de statut. -->
                                        <?php if (
                                            !empty($history['note'])
                                        ): ?>

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

                        <!-- -------------------------------------------------- -->
                        <!-- absence d'historique -->
                        <!-- -------------------------------------------------- -->

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