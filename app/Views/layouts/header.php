<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($pageTitle); ?> | Vite & Gourmand
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?php echo BASE_URL; ?>/assets/css/style.css"
    >
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
                aria-label="Afficher le menu"
            >
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
                        <a class="nav-link disabled" href="#" aria-disabled="true">
                            Nos menus
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" aria-disabled="true">
                            Contact
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" aria-disabled="true">
                            Connexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>