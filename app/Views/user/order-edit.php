<?php

/** @var array $order */
/** @var array $menu */
/** @var array $formData */
/** @var array $deliveryTimes */
/** @var int $availableStock */
/** @var string $minimumEventDate */
/** @var string|null $maximumEventDate */
/** @var string|null $availabilityMessage */
/** @var array $errors */

?>

<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <div class="auth-card-header text-center mb-4">
                <p class="section-subtitle mb-2">
                    Espace personnel
                </p>

                <h1 class="auth-title">
                    Modifier ma commande
                </h1>

                <p class="auth-description">
                    Référence
                    <?php
                    echo htmlspecialchars(
                        $order['order_number']
                    );
                    ?>
                </p>
            </div>

            <div class="alert alert-secondary" role="status">
                <strong>Menu conservé :</strong>
                <?php echo htmlspecialchars($menu['title']); ?>

                <br>

                Le choix du menu ne peut pas être modifié.
            </div>

            <?php if (!empty($errors)): ?>

                <div class="alert alert-danger" role="alert">
                    <strong>Modification impossible.</strong>

                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?php echo htmlspecialchars($error); ?>
                            </li>

                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endif; ?>

            <form
                action="<?php echo BASE_URL; ?>/user/order/update"
                method="post"
                class="auth-form"
                id="order-edit-form">

                <input
                    type="hidden"
                    name="order_id"
                    value="<?php echo (int) $order['id']; ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                            echo htmlspecialchars(
                                $_SESSION['order_edit_csrf_token']
                            );
                            ?>">

                <div class="row g-3">

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
                                    );
                                    ?>"
                            maxlength="100"
                            required>
                    </div>

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
                                    );
                                    ?>"
                            maxlength="100"
                            required>
                    </div>

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
                        value="<?php
                                echo htmlspecialchars(
                                    $formData['email']
                                );
                                ?>"
                        maxlength="255"
                        required>
                </div>

                <div class="mt-3">
                    <label for="phone" class="form-label">
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
                                );
                                ?>"
                        maxlength="30"
                        required>
                </div>

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

                        <button
                            type="button"
                            class="btn btn-custom"
                            id="search-address-button">
                            Rechercher
                        </button>

                    </div>

                    <small class="form-text">
                        Recherchez puis sélectionnez l’adresse exacte,
                        même si vous conservez l’adresse actuelle.
                    </small>

                    <div
                        class="address-search-status mt-2"
                        id="address-search-status"
                        role="status"
                        aria-live="polite">
                    </div>

                    <div
                        class="address-search-results mt-2"
                        id="address-search-results"
                        aria-label="Propositions d’adresses">
                    </div>

                    <input
                        type="hidden"
                        id="delivery_latitude"
                        name="delivery_latitude">

                    <input
                        type="hidden"
                        id="delivery_longitude"
                        name="delivery_longitude">

                </div>

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
                            maxlength="10"
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
                            maxlength="150"
                            required>
                    </div>

                </div>

                <div class="row g-3 mt-0">

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
                            required>

                        <small class="form-text">
                            Le délai minimum du menu reste applicable :
                            <?php
                            echo (int) $menu['minimum_order_days'];
                            ?>
                            jour<?php
                                echo (int) $menu['minimum_order_days'] > 1 ? 's' : '';
                                ?>.
                        </small>
                    </div>

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

                            <?php foreach ($deliveryTimes as $time): ?>

                                <option
                                    value="<?php
                                            echo htmlspecialchars($time);
                                            ?>"
                                    <?php
                                    echo $formData['delivery_time'] === $time
                                        ? 'selected'
                                        : '';
                                    ?>>
                                    <?php
                                    echo htmlspecialchars(
                                        str_replace(':', 'h', $time)
                                    );
                                    ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <small class="form-text">
                            Livraison possible de 10h00 à 20h00.
                        </small>
                    </div>

                </div>

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
                        max="<?php echo $availableStock; ?>"
                        value="<?php
                                echo (int) $formData['people_count'];
                                ?>"
                        data-minimum-people="<?php
                                                echo (int) $menu['minimum_people'];
                                                ?>"
                        data-base-price="<?php
                                            echo (float) $menu['base_price'];
                                            ?>"
                        data-current-delivery-fee="<?php
                                                    echo (float) $order['delivery_price'];
                                                    ?>"
                        required>

                    <p class="form-text order-stock-information">
                        Quantité maximale disponible pour cette commande :
                        <strong>
                            <?php echo $availableStock; ?>
                            portions
                        </strong>
                    </p>

                    <small class="form-text">
                        Minimum :
                        <?php
                        echo (int) $menu['minimum_people'];
                        ?>
                        personnes.
                    </small>
                </div>

                <div class="order-price-summary mt-4">

                    <h2 class="order-price-title">
                        Nouveau récapitulatif du prix
                    </h2>

                    <div class="order-price-line">
                        <span>Prix par personne</span>

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

                    <div class="order-price-line">
                        <span>Nombre de personnes</span>

                        <strong id="summary-people-count">
                            <?php
                            echo (int) $formData['people_count'];
                            ?>
                        </strong>
                    </div>

                    <?php
                    $discountApplies =
                        (int) $formData['people_count']
                        >= (int) $menu['minimum_people'] + 5;
                    ?>

                    <div
                        class="order-price-line"
                        id="discount-line"
                        <?php
                        echo $discountApplies
                            ? ''
                            : 'hidden';
                        ?>>
                        <span>Réduction</span>
                        <strong>- 10 %</strong>
                    </div>

                    <div class="order-price-line">
                        <span>Prix du menu</span>

                        <strong id="menu-total-price">
                            <?php
                            echo number_format(
                                (float) $order['menu_price'],
                                2,
                                ',',
                                ' '
                            );
                            ?>
                            €
                        </strong>
                    </div>

                    <div
                        class="order-price-delivery"
                        id="delivery-price-details">

                        <div class="order-price-line">
                            <span>Distance routière</span>
                            <strong id="delivery-distance">—</strong>
                        </div>

                        <div class="order-price-line">
                            <span>Forfait hors Bordeaux</span>
                            <strong id="delivery-fixed-fee">—</strong>
                        </div>

                        <div class="order-price-line">
                            <span>Montant kilométrique</span>
                            <strong id="delivery-distance-fee">—</strong>
                        </div>

                        <div class="order-price-line">
                            <span>Frais de livraison</span>

                            <strong id="delivery-fee">
                                <?php
                                echo number_format(
                                    (float) $order['delivery_price'],
                                    2,
                                    ',',
                                    ' '
                                );
                                ?>
                                €
                            </strong>
                        </div>

                    </div>

                    <div
                        class="
                            order-price-line
                            order-price-total
                        ">
                        <span>Total général</span>

                        <strong id="order-total-price">
                            <?php
                            echo number_format(
                                (float) $order['total_price'],
                                2,
                                ',',
                                ' '
                            );
                            ?>
                            €
                        </strong>
                    </div>

                </div>

                <div class="order-submit mt-4">

                    <button
                        type="submit"
                        class="btn btn-custom w-100">
                        Enregistrer les modifications
                    </button>

                </div>

            </form>

            <div class="text-center mt-4">
                <a
                    href="<?php
                            echo BASE_URL
                                . '/user/order/detail?id='
                                . (int) $order['id'];
                            ?>"
                    class="auth-link">
                    Retour au détail de la commande
                </a>
            </div>

        </div>
    </div>
</section>

<script>
    window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;
</script>

<script src="<?php
                echo BASE_URL;
                ?>/assets/js/address-search.js"></script>

<script src="<?php
                echo BASE_URL;
                ?>/assets/js/order-price.js"></script>