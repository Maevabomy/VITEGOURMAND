<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($pageTitle ?? 'Accueil'); ?> | Vite & Gourmand
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">
                    Vite & Gourmand
                </a>

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

                <div class="collapse navbar-collapse" id="main-navigation">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_URL; ?>/">
                                Accueil
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/menus">
                                Nos menus
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="<?php echo BASE_URL; ?>/contact">
                                Contact
                            </a>
                        </li>

                        <?php if (!empty($_SESSION['user'])): ?>

                            <?php
                            $connectedUserRole =
                                $_SESSION['user']['role'] ?? null;

                            if ($connectedUserRole === 'user') {
                                $accountUrl =
                                    BASE_URL . '/user/dashboard';

                                $accountLabel =
                                    'Mon compte';
                            } elseif ($connectedUserRole === 'admin') {
                                $accountUrl =
                                    BASE_URL . '/admin';

                                $accountLabel =
                                    'Administration';
                            } else {
                                $accountUrl =
                                    BASE_URL . '/employee';

                                $accountLabel =
                                    'Espace professionnel';
                            }
                            ?>

                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="<?php echo htmlspecialchars($accountUrl); ?>">
                                    <?php echo htmlspecialchars($accountLabel); ?>
                                </a>
                            </li>

                            <li class="nav-item">
                                <form
                                    action="<?php echo BASE_URL; ?>/logout"
                                    method="post"
                                    class="d-inline">

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
                                        class="nav-link border-0 bg-transparent">
                                        Déconnexion
                                    </button>
                                </form>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/register">
                                    Inscription
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/login">
                                    Connexion
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>