<?php

/** @var string $errorTitle */
/** @var string $errorMessage */
/** @var array $errors */
/** @var string $returnUrl */

?>
<section
    class="order-error-section py-5"
    aria-labelledby="order-error-title">

    <div class="container">

        <div
            class="order-error-card mx-auto"
            role="alert">

            <!-- -------------------------------------------------- -->
            <!-- en-tête -->
            <!-- -------------------------------------------------- -->

            <div class="order-error-header text-center">

                <div
                    class="order-error-icon"
                    aria-hidden="true">
                    !
                </div>

                <p class="section-subtitle mb-2">
                    Vérification nécessaire
                </p>

                <h1
                    class="order-error-title"
                    id="order-error-title">
                    <?php echo htmlspecialchars($errorTitle); ?>
                </h1>

                <p class="order-error-introduction">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </p>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- erreurs -->
            <!-- -------------------------------------------------- -->

            <?php if (!empty($errors)): ?>
                <div class="order-error-details">

                    <h2 class="order-error-subtitle">
                        Informations à corriger
                    </h2>

                    <ul class="order-error-list">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?php echo htmlspecialchars($error); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- action -->
            <!-- -------------------------------------------------- -->

            <div class="order-error-actions">
                <a
                    href="<?php echo htmlspecialchars($returnUrl); ?>"
                    class="btn btn-custom">
                    Retour au formulaire
                </a>
            </div>

        </div>

    </div>

</section>