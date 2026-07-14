<?php

namespace App\Controllers;

use App\Models\OpeningHour;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Services\MailService;


class AuthController
{
    /* -------------------------------------------------- */
    /* affichage de l'inscription */
    /* -------------------------------------------------- */

    /* Prépare la page de création de compte. */
    public function register(): void
    {
        $this->showRegisterForm();
    }

    /* -------------------------------------------------- */
    /* traitement de l'inscription */
    /* -------------------------------------------------- */

    /* Vérifie le formulaire et crée le compte. */
    public function store(): void
    {
        $errors = [];
        $success = null;

        $formData = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
        ];

        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        /* Vérifie les champs obligatoires. */
        foreach ($formData as $field => $value) {
            if ($value === '') {
                $errors[] = 'Tous les champs sont obligatoires.';
                break;
            }
        }

        /* Vérifie le format de l'adresse mail. */
        if ($formData['email'] !== '' && !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L’adresse mail n’est pas valide.';
        }

        /* Vérifie si l'adresse mail est déjà utilisée. */
        if ($formData['email'] !== '' && User::emailExists($formData['email'])) {
            $errors[] = 'Cette adresse mail est déjà utilisée.';
        }

        /* Vérifie la sécurité du mot de passe. */
        if (!$this->isPasswordSecure($password)) {
            $errors[] = 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
        }

        /* Vérifie la confirmation du mot de passe. */
        if ($password !== $passwordConfirmation) {
            $errors[] = 'Les deux mots de passe ne correspondent pas.';
        }

        $roleId = User::getUserRoleId();

        /* Vérifie que le rôle user existe en base. */
        if ($roleId === null) {
            $errors[] = 'Le rôle utilisateur est introuvable en base de données.';
        }

