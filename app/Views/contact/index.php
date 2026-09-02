<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var string|null $success */
/** @var array $formData */

?>

<section class="contact-section py-5">
    <div class="container">

        <div class="contact-card mx-auto">

            <div class="text-center mb-4">

                <p class="section-subtitle mb-2">
                    Nous contacter
                </p>

                <h1 class="contact-title">
                    Une question ? Ecrivez-nous
                </h1>

                <p class="contact-description">
                    Une question sur nos menus ou votre future
                    prestation ? Envoyez-nous votre demande.
                    Julie et José vous répondront dès que possible.
                </p>

            </div>

            <?php if (!empty($errors)): ?>

                <div
                    class="alert alert-danger"
                    role="alert">

                    <strong>
                        Votre message n’a pas pu être envoyé.
                    </strong>

                    <ul class="mb-0 mt-2">

                        <?php foreach (
                            $errors
                            as $error
                        ): ?>

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

            <?php if (!empty($success)): ?>

                <div
                    class="alert alert-success"
                    role="status">
                    <?php
                    echo htmlspecialchars(
                        $success
                    );
                    ?>
                </div>

            <?php endif; ?>

            <form
                action="<?php
                    echo BASE_URL;
                ?>/contact"
                method="post"
                class="contact-form">

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
                        for="contact-email"
                        class="form-label">
                        Adresse mail *
                    </label>

                    <input
                        type="email"
                        id="contact-email"
                        name="email"
                        class="form-control"
                        maxlength="190"
                        autocomplete="email"
                        value="<?php
                            echo htmlspecialchars(
                                $formData['email']
                                ?? ''
                            );
                        ?>"
                        required>

                </div>

                <div class="mb-4">

                    <label
                        for="contact-subject"
                        class="form-label">
                        Titre *
                    </label>

                    <input
                        type="text"
                        id="contact-subject"
                        name="subject"
                        class="form-control"
                        minlength="3"
                        maxlength="150"
                        value="<?php
                            echo htmlspecialchars(
                                $formData['subject']
                                ?? ''
                            );
                        ?>"
                        required>

                </div>

                <div class="mb-4">

                    <label
                        for="contact-message"
                        class="form-label">
                        Description *
                    </label>

                    <textarea
                        id="contact-message"
                        name="message"
                        class="form-control"
                        rows="7"
                        minlength="10"
                        maxlength="5000"
                        required><?php
                            echo htmlspecialchars(
                                $formData['message']
                                ?? ''
                            );
                        ?></textarea>

                    <p class="form-text">
                        Décrivez votre demande en quelques lignes.
                    </p>

                </div>

                <button
                    type="submit"
                    class="btn btn-custom w-100">
                    Envoyer ma demande
                </button>

            </form>

        </div>

    </div>
</section>