<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <div class="auth-card-header text-center mb-4">
                <p class="section-subtitle mb-2">
                    Espace client
                </p>

                <h1 class="auth-title">
                    Créer un compte
                </h1>

                <p class="auth-description">
                    Créez votre espace client pour préparer vos prochaines commandes
                    auprès de Vite & Gourmand.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Inscription impossible.</strong>

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

            <form action="<?php echo BASE_URL; ?>/register" method="post" class="auth-form">

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
                <!-- identité -->
                <!-- -------------------------------------------------- -->

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">
                            Prénom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="first_name"
                            name="first_name"
                            value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>"
                            autocomplete="given-name"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">
                            Nom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="last_name"
                            name="last_name"
                            value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>"
                            autocomplete="family-name"
                            required>
                    </div>
                </div>

                <!-- -------------------------------------------------- -->
                <!-- coordonnées -->
                <!-- -------------------------------------------------- -->

                <div class="mt-3">
                    <label for="phone" class="form-label">
                        Téléphone
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        id="phone"
                        name="phone"
                        value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>"
                        autocomplete="tel"
                        required>
                </div>

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
                    <label for="address" class="form-label">
                        Adresse postale
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="address"
                        name="address"
                        value="<?php echo htmlspecialchars($formData['address'] ?? ''); ?>"
                        autocomplete="street-address"
                        required>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-4">
                        <label for="postal_code" class="form-label">
                            Code postal
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="postal_code"
                            name="postal_code"
                            value="<?php echo htmlspecialchars($formData['postal_code'] ?? ''); ?>"
                            autocomplete="postal-code"
                            required>
                    </div>

                    <div class="col-md-8">
                        <label for="city" class="form-label">
                            Ville
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="city"
                            name="city"
                            value="<?php echo htmlspecialchars($formData['city'] ?? ''); ?>"
                            autocomplete="address-level2"
                            required>
                    </div>
                </div>

                <!-- -------------------------------------------------- -->
                <!-- sécurité -->
                <!-- -------------------------------------------------- -->

                <div class="mt-3">
                    <label for="password" class="form-label">
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        minlength="10"
                        required>

                    <p class="auth-help mt-2 mb-0">
                        Minimum 10 caractères avec une majuscule, une minuscule,
                        un chiffre et un caractère spécial.
                    </p>
                </div>

                <div class="mt-3">
                    <label for="password_confirmation" class="form-label">
                        Confirmation du mot de passe
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        minlength="10"
                        required>
                </div>

                <!-- -------------------------------------------------- -->
                <!-- validation -->
                <!-- -------------------------------------------------- -->

                <button type="submit" class="btn btn-custom w-100 mt-4">
                    Créer mon compte
                </button>

                <p class="auth-footer-text text-center mt-4 mb-0">
                    Vous avez déjà un compte ?
                    <a href="<?php echo BASE_URL; ?>/login">
                        Se connecter
                    </a>
                </p>
            </form>
        </div>
    </div>
</section>