<!DOCTYPE html>
<html lang="fr">

<head>

    <!-- -------------------------------------------------- -->
    <!-- configuration de la page -->
    <!-- -------------------------------------------------- -->

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?php
        echo htmlspecialchars(
            $pageTitle ?? 'Accueil'
        );
        ?> | Vite & Gourmand
    </title>

    <!-- Charge Bootstrap depuis le CDN. -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Charge la feuille de style principale du site. -->
    <link
        rel="stylesheet"
        href="<?php echo BASE_URL; ?>/assets/css/style.css">

</head>

<body>

    <!-- -------------------------------------------------- -->
    <!-- en-tête du site -->
    <!-- -------------------------------------------------- -->

    <header>

        <!-- -------------------------------------------------- -->
        <!-- navigation principale -->
        <!-- -------------------------------------------------- -->

        <nav class="navbar navbar-expand-lg navbar-dark">

            <div class="container">

                <!-- Lien vers la page d'accueil. -->
                <a
                    class="navbar-brand"
                    href="<?php echo BASE_URL; ?>/">
                    Vite & Gourmand
                </a>

                <!-- Affiche le menu sur les petits écrans. -->
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#main-navigation"
                    aria-controls="main-navigation"
                    aria-expanded="false"
                    aria-label="Afficher le menu">

                    <span class="navbar-toggler-icon"></span>

                </button>

                <div
                    class="collapse navbar-collapse"
                    id="main-navigation">

                    <ul class="navbar-nav ms-auto">

                        <!-- Accès à la page d'accueil. -->
                        <li class="nav-item">

                            <a
                                class="nav-link active"
                                href="<?php echo BASE_URL; ?>/">
                                Accueil
                            </a>

                        </li>

                        <!-- Accès au catalogue des menus. -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="<?php echo BASE_URL; ?>/menus">
                                Nos menus
                            </a>

                        </li>

                        <!-- Accès au formulaire de contact. -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="<?php echo BASE_URL; ?>/contact">
                                Contact
                            </a>

                        </li>

                        <!-- -------------------------------------------------- -->
                        <!-- navigation de l'utilisateur connecté -->
                        <!-- -------------------------------------------------- -->

                        <?php if (!empty($_SESSION['user'])): ?>

                            <?php

                            /* -------------------------------------------------- */
                            /* préparation du lien vers l'espace utilisateur */
                            /* -------------------------------------------------- */

                            /* Récupère le rôle du compte actuellement connecté. */
                            $connectedUserRole =
                                $_SESSION['user']['role']
                                ?? null;

                            /* Définit l'espace accessible selon le rôle. */
                            if ($connectedUserRole === 'user') {
                                $accountUrl =
                                    BASE_URL
                                    . '/user/dashboard';

                                $accountLabel =
                                    'Mon compte';
                            } elseif (
                                $connectedUserRole === 'admin'
                            ) {
                                $accountUrl =
                                    BASE_URL
                                    . '/admin';

                                $accountLabel =
                                    'Administration';
                            } else {
                                $accountUrl =
                                    BASE_URL
                                    . '/employee';

                                $accountLabel =
                                    'Espace professionnel';
                            }

                            ?>

                            <!-- Accès à l'espace correspondant au rôle. -->
                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="<?php
                                            echo htmlspecialchars(
                                                $accountUrl
                                            );
                                            ?>">
                                    <?php
                                    echo htmlspecialchars(
                                        $accountLabel
                                    );
                                    ?>
                                </a>

                            </li>

                            <!-- Déconnecte l'utilisateur de sa session. -->
                            <li class="nav-item">

                                <form
                                    action="<?php
                                            echo BASE_URL;
                                            ?>/logout"
                                    method="post"
                                    class="d-inline">

                                    <!-- Protège la déconnexion contre les requêtes CSRF. -->
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php
                                                echo htmlspecialchars(
                                                    $_SESSION['auth_csrf_token'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>">

                                    <button
                                        type="submit"
                                        class="
                                            nav-link
                                            border-0
                                            bg-transparent
                                        ">
                                        Déconnexion
                                    </button>

                                </form>

                            </li>

                        <?php else: ?>

                            <!-- -------------------------------------------------- -->
                            <!-- navigation du visiteur -->
                            <!-- -------------------------------------------------- -->

                            <!-- Accès à la création d'un compte. -->
                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="<?php
                                            echo BASE_URL;
                                            ?>/register">
                                    Inscription
                                </a>

                            </li>

                            <!-- Accès à la page de connexion. -->
                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="<?php
                                            echo BASE_URL;
                                            ?>/login">
                                    Connexion
                                </a>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>

        </nav>

    </header>

    <!-- -------------------------------------------------- -->
    <!-- contenu principal -->
    <!-- -------------------------------------------------- -->

    <main>