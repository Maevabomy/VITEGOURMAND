<?php

/** @var array|null $menu */
/** @var array $themes */
/** @var array $dietaryTypes */
/** @var array $availableDishes */
/** @var string $csrfToken */
/** @var string|null $formError */


/* -------------------------------------------------- */
/* préparation du formulaire */
/* -------------------------------------------------- */

/* Indique si le formulaire sert à modifier un menu existant. */
$isEditing =
    !empty($menu['id']);

/* Récupère les plats déjà associés au menu. */
$selectedDishIds = array_map(
    'intval',
    $menu['dish_ids'] ?? []
);

/* Associe chaque type de plat à son libellé français. */
$dishTypeLabels = [
    'starter' => 'Entrées',
    'main_course' => 'Plats',
    'dessert' => 'Desserts',
];

?>

<!-- -------------------------------------------------- -->
<!-- présentation du formulaire de menu -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Gestion des menus
        </p>

        <h1 class="employee-dashboard-title">
            <?php
            echo $isEditing
                ? 'Modifier un menu'
                : 'Créer un menu';
            ?>
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Renseignez les informations du menu et choisissez
            les plats qui composeront sa galerie.
        </p>

    </div>
</section>

<?php

/* Charge la navigation de l'espace employé. */
require BASE_PATH
    . '/app/Views/employee/_navigation.php';

?>

