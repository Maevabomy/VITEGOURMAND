<?php

/** @var array $reviews */
/** @var string $reviewCsrfToken */
/** @var string|null $success */
/** @var string|null $error */

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Modération des avis
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Vérifiez les avis clients avant leur publication
            sur la page d’accueil.
        </p>

    </div>
</section>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

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

        <section class="employee-dashboard-card">

            <div class="employee-orders-heading">

                <div class="employee-dashboard-card-header mb-0">

                    <p class="section-subtitle mb-2">
                        Avis clients
                    </p>

                    <h2 class="employee-dashboard-card-title">
                        En attente de validation
                    </h2>

                </div>

                <span class="employee-order-count">
                    <?php echo count($reviews); ?>
                    avis
                </span>

            </div>

            <?php if (!empty($reviews)): ?>

                <div class="employee-review-list">

                    <?php foreach ($reviews as $review): ?>

                        <article class="employee-review-card">

                            <div class="employee-review-header">

                                <div>
                                    <h3 class="employee-order-client">
                                        <?php
                                        echo htmlspecialchars(
                                            $review['first_name']
                                            . ' '
                                            . $review['last_name']
                                        );
                                        ?>
                                    </h3>

                                    <p class="employee-order-email mb-0">
                                        <?php
                                        echo htmlspecialchars(
                                            $review['email']
                                        );
                                        ?>
                                    </p>
                                </div>

                                <div class="employee-review-rating">
                                    <?php
                                    echo str_repeat(
                                        '★',
                                        (int) $review['rating']
                                    );

                                    echo str_repeat(
                                        '☆',
                                        5 - (int) $review['rating']
                                    );
                                    ?>
                                </div>

                            </div>

                            <div class="employee-review-meta">

                                <div>
                                    <span class="user-order-label">
                                        Commande
                                    </span>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $review['order_number']
                                        );
                                        ?>
                                    </strong>
                                </div>

                                <div>
                                    <span class="user-order-label">
                                        Menu
                                    </span>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $review['menu_title']
                                        );
                                        ?>
                                    </strong>
                                </div>

                                <div>
                                    <span class="user-order-label">
                                        Déposé le
                                    </span>

                                    <strong>
                                        <?php
                                        echo date(
                                            'd/m/Y à H\hi',
                                            strtotime(
                                                $review['created_at']
                                            )
                                        );
                                        ?>
                                    </strong>
                                </div>

                            </div>

                            <blockquote class="employee-review-comment">
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $review['comment']
                                    )
                                );
                                ?>
                            </blockquote>

                            <div class="employee-review-actions">

                                <form
                                    action="<?php
                                    echo BASE_URL
                                        . '/employee/review/moderate';
                                    ?>"
                                    method="post">

                                    <input
                                        type="hidden"
                                        name="review_id"
                                        value="<?php
                                        echo (int) $review['id'];
                                        ?>">

                                    <input
                                        type="hidden"
                                        name="moderation_status"
                                        value="approved">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php
                                        echo htmlspecialchars(
                                            $reviewCsrfToken
                                        );
                                        ?>">

                                    <button
                                        type="submit"
                                        class="btn btn-custom">
                                        Approuver
                                    </button>

                                </form>

                                <form
                                    action="<?php
                                    echo BASE_URL
                                        . '/employee/review/moderate';
                                    ?>"
                                    method="post"
                                    onsubmit="
                                        return confirm(
                                            'Refuser cet avis ?'
                                        );
                                    ">

                                    <input
                                        type="hidden"
                                        name="review_id"
                                        value="<?php
                                        echo (int) $review['id'];
                                        ?>">

                                    <input
                                        type="hidden"
                                        name="moderation_status"
                                        value="refused">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php
                                        echo htmlspecialchars(
                                            $reviewCsrfToken
                                        );
                                        ?>">

                                    <button
                                        type="submit"
                                        class="
                                            btn
                                            employee-review-refuse-button
                                        ">
                                        Refuser
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="user-orders-empty">

                    <h3 class="user-orders-empty-title">
                        Aucun avis à modérer
                    </h3>

                    <p class="mb-0">
                        Tous les avis reçus ont déjà été traités.
                    </p>

                </div>

            <?php endif; ?>

        </section>

        <div class="mt-4">
            <a
                href="<?php
                echo BASE_URL . '/employee/dashboard';
                ?>"
                class="btn employee-filter-reset-button">
                Retour aux commandes
            </a>
        </div>

    </div>
</section>