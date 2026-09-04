<?php

/** @var array $dishes */
/** @var string $csrfToken */
/** @var string|null $success */
/** @var string|null $error */


/* -------------------------------------------------- */
/* préparation de l'affichage */
/* -------------------------------------------------- */

/* Associe chaque type de plat à son libellé français. */
$dishTypeLabels = [
    'starter' => 'Entrée',
    'main_course' => 'Plat',
    'dessert' => 'Dessert',
];

?>

<!-- -------------------------------------------------- -->
<!-- présentation de la gestion des plats -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Gestion des plats
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Créez et modifiez les plats proposés dans les menus.
        </p>

    </div>
</section>

<?php

/* Charge la navigation de l'espace employé. */
require BASE_PATH
    . '/app/Views/employee/_navigation.php';

?>

<!-- -------------------------------------------------- -->
<!-- gestion des plats -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <!-- -------------------------------------------------- -->
        <!-- messages de validation -->
        <!-- -------------------------------------------------- -->

        <!-- Affiche le message de confirmation après une action réussie. -->
        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success"
                role="status">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <!-- Affiche le message d'erreur en cas d'échec. -->
        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger"
                role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <!-- -------------------------------------------------- -->
        <!-- en-tête de la liste -->
        <!-- -------------------------------------------------- -->

        <div class="employee-dishes-heading">

            <div>

                <p class="section-subtitle mb-2">
                    Catalogue
                </p>

                <h2 class="employee-dashboard-card-title mb-0">
                    Tous les plats
                </h2>

            </div>

            <a
                href="<?php echo BASE_URL; ?>/employee/dish/form"
                class="btn btn-custom">
                Ajouter un plat
            </a>

        </div>

        <?php if (!empty($dishes)): ?>

            <!-- -------------------------------------------------- -->
            <!-- filtres des plats -->
            <!-- -------------------------------------------------- -->

            <div
                class="employee-dish-filters"
                aria-label="Filtres des plats">

                <!-- Recherche un plat par son nom. -->
                <div class="employee-dish-filter-field">

                    <label
                        for="dish-search"
                        class="form-label">
                        Rechercher
                    </label>

                    <input
                        type="search"
                        id="dish-search"
                        class="form-control"
                        placeholder="Nom d’un plat"
                        autocomplete="off"
                        data-dish-search>

                </div>

                <!-- Filtre les plats par type. -->
                <div class="employee-dish-filter-field">

                    <label
                        for="dish-type-filter"
                        class="form-label">
                        Type
                    </label>

                    <select
                        id="dish-type-filter"
                        class="form-select"
                        data-dish-type-filter>

                        <option value="">
                            Tous les types
                        </option>

                        <option value="starter">
                            Entrées
                        </option>

                        <option value="main_course">
                            Plats
                        </option>

                        <option value="dessert">
                            Desserts
                        </option>

                    </select>

                </div>

                <!-- Filtre les plats par statut. -->
                <div class="employee-dish-filter-field">

                    <label
                        for="dish-status-filter"
                        class="form-label">
                        Statut
                    </label>

                    <select
                        id="dish-status-filter"
                        class="form-select"
                        data-dish-status-filter>

                        <option value="">
                            Tous les statuts
                        </option>

                        <option value="active">
                            Actifs
                        </option>

                        <option value="inactive">
                            Inactifs
                        </option>

                    </select>

                </div>

                <!-- Filtre les plats selon leur utilisation dans les menus. -->
                <div class="employee-dish-filter-field">

                    <label
                        for="dish-usage-filter"
                        class="form-label">
                        Utilisation
                    </label>

                    <select
                        id="dish-usage-filter"
                        class="form-select"
                        data-dish-usage-filter>

                        <option value="">
                            Tous les plats
                        </option>

                        <option value="used">
                            Utilisés dans un menu
                        </option>

                        <option value="unused">
                            Non utilisés
                        </option>

                    </select>

                </div>

                <div class="employee-dish-filter-actions">

                    <button
                        type="button"
                        class="btn btn-outline-light"
                        data-dish-reset>
                        Réinitialiser
                    </button>

                </div>

            </div>

            <!-- Affiche le nombre de plats correspondant aux filtres. -->
            <p
                class="employee-dish-filter-count"
                aria-live="polite"
                data-dish-filter-count>
            </p>

            <!-- -------------------------------------------------- -->
            <!-- liste des plats -->
            <!-- -------------------------------------------------- -->

            <div
                class="employee-dishes-grid"
                data-dishes-grid>

                <?php foreach ($dishes as $dish): ?>

                    <?php

                    /* Indique si le plat est actuellement actif. */
                    $isActive =
                        (bool) $dish['is_active'];

                    /* Compte les menus actifs utilisant ce plat. */
                    $activeMenuCount =
                        (int) $dish['active_menu_count'];

                    ?>

                    <article
                        class="employee-dish-card<?php
                            echo $isActive
                                ? ''
                                : ' inactive';
                        ?>"
                        data-dish-card
                        data-dish-name="<?php
                            echo htmlspecialchars(
                                mb_strtolower($dish['name'])
                            );
                        ?>"
                        data-dish-type="<?php
                            echo htmlspecialchars(
                                $dish['dish_type']
                            );
                        ?>"
                        data-dish-status="<?php
                            echo $isActive
                                ? 'active'
                                : 'inactive';
                        ?>"
                        data-dish-usage="<?php
                            echo $activeMenuCount > 0
                                ? 'used'
                                : 'unused';
                        ?>">

                        <!-- -------------------------------------------------- -->
                        <!-- photo du plat -->
                        <!-- -------------------------------------------------- -->

                        <div class="employee-dish-image-wrapper">

                            <?php if (!empty($dish['photo_mime_type'])): ?>

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
                                    ?>"
                                    class="employee-dish-image">

                            <?php else: ?>

                                <div class="employee-dish-image-placeholder">
                                    Aucune photo
                                </div>

                            <?php endif; ?>

                        </div>

                        <!-- -------------------------------------------------- -->
                        <!-- informations du plat -->
                        <!-- -------------------------------------------------- -->

                        <div class="employee-dish-card-body">

                            <div class="employee-dish-card-top">

                                <span class="employee-dish-type">
                                    <?php
                                    echo htmlspecialchars(
                                        $dishTypeLabels[
                                            $dish['dish_type']
                                        ] ?? 'Plat'
                                    );
                                    ?>
                                </span>

                                <span
                                    class="employee-dish-status<?php
                                        echo $isActive
                                            ? ''
                                            : ' inactive';
                                    ?>">
                                    <?php
                                    echo $isActive
                                        ? 'Actif'
                                        : 'Inactif';
                                    ?>
                                </span>

                            </div>

                            <h3 class="employee-dish-title">
                                <?php
                                echo htmlspecialchars(
                                    $dish['name']
                                );
                                ?>
                            </h3>

                            <p class="employee-dish-description">
                                <?php
                                echo htmlspecialchars(
                                    $dish['description']
                                        ?: 'Aucune description.'
                                );
                                ?>
                            </p>

                            <p class="employee-dish-meta">
                                <strong>
                                    Allergènes :
                                </strong>

                                <?php
                                echo htmlspecialchars(
                                    $dish['allergen_names']
                                        ?: 'Aucun allergène renseigné'
                                );
                                ?>
                            </p>

                            <p class="employee-dish-meta">
                                <strong>
                                    Utilisé dans :
                                </strong>

                                <?php if ($activeMenuCount === 0): ?>

                                    aucun menu actif

                                <?php elseif ($activeMenuCount === 1): ?>

                                    1 menu actif

                                <?php else: ?>

                                    <?php echo $activeMenuCount; ?>
                                    menus actifs

                                <?php endif; ?>
                            </p>

                            <!-- -------------------------------------------------- -->
                            <!-- actions du plat -->
                            <!-- -------------------------------------------------- -->

                            <div class="employee-dish-actions">

                                <a
                                    href="<?php
                                        echo BASE_URL
                                            . '/employee/dish/form?id='
                                            . (int) $dish['id'];
                                    ?>"
                                    class="btn btn-outline-light">
                                    Modifier
                                </a>

                                <form
                                    action="<?php
                                        echo BASE_URL
                                            . '/employee/dish/toggle';
                                    ?>"
                                    method="post">

                                    <!-- Protège l'action contre les requêtes CSRF. -->
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php
                                            echo htmlspecialchars(
                                                $csrfToken
                                            );
                                        ?>">

                                    <!-- Identifie le plat à modifier. -->
                                    <input
                                        type="hidden"
                                        name="dish_id"
                                        value="<?php
                                            echo (int) $dish['id'];
                                        ?>">

                                    <!-- Définit le nouveau statut du plat. -->
                                    <input
                                        type="hidden"
                                        name="is_active"
                                        value="<?php
                                            echo $isActive
                                                ? '0'
                                                : '1';
                                        ?>">

                                    <button
                                        type="submit"
                                        class="btn <?php
                                            echo $isActive
                                                ? 'btn-outline-danger'
                                                : 'btn-outline-success';
                                        ?>"
                                        <?php
                                        echo (
                                            $isActive
                                            && $activeMenuCount > 0
                                        )
                                            ? 'disabled'
                                            : '';
                                        ?>>
                                        <?php
                                        echo $isActive
                                            ? 'Désactiver'
                                            : 'Réactiver';
                                        ?>
                                    </button>

                                </form>

                            </div>

                            <!-- Avertit lorsque le plat ne peut pas être désactivé. -->
                            <?php if (
                                $isActive
                                && $activeMenuCount > 0
                            ): ?>

                                <p class="employee-dish-warning mb-0">
                                    Ce plat est encore utilisé dans un menu publié.
                                    Retirez-le du menu avant de le désactiver.
                                </p>

                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <!-- Affiche un message lorsque les filtres ne retournent aucun plat. -->
            <div
                class="employee-empty-state d-none"
                data-dish-no-result>

                <p class="mb-0">
                    Aucun plat ne correspond aux filtres sélectionnés.
                </p>

            </div>

        <?php else: ?>

            <!-- Affiche un message lorsqu'aucun plat n'est enregistré. -->
            <div class="employee-empty-state">

                <p class="mb-0">
                    Aucun plat n’est enregistré.
                </p>

            </div>

        <?php endif; ?>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- script des filtres -->
<!-- -------------------------------------------------- -->

<script
    src="<?php
        echo BASE_URL
            . '/assets/js/employee-dishes-filters.js';
    ?>">
</script>