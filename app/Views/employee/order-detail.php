<?php

/** @var array $order */
/** @var array $statusHistory */
/** @var array $allowedNextStatuses */
/** @var string $statusCsrfToken */
/** @var string $cancelCsrfToken */
/** @var bool $canCancelOrder */
/** @var string|null $success */
/** @var string|null $error */


/* -------------------------------------------------- */
/* préparation de l'affichage */
/* -------------------------------------------------- */

/* Prépare la date de prestation pour l'affichage. */
$eventDate =
    date_create($order['event_date']);

/* Prépare l'heure de livraison pour l'affichage. */
$deliveryTime =
    date_create($order['delivery_time']);

/* Prépare la date de création de la commande pour l'affichage. */
$createdAt =
    date_create($order['created_at']);

?>

<!-- -------------------------------------------------- -->
<!-- présentation du détail de la commande -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Détail de la commande
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Référence
            <?php
            echo htmlspecialchars(
                $order['order_number']
            );
            ?>
        </p>

    </div>
</section>

<?php

/* Charge la navigation de l'espace employé. */
require BASE_PATH
    . '/app/Views/employee/_navigation.php';

?>

<!-- -------------------------------------------------- -->
<!-- détail de la commande -->
<!-- -------------------------------------------------- -->

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <!-- -------------------------------------------------- -->
        <!-- messages de validation -->
        <!-- -------------------------------------------------- -->

        <!-- Affiche le message de confirmation après une action réussie. -->
        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success mb-4"
                role="status">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <!-- Affiche le message d'erreur en cas d'échec. -->
        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger mb-4"
                role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="row g-4">

            <!-- -------------------------------------------------- -->
            <!-- résumé de la commande -->
            <!-- -------------------------------------------------- -->

            <div class="col-12">

                <section class="employee-dashboard-card">

                    <div class="employee-order-detail-heading">

                        <div>

                            <p class="section-subtitle mb-2">
                                Commande
                            </p>

                            <h2 class="employee-dashboard-card-title">
                                Informations générales
                            </h2>

                        </div>

                        <span class="user-order-status">
                            <?php
                            echo htmlspecialchars(
                                $order['status_name']
                            );
                            ?>
                        </span>

                    </div>

                    <div class="employee-order-detail-grid">

                        <div>

                            <span class="user-order-label">
                                Numéro
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $order['order_number']
                                );
                                ?>
                            </strong>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Menu
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $order['menu_title']
                                );
                                ?>
                            </strong>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Commandée le
                            </span>

                            <strong>
                                <?php
                                echo $createdAt
                                    ? $createdAt->format(
                                        'd/m/Y à H\hi'
                                    )
                                    : '';
                                ?>
                            </strong>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Nombre de personnes
                            </span>

                            <strong>
                                <?php
                                echo (int)
                                $order['people_count'];
                                ?>
                            </strong>

                        </div>

                    </div>

                </section>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- changement de statut -->
            <!-- -------------------------------------------------- -->

            <?php if (!empty($allowedNextStatuses)): ?>

                <div class="col-12">

                    <section class="employee-dashboard-card">

                        <div class="employee-dashboard-card-header">

                            <p class="section-subtitle mb-2">
                                Traitement
                            </p>

                            <h2 class="employee-dashboard-card-title">
                                Mettre à jour le statut
                            </h2>

                        </div>

                        <form
                            action="<?php
                                    echo BASE_URL
                                        . '/employee/order/status';
                                    ?>"
                            method="post"
                            class="employee-status-form">

                            <!-- Identifie la commande à mettre à jour. -->
                            <input
                                type="hidden"
                                name="order_id"
                                value="<?php
                                        echo (int) $order['id'];
                                        ?>">

                            <!-- Protège l'action contre les requêtes CSRF. -->
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php
                                        echo htmlspecialchars(
                                            $statusCsrfToken
                                        );
                                        ?>">

                            <!-- Sélection du nouveau statut. -->
                            <div>

                                <label
                                    for="new-status"
                                    class="form-label">
                                    Nouveau statut
                                </label>

                                <select
                                    class="form-select"
                                    id="new-status"
                                    name="new_status"
                                    required>

                                    <option value="">
                                        Sélectionner
                                    </option>

                                    <?php foreach (
                                        $allowedNextStatuses
                                        as $allowedStatus
                                    ): ?>

                                        <option
                                            value="<?php
                                                    echo htmlspecialchars(
                                                        $allowedStatus
                                                    );
                                                    ?>">
                                            <?php
                                            echo htmlspecialchars(
                                                $allowedStatus
                                            );
                                            ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <!-- Note interne facultative. -->
                            <div>

                                <label
                                    for="status-note"
                                    class="form-label">
                                    Note interne
                                </label>

                                <textarea
                                    class="form-control"
                                    id="status-note"
                                    name="note"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Information facultative sur ce changement"></textarea>

                            </div>

                            <div class="employee-status-form-action">

                                <button
                                    type="submit"
                                    class="btn btn-custom">
                                    Mettre à jour
                                </button>

                            </div>

                        </form>

                        <!-- Précise les statuts possibles après une livraison. -->
                        <?php if (
                            $order['status_name'] === 'Livrée'
                        ): ?>

                            <p class="form-text mt-3 mb-0">
                                Choisissez « Terminée » si aucun
                                matériel n’a été prêté. Choisissez
                                « En attente du retour de matériel »
                                si le client doit restituer du matériel.
                            </p>

                        <?php endif; ?>

                    </section>

                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- annulation de la commande -->
            <!-- -------------------------------------------------- -->

            <?php if ($canCancelOrder): ?>

                <div class="col-12">

                    <section class="
                        employee-dashboard-card
                        employee-cancellation-card
                    ">

                        <div class="employee-dashboard-card-header">

                            <p class="section-subtitle mb-2">
                                Action sensible
                            </p>

                            <h2 class="employee-dashboard-card-title">
                                Annuler la commande
                            </h2>

                        </div>

                        <p class="employee-cancellation-warning">
                            Contactez le client avant d’annuler la commande.
                            Le mode de contact et le motif seront enregistrés
                            dans le suivi interne.
                        </p>

                        <form
                            action="<?php
                                    echo BASE_URL
                                        . '/employee/order/cancel';
                                    ?>"
                            method="post"
                            class="employee-cancellation-form"
                            onsubmit="
                                return confirm(
                                    'Confirmer définitivement '
                                    + 'l’annulation de cette commande ?'
                                );
                            ">

                            <!-- Identifie la commande à annuler. -->
                            <input
                                type="hidden"
                                name="order_id"
                                value="<?php
                                        echo (int) $order['id'];
                                        ?>">

                            <!-- Protège l'action contre les requêtes CSRF. -->
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php
                                        echo htmlspecialchars(
                                            $cancelCsrfToken
                                        );
                                        ?>">

                            <!-- Mode utilisé pour contacter le client. -->
                            <div>

                                <label
                                    for="contact-method"
                                    class="form-label">
                                    Mode de contact
                                </label>

                                <select
                                    class="form-select"
                                    id="contact-method"
                                    name="contact_method"
                                    required>

                                    <option value="">
                                        Sélectionner
                                    </option>

                                    <option value="Téléphone">
                                        Téléphone
                                    </option>

                                    <option value="E-mail">
                                        E-mail
                                    </option>

                                </select>

                            </div>

                            <!-- Motif de l'annulation. -->
                            <div>

                                <label
                                    for="cancellation-reason"
                                    class="form-label">
                                    Motif de l’annulation
                                </label>

                                <textarea
                                    class="form-control"
                                    id="cancellation-reason"
                                    name="cancellation_reason"
                                    rows="4"
                                    minlength="10"
                                    maxlength="1000"
                                    required
                                    placeholder="Expliquez précisément la raison de l’annulation"></textarea>

                            </div>

                            <!-- Confirme que le client a été contacté avant l'annulation. -->
                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="1"
                                    id="contact-confirmed"
                                    name="contact_confirmed"
                                    required>

                                <label
                                    class="form-check-label"
                                    for="contact-confirmed">
                                    Je confirme avoir contacté le client
                                    avant cette annulation.
                                </label>

                            </div>

                            <div>

                                <button
                                    type="submit"
                                    class="
                                        btn
                                        employee-cancellation-button
                                    ">
                                    Annuler définitivement
                                </button>

                            </div>

                        </form>

                    </section>

                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- coordonnées du client -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">

                <section class="employee-dashboard-card h-100">

                    <div class="employee-dashboard-card-header">

                        <p class="section-subtitle mb-2">
                            Client
                        </p>

                        <h2 class="employee-dashboard-card-title">
                            Coordonnées
                        </h2>

                    </div>

                    <div class="employee-order-detail-list">

                        <div>

                            <span class="user-order-label">
                                Nom complet
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_first_name']
                                        . ' '
                                        . $order['customer_last_name']
                                );
                                ?>
                            </strong>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Adresse mail
                            </span>

                            <a
                                href="mailto:<?php
                                                echo htmlspecialchars(
                                                    $order['customer_email']
                                                );
                                                ?>">
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_email']
                                );
                                ?>
                            </a>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Téléphone
                            </span>

                            <a
                                href="tel:<?php
                                            echo htmlspecialchars(
                                                $order['customer_phone']
                                            );
                                            ?>">
                                <?php
                                echo htmlspecialchars(
                                    $order['customer_phone']
                                );
                                ?>
                            </a>

                        </div>

                    </div>

                </section>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- livraison de la prestation -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">

                <section class="employee-dashboard-card h-100">

                    <div class="employee-dashboard-card-header">

                        <p class="section-subtitle mb-2">
                            Prestation
                        </p>

                        <h2 class="employee-dashboard-card-title">
                            Livraison
                        </h2>

                    </div>

                    <div class="employee-order-detail-list">

                        <div>

                            <span class="user-order-label">
                                Adresse
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $order['delivery_address']
                                );
                                ?>
                                <br>
                                <?php
                                echo htmlspecialchars(
                                    $order['delivery_postal_code']
                                        . ' '
                                        . $order['delivery_city']
                                );
                                ?>
                            </strong>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Date
                            </span>

                            <strong>
                                <?php
                                echo $eventDate
                                    ? $eventDate->format('d/m/Y')
                                    : '';
                                ?>
                            </strong>

                        </div>

                        <div>

                            <span class="user-order-label">
                                Heure
                            </span>

                            <strong>
                                <?php
                                echo $deliveryTime
                                    ? $deliveryTime->format('H\hi')
                                    : '';
                                ?>
                            </strong>

                        </div>

                    </div>

                </section>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- détail du prix -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">

                <section class="employee-dashboard-card h-100">

                    <div class="employee-dashboard-card-header">

                        <p class="section-subtitle mb-2">
                            Facturation
                        </p>

                        <h2 class="employee-dashboard-card-title">
                            Détail du prix
                        </h2>

                    </div>

                    <div class="employee-order-detail-list">

                        <div>

                            <span class="user-order-label">
                                Prix du menu
                            </span>

                            <strong>
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

                        <div>

                            <span class="user-order-label">
                                Livraison
                            </span>

                            <strong>
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

                        <div>

                            <span class="user-order-label">
                                Total
                            </span>

                            <strong class="employee-order-total">
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

                </section>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- suivi du matériel -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-6">

                <section class="employee-dashboard-card h-100">

                    <div class="employee-dashboard-card-header">

                        <p class="section-subtitle mb-2">
                            Logistique
                        </p>

                        <h2 class="employee-dashboard-card-title">
                            Matériel
                        </h2>

                    </div>

                    <div class="employee-order-detail-list">

                        <div>

                            <span class="user-order-label">
                                Matériel prêté
                            </span>

                            <strong>
                                <?php
                                echo (int) $order['equipment_loaned'] === 1
                                    ? 'Oui'
                                    : 'Non';
                                ?>
                            </strong>

                        </div>

                        <!-- Affiche le suivi du matériel lorsqu'un prêt est enregistré. -->
                        <?php if (
                            (int) $order['equipment_loaned'] === 1
                        ): ?>

                            <div>

                                <span class="user-order-label">
                                    Date limite de retour
                                </span>

                                <strong>
                                    <?php
                                    if (
                                        !empty($order['equipment_return_deadline'])
                                    ) {
                                        echo date(
                                            'd/m/Y à H\hi',
                                            strtotime(
                                                $order['equipment_return_deadline']
                                            )
                                        );
                                    } else {
                                        echo 'Non définie';
                                    }
                                    ?>
                                </strong>

                            </div>

                            <div>

                                <span class="user-order-label">
                                    Matériel restitué
                                </span>

                                <strong>
                                    <?php
                                    echo !empty($order['equipment_returned_at'])
                                        ? 'Oui'
                                        : 'Non';
                                    ?>
                                </strong>

                            </div>

                        <?php endif; ?>

                    </div>

                </section>

            </div>

            <!-- -------------------------------------------------- -->
            <!-- informations d'annulation -->
            <!-- -------------------------------------------------- -->

            <?php if (
                !empty($order['cancellation_reason'])
                || !empty($order['cancellation_contact_method'])
            ): ?>

                <div class="col-12">

                    <section class="employee-dashboard-card">

                        <div class="employee-dashboard-card-header">

                            <p class="section-subtitle mb-2">
                                Annulation
                            </p>

                            <h2 class="employee-dashboard-card-title">
                                Informations d’annulation
                            </h2>

                        </div>

                        <div class="employee-order-detail-grid">

                            <div>

                                <span class="user-order-label">
                                    Mode de contact
                                </span>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $order['cancellation_contact_method']
                                            ?? 'Non renseigné'
                                    );
                                    ?>
                                </strong>

                            </div>

                            <div>

                                <span class="user-order-label">
                                    Motif
                                </span>

                                <strong>
                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $order['cancellation_reason']
                                                ?? 'Non renseigné'
                                        )
                                    );
                                    ?>
                                </strong>

                            </div>

                        </div>

                    </section>

                </div>

            <?php endif; ?>

            <!-- -------------------------------------------------- -->
            <!-- historique des statuts -->
            <!-- -------------------------------------------------- -->

            <div class="col-12">

                <section class="employee-dashboard-card">

                    <div class="employee-dashboard-card-header">

                        <p class="section-subtitle mb-2">
                            Suivi
                        </p>

                        <h2 class="employee-dashboard-card-title">
                            Historique des statuts
                        </h2>

                    </div>

                    <?php if (!empty($statusHistory)): ?>

                        <div class="employee-status-history">

                            <?php foreach (
                                $statusHistory
                                as $historyItem
                            ): ?>

                                <article
                                    class="employee-status-history-item">

                                    <div>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $historyItem['status_name']
                                            );
                                            ?>
                                        </strong>

                                        <p class="mb-0">
                                            <?php
                                            echo date(
                                                'd/m/Y à H\hi',
                                                strtotime(
                                                    $historyItem['created_at']
                                                )
                                            );
                                            ?>
                                        </p>

                                    </div>

                                    <!-- Affiche l'employé ayant effectué le changement. -->
                                    <?php if (
                                        !empty($historyItem['employee_first_name'])
                                    ): ?>

                                        <p class="mb-0">
                                            Modifié par
                                            <?php
                                            echo htmlspecialchars(
                                                $historyItem['employee_first_name']
                                                    . ' '
                                                    . $historyItem['employee_last_name']
                                            );
                                            ?>
                                        </p>

                                    <?php endif; ?>

                                    <!-- Affiche la note associée au changement de statut. -->
                                    <?php if (
                                        !empty($historyItem['note'])
                                    ): ?>

                                        <p class="mb-0">
                                            <?php
                                            echo nl2br(
                                                htmlspecialchars(
                                                    $historyItem['note']
                                                )
                                            );
                                            ?>
                                        </p>

                                    <?php endif; ?>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <!-- Affiche un message lorsqu'aucun historique n'existe. -->
                        <p class="mb-0">
                            Aucun historique n’est disponible.
                        </p>

                    <?php endif; ?>

                </section>

            </div>

        </div>

        <!-- -------------------------------------------------- -->
        <!-- retour à la liste des commandes -->
        <!-- -------------------------------------------------- -->

        <div class="mt-4">

            <a
                href="<?php
                        echo BASE_URL
                            . '/employee/dashboard';
                        ?>"
                class="btn employee-filter-reset-button">
                Retour aux commandes
            </a>

        </div>

    </div>
</section>