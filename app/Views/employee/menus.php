<?php

/** @var array $menus */
/** @var string $csrfToken */
/** @var string|null $success */
/** @var string|null $error */

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Gestion des menus
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Créez et modifiez les menus proposés aux clients.
        </p>

    </div>
</section>

<?php

require BASE_PATH
    . '/app/Views/employee/_navigation.php';

?>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <?php if (!empty($success)): ?>

            <div class="alert alert-success" role="status">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="employee-menus-heading">

            <div>
                <p class="section-subtitle mb-2">
                    Catalogue
                </p>

                <h2 class="employee-dashboard-card-title mb-0">
                    Tous les menus
                </h2>
            </div>

            <a
                href="<?php
                    echo BASE_URL;
                ?>/employee/menu/form"
                class="btn btn-custom">
                Ajouter un menu
            </a>

        </div>

        <?php if (!empty($menus)): ?>

            <div class="employee-menus-grid">

                <?php foreach ($menus as $menu): ?>

                    <?php
                    $isActive =
                        (bool) $menu['is_active'];
                    ?>

                    <article
                        class="employee-menu-card<?php
                            echo $isActive
                                ? ''
                                : ' inactive';
                        ?>">

                        <div class="employee-menu-card-top">

                            <div>
                                <span class="employee-menu-theme">
                                    <?php
                                    echo htmlspecialchars(
                                        $menu['theme_name']
                                    );
                                    ?>
                                </span>

                                <span class="employee-menu-diet">
                                    <?php
                                    echo htmlspecialchars(
                                        $menu[
                                            'dietary_type_name'
                                        ]
                                    );
                                    ?>
                                </span>
                            </div>

                            <span
                                class="employee-menu-status<?php
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

                        <h3 class="employee-menu-title">
                            <?php
                            echo htmlspecialchars(
                                $menu['title']
                            );
                            ?>
                        </h3>

                        <p class="employee-menu-description">
                            <?php
                            echo htmlspecialchars(
                                $menu['description']
                            );
                            ?>
                        </p>

                        <div class="employee-menu-information">

                            <p>
                                <strong>
                                    Minimum :
                                </strong>

                                <?php
                                echo (int)
                                    $menu['minimum_people'];
                                ?>
                                personnes
                            </p>

                            <p>
                                <strong>
                                    Prix :
                                </strong>

                                <?php
                                echo number_format(
                                    (float) $menu['base_price'],
                                    2,
                                    ',',
                                    ' '
                                );
                                ?>
                                €
                            </p>

                            <p>
                                <strong>
                                    Stock :
                                </strong>

                                <?php
                                echo (int)
                                    $menu['stock_quantity'];
                                ?>
                                personnes
                            </p>

                            <p>
                                <strong>
                                    Délai :
                                </strong>

                                <?php
                                echo (int)
                                    $menu['minimum_order_days'];
                                ?>
                                jour(s)
                            </p>

                        </div>

                        <p class="employee-menu-dishes">
                            <strong>Plats associés :</strong><br>

                            <?php
                            echo htmlspecialchars(
                                $menu['dish_names']
                                ?: 'Aucun plat associé'
                            );
                            ?>
                        </p>

                        <?php if (
                            !empty($menu['available_from'])
                            || !empty($menu['available_until'])
                        ): ?>

                            <p class="employee-menu-availability">

                                <strong>
                                    Disponibilité :
                                </strong>

                                <?php
                                echo htmlspecialchars(
                                    $menu['available_from']
                                    ?: 'sans date de début'
                                );
                                ?>

                                →

                                <?php
                                echo htmlspecialchars(
                                    $menu['available_until']
                                    ?: 'sans date de fin'
                                );
                                ?>

                            </p>

                        <?php endif; ?>

                        <div class="employee-menu-actions">

                            <a
                                href="<?php
                                    echo BASE_URL
                                        . '/employee/menu/form?id='
                                        . (int) $menu['id'];
                                ?>"
                                class="btn btn-outline-light">
                                Modifier
                            </a>

                            <form
                                action="<?php
                                    echo BASE_URL;
                                ?>/employee/menu/toggle"
                                method="post">

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $csrfToken
                                        );
                                    ?>">

                                <input
                                    type="hidden"
                                    name="menu_id"
                                    value="<?php
                                        echo (int)
                                            $menu['id'];
                                    ?>">

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
                                    ?>">
                                    <?php
                                    echo $isActive
                                        ? 'Désactiver'
                                        : 'Réactiver';
                                    ?>
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="employee-empty-state">

                <p class="mb-0">
                    Aucun menu n’est enregistré.
                </p>

            </div>

        <?php endif; ?>

    </div>
</section>