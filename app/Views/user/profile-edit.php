<?php

/** @var array $errors */
/** @var array $formData */

?>

<!-- -------------------------------------------------- -->
<!-- présentation de la modification du profil -->
<!-- -------------------------------------------------- -->

<section class="user-dashboard-hero">
    <div class="container py-5">

        <p class="section-subtitle mb-2">
            Espace personnel
        </p>

        <h1 class="user-dashboard-title">
            Modifier mes informations
        </h1>

        <p class="user-dashboard-introduction mb-0">
            Maintenez vos coordonnées à jour pour faciliter
            le suivi de vos prochaines commandes.
        </p>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- formulaire de modification du profil -->
<!-- -------------------------------------------------- -->

<section class="user-dashboard-section py-5">
    <div class="container py-4">

        <!-- -------------------------------------------------- -->
        <!-- navigation -->
        <!-- -------------------------------------------------- -->

        <div class="user-profile-form-navigation mb-4">

            <a
                href="<?php
                        echo BASE_URL;
                        ?>/user/dashboard"
                class="user-order-back-link">
                ← Retour à mon compte
            </a>

        </div>

        <div class="user-profile-form-card">

            <!-- -------------------------------------------------- -->
            <!-- messages de validation -->
            <!-- -------------------------------------------------- -->

            <?php if (!empty($errors)): ?>

                <div
                    class="alert alert-danger"
                    role="alert">

                    <strong>
                        Modification impossible.
                    </strong>

                    <ul class="mb-0 mt-2">

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?php
                                echo htmlspecialchars(
                                    $error
                                );
                                ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- saisie des informations -->
            <!-- -------------------------------------------------- -->

            <form
                action="<?php
                        echo BASE_URL
                            . '/user/profile/update';
                        ?>"
                method="post"
                class="auth-form">

                <!-- Protège le formulaire contre les requêtes CSRF. -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                            echo htmlspecialchars(
                                $_SESSION['profile_csrf_token']
                                    ?? ''
                            );
                            ?>">

                <!-- -------------------------------------------------- -->
                <!-- identité -->
                <!-- -------------------------------------------------- -->

                <div class="row g-3">

                    <!-- Prénom de l'utilisateur. -->
                    <div class="col-md-6">

                        <label
                            for="first_name"
                            class="form-label">
                            Prénom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="first_name"
                            name="first_name"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['first_name']
                                            ?? ''
                                    );
                                    ?>"
                            autocomplete="given-name"
                            required>

                    </div>

                    <!-- Nom de l'utilisateur. -->
                    <div class="col-md-6">

                        <label
                            for="last_name"
                            class="form-label">
                            Nom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="last_name"
                            name="last_name"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['last_name']
                                            ?? ''
                                    );
                                    ?>"
                            autocomplete="family-name"
                            required>

                    </div>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- coordonnées -->
                <!-- -------------------------------------------------- -->

                <div class="row g-3 mt-0">

                    <!-- Numéro de téléphone. -->
                    <div class="col-md-6">

                        <label
                            for="phone"
                            class="form-label">
                            Téléphone
                        </label>

                        <input
                            type="tel"
                            class="form-control"
                            id="phone"
                            name="phone"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['phone']
                                            ?? ''
                                    );
                                    ?>"
                            autocomplete="tel"
                            required>

                    </div>

                    <!-- Adresse mail. -->
                    <div class="col-md-6">

                        <label
                            for="email"
                            class="form-label">
                            Adresse mail
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['email']
                                            ?? ''
                                    );
                                    ?>"
                            autocomplete="email"
                            required>

                    </div>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- adresse postale -->
                <!-- -------------------------------------------------- -->

                <div class="mt-3">

                    <label
                        for="address"
                        class="form-label">
                        Adresse postale
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="address"
                        name="address"
                        value="<?php
                                echo htmlspecialchars(
                                    $formData['address']
                                        ?? ''
                                );
                                ?>"
                        autocomplete="street-address"
                        required>

                </div>

                <div class="row g-3 mt-0">

                    <!-- Code postal. -->
                    <div class="col-md-4">

                        <label
                            for="postal_code"
                            class="form-label">
                            Code postal
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="postal_code"
                            name="postal_code"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['postal_code']
                                            ?? ''
                                    );
                                    ?>"
                            autocomplete="postal-code"
                            inputmode="numeric"
                            maxlength="5"
                            pattern="[0-9]{5}"
                            required>

                    </div>

                    <!-- Ville. -->
                    <div class="col-md-8">

                        <label
                            for="city"
                            class="form-label">
                            Ville
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="city"
                            name="city"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['city']
                                            ?? ''
                                    );
                                    ?>"
                            autocomplete="address-level2"
                            required>

                    </div>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- validation -->
                <!-- -------------------------------------------------- -->

                <div class="user-profile-form-actions mt-4">

                    <a
                        href="<?php
                                echo BASE_URL
                                    . '/user/dashboard';
                                ?>"
                        class="btn btn-outline-light">
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="btn btn-custom">
                        Enregistrer les modifications
                    </button>

                </div>

            </form>

        </div>

    </div>
</section>