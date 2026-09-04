<?php

/** @var array $errors */
/** @var string|null $success */
/** @var string $token */
/** @var array|null $resetToken */

?>

<!-- -------------------------------------------------- -->
<!-- formulaire de réinitialisation du mot de passe -->
<!-- -------------------------------------------------- -->

<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <!-- -------------------------------------------------- -->
            <!-- présentation du formulaire -->
            <!-- -------------------------------------------------- -->

            <div class="auth-card-header text-center mb-4">

                <p class="section-subtitle mb-2">
                    Espace client
                </p>

                <h1 class="auth-title">
                    Nouveau mot de passe
                </h1>

                <p class="auth-description">
                    Choisissez un nouveau mot de passe sécurisé.
                </p>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- messages de validation -->
            <!-- -------------------------------------------------- -->

            <!-- Affiche les erreurs rencontrées pendant la réinitialisation. -->
            <?php if (!empty($errors)): ?>

                <div
                    class="alert alert-danger"
                    role="alert">

                    <strong>
                        Réinitialisation impossible.
                    </strong>

                    <ul class="mb-0 mt-2">

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?php echo htmlspecialchars($error); ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <!-- Affiche le message de confirmation après modification. -->
            <?php if (!empty($success)): ?>

                <div
                    class="alert alert-success"
                    role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- saisie du nouveau mot de passe -->
            <!-- -------------------------------------------------- -->

            <?php if (
                empty($errors)
                && $resetToken !== null
            ): ?>

                <form
                    action="<?php echo BASE_URL; ?>/reset-password"
                    method="post"
                    class="auth-form">

                    <!-- Transmet le jeton de réinitialisation. -->
                    <input
                        type="hidden"
                        name="token"
                        value="<?php
                                echo htmlspecialchars($token);
                                ?>">

                    <!-- Protège le formulaire contre les requêtes CSRF. -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                                echo htmlspecialchars(
                                    $_SESSION['auth_csrf_token'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>">

                    <!-- Nouveau mot de passe. -->
                    <div class="mt-3">

                        <label
                            for="password"
                            class="form-label">
                            Nouveau mot de passe
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            required>

                        <small class="form-text">
                            Au moins 10 caractères, avec une majuscule,
                            une minuscule, un chiffre et un caractère spécial.
                        </small>

                    </div>

                    <!-- Confirmation du nouveau mot de passe. -->
                    <div class="mt-3">

                        <label
                            for="password_confirmation"
                            class="form-label">
                            Confirmer le mot de passe
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-custom w-100 mt-4">
                        Enregistrer le mot de passe
                    </button>

                </form>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- retour à la connexion -->
            <!-- -------------------------------------------------- -->

            <p class="text-center mt-4 mb-0">
                <a href="<?php echo BASE_URL; ?>/login">
                    Retour à la connexion
                </a>
            </p>

        </div>
    </div>
</section>