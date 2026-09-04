<?php

/** @var array|null $dish */
/** @var array $allergens */
/** @var string $csrfToken */
/** @var string|null $formError */


/* -------------------------------------------------- */
/* préparation du formulaire */
/* -------------------------------------------------- */

/* Indique si le formulaire sert à modifier un plat existant. */
$isEditing = !empty($dish['id']);

/* Récupère les allergènes déjà associés au plat. */
$selectedAllergenIds = array_map(
    'intval',
    $dish['allergen_ids'] ?? []
);

?>

<!-- -------------------------------------------------- -->
<!-- présentation du formulaire de plat -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Gestion des plats
        </p>

        <h1 class="employee-dashboard-title">
            <?php
            echo $isEditing
                ? 'Modifier un plat'
                : 'Créer un plat';
            ?>
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Renseignez les informations visibles dans les menus.
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
            action="<?php echo BASE_URL; ?>/employee/dish/save"
            method="post"
            enctype="multipart/form-data"
            class="employee-dashboard-card employee-dish-form">

            <!-- Protège le formulaire contre les requêtes CSRF. -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars($csrfToken); ?>">

            <!-- Transmet l'identifiant du plat lors d'une modification. -->
            <?php if ($isEditing): ?>

                <input
                    type="hidden"
                    name="dish_id"
                    value="<?php echo (int) $dish['id']; ?>">

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- informations du plat -->
            <!-- -------------------------------------------------- -->

            <!-- Nom du plat. -->
            <div class="mb-4">

                <label
                    for="dish-name"
                    class="form-label">
                    Nom du plat *
                </label>

                <input
                    type="text"
                    id="dish-name"
                    name="name"
                    maxlength="150"
                    required
                    class="form-control"
                    value="<?php
                            echo htmlspecialchars(
                                $dish['name'] ?? ''
                            );
                            ?>">

            </div>

            <!-- Type du plat. -->
            <div class="mb-4">

                <label
                    for="dish-type"
                    class="form-label">
                    Type de plat *
                </label>

                <select
                    id="dish-type"
                    name="dish_type"
                    required
                    class="form-select">

                    <option value="">
                        Sélectionner
                    </option>

                    <option
                        value="starter"
                        <?php
                        echo ($dish['dish_type'] ?? '') === 'starter'
                            ? 'selected'
                            : '';
                        ?>>
                        Entrée
                    </option>

                    <option
                        value="main_course"
                        <?php
                        echo ($dish['dish_type'] ?? '') === 'main_course'
                            ? 'selected'
                            : '';
                        ?>>
                        Plat
                    </option>

                    <option
                        value="dessert"
                        <?php
                        echo ($dish['dish_type'] ?? '') === 'dessert'
                            ? 'selected'
                            : '';
                        ?>>
                        Dessert
                    </option>

                </select>

            </div>

            <!-- Description du plat. -->
            <div class="mb-4">

                <label
                    for="dish-description"
                    class="form-label">
                    Description
                </label>

                <textarea
                    id="dish-description"
                    name="description"
                    rows="6"
                    maxlength="3000"
                    class="form-control"><?php
                                            echo htmlspecialchars(
                                                $dish['description'] ?? ''
                                            );
                                            ?></textarea>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- allergènes -->
            <!-- -------------------------------------------------- -->

            <fieldset class="employee-dish-allergens mb-4">

                <legend class="form-label">
                    Allergènes
                </legend>

                <div class="employee-dish-allergen-grid">

                    <?php foreach ($allergens as $allergen): ?>

                        <?php

                        /* Récupère l'identifiant de l'allergène courant. */
                        $allergenId =
                            (int) $allergen['id'];

                        ?>

                        <div class="form-check">

                            <input
                                type="checkbox"
                                id="allergen-<?php
                                                echo $allergenId;
                                                ?>"
                                name="allergen_ids[]"
                                value="<?php echo $allergenId; ?>"
                                class="form-check-input"
                                <?php
                                echo in_array(
                                    $allergenId,
                                    $selectedAllergenIds,
                                    true
                                )
                                    ? 'checked'
                                    : '';
                                ?>>

                            <label
                                for="allergen-<?php
                                                echo $allergenId;
                                                ?>"
                                class="form-check-label">
                                <?php
                                echo htmlspecialchars(
                                    $allergen['name']
                                );
                                ?>
                            </label>

                        </div>

                    <?php endforeach; ?>

                </div>

            </fieldset>

            <!-- -------------------------------------------------- -->
            <!-- photo du plat -->
            <!-- -------------------------------------------------- -->

            <div class="mb-4">

                <label
                    for="dish-photo"
                    class="form-label">
                    Photo
                </label>

                <input
                    type="file"
                    id="dish-photo"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="form-control">

                <p class="form-text">
                    JPG, PNG ou WebP — 1 Mo maximum.
                    <?php if ($isEditing): ?>
                        Laissez vide pour conserver la photo actuelle.
                    <?php endif; ?>
                </p>

            </div>

            <!-- Affiche la photo actuelle lors de la modification d'un plat. -->
            <?php if (
                $isEditing
                && !empty($dish['photo_mime_type'])
            ): ?>

                <div class="employee-dish-current-image mb-4">

                    <p class="form-label">
                        Photo actuelle
                    </p>

                    <img
                        src="<?php
                                echo BASE_URL
                                    . '/dish/image?id='
                                    . (int) $dish['id'];
                                ?>"
                        alt="<?php
                                echo htmlspecialchars(
                                    $dish['name']
                                );
                                ?>">

                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- actions du formulaire -->
            <!-- -------------------------------------------------- -->

            <div class="employee-dish-form-actions">

                <a
                    href="<?php
                            echo BASE_URL
                                . '/employee/dishes';
                            ?>"
                    class="btn btn-outline-light">
                    Annuler
                </a>

                <button
                    type="submit"
                    class="btn btn-custom">
                    <?php
                    echo $isEditing
                        ? 'Enregistrer les modifications'
                        : 'Créer le plat';
                    ?>
                </button>

            </div>

        </form>

    </div>
</section>