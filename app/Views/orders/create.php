<?php

/** @var array $menu */
/** @var array $formData */
/** @var string $minimumEventDate */
/** @var string|null $maximumEventDate */
/** @var string|null $availabilityMessage */
/** @var array $errors */

?>

<!-- -------------------------------------------------- -->
<!-- formulaire de commande -->
<!-- -------------------------------------------------- -->

<section class="auth-section py-5">
    <div class="container">

        <div class="auth-card mx-auto">

            <!-- -------------------------------------------------- -->
            <!-- présentation de la commande -->
            <!-- -------------------------------------------------- -->

            <div class="auth-card-header text-center mb-4">

                <p class="section-subtitle mb-2">
                    Votre prestation
                </p>

                <h1 class="auth-title">
                    Commander
                </h1>

                <p class="auth-description">
                    Menu
                    <?php
                    echo htmlspecialchars(
                        $menu['title']
                    );
                    ?>
                    pour au moins
                    <?php
                    echo (int) $menu['minimum_people'];
                    ?>
                    personnes.
                </p>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- messages de validation -->
            <!-- -------------------------------------------------- -->

            <!-- Affiche les erreurs empêchant la commande. -->
            <?php if (!empty($errors)): ?>

                <div
                    class="alert alert-danger"
                    role="alert">

                    <strong>
                        Commande indisponible.
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
            <!-- saisie de la commande -->
            <!-- -------------------------------------------------- -->

            <form
                action="<?php echo BASE_URL; ?>/order/store"
                method="post"
                class="auth-form">

                <!-- Identifie le menu commandé. -->
                <input
                    type="hidden"
                    name="menu_id"
                    value="<?php
                            echo (int) $menu['id'];
                            ?>">

                <!-- Protège le formulaire contre les requêtes CSRF. -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                            echo htmlspecialchars(
                                $_SESSION['order_csrf_token']
                            );
                            ?>">

                <!-- -------------------------------------------------- -->
                <!-- informations du client -->
                <!-- -------------------------------------------------- -->

                <div class="row g-3">

                    <!-- Prénom du client. -->
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
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['first_name']
                                    );
                                    ?>"
                            readonly>

                    </div>

                    <!-- Nom du client. -->
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
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['last_name']
                                    );
                                    ?>"
                            readonly>

                    </div>

                </div>

                <!-- Adresse mail du client. -->
                <div class="mt-3">

                    <label
                        for="email"
                        class="form-label">
                        Adresse mail
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        value="<?php
                                echo htmlspecialchars(
                                    $formData['email']
                                );
                                ?>"
                        readonly>

                </div>

                <!-- Numéro de téléphone du client. -->
                <div class="mt-3">

                    <label
                        for="phone"
                        class="form-label">
                        Téléphone
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        id="phone"
                        value="<?php
                                echo htmlspecialchars(
                                    $formData['phone']
                                );
                                ?>"
                        readonly>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- adresse de livraison -->
                <!-- -------------------------------------------------- -->

                <div class="mt-3">

                    <label
                        for="delivery_address"
                        class="form-label">
                        Adresse de livraison
                    </label>

                    <div class="input-group">

                        <input
                            type="text"
                            class="form-control"
                            id="delivery_address"
                            name="delivery_address"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['delivery_address']
                                    );
                                    ?>"
                            autocomplete="street-address"
                            required>

                        <!-- Lance la recherche de l'adresse saisie. -->
                        <button
                            type="button"
                            class="btn btn-custom"
                            id="search-address-button">
                            Rechercher
                        </button>

                    </div>

                    <small class="form-text">
                        Saisissez au moins 5 caractères, puis recherchez
                        et sélectionnez l’adresse exacte.
                    </small>

                    <!-- Affiche l'état de la recherche d'adresse. -->
                    <div
                        class="address-search-status mt-2"
                        id="address-search-status"
                        role="status"
                        aria-live="polite">
                    </div>

                    <!-- Affiche les propositions d'adresses. -->
                    <div
                        class="address-search-results mt-2"
                        id="address-search-results"
                        aria-label="Propositions d’adresses">
                    </div>

                    <!-- Stocke la latitude de l'adresse sélectionnée. -->
                    <input
                        type="hidden"
                        id="delivery_latitude"
                        name="delivery_latitude">

                    <!-- Stocke la longitude de l'adresse sélectionnée. -->
                    <input
                        type="hidden"
                        id="delivery_longitude"
                        name="delivery_longitude">

                </div>

                <!-- Code postal et ville de livraison. -->
                <div class="row g-3 mt-0">

                    <div class="col-md-4">

                        <label
                            for="delivery_postal_code"
                            class="form-label">
                            Code postal
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="delivery_postal_code"
                            name="delivery_postal_code"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['delivery_postal_code']
                                    );
                                    ?>"
                            required>

                    </div>

                    <div class="col-md-8">

                        <label
                            for="delivery_city"
                            class="form-label">
                            Ville
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="delivery_city"
                            name="delivery_city"
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['delivery_city']
                                    );
                                    ?>"
                            required>

                    </div>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- date et horaire de la prestation -->
                <!-- -------------------------------------------------- -->

                <div class="row g-3 mt-0">

                    <!-- Date de la prestation. -->
                    <div class="col-md-6">

                        <label
                            for="event_date"
                            class="form-label">
                            Date de la prestation
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="event_date"
                            name="event_date"
                            min="<?php
                                    echo htmlspecialchars(
                                        $minimumEventDate
                                    );
                                    ?>"
                            <?php if ($maximumEventDate !== null): ?>
                            max="<?php
                                    echo htmlspecialchars(
                                        $maximumEventDate
                                    );
                                    ?>"
                            <?php endif; ?>
                            value="<?php
                                    echo htmlspecialchars(
                                        $formData['event_date']
                                    );
                                    ?>"
                            <?php
                            echo !empty($errors)
                                ? 'disabled'
                                : 'required';
                            ?>>

                        <small class="form-text">
                            Commande à effectuer au minimum
                            <?php
                            echo (int)
                            $menu['minimum_order_days'];
                            ?>
                            jour<?php
                                echo (int) $menu['minimum_order_days'] > 1
                                    ? 's'
                                    : '';
                                ?>
                            avant la prestation.
                        </small>

                    </div>

                    <!-- Créneau de livraison. -->
                    <div class="col-md-6">

                        <label
                            for="delivery_time"
                            class="form-label">
                            Créneau de livraison
                        </label>

                        <select
                            class="form-select"
                            id="delivery_time"
                            name="delivery_time"
                            required>

                            <option value="">
                                Choisir un horaire
                            </option>

                            <?php

                            /* Définit le premier créneau disponible. */
                            $startTime =
                                strtotime('10:00');

                            /* Définit le dernier créneau disponible. */
                            $endTime =
                                strtotime('20:00');

                            ?>

                            <?php for (
                                $time = $startTime;
                                $time <= $endTime;
                                $time += 30 * 60
                            ): ?>

                                <?php

                                /* Prépare la valeur technique du créneau. */
                                $timeValue =
                                    date('H:i', $time);

                                /* Prépare le libellé affiché du créneau. */
                                $timeLabel =
                                    date('H\hi', $time);

                                ?>

                                <option
                                    value="<?php
                                            echo $timeValue;
                                            ?>"
                                    <?php
                                    echo $formData['delivery_time']
                                        === $timeValue
                                        ? 'selected'
                                        : '';
                                    ?>>
                                    <?php echo $timeLabel; ?>
                                </option>

                            <?php endfor; ?>

                        </select>

                        <small class="form-text">
                            Livraison possible de 10h00 à 20h00.
                        </small>

                    </div>

                </div>

                <!-- Affiche les informations concernant la disponibilité du menu. -->
                <?php if ($availabilityMessage !== null): ?>

                    <div
                        class="alert alert-secondary mt-3 mb-0"
                        role="status">
                        <?php
                        echo htmlspecialchars(
                            $availabilityMessage
                        );
                        ?>
                    </div>

                <?php endif; ?>

                <!-- -------------------------------------------------- -->
                <!-- nombre de personnes -->
                <!-- -------------------------------------------------- -->

                <div class="mt-3">

                    <label
                        for="people_count"
                        class="form-label">
                        Nombre de personnes
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="people_count"
                        name="people_count"
                        min="<?php
                                echo (int) $menu['minimum_people'];
                                ?>"
                        max="<?php
                                echo (int) $menu['stock_quantity'];
                                ?>"
                        value="<?php
                                echo (int) $formData['people_count'];
                                ?>"
                        data-minimum-people="<?php
                                                echo (int) $menu['minimum_people'];
                                                ?>"
                        data-base-price="<?php
                                            echo (float) $menu['base_price'];
                                            ?>"
                        required>

                    <!-- Affiche le stock encore disponible. -->
                    <p class="form-text order-stock-information">
                        Quantité actuellement disponible :
                        <strong>
                            <?php
                            echo (int)
                            $menu['stock_quantity'];
                            ?>
                            portions
                        </strong>
                    </p>

                    <small class="form-text">
                        Minimum :
                        <?php
                        echo (int)
                        $menu['minimum_people'];
                        ?>
                        personnes.
                    </small>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- récapitulatif du prix -->
                <!-- -------------------------------------------------- -->

                <div class="order-price-summary mt-4">

                    <h2 class="order-price-title">
                        Récapitulatif du prix
                    </h2>

                    <!-- Prix calculé pour une personne. -->
                    <div class="order-price-line">

                        <span>
                            Prix par personne
                        </span>

                        <strong id="price-per-person">
                            <?php
                            echo number_format(
                                $menu['base_price']
                                    / $menu['minimum_people'],
                                2,
                                ',',
                                ' '
                            );
                            ?>
                            €
                        </strong>

                    </div>

                    <!-- Nombre de personnes utilisé pour le calcul. -->
                    <div class="order-price-line">

                        <span>
                            Nombre de personnes
                        </span>

                        <strong id="summary-people-count">
                            <?php
                            echo (int)
                            $menu['minimum_people'];
                            ?>
                        </strong>

                    </div>

                    <!-- Affiche la réduction lorsqu'elle s'applique. -->
                    <div
                        class="order-price-line"
                        id="discount-line"
                        hidden>

                        <span>
                            Réduction
                        </span>

                        <strong>
                            - 10 %
                        </strong>

                    </div>

                    <!-- Prix total du menu. -->
                    <div class="order-price-line">

                        <span>
                            Prix du menu
                        </span>

                        <strong id="menu-total-price">
                            <?php
                            echo number_format(
                                $menu['base_price'],
                                2,
                                ',',
                                ' '
                            );
                            ?>
                            €
                        </strong>

                    </div>

                    <!-- -------------------------------------------------- -->
                    <!-- frais de livraison -->
                    <!-- -------------------------------------------------- -->

                    <div
                        class="order-price-delivery"
                        id="delivery-price-details"
                        hidden>

                        <!-- Distance routière calculée. -->
                        <div class="order-price-line">

                            <span>
                                Distance routière
                            </span>

                            <strong id="delivery-distance">
                                —
                            </strong>

                        </div>

                        <!-- Forfait appliqué hors Bordeaux. -->
                        <div class="order-price-line">

                            <span>
                                Forfait hors Bordeaux
                            </span>

                            <strong id="delivery-fixed-fee">
                                —
                            </strong>

                        </div>

                        <!-- Montant calculé selon la distance. -->
                        <div class="order-price-line">

                            <span>
                                Montant kilométrique
                            </span>

                            <strong id="delivery-distance-fee">
                                —
                            </strong>

                        </div>

                        <!-- Total des frais de livraison. -->
                        <div class="order-price-line">

                            <span>
                                Frais de livraison
                            </span>

                            <strong id="delivery-fee">
                                —
                            </strong>

                        </div>

                    </div>

                    <!-- Montant total de la commande. -->
                    <div
                        class="
                            order-price-line
                            order-price-total
                        ">

                        <span>
                            Total général
                        </span>

                        <strong id="order-total-price">
                            <?php
                            echo number_format(
                                $menu['base_price'],
                                2,
                                ',',
                                ' '
                            );
                            ?>
                            €
                        </strong>

                    </div>

                </div>

                <!-- -------------------------------------------------- -->
                <!-- validation de la commande -->
                <!-- -------------------------------------------------- -->

                <div class="order-submit mt-4">

                    <button
                        type="submit"
                        class="btn btn-custom w-100">
                        Valider ma commande
                    </button>

                </div>

            </form>

        </div>

    </div>
</section>

<!-- -------------------------------------------------- -->
<!-- scripts de la commande -->
<!-- -------------------------------------------------- -->

<!-- Transmet l'URL de base aux scripts JavaScript. -->
<script>
    window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;
</script>

<!-- Gère la recherche et la sélection de l'adresse. -->
<script
    src="<?php
            echo BASE_URL;
            ?>/assets/js/address-search.js">
</script>

<!-- Gère le calcul dynamique du prix de la commande. -->
<script
    src="<?php
            echo BASE_URL;
            ?>/assets/js/order-price.js">
</script>