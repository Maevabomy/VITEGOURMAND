<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <div class="auth-card-header text-center mb-4">
                <p class="section-subtitle mb-2">
                    Espace client
                </p>

                <h1 class="auth-title">
                    Mot de passe oublié
                </h1>

                <p class="auth-description">
                    Indiquez votre adresse mail pour recevoir un lien de réinitialisation.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Demande impossible.</strong>

                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?php echo htmlspecialchars($error); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form
                action="<?php echo BASE_URL; ?>/forgot-password"
                method="post"
                class="auth-form">

                <!-- -------------------------------------------------- -->
                <!-- adresse mail -->
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

                <!-- -------------------------------------------------- -->
                <!-- validation -->
                <!-- -------------------------------------------------- -->

                <button type="submit" class="btn btn-custom w-100 mt-4">
                    Envoyer le lien
                </button>

                <p class="text-center mt-4 mb-0">
                    <a href="<?php echo BASE_URL; ?>/login">
                        Retour à la connexion
                    </a>
                </p>
            </form>
        </div>
    </div>
</section>