<!-- -------------------------------------------------- -->
<!-- formulaire de création ou modification -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <!-- Affiche le message d'erreur en cas d'échec. -->
        <?php if (!empty($formError)): ?>

            <div
                class="alert alert-danger"
                role="alert">
                <?php echo htmlspecialchars($formError); ?>
            </div>

        <?php endif; ?>

        <form
            action="<?php
                echo BASE_URL;
            ?>/employee/menu/save"
            method="post"
            class="employee-dashboard-card employee-menu-form">

            <!-- Protège le formulaire contre les requêtes CSRF. -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                    echo htmlspecialchars(
                        $csrfToken
                    );
                ?>">

            <!-- Transmet l'identifiant du menu lors d'une modification. -->
            <?php if ($isEditing): ?>

                <input
                    type="hidden"
                    name="menu_id"
                    value="<?php
                        echo (int) $menu['id'];
                    ?>">

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- informations principales du menu -->
            <!-- -------------------------------------------------- -->

            <div class="row g-4">

                <!-- Titre du menu. -->
                <div class="col-md-8">

                    <label
                        for="menu-title"
                        class="form-label">
                        Titre du menu *
                    </label>

                    <input
                        type="text"
                        id="menu-title"
                        name="title"
                        maxlength="150"
                        required
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $menu['title'] ?? ''
                            );
                        ?>">

                </div>

                <!-- Thème du menu. -->
                <div class="col-md-4">

                    <label
                        for="menu-theme"
                        class="form-label">
                        Thème *
                    </label>

                    <select
                        id="menu-theme"
                        name="theme_id"
                        required
                        class="form-select">

                        <option value="">
                            Sélectionner
                        </option>

                        <?php foreach ($themes as $theme): ?>

                            <option
                                value="<?php
                                    echo (int) $theme['id'];
                                ?>"
                                <?php
                                echo (
                                    (int) (
                                        $menu['theme_id']
                                        ?? 0
                                    )
                                    === (int) $theme['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>>
                                <?php
                                echo htmlspecialchars(
                                    $theme['name']
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Régime alimentaire du menu. -->
                <div class="col-md-4">

                    <label
                        for="menu-diet"
                        class="form-label">
                        Régime *
                    </label>

                    <select
                        id="menu-diet"
                        name="dietary_type_id"
                        required
                        class="form-select">

                        <option value="">
                            Sélectionner
                        </option>

                        <?php foreach (
                            $dietaryTypes
                            as $dietaryType
                        ): ?>

                            <option
                                value="<?php
                                    echo (int)
                                        $dietaryType['id'];
                                ?>"
                                <?php
                                echo (
                                    (int) (
                                        $menu['dietary_type_id']
                                        ?? 0
                                    )
                                    === (int) $dietaryType['id']
                                )
                                    ? 'selected'
                                    : '';
                                ?>>
                                <?php
                                echo htmlspecialchars(
                                    $dietaryType['name']
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Nombre minimum de personnes. -->
                <div class="col-md-4">

                    <label
                        for="menu-minimum-people"
                        class="form-label">
                        Nombre minimum de personnes *
                    </label>

                    <input
                        type="number"
                        id="menu-minimum-people"
                        name="minimum_people"
                        min="1"
                        step="1"
                        required
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                (string) (
                                    $menu['minimum_people']
                                    ?? ''
                                )
                            );
                        ?>">

                </div>

                <!-- Prix du menu pour le nombre minimum de personnes. -->
                <div class="col-md-4">

                    <label
                        for="menu-base-price"
                        class="form-label">
                        Prix pour le minimum *
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            id="menu-base-price"
                            name="base_price"
                            min="0.01"
                            step="0.01"
                            required
                            class="form-control"
                            value="<?php
                                echo htmlspecialchars(
                                    (string) (
                                        $menu['base_price']
                                        ?? ''
                                    )
                                );
                            ?>">

                        <span class="input-group-text">
                            €
                        </span>

                    </div>

                </div>

                <!-- Stock disponible pour le menu. -->
                <div class="col-md-4">

                    <label
                        for="menu-stock"
                        class="form-label">
                        Stock disponible *
                    </label>

                    <input
                        type="number"
                        id="menu-stock"
                        name="stock_quantity"
                        min="0"
                        step="1"
                        required
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                (string) (
                                    $menu['stock_quantity']
                                    ?? ''
                                )
                            );
                        ?>">

                    <p class="form-text">
                        Nombre de personnes encore disponibles.
                    </p>

                </div>

                <!-- Délai minimum avant la prestation. -->
                <div class="col-md-4">

                    <label
                        for="menu-order-days"
                        class="form-label">
                        Délai minimum *
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            id="menu-order-days"
                            name="minimum_order_days"
                            min="0"
                            step="1"
                            required
                            class="form-control"
                            value="<?php
                                echo htmlspecialchars(
                                    (string) (
                                        $menu['minimum_order_days']
                                        ?? 1
                                    )
                                );
                            ?>">

                        <span class="input-group-text">
                            jours
                        </span>

                    </div>

                </div>

                <!-- Date de début de disponibilité du menu. -->
                <div class="col-md-4">

                    <label
                        for="menu-available-from"
                        class="form-label">
                        Disponible à partir du
                    </label>

                    <input
                        type="date"
                        id="menu-available-from"
                        name="available_from"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $menu['available_from']
                                    ?? ''
                            );
                        ?>">

                </div>

                <!-- Date de fin de disponibilité du menu. -->
                <div class="col-md-4">

                    <label
                        for="menu-available-until"
                        class="form-label">
                        Disponible jusqu’au
                    </label>

                    <input
                        type="date"
                        id="menu-available-until"
                        name="available_until"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $menu['available_until']
                                    ?? ''
                            );
                        ?>">

                </div>

                <!-- -------------------------------------------------- -->
                <!-- description et conditions -->
                <!-- -------------------------------------------------- -->

                <!-- Description publique du menu. -->
                <div class="col-12">

                    <label
                        for="menu-description"
                        class="form-label">
                        Description *
                    </label>

                    <textarea
                        id="menu-description"
                        name="description"
                        rows="5"
                        required
                        class="form-control"><?php
                        echo htmlspecialchars(
                            $menu['description'] ?? ''
                        );
                    ?></textarea>

                </div>

                <!-- Conditions particulières du menu. -->
                <div class="col-12">

                    <label
                        for="menu-conditions"
                        class="form-label">
                        Conditions *
                    </label>

                    <textarea
                        id="menu-conditions"
                        name="conditions"
                        rows="4"
                        required
                        class="form-control"><?php
                        echo htmlspecialchars(
                            $menu['conditions'] ?? ''
                        );
                    ?></textarea>

                    <p class="form-text">
                        Exemple : délai de commande ou précautions
                        de conservation.
                    </p>

                </div>

            </div>

            <hr class="employee-menu-divider">

            <!-- -------------------------------------------------- -->
            <!-- composition du menu -->
            <!-- -------------------------------------------------- -->

            <div class="employee-menu-dish-selection">

                <div class="mb-4">

                    <p class="section-subtitle mb-2">
                        Composition
                    </p>

                    <h2 class="employee-dashboard-card-title">
                        Plats associés
                    </h2>

                    <p class="employee-menu-help">
                        Sélectionnez au minimum une entrée, un plat
                        et un dessert. Les photos de ces plats
                        constitueront automatiquement la galerie
                        du menu.
                    </p>

                </div>

                <!-- Affiche les plats disponibles par catégorie. -->
                <?php foreach (
                    $dishTypeLabels
                    as $dishType => $dishTypeLabel
                ): ?>

                    <fieldset class="employee-menu-dish-group">

                        <legend>
                            <?php
                            echo htmlspecialchars(
                                $dishTypeLabel
                            );
                            ?>
                        </legend>

                        <div class="employee-menu-dish-grid">

                            <?php

                            /* Indique si au moins un plat existe dans cette catégorie. */
                            $hasDishForType = false;

                            ?>

                            <?php foreach (
                                $availableDishes
                                as $dish
                            ): ?>

                                <?php

                                /* Ignore les plats qui ne correspondent pas à la catégorie. */
                                if (
                                    $dish['dish_type']
                                    !== $dishType
                                ) {
                                    continue;
                                }

                                $hasDishForType = true;

                                /* Récupère l'identifiant du plat courant. */
                                $dishId =
                                    (int) $dish['id'];

                                ?>

                                <label
                                    class="employee-menu-dish-option"
                                    for="menu-dish-<?php
                                        echo $dishId;
                                    ?>">

                                    <input
                                        type="checkbox"
                                        id="menu-dish-<?php
                                            echo $dishId;
                                        ?>"
                                        name="dish_ids[]"
                                        value="<?php
                                            echo $dishId;
                                        ?>"
                                        class="form-check-input"
                                        <?php
                                        echo in_array(
                                            $dishId,
                                            $selectedDishIds,
                                            true
                                        )
                                            ? 'checked'
                                            : '';
                                        ?>>

                                    <!-- Affiche la photo du plat lorsqu'elle existe. -->
                                    <?php if (
                                        !empty(
                                            $dish['photo_mime_type']
                                        )
                                    ): ?>

                                        <img
                                            src="<?php
                                                echo BASE_URL
                                                    . '/dish/image?id='
                                                    . $dishId;
                                            ?>"
                                            alt=""
                                            class="employee-menu-dish-photo">

                                    <?php endif; ?>

                                    <span>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $dish['name']
                                            );
                                            ?>
                                        </strong>

                                        <!-- Affiche les allergènes renseignés pour le plat. -->
                                        <?php if (
                                            !empty(
                                                $dish['allergen_names']
                                            )
                                        ): ?>

                                            <small>
                                                Allergènes :
                                                <?php
                                                echo htmlspecialchars(
                                                    $dish['allergen_names']
                                                );
                                                ?>
                                            </small>

                                        <?php endif; ?>

                                    </span>

                                </label>

                            <?php endforeach; ?>

                            <!-- Affiche un message si aucun plat n'est disponible dans la catégorie. -->
                            <?php if (!$hasDishForType): ?>

                                <p class="employee-menu-help">
                                    Aucun plat actif disponible
                                    dans cette catégorie.
                                </p>

                            <?php endif; ?>

                        </div>

                    </fieldset>

                <?php endforeach; ?>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- actions du formulaire -->
            <!-- -------------------------------------------------- -->

            <div class="employee-menu-form-actions">

                <a
                    href="<?php
                        echo BASE_URL;
                    ?>/employee/menus"
                    class="btn btn-outline-light">
                    Annuler
                </a>

                <button
                    type="submit"
                    class="btn btn-custom">
                    <?php
                    echo $isEditing
                        ? 'Enregistrer les modifications'
                        : 'Créer le menu';
                    ?>
                </button>

            </div>

        </form>

    </div>
</section>