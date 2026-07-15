<?php

/** @var array $menu */
/** @var array $formData */
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
                    Votre prestation
                </p>

                <h1 class="auth-title">
                    Commander
                </h1>

                <p class="auth-description">
                    Menu <?php echo htmlspecialchars($menu['title']); ?>
                    pour au moins
                    <?php echo (int) $menu['minimum_people']; ?> personnes.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Commande indisponible.</strong>

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
                action="<?php echo BASE_URL; ?>/order/store"
                method="post"
                class="auth-form">

                <input
                    type="hidden"
                    name="menu_id"
                    value="<?php echo (int) $menu['id']; ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                            echo htmlspecialchars(
                                $_SESSION['order_csrf_token']
                            );
                            ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">
                            Prénom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="first_name"
                            value="<?php echo htmlspecialchars($formData['first_name']); ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">
                            Nom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="last_name"
                            value="<?php echo htmlspecialchars($formData['last_name']); ?>"
                            readonly>
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
                        value="<?php echo htmlspecialchars($formData['email']); ?>"
                        readonly>
                </div>

                <div class="mt-3">
                    <label for="phone" class="form-label">
                        Téléphone
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        id="phone"
                        value="<?php echo htmlspecialchars($formData['phone']); ?>"
                        readonly>
                </div>

                <div class="mt-3">
                    <label for="delivery_address" class="form-label">
                        Adresse de livraison
                    </label>

                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            id="delivery_address"
                            name="delivery_address"
                            value="<?php echo htmlspecialchars($formData['delivery_address']); ?>"
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
                        Saisissez au moins 5 caractères, puis recherchez et sélectionnez
                        l’adresse exacte.
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
                        <label for="delivery_postal_code" class="form-label">
                            Code postal
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="delivery_postal_code"
                            name="delivery_postal_code"
                            value="<?php echo htmlspecialchars($formData['delivery_postal_code']); ?>"
                            required>
                    </div>

                    <div class="col-md-8">
                        <label for="delivery_city" class="form-label">
                            Ville
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="delivery_city"
                            name="delivery_city"
                            value="<?php echo htmlspecialchars($formData['delivery_city']); ?>" required>
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-6">
                        <label for="event_date" class="form-label">
                            Date de la prestation
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="event_date"
                            name="event_date"
                            min="<?php echo htmlspecialchars($minimumEventDate); ?>"
                            <?php if ($maximumEventDate !== null): ?>
                            max="<?php echo htmlspecialchars($maximumEventDate); ?>"
                            <?php endif; ?>
                            value="<?php echo htmlspecialchars($formData['event_date']); ?>"
                            <?php echo !empty($errors) ? 'disabled' : 'required'; ?>>

                        <small class="form-text">
                            Commande à effectuer au minimum
                            <?php echo (int) $menu['minimum_order_days']; ?>
                            jour<?php echo (int) $menu['minimum_order_days'] > 1 ? 's' : ''; ?>
                            avant la prestation.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="delivery_time" class="form-label">
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
                            $startTime = strtotime('10:00');
                            $endTime = strtotime('20:00');

                            for (
                                $time = $startTime;
                                $time <= $endTime;
                                $time += 30 * 60
                            ):
                                $timeValue = date('H:i', $time);
                                $timeLabel = date('H\hi', $time);
                            ?>
                                <option
                                    value="<?php echo $timeValue; ?>"
                                    <?php
                                    echo $formData['delivery_time'] === $timeValue
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

                <?php if ($availabilityMessage !== null): ?>
                    <div class="alert alert-secondary mt-3 mb-0" role="status">
                        <?php echo htmlspecialchars($availabilityMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                    <label for="people_count" class="form-label">
                        Nombre de personnes
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="people_count"
                        name="people_count"
                        min="<?php echo (int) $menu['minimum_people']; ?>"
                        value="<?php echo (int) $formData['people_count']; ?>"
                        data-minimum-people="<?php echo (int) $menu['minimum_people']; ?>"
                        data-base-price="<?php echo (float) $menu['base_price']; ?>"
                        required>

                    <small class="form-text">
                        Minimum :
                        <?php echo (int) $menu['minimum_people']; ?>
                        personnes.
                    </small>
                </div>

                <div class="order-price-summary mt-4">
                    <h2 class="order-price-title">
                        Récapitulatif du prix
                    </h2>

                    <div class="order-price-line">
                        <span>Prix par personne</span>

                        <strong id="price-per-person">
                            <?php
                            echo number_format(
                                $menu['base_price'] / $menu['minimum_people'],
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
                            <?php echo (int) $menu['minimum_people']; ?>
                        </strong>
                    </div>

                    <div
                        class="order-price-line"
                        id="discount-line"
                        hidden>
                        <span>Réduction</span>
                        <strong>- 10 %</strong>
                    </div>

                    <div class="order-price-line">
                        <span>Prix du menu</span>

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

                    <div
                        class="order-price-delivery"
                        id="delivery-price-details"
                        hidden>

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
                            <strong id="delivery-fee">—</strong>
                        </div>
                    </div>

                    <div class="order-price-line order-price-total">
                        <span>Total général</span>
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
                <div class="order-submit mt-4">
                    <button
                        type="submit"
                        class="btn btn-primary w-100">
                        Valider ma commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;
</script>

<script src="<?php echo BASE_URL; ?>/assets/js/address-search.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/order-price.js"></script>