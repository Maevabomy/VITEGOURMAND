<?php
/* Indique les variables préparées par le contrôleur. */
/** @var array $menus */
/** @var array $themes */
/** @var array $dietaryTypes */
?>

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

        <?php if (!empty($menus)): ?>

            <!-- -------------------------------------------------- -->
            <!-- filtres des menus -->
            <!-- -------------------------------------------------- -->

            <section class="menu-filters mb-5" aria-labelledby="filters-title">

                <div class="menu-filters-header">
                    <div>
                        <h2 id="filters-title" class="menu-filters-title">
                            Affiner votre recherche
                        </h2>

                        <p class="menu-filters-description mb-0">
                            Sélectionnez les critères correspondant à votre événement.
                        </p>
                    </div>

                    <p
                        id="menus-count"
                        class="menus-count mb-0"
                        aria-live="polite">
                        <?php echo count($menus); ?> menus disponibles
                    </p>
                </div>

                <div class="row g-3 align-items-end mt-1">

                    <div class="col-xl-2 col-md-4">
                        <label for="minimum-price" class="form-label">
                            Prix minimum
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                id="minimum-price"
                                class="form-control"
                                min="0"
                                step="1"
                                placeholder="Ex. 150">

                            <span class="input-group-text">
                                €
                            </span>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label for="maximum-price" class="form-label">
                            Prix maximum
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                id="maximum-price"
                                class="form-control"
                                min="0"
                                step="1"
                                placeholder="Ex. 300">

                            <span class="input-group-text">
                                €
                            </span>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label for="theme-filter" class="form-label">
                            Thème
                        </label>

                        <select id="theme-filter" class="form-select">
                            <option value="">
                                Tous les thèmes
                            </option>

                            <?php foreach ($themes as $theme): ?>
                                <option value="<?php echo htmlspecialchars($theme); ?>">
                                    <?php echo htmlspecialchars($theme); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label for="dietary-type-filter" class="form-label">
                            Régime
                        </label>

                        <select id="dietary-type-filter" class="form-select">
                            <option value="">
                                Tous les régimes
                            </option>

                            <?php foreach ($dietaryTypes as $dietaryType): ?>
                                <option
                                    value="<?php echo htmlspecialchars($dietaryType); ?>">
                                    <?php echo htmlspecialchars($dietaryType); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label for="people-filter" class="form-label">
                            Nombre de convives
                        </label>

                        <input
                            type="number"
                            id="people-filter"
                            class="form-control"
                            min="1"
                            step="1"
                            placeholder="Ex. 8">
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <button
                            type="button"
                            id="reset-filters"
                            class="btn btn-filter-reset w-100">
                            Réinitialiser
                        </button>
                    </div>

                </div>
            </section>

            <!-- -------------------------------------------------- -->
            <!-- liste des menus -->
            <!-- -------------------------------------------------- -->

            <div id="menus-list" class="row gy-4">

                <?php foreach ($menus as $menu): ?>

                    <?php
                    $pricePerPerson = $menu['base_price']
                        / $menu['minimum_people'];

                    $carouselId = 'menu-carousel-' . $menu['id'];
                    ?>

                    <div
                        class="col-xl-6 menu-item"
                        data-price="<?php echo $menu['base_price']; ?>"
                        data-theme="<?php echo htmlspecialchars($menu['theme_name']); ?>"
                        data-dietary-type="<?php
                                            echo htmlspecialchars($menu['dietary_type_name']);
                                            ?>"
                        data-minimum-people="<?php echo $menu['minimum_people']; ?>">
                        <article class="menu-card">

                            <!-- -------------------------------------------------- -->
                            <!-- galerie des plats -->
                            <!-- -------------------------------------------------- -->

                            <div class="menu-card-gallery">

                                <div
                                    id="<?php echo $carouselId; ?>"
                                    class="carousel slide h-100">
                                    <div class="carousel-inner h-100">

                                        <?php foreach ($menu['dishes'] as $index => $dish): ?>

                                            <div
                                                class="carousel-item h-100
                                                <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img
                                                    src="<?php echo BASE_URL; ?>/dish/image?id=<?php echo $dish['id']; ?>"
                                                    class="menu-card-image"
                                                    alt="<?php echo htmlspecialchars($dish['name']); ?>">
                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                    <!-- Affiche la flèche précédente. -->
                                    <button
                                        class="carousel-control-prev"
                                        type="button"
                                        data-bs-target="#<?php echo $carouselId; ?>"
                                        data-bs-slide="prev">
                                        <span
                                            class="carousel-control-prev-icon"
                                            aria-hidden="true"></span>

                                        <span class="visually-hidden">
                                            Photo précédente
                                        </span>
                                    </button>

                                    <!-- Affiche la flèche suivante. -->
                                    <button
                                        class="carousel-control-next"
                                        type="button"
                                        data-bs-target="#<?php echo $carouselId; ?>"
                                        data-bs-slide="next">
                                        <span
                                            class="carousel-control-next-icon"
                                            aria-hidden="true"></span>

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
                                    href="<?php echo BASE_URL; ?>/menus/detail?id=<?php echo $menu['id']; ?>"
                                    class="btn btn-custom mt-3">
                                    Voir le détail
                                </a>

                            </div>

                        </article>
                    </div>

                <?php endforeach; ?>

            </div>

            <!-- Affiche un message lorsque la recherche ne donne aucun résultat. -->
            <p
                id="no-menu-message"
                class="no-menu-message d-none text-center mb-0"
                aria-live="polite">
                Aucun menu ne correspond à votre recherche.
            </p>

        <?php else: ?>

            <p class="text-center mb-0">
                Aucun menu n'est disponible pour le moment.
            </p>

        <?php endif; ?>

    </div>
</section>

<script
    src="<?php echo BASE_URL; ?>/assets/js/menu-filters.js"
    defer>
</script>