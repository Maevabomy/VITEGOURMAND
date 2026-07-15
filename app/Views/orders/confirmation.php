<?php

/** @var array $confirmation */

$formattedEventDate = date(
    'd/m/Y',
    strtotime($confirmation['event_date'])
);

$formattedDeliveryTime = substr(
    $confirmation['delivery_time'],
    0,
    5
);

?>
<section class="order-confirmation-section py-5">
    <div class="container">

        <div class="order-confirmation-card mx-auto">

            <!-- -------------------------------------------------- -->
            <!-- en-tête -->
            <!-- -------------------------------------------------- -->

            <div class="order-confirmation-header text-center">
                <div
                    class="order-confirmation-icon"
                    aria-hidden="true">
                    ✓
                </div>

                <p class="section-subtitle mb-2">
                    Merci pour votre confiance
                </p>

                <h1 class="order-confirmation-title">
                    Commande confirmée
                </h1>

                <p class="order-confirmation-introduction">
                    Votre commande a bien été enregistrée.
                    Notre équipe va maintenant l’examiner.
                </p>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- numéro et statut -->
            <!-- -------------------------------------------------- -->

            <div class="order-confirmation-reference">
                <div>
                    <span class="order-confirmation-label">
                        Numéro de commande
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $confirmation['order_number']
                        );
                        ?>
                    </strong>
                </div>

                <div>
                    <span class="order-confirmation-label">
                        Statut
                    </span>

                    <span class="order-status-badge">
                        <?php
                        echo htmlspecialchars(
                            $confirmation['status_name']
                        );
                        ?>
                    </span>
                </div>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- prestation -->
            <!-- -------------------------------------------------- -->

            <div class="order-confirmation-block">
                <h2 class="order-confirmation-subtitle">
                    Votre prestation
                </h2>

                <dl class="order-confirmation-list">
                    <div class="order-confirmation-row">
                        <dt>Menu</dt>

                        <dd>
                            <?php
                            echo htmlspecialchars(
                                $confirmation['menu_title']
                            );
                            ?>
                        </dd>
                    </div>

                    <div class="order-confirmation-row">
                        <dt>Nombre de personnes</dt>

                        <dd>
                            <?php
                            echo (int) $confirmation['people_count'];
                            ?>
                        </dd>
                    </div>

                    <div class="order-confirmation-row">
                        <dt>Date</dt>

                        <dd>
                            <?php echo $formattedEventDate; ?>
                        </dd>
                    </div>

                    <div class="order-confirmation-row">
                        <dt>Heure de livraison</dt>

                        <dd>
                            <?php echo $formattedDeliveryTime; ?>
                        </dd>
                    </div>

                    <div class="order-confirmation-row">
                        <dt>Adresse</dt>

                        <dd>
                            <?php
                            echo htmlspecialchars(
                                $confirmation['delivery_address']
                            );
                            ?>
                            <br>

                            <?php
                            echo htmlspecialchars(
                                $confirmation['delivery_postal_code']
                            );
                            ?>

                            <?php
                            echo htmlspecialchars(
                                $confirmation['delivery_city']
                            );
                            ?>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- prix -->
            <!-- -------------------------------------------------- -->

            <div class="order-confirmation-block">
                <h2 class="order-confirmation-subtitle">
                    Récapitulatif du prix
                </h2>

                <div class="order-confirmation-price-line">
                    <span>Prix du menu</span>

                    <strong>
                        <?php
                        echo number_format(
                            (float) $confirmation['menu_price'],
                            2,
                            ',',
                            ' '
                        );
                        ?>
                        €
                    </strong>
                </div>

                <div class="order-confirmation-price-line">
                    <span>Frais de livraison</span>

                    <strong>
                        <?php
                        echo number_format(
                            (float) $confirmation['delivery_price'],
                            2,
                            ',',
                            ' '
                        );
                        ?>
                        €
                    </strong>
                </div>

                <div
                    class="
                        order-confirmation-price-line
                        order-confirmation-total
                    ">
                    <span>Total général</span>

                    <strong>
                        <?php
                        echo number_format(
                            (float) $confirmation['total_price'],
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
            <!-- information e-mail -->
            <!-- -------------------------------------------------- -->

            <?php if (!empty($confirmation['email_sent'])): ?>
                <div
                    class="order-confirmation-message"
                    role="status">
                    Un e-mail de confirmation a été envoyé à
                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $confirmation['customer_email']
                        );
                        ?>
                    </strong>.
                </div>
            <?php else: ?>
                <div
                    class="order-confirmation-message"
                    role="status">
                    Votre commande est bien enregistrée.
                    L’e-mail n’a pas pu être expédié, mais cela
                    n’a aucune incidence sur votre commande.
                </div>
            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- actions -->
            <!-- -------------------------------------------------- -->

            <div class="order-confirmation-actions">
                <a
                    href="<?php echo BASE_URL; ?>/menus"
                    class="btn btn-custom">
                    Découvrir les autres menus
                </a>
            </div>

        </div>
    </div>
</section>