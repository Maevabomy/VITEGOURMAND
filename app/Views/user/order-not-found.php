<?php
/* Indique les variables préparées par le contrôleur. */

/** @var string $errorTitle */
/** @var string $errorMessage */
?>

<section class="user-dashboard-section py-5">
    <div class="container py-5">

        <div class="user-order-error">

            <p class="section-subtitle mb-2">
                Erreur 404
            </p>

            <h1 class="user-dashboard-title">
                <?php echo htmlspecialchars($errorTitle); ?>
            </h1>

            <p class="user-dashboard-introduction">
                <?php echo htmlspecialchars($errorMessage); ?>
            </p>

            <a
                href="<?php echo BASE_URL; ?>/user/dashboard"
                class="btn user-order-detail-button">
                Retour à mes commandes
            </a>

        </div>

    </div>
</section>