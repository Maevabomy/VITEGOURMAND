<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <div class="auth-card-header text-center mb-4">
                <p class="section-subtitle mb-2">
                    Espace client
                </p>

                <h1 class="auth-title">
                    Connexion
                </h1>

                <p class="auth-description">
                    Connectez-vous pour accéder à votre espace client.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Connexion impossible.</strong>

                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/login" method="post" class="auth-form">

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

                <!-- -------------------------------------------------- -->
                <!-- connexion -->
                <!-- -------------------------------------------------- -->

                <div class="mt-3">
                    <label for="email" class="form-label">
                        Adresse mail
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                        autocomplete="email"
                        required>
                </div>

                <div class="mt-3">
                    <label for="password" class="form-label">
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required>
                </div>

                <!-- -------------------------------------------------- -->
                <!-- validation -->
                <!-- -------------------------------------------------- -->

                <button type="submit" class="btn btn-custom w-100 mt-4">
                    Se connecter
                </button>

                <p class="text-center mt-4 mb-2">
                    <a href="<?php echo BASE_URL; ?>/register">
                        Créer un compte
                    </a>
                </p>

                <p class="text-center mb-0">
                    <a href="<?php echo BASE_URL; ?>/forgot-password">
                        Mot de passe oublié ?
                    </a>
                </p>
            </form>
        </div>
    </div>
</section>