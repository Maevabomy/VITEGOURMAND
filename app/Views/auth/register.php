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

            <form action="#" method="post" class="auth-form">

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

                <p class="auth-footer-text mt-3 mb-0 text-center">
                    Vous avez déjà un compte ?
                    <span>La connexion sera disponible prochainement.</span>
                </p>
            </form>
        </div>
    </div>
</section>