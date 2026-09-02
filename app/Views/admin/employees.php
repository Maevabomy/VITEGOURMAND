<?php

/** @var array $employees */
/** @var string $csrfToken */
/** @var string|null $success */
/** @var string|null $error */
/** @var array $formData */

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Administration
        </p>

        <h1 class="employee-dashboard-title">
            Gestion des employés
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Créez les accès professionnels et gérez
            les comptes existants.
        </p>

    </div>
</section>

<?php

require BASE_PATH
    . '/app/Views/admin/_navigation.php';

?>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success"
                role="status">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger"
                role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="admin-employees-layout">

            <section class="employee-dashboard-card">

                <p class="section-subtitle mb-2">
                    Nouveau compte
                </p>

                <h2 class="employee-dashboard-card-title">
                    Créer un employé
                </h2>

                <p class="employee-menu-help">
                    Le mot de passe devra être communiqué
                    directement à l’employé.
                </p>

                <form
                    action="<?php
                            echo BASE_URL;
                            ?>/admin/employee/create"
                    method="post">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                                echo htmlspecialchars(
                                    $csrfToken
                                );
                                ?>">

                    <div class="mb-4">

                        <label
                            for="employee-email"
                            class="form-label">
                            Adresse mail *
                        </label>

                        <input
                            type="email"
                            id="employee-email"
                            name="email"
                            required
                            autocomplete="email"
                            class="form-control"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['email']
                                            ?? ''
                                    );
                                    ?>">

                    </div>

                    <div class="mb-4">

                        <label
                            for="employee-password"
                            class="form-label">
                            Mot de passe *
                        </label>

                        <input
                            type="password"
                            id="employee-password"
                            name="password"
                            required
                            minlength="10"
                            autocomplete="new-password"
                            class="form-control">

                        <p class="form-text">
                            10 caractères minimum avec une majuscule,
                            une minuscule, un chiffre et un caractère
                            spécial.
                        </p>

                    </div>

                    <div class="mb-4">

                        <label
                            for="employee-password-confirmation"
                            class="form-label">
                            Confirmer le mot de passe *
                        </label>

                        <input
                            type="password"
                            id="employee-password-confirmation"
                            name="password_confirmation"
                            required
                            minlength="10"
                            autocomplete="new-password"
                            class="form-control">

                    </div>

                    <button
                        type="submit"
                        class="btn btn-custom">
                        Créer le compte employé
                    </button>

                </form>

            </section>

            <section class="employee-dashboard-card">

                <p class="section-subtitle mb-2">
                    Équipe
                </p>

                <h2 class="employee-dashboard-card-title">
                    Comptes employés
                </h2>

                <?php if (!empty($employees)): ?>

                    <div class="admin-employee-list">

                        <?php foreach (
                            $employees
                            as $employee
                        ): ?>

                            <?php
                            $isActive =
                                (bool)
                                $employee['is_active'];
                            ?>

                            <article
                                class="admin-employee-item<?php
                                                            echo $isActive
                                                                ? ''
                                                                : ' inactive';
                                                            ?>">

                                <div>

                                    <h3 class="admin-employee-email">
                                        <?php
                                        echo htmlspecialchars(
                                            $employee['email']
                                        );
                                        ?>
                                    </h3>

                                    <p class="admin-employee-meta mb-0">
                                        Compte créé le
                                        <?php
                                        echo date(
                                            'd/m/Y',
                                            strtotime(
                                                $employee['created_at']
                                            )
                                        );
                                        ?>
                                    </p>

                                </div>

                                <div class="admin-employee-actions">

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

                                    <form
                                        action="<?php
                                                echo BASE_URL;
                                                ?>/admin/employee/toggle"
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
                                            name="employee_id"
                                            value="<?php
                                                    echo (int)
                                                    $employee['id'];
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
                            Aucun compte employé n’est enregistré.
                        </p>
                    </div>

                <?php endif; ?>

            </section>

        </div>

    </div>
</section>