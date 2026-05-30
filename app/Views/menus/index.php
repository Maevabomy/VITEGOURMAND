<section class="menus-hero-section">
    <div class="container py-5">

        <!-- -------------------------------------------------- -->
        <!-- titre de la page -->
        <!-- -------------------------------------------------- -->

        <p class="section-subtitle mb-2">
            Notre sélection
        </p>

        <h1 class="menus-page-title">
            Nos menus
        </h1>

        <p class="menus-page-description mb-0">
            Découvrez des compositions pensées pour accompagner vos repas,
            réceptions et événements.
        </p>

    </div>
</section>

<section class="menus-catalog-section py-5">
    <div class="container py-4">

        <!-- -------------------------------------------------- -->
        <!-- liste des menus -->
        <!-- -------------------------------------------------- -->

        <?php if (!empty($menus)): ?>

            <div class="row gy-4">

                <?php foreach ($menus as $menu): ?>

                    <?php
                    $pricePerPerson = $menu['base_price']
                        / $menu['minimum_people'];

                    $carouselId = 'menu-carousel-' . $menu['id'];
                    ?>

                    <div class="col-xl-6">
                        <article class="menu-card">

                            <!-- -------------------------------------------------- -->
                            <!-- galerie des plats -->
                            <!-- -------------------------------------------------- -->

                            <div class="menu-card-gallery">

                                <div
                                    id="<?php echo $carouselId; ?>"
                                    class="carousel slide h-100"
                                >
                                    <div class="carousel-inner h-100">

                                        <?php foreach ($menu['dishes'] as $index => $dish): ?>

                                            <div
                                                class="carousel-item h-100
                                                <?php echo $index === 0 ? 'active' : ''; ?>"
                                            >
                                                <img
                                                    src="<?php echo BASE_URL; ?>/dish/image?id=<?php echo $dish['id']; ?>"
                                                    class="menu-card-image"
                                                    alt="<?php echo htmlspecialchars($dish['name']); ?>"
                                                >
                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                    <!-- Affiche la flèche précédente. -->
                                    <button
                                        class="carousel-control-prev"
                                        type="button"
                                        data-bs-target="#<?php echo $carouselId; ?>"
                                        data-bs-slide="prev"
                                    >
                                        <span
                                            class="carousel-control-prev-icon"
                                            aria-hidden="true"
                                        ></span>

                                        <span class="visually-hidden">
                                            Photo précédente
                                        </span>
                                    </button>

                                    <!-- Affiche la flèche suivante. -->
                                    <button
                                        class="carousel-control-next"
                                        type="button"
                                        data-bs-target="#<?php echo $carouselId; ?>"
                                        data-bs-slide="next"
                                    >
                                        <span
                                            class="carousel-control-next-icon"
                                            aria-hidden="true"
                                        ></span>

                                        <span class="visually-hidden">
                                            Photo suivante
                                        </span>
                                    </button>
                                </div>

                            </div>

                            <!-- -------------------------------------------------- -->
                            <!-- informations du menu -->
                            <!-- -------------------------------------------------- -->

                            <div class="menu-card-content">

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="menu-badge menu-badge-theme">
                                        <?php echo htmlspecialchars($menu['theme_name']); ?>
                                    </span>

                                    <span class="menu-badge menu-badge-diet">
                                        <?php
                                        echo htmlspecialchars(
                                            $menu['dietary_type_name']
                                        );
                                        ?>
                                    </span>
                                </div>

                                <h2 class="menu-card-title">
                                    <?php echo htmlspecialchars($menu['title']); ?>
                                </h2>

                                <p class="menu-card-description">
                                    <?php
                                    echo htmlspecialchars(
                                        $menu['description']
                                    );
                                    ?>
                                </p>

                                <div class="menu-card-price">
                                    <strong>
                                        <?php
                                        echo number_format(
                                            $pricePerPerson,
                                            2,
                                            ',',
                                            ' '
                                        );
                                        ?>
                                        € par personne
                                    </strong>

                                    <span>
                                        À partir de
                                        <?php
                                        echo number_format(
                                            $menu['base_price'],
                                            2,
                                            ',',
                                            ' '
                                        );
                                        ?>
                                        € pour
                                        <?php echo $menu['minimum_people']; ?>
                                        personnes
                                    </span>
                                </div>

                                <a
                                    href="#"
                                    class="btn btn-custom mt-3"
                                    aria-disabled="true"
                                >
                                    Voir le détail
                                </a>

                            </div>

                        </article>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p class="text-center mb-0">
                Aucun menu n'est disponible pour le moment.
            </p>

        <?php endif; ?>

    </div>
</section>