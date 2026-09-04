<!-- -------------------------------------------------- -->
<!-- bannière principale -->
<!-- -------------------------------------------------- -->

<section class="hero-section">
    <div class="container">

        <div class="hero-content">

            <p class="hero-subtitle">
                Traiteur événementiel à Bordeaux
            </p>

            <h1>
                Des menus gourmands pour vos plus beaux moments
            </h1>

            <p class="hero-description">
                Depuis 25 ans, Julie et José imaginent des prestations généreuses
                et soignées pour vos repas, fêtes et événements.
            </p>

            <a
                href="<?php echo BASE_URL; ?>/menus"
                class="btn btn-custom">
                Découvrir nos menus
            </a>

        </div>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- présentation de l'entreprise -->
<!-- -------------------------------------------------- -->

<section class="presentation-section py-5">
    <div class="container py-4">

        <div class="row align-items-center gy-4 gx-lg-5">

            <!-- Illustre une prestation événementielle. -->
            <div class="col-lg-6">

                <div class="presentation-image-wrapper">

                    <img
                        src="<?php
                            echo BASE_URL;
                        ?>/assets/images/home/event.jpg"
                        alt="Réception événementielle de nuit sur la Garonne à Bordeaux"
                        class="presentation-image"
                        loading="lazy">

                </div>

            </div>

            <!-- Présente l'histoire de Vite & Gourmand. -->
            <div class="col-lg-6">

                <p class="section-subtitle">
                    Notre histoire
                </p>

                <h2>
                    Une cuisine faite avec passion
                </h2>

                <p>
                    Vite & Gourmand accompagne les particuliers
                    et les entreprises dans l'organisation
                    de leurs événements à Bordeaux.
                </p>

                <p class="mb-0">
                    Chaque prestation est préparée avec attention
                    afin de proposer une expérience chaleureuse,
                    professionnelle et savoureuse.
                </p>

            </div>

        </div>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- savoir-faire et valeurs -->
<!-- -------------------------------------------------- -->

<section class="values-section py-5">
    <div class="container py-4">

        <!-- Présente la section consacrée au savoir-faire. -->
        <div class="text-center mb-5">

            <p class="section-subtitle">
                Notre savoir-faire
            </p>

            <h2>
                Une équipe attentive à chaque détail
            </h2>

        </div>

        <div class="row gy-4">

            <!-- Expérience de l'entreprise. -->
            <div class="col-md-4">

                <article class="value-card">

                    <h3>
                        25 ans d'expérience
                    </h3>

                    <p>
                        Une entreprise bordelaise reconnue pour son sérieux
                        et la qualité de ses prestations.
                    </p>

                </article>

            </div>

            <!-- Diversité des menus. -->
            <div class="col-md-4">

                <article class="value-card">

                    <h3>
                        Des menus variés
                    </h3>

                    <p>
                        Des propositions adaptées aux saisons, aux événements
                        et aux différents régimes alimentaires.
                    </p>

                </article>

            </div>

            <!-- Accompagnement proposé aux clients. -->
            <div class="col-md-4">

                <article class="value-card">

                    <h3>
                        Un accompagnement soigné
                    </h3>

                    <p>
                        Une équipe disponible pour préparer votre réception
                        dans les meilleures conditions.
                    </p>

                </article>

            </div>

        </div>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- avis clients -->
<!-- -------------------------------------------------- -->

<section class="reviews-section py-5">
    <div class="container py-4">

        <!-- -------------------------------------------------- -->
        <!-- présentation des avis -->
        <!-- -------------------------------------------------- -->

        <div class="text-center mb-5">

            <p class="section-subtitle">
                Avis clients
            </p>

            <h2>
                Ils nous font confiance
            </h2>

        </div>

        <!-- -------------------------------------------------- -->
        <!-- carrousel des avis validés -->
        <!-- -------------------------------------------------- -->

        <?php if (!empty($reviews)): ?>

            <div
                id="reviews-carousel"
                class="carousel slide"
                data-bs-ride="carousel"
                data-bs-interval="6000"
                data-bs-pause="hover">

                <!-- Affiche un indicateur pour chaque avis. -->
                <div class="carousel-indicators">

                    <?php foreach ($reviews as $index => $review): ?>

                        <button
                            type="button"
                            data-bs-target="#reviews-carousel"
                            data-bs-slide-to="<?php echo $index; ?>"
                            class="<?php
                                echo $index === 0
                                    ? 'active'
                                    : '';
                            ?>"
                            aria-current="<?php
                                echo $index === 0
                                    ? 'true'
                                    : 'false';
                            ?>"
                            aria-label="Afficher l'avis <?php
                                echo $index + 1;
                            ?>">
                        </button>

                    <?php endforeach; ?>

                </div>

                <!-- Affiche les avis un par un. -->
                <div class="carousel-inner">

                    <?php foreach ($reviews as $index => $review): ?>

                        <div
                            class="carousel-item <?php
                                echo $index === 0
                                    ? 'active'
                                    : '';
                            ?>">

                            <article
                                class="review-card review-card-carousel mx-auto">

                                <!-- Affiche la note du client sur cinq étoiles. -->
                                <p class="review-stars">
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
                                </p>

                                <!-- Affiche le commentaire validé. -->
                                <p class="review-comment">
                                    « <?php
                                    echo htmlspecialchars(
                                        $review['comment']
                                    );
                                    ?> »
                                </p>

                                <!-- Affiche le nom de l'auteur de l'avis. -->
                                <p class="review-author mb-0">
                                    —
                                    <?php
                                    echo htmlspecialchars(
                                        $review['first_name']
                                        . ' '
                                        . $review['last_name']
                                    );
                                    ?>
                                </p>

                            </article>

                        </div>

                    <?php endforeach; ?>

                </div>

                <!-- Affiche la flèche précédente. -->
                <button
                    class="carousel-control-prev"
                    type="button"
                    data-bs-target="#reviews-carousel"
                    data-bs-slide="prev">

                    <span
                        class="carousel-control-prev-icon"
                        aria-hidden="true">
                    </span>

                    <span class="visually-hidden">
                        Avis précédent
                    </span>

                </button>

                <!-- Affiche la flèche suivante. -->
                <button
                    class="carousel-control-next"
                    type="button"
                    data-bs-target="#reviews-carousel"
                    data-bs-slide="next">

                    <span
                        class="carousel-control-next-icon"
                        aria-hidden="true">
                    </span>

                    <span class="visually-hidden">
                        Avis suivant
                    </span>

                </button>

            </div>

        <?php else: ?>

            <!-- Affiche un message lorsqu'aucun avis validé n'est disponible. -->
            <p class="text-center mb-0">
                Aucun avis client n'est disponible pour le moment.
            </p>

        <?php endif; ?>

    </div>
</section>