        if (empty($errors)) {
            $created = User::create([
                'role_id' => $roleId,
                'first_name' => $formData['first_name'],
                'last_name' => $formData['last_name'],
                'phone' => $formData['phone'],
                'email' => $formData['email'],
                'address' => $formData['address'],
                'postal_code' => $formData['postal_code'],
                'city' => $formData['city'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            if ($created) {
                MailService::sendWelcomeEmail(
                    $formData['email'],
                    $formData['first_name']
                );

                $success = 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.';

                /* Vide le formulaire après création du compte. */
                $formData = [
                    'first_name' => '',
                    'last_name' => '',
                    'phone' => '',
                    'email' => '',
                    'address' => '',
                    'postal_code' => '',
                    'city' => '',
                ];
            } else {
                $errors[] = 'Une erreur est survenue pendant la création du compte.';
            }
        }

        $this->showRegisterForm($errors, $success, $formData);
    }

    /* -------------------------------------------------- */
    /* affichage de la connexion */
    /* -------------------------------------------------- */

    /* Prépare la page de connexion. */
    public function login(): void
    {
        $pageTitle = 'Connexion';
        $errors = [];
        $success = null;
        $formData = [];

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/auth/login.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* -------------------------------------------------- */
    /* traitement de la connexion */
    /* -------------------------------------------------- */

    /* Vérifie les identifiants de connexion. */
    public function authenticate(): void
    {
        $errors = [];
        $success = null;

        $formData = [
            'email' => trim($_POST['email'] ?? ''),
        ];

        $password = $_POST['password'] ?? '';

        /* Vérifie les champs obligatoires. */
        if ($formData['email'] === '' || $password === '') {
            $errors[] = 'Veuillez renseigner votre adresse mail et votre mot de passe.';
        }

        $user = null;

        if (empty($errors)) {
            $user = User::findByEmail($formData['email']);

            if (!$user) {
                $errors[] = 'Adresse mail ou mot de passe incorrect.';
            }
        }

        /* Vérifie que le compte est actif. */
        if ($user && !$user['is_active']) {
            $errors[] = 'Votre compte est désactivé.';
        }

        /* Vérifie le mot de passe. */
        if (
            $user
            && !password_verify($password, $user['password_hash'])
        ) {
            $errors[] = 'Adresse mail ou mot de passe incorrect.';
        }

        if (!empty($errors)) {

            $pageTitle = 'Connexion';

            $openingHours = OpeningHour::getAll();

            $view = BASE_PATH . '/app/Views/auth/login.php';

            require BASE_PATH . '/app/Views/layouts/main.php';

            return;
        }

        /* Enregistre les informations utiles dans la session. */
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        /* Redirige vers l'accueil après connexion. */
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    /* -------------------------------------------------- */
    /* mot de passe oublié */
    /* -------------------------------------------------- */

    /* Affiche le formulaire de demande de réinitialisation. */
    public function forgotPassword(): void
    {
        $pageTitle = 'Mot de passe oublié';
        $errors = [];
        $success = null;
        $formData = [];

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/auth/forgot-password.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Traite la demande et prépare le mail de réinitialisation. */
    public function sendResetLink(): void
    {
        $errors = [];
        $success = null;

        $formData = [
            'email' => trim($_POST['email'] ?? ''),
        ];

        /* Vérifie l'adresse mail. */
        if ($formData['email'] === '') {
            $errors[] = 'Veuillez renseigner votre adresse mail.';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L’adresse mail n’est pas valide.';
        }

        if (empty($errors)) {
            $user = User::findByEmail($formData['email']);

            /*
         * Ne révèle pas si une adresse existe.
         * Le traitement est effectué uniquement pour un compte actif.
         */
            if ($user && $user['is_active']) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expiresAt = date('Y-m-d H:i:s', time() + 3600);

                $created = PasswordResetToken::create(
                    (int) $user['id'],
                    $tokenHash,
                    $expiresAt
                );

                if (!$created) {
                    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
                } else {
                    $resetLink = BASE_URL
                        . '/reset-password?token='
                        . rawurlencode($token);

                    MailService::sendPasswordResetEmail(
                        $user['email'],
                        $user['first_name'],
                        $resetLink
                    );
                }
            }

            if (empty($errors)) {
                $success = 'Si un compte actif correspond à cette adresse, un lien de réinitialisation a été envoyé.';

                $formData['email'] = '';
            }
        }

        $pageTitle = 'Mot de passe oublié';

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/auth/forgot-password.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Affiche le formulaire si le jeton est encore valide. */
    public function resetPassword(): void
    {
        $pageTitle = 'Nouveau mot de passe';
        $errors = [];
        $success = null;

        $token = trim($_GET['token'] ?? '');
        $resetToken = null;

        if ($token === '') {
            $errors[] = 'Le lien de réinitialisation est invalide.';
        } else {
            $tokenHash = hash('sha256', $token);
            $resetToken = PasswordResetToken::findValid($tokenHash);

            if ($resetToken === null) {
                $errors[] = 'Ce lien est invalide, expiré ou déjà utilisé.';
            }
        }

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/auth/reset-password.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* Enregistre le nouveau mot de passe. */
    public function updatePassword(): void
    {
        $pageTitle = 'Nouveau mot de passe';
        $errors = [];
        $success = null;
        $resetToken = null;

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        /* Vérifie le jeton. */
        if ($token === '') {
            $errors[] = 'Le lien de réinitialisation est invalide.';
        } else {
            $tokenHash = hash('sha256', $token);
            $resetToken = PasswordResetToken::findValid($tokenHash);

            if ($resetToken === null) {
                $errors[] = 'Ce lien est invalide, expiré ou déjà utilisé.';
            }
        }

        /* Vérifie la sécurité du nouveau mot de passe. */
        if (!$this->isPasswordSecure($password)) {
            $errors[] = 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
        }

        /* Vérifie la confirmation. */
        if ($password !== $passwordConfirmation) {
            $errors[] = 'Les deux mots de passe ne correspondent pas.';
        }

        if (empty($errors) && $resetToken !== null) {
            $updated = User::updatePassword(
                (int) $resetToken['user_id'],
                password_hash($password, PASSWORD_DEFAULT)
            );

            if (!$updated) {
                $errors[] = 'Une erreur est survenue pendant la mise à jour du mot de passe.';
            } else {
                $tokenUsed = PasswordResetToken::markAsUsed(
                    (int) $resetToken['id']
                );

                if (!$tokenUsed) {
                    $errors[] = 'Une erreur est survenue pendant la validation du lien.';
                } else {
                    $success = 'Votre mot de passe a bien été modifié. Vous pouvez maintenant vous connecter.';
                    $resetToken = null;
                    $token = '';
                }
            }
        }

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/auth/reset-password.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /* -------------------------------------------------- */
    /* déconnexion */
    /* -------------------------------------------------- */

    /* Détruit la session puis retourne à l'accueil. */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $cookieParameters = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookieParameters['path'],
                $cookieParameters['domain'],
                $cookieParameters['secure'],
                $cookieParameters['httponly']
            );
        }

        session_destroy();

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /* -------------------------------------------------- */
    /* sécurité mot de passe */
    /* -------------------------------------------------- */

    /* Vérifie les règles demandées dans l'énoncé. */
    private function isPasswordSecure(string $password): bool
    {
        return strlen($password) >= 10
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }

    /* -------------------------------------------------- */
    /* affichage commun */
    /* -------------------------------------------------- */

    /* Charge la vue inscription avec ses messages. */
    private function showRegisterForm(
        array $errors = [],
        ?string $success = null,
        array $formData = []
    ): void {
        $pageTitle = 'Créer un compte';

        $openingHours = OpeningHour::getAll();

        $view = BASE_PATH . '/app/Views/auth/register.php';

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}
