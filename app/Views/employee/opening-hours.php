<?php

/** @var array $openingHours */
/** @var string $csrfToken */
/** @var string|null $success */
/** @var string|null $error */

?>

<section class="employee-dashboard-hero py-5">
    <div class="container py-4">

        <p class="section-subtitle mb-2">
            Espace professionnel
        </p>

        <h1 class="employee-dashboard-title">
            Gestion des horaires
        </h1>

        <p class="employee-dashboard-introduction mb-0">
            Modifiez les horaires affichés dans le pied de page
            du site.
        </p>

    </div>
</section>

<?php

require BASE_PATH
    . '/app/Views/employee/_navigation.php';

?>

<section class="employee-dashboard-section py-5">
    <div class="container py-3">

        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success"
                role="status">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger"
                role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <div class="employee-dashboard-card">

            <div class="employee-dashboard-card-header">

                <div>
                    <p class="section-subtitle mb-2">
                        Horaires d’ouverture
                    </p>

                    <h2 class="employee-dashboard-card-title mb-0">
                        Semaine complète
                    </h2>
                </div>

            </div>

            <?php if (!empty($openingHours)): ?>

                <form
                    action="<?php
                        echo BASE_URL
                            . '/employee/opening-hours/update';
                    ?>"
                    method="post"
                    class="employee-opening-hours-form">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                            echo htmlspecialchars($csrfToken);
                        ?>">

                    <div class="employee-opening-hours-list">

                        <?php foreach ($openingHours as $openingHour): ?>

                            <?php
                            $dayNumber =
                                (int) $openingHour['day_number'];

                            $isClosed =
                                (bool) $openingHour['is_closed'];

                            $openingTime =
                                $openingHour['opening_time']
                                ? substr(
                                    $openingHour['opening_time'],
                                    0,
                                    5
                                )
                                : '';

                            $closingTime =
                                $openingHour['closing_time']
                                ? substr(
                                    $openingHour['closing_time'],
                                    0,
                                    5
                                )
                                : '';
                            ?>

                            <fieldset
                                class="employee-opening-hour-item"
                                data-opening-hour-row>

                                <legend
                                    class="employee-opening-hour-day">
                                    <?php
                                    echo htmlspecialchars(
                                        $openingHour['day_name']
                                    );
                                    ?>
                                </legend>

                                <div class="employee-opening-hour-fields">

                                    <div
                                        class="employee-opening-hour-field">

                                        <label
                                            for="opening-time-<?php
                                                echo $dayNumber;
                                            ?>"
                                            class="form-label">
                                            Ouverture
                                        </label>

                                        <input
                                            type="time"
                                            id="opening-time-<?php
                                                echo $dayNumber;
                                            ?>"
                                            name="opening_hours[<?php
                                                echo $dayNumber;
                                            ?>][opening_time]"
                                            value="<?php
                                                echo htmlspecialchars(
                                                    $openingTime
                                                );
                                            ?>"
                                            class="form-control"
                                            data-opening-time
                                            <?php
                                            echo $isClosed
                                                ? 'disabled'
                                                : '';
                                            ?>>

                                    </div>

                                    <div
                                        class="employee-opening-hour-field">

                                        <label
                                            for="closing-time-<?php
                                                echo $dayNumber;
                                            ?>"
                                            class="form-label">
                                            Fermeture
                                        </label>

                                        <input
                                            type="time"
                                            id="closing-time-<?php
                                                echo $dayNumber;
                                            ?>"
                                            name="opening_hours[<?php
                                                echo $dayNumber;
                                            ?>][closing_time]"
                                            value="<?php
                                                echo htmlspecialchars(
                                                    $closingTime
                                                );
                                            ?>"
                                            class="form-control"
                                            data-closing-time
                                            <?php
                                            echo $isClosed
                                                ? 'disabled'
                                                : '';
                                            ?>>

                                    </div>

                                    <div
                                        class="employee-opening-hour-closed">

                                        <div class="form-check">

                                            <input
                                                type="checkbox"
                                                id="is-closed-<?php
                                                    echo $dayNumber;
                                                ?>"
                                                name="opening_hours[<?php
                                                    echo $dayNumber;
                                                ?>][is_closed]"
                                                value="1"
                                                class="form-check-input"
                                                data-closed-checkbox
                                                <?php
                                                echo $isClosed
                                                    ? 'checked'
                                                    : '';
                                                ?>>

                                            <label
                                                for="is-closed-<?php
                                                    echo $dayNumber;
                                                ?>"
                                                class="form-check-label">
                                                Fermé
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </fieldset>

                        <?php endforeach; ?>

                    </div>

                    <div class="employee-opening-hours-actions">

                        <button
                            type="submit"
                            class="btn btn-custom">
                            Enregistrer les horaires
                        </button>

                    </div>

                </form>

            <?php else: ?>

                <div class="employee-empty-state">
                    <p class="mb-0">
                        Aucun horaire n’est enregistré.
                    </p>
                </div>

            <?php endif; ?>

        </div>

    </div>
</section>

<script
    src="<?php
        echo BASE_URL
            . '/assets/js/opening-hours.js';
    ?>">
</script>