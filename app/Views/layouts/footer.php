</main>

<footer class="site-footer">
    <div class="container py-5">
        <div class="row gy-4">

            <!-- -------------------------------------------------- -->
            <!-- présentation de l'entreprise -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-4 col-md-6">
                <h2 class="footer-title">
                    Vite & Gourmand
                </h2>

                <p class="mb-0">
                    Traiteur événementiel à Bordeaux depuis 25 ans.
                </p>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- horaires d'ouverture -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-5 col-md-6">
                <h2 class="footer-title">
                    Horaires
                </h2>

                <?php if (!empty($openingHours)): ?>
                    <ul class="opening-hours-list list-unstyled mb-0">

                        <?php foreach ($openingHours as $openingHour): ?>
                            <li class="opening-hours-item">

                                <span>
                                    <?php echo htmlspecialchars($openingHour['day_name']); ?>
                                </span>

                                <span>
                                    <?php if ((bool) $openingHour['is_closed']): ?>
                                        Fermé
                                    <?php else: ?>
                                        <?php
                                        echo htmlspecialchars(
                                            substr($openingHour['opening_time'], 0, 5)
                                        );
                                        ?>

                                        -

                                        <?php
                                        echo htmlspecialchars(
                                            substr($openingHour['closing_time'], 0, 5)
                                        );
                                        ?>
                                    <?php endif; ?>
                                </span>

                            </li>
                        <?php endforeach; ?>

                    </ul>
                <?php else: ?>
                    <p class="mb-0">
                        Les horaires seront bientôt disponibles.
                    </p>
                <?php endif; ?>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- liens utiles -->
            <!-- -------------------------------------------------- -->

            <div class="col-lg-3 col-md-6">
                <h2 class="footer-title">
                    Informations
                </h2>

                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="#" class="footer-link">
                            Mentions légales
                        </a>
                    </li>

                    <li>
                        <a href="#" class="footer-link">
                            Conditions générales de vente
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>