<?php

/** @var array $menu */


/* -------------------------------------------------- */
/* préparation de l'affichage */
/* -------------------------------------------------- */

/* Calcule le tarif indicatif pour une personne. */
$pricePerPerson =
    $menu['base_price']
    / $menu['minimum_people'];

/* Traduit les types techniques des plats en français. */
$dishTypeLabels = [
    'starter' => 'Entrée',
    'main_course' => 'Plat',
    'dessert' => 'Dessert',
];

/* Prépare un identifiant unique pour le carrousel. */
$carouselId =
    'menu-detail-carousel-'
    . $menu['id'];

/* Formate les dates de disponibilité du menu. */
$availableFrom = !empty($menu['available_from'])
    ? date(
        'd/m/Y',
        strtotime($menu['available_from'])
    )
    : null;

$availableUntil = !empty($menu['available_until'])
    ? date(
        'd/m/Y',
        strtotime($menu['available_until'])
    )
    : null;

?>

<!-- -------------------------------------------------- -->
<!-- présentation principale du menu -->
<!-- -------------------------------------------------- -->

<section class="menu-detail-section py-5">
    <div class="container py-4">

        <div class="row g-4 align-items-stretch">

            <!-- -------------------------------------------------- -->
            <!-- galerie des photos -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">

                <div class="menu-detail-gallery">

                    <div
                        id="<?php echo $carouselId; ?>"
                        class="carousel slide">

                        <div class="carousel-inner">

                            <?php foreach (
                                $menu['dishes']
                                as $index => $dish
                            ): ?>

                                <div
                                    class="carousel-item <?php
                                                            echo $index === 0
                                                                ? 'active'
                                                                : '';
                                                            ?>">

                                    <img
                                        src="<?php
                                                echo BASE_URL;
                                                ?>/dish/image?id=<?php
                                                                    echo $dish['id'];
                                                                    ?>"
                                        class="menu-detail-main-image"
                                        alt="<?php
                                                echo htmlspecialchars(
                                                    $dish['name']
                                                );
                                                ?>">

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <!-- Affiche la photo précédente. -->
                        <button
                            class="carousel-control-prev"
                            type="button"
                            data-bs-target="#<?php
                                                echo $carouselId;
                                                ?>"
                            data-bs-slide="prev">

                            <span
                                class="carousel-control-prev-icon"
                                aria-hidden="true">
                            </span>

                            <span class="visually-hidden">
                                Photo précédente
                            </span>

                        </button>

                        <!-- Affiche la photo suivante. -->
                        <button
                            class="carousel-control-next"
                            type="button"
                            data-bs-target="#<?php
                                                echo $carouselId;
                                                ?>"
                            data-bs-slide="next">

                            <span
                                class="carousel-control-next-icon"
                                aria-hidden="true">
                            </span>

                            <span class="visually-hidden">
                                Photo suivante
                            </span>

                        </button>

                    </div>

                    <!-- -------------------------------------------------- -->
                    <!-- miniatures des photos -->
                    <!-- -------------------------------------------------- -->

                    <div class="menu-detail-thumbnails mt-3">

                        <?php foreach (
                            $menu['dishes']
                            as $index => $dish
                        ): ?>

                            <button
                                type="button"
                                class="menu-detail-thumbnail <?php
                                                                echo $index === 0
                                                                    ? 'active'
                                                                    : '';
                                                                ?>"
                                data-bs-target="#<?php
                                                    echo $carouselId;
                                                    ?>"
                                data-bs-slide-to="<?php
                                                    echo $index;
                                                    ?>"
                                aria-label="Afficher <?php
                                                        echo htmlspecialchars(
                                                            $dish['name']
                                                        );
                                                        ?>">

                                <img
                                    src="<?php
                                            echo BASE_URL;
                                            ?>/dish/image?id=<?php
                                                                echo $dish['id'];
                                                                ?>"
                                    alt="">

                            </button>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- informations essentielles -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">

                <article class="menu-detail-summary h-100">

                    <p class="section-subtitle mb-2">
                        Notre sélection
                    </p>

                    <h1 class="menu-detail-title">
                        <?php
                        echo htmlspecialchars(
                            $menu['title']
                        );
                        ?>
                    </h1>

                    <!-- Affiche le thème, le régime et la disponibilité. -->
                    <div class="d-flex flex-wrap gap-2 my-3">

                        <span class="menu-badge menu-badge-theme">
                            <?php
                            echo htmlspecialchars(
                                $menu['theme_name']
                            );
                            ?>
                        </span>

                        <span class="menu-badge menu-badge-diet">
                            <?php
                            echo htmlspecialchars(
                                $menu['dietary_type_name']
                            );
                            ?>
                        </span>

                        <?php if ($menu['stock_quantity'] > 0): ?>

                            <span
                                class="
                                    menu-stock-badge
                                    menu-stock-available
                                ">
                                Disponible
                            </span>

                        <?php else: ?>

                            <span
                                class="
                                    menu-stock-badge
                                    menu-stock-unavailable
                                ">
                                Indisponible
                            </span>

                        <?php endif; ?>

                    </div>

                    <p class="menu-detail-description">
                        <?php
                        echo htmlspecialchars(
                            $menu['description']
                        );
                        ?>
                    </p>

                    <!-- -------------------------------------------------- -->
                    <!-- informations tarifaires -->
                    <!-- -------------------------------------------------- -->

                    <div class="menu-detail-pricing mt-4">

                        <div class="menu-detail-price-item">

                            <span>
                                Prix total minimum
                            </span>

                            <strong>
                                <?php
                                echo number_format(
                                    $menu['base_price'],
                                    2,
                                    ',',
                                    ' '
                                );
                                ?>
                                €
                            </strong>

                        </div>

                        <div class="menu-detail-price-item">

                            <span>
                                Tarif indicatif
                            </span>

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

                        </div>

                        <div class="menu-detail-price-item">

                            <span>
                                Commande minimum
                            </span>

                            <strong>
                                <?php
                                echo $menu['minimum_people'];
                                ?>
                                personnes
                            </strong>

                        </div>

                        <div class="menu-detail-price-item">

                            <span>
                                Stock disponible
                            </span>

                            <strong>
                                <?php
                                echo $menu['stock_quantity'];
                                ?>
                                portions disponibles
                            </strong>

                        </div>

                        <div class="menu-detail-price-item">

                            <span>
                                Délai de commande
                            </span>

                            <strong>
                                <?php
                                echo (int) $menu['minimum_order_days'];
                                ?>
                                jours minimum
                            </strong>

                        </div>

                        <div class="menu-detail-price-item">

                            <span>
                                Disponibilité
                            </span>

                            <strong>

                                <?php if (
                                    $availableFrom !== null
                                    && $availableUntil !== null
                                ): ?>

                                    Du <?php echo $availableFrom; ?>
                                    au <?php echo $availableUntil; ?>

                                <?php elseif ($availableFrom !== null): ?>

                                    À partir du
                                    <?php echo $availableFrom; ?>

                                <?php elseif ($availableUntil !== null): ?>

                                    Jusqu'au
                                    <?php echo $availableUntil; ?>

                                <?php else: ?>

                                    Toute l'année

                                <?php endif; ?>

                            </strong>

                        </div>

                    </div>

                    <!-- -------------------------------------------------- -->
                    <!-- commande du menu -->
                    <!-- -------------------------------------------------- -->

                    <?php if ($menu['stock_quantity'] > 0): ?>

                        <a
                            href="<?php
                                    echo BASE_URL;
                                    ?>/order/create?menu_id=<?php
                                                            echo (int) $menu['id'];
                                                            ?>"
                            class="
                                btn
                                btn-custom
                                menu-detail-order-button
                                mt-4
                            ">
                            Commander
                        </a>

                    <?php else: ?>

                        <button
                            type="button"
                            class="
                                btn
                                btn-custom
                                menu-detail-order-button
                                mt-4
                            "
                            disabled>
                            Menu indisponible
                        </button>

                    <?php endif; ?>

                </article>

            </div>

        </div>

        <!-- -------------------------------------------------- -->
        <!-- détail des plats -->
        <!-- -------------------------------------------------- -->

        <section class="menu-detail-dishes mt-5">

            <p class="section-subtitle mb-2">
                Composition
            </p>

            <h2 class="menu-detail-section-title">
                Découvrez les plats de ce menu
            </h2>

            <div class="menu-detail-dishes-list mt-4">

                <?php foreach ($menu['dishes'] as $dish): ?>

                    <?php

                    /* Récupère le libellé français du type de plat. */
                    $dishTypeLabel =
                        $dishTypeLabels[$dish['dish_type']]
                        ?? 'Plat';

                    ?>

                    <article class="menu-detail-dish-card">

                        <div class="row g-0 align-items-stretch">

                            <!-- Photo du plat. -->
                            <div class="col-md-4">

                                <img
                                    src="<?php
                                            echo BASE_URL;
                                            ?>/dish/image?id=<?php
                                                                echo $dish['id'];
                                                                ?>"
                                    class="menu-detail-dish-image"
                                    alt="<?php
                                            echo htmlspecialchars(
                                                $dish['name']
                                            );
                                            ?>">

                            </div>

                            <!-- Informations du plat. -->
                            <div class="col-md-8">

                                <div class="menu-detail-dish-content">

                                    <p class="menu-detail-dish-type">
                                        <?php
                                        echo $dishTypeLabel;
                                        ?>
                                    </p>

                                    <h3 class="menu-detail-dish-title">
                                        <?php
                                        echo htmlspecialchars(
                                            $dish['name']
                                        );
                                        ?>
                                    </h3>

                                    <p class="menu-detail-dish-description">
                                        <?php
                                        echo htmlspecialchars(
                                            $dish['description']
                                        );
                                        ?>
                                    </p>

                                    <!-- -------------------------------------------------- -->
                                    <!-- allergènes du plat -->
                                    <!-- -------------------------------------------------- -->

                                    <div class="menu-detail-allergens">

                                        <p
                                            class="
                                                menu-detail-allergens-title
                                                mb-2
                                            ">
                                            Allergènes
                                        </p>

                                        <?php if (
                                            !empty($dish['allergens'])
                                        ): ?>

                                            <div
                                                class="
                                                    d-flex
                                                    flex-wrap
                                                    gap-2
                                                ">

                                                <?php foreach (
                                                    $dish['allergens']
                                                    as $allergen
                                                ): ?>

                                                    <span
                                                        class="
                                                            menu-allergen-badge
                                                        ">
                                                        <?php
                                                        echo htmlspecialchars(
                                                            $allergen['name']
                                                        );
                                                        ?>
                                                    </span>

                                                <?php endforeach; ?>

                                            </div>

                                        <?php else: ?>

                                            <p
                                                class="
                                                    menu-detail-no-allergen
                                                    mb-0
                                                ">
                                                Aucun allergène obligatoire
                                                déclaré.
                                            </p>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

        <!-- -------------------------------------------------- -->
        <!-- conditions de commande -->
        <!-- -------------------------------------------------- -->

        <section class="menu-detail-conditions mt-5">

            <div
                class="menu-detail-conditions-icon"
                aria-hidden="true">

                <i class="fa-solid fa-circle-info"></i>

            </div>

            <div>

                <h2 class="menu-detail-conditions-title">
                    Conditions de commande
                </h2>

                <p class="mb-0">
                    <?php
                    echo htmlspecialchars(
                        $menu['conditions']
                    );
                    ?>
                </p>

            </div>

        </section>

    </div>
</section>