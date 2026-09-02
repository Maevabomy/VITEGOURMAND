<?php

namespace App\Controllers;

use App\Models\OpeningHour;
use App\Models\OrderStatistics;
use App\Models\User;
use App\Services\MailService;
use App\Services\MongoOrderStatistics;

class AdminController
{
    /* -------------------------------------------------- */
    /* accueil administrateur */
    /* -------------------------------------------------- */

    /* Affiche l'accueil de l'espace administrateur. */
    public function home(): void
    {
        $admin =
            $this->requireAdmin('/admin');

        $openingHours =
            OpeningHour::getAll();

        $pageTitle =
            'Administration';

        $view =
            BASE_PATH
            . '/app/Views/admin/home.php';

        require BASE_PATH
            . '/app/Views/layouts/main.php';
    }

    /* -------------------------------------------------- */
    /* gestion des employés */
    /* -------------------------------------------------- */

    /* Affiche les comptes employés. */
    public function employees(): void
    {
        $this->requireAdmin(
            '/admin/employees'
        );

        if (empty($_SESSION['admin_employee_csrf_token'])) {
            $_SESSION['admin_employee_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['admin_employee_csrf_token'];

        $employees =
            User::getAllEmployees();

        $success =
            $_SESSION['admin_employee_success']
            ?? null;

        $error =
            $_SESSION['admin_employee_error']
            ?? null;

        $formData =
            $_SESSION['admin_employee_form_data']
            ?? [];

        unset(
            $_SESSION['admin_employee_success'],
            $_SESSION['admin_employee_error'],
            $_SESSION['admin_employee_form_data']
        );

        $openingHours =
            OpeningHour::getAll();

        $pageTitle =
            'Gestion des employés';

        $view =
            BASE_PATH
            . '/app/Views/admin/employees.php';

        require BASE_PATH
            . '/app/Views/layouts/main.php';
    }

    /* Crée un nouveau compte employé. */
    public function createEmployee(): void
    {
        $this->requireAdmin(
            '/admin/employees'
        );

        $this->validateCsrfToken();

        $email = trim(
            (string) ($_POST['email'] ?? '')
        );

        $password =
            (string) ($_POST['password'] ?? '');

        $passwordConfirmation =
            (string) (
                $_POST['password_confirmation']
                ?? ''
            );

        $formData = [
            'email' => $email,
        ];

        if ($email === '') {
            $this->redirectEmployeeError(
                $formData,
                'L’adresse mail est obligatoire.'
            );
        }

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->redirectEmployeeError(
                $formData,
                'L’adresse mail n’est pas valide.'
            );
        }

        if (User::emailExists($email)) {
            $this->redirectEmployeeError(
                $formData,
                'Cette adresse mail est déjà utilisée.'
            );
        }

        if (!$this->isPasswordSecure($password)) {
            $this->redirectEmployeeError(
                $formData,
                'Le mot de passe doit contenir au moins '
                    . '10 caractères, une majuscule, une minuscule, '
                    . 'un chiffre et un caractère spécial.'
            );
        }

        if ($password !== $passwordConfirmation) {
            $this->redirectEmployeeError(
                $formData,
                'Les deux mots de passe ne correspondent pas.'
            );
        }

        $created =
            User::createEmployee(
                $email,
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                )
            );

        if (!$created) {
            $this->redirectEmployeeError(
                $formData,
                'Le compte employé n’a pas pu être créé.'
            );
        }

        /* Le mot de passe n'est volontairement jamais transmis au service mail. */
        MailService::sendEmployeeAccountCreatedEmail(
            $email
        );

        $_SESSION['admin_employee_success'] =
            'Le compte employé a bien été créé.';

        header(
            'Location: '
                . BASE_URL
                . '/admin/employees'
        );
        exit;
    }

    /* Active ou désactive un compte employé. */
    public function toggleEmployee(): void
    {
        $this->requireAdmin(
            '/admin/employees'
        );

        $this->validateCsrfToken();

        $employeeId = filter_input(
            INPUT_POST,
            'employee_id',
            FILTER_VALIDATE_INT
        );

        $requestedStatus =
            (string) ($_POST['is_active'] ?? '');

        if (
            !$employeeId
            || !in_array(
                $requestedStatus,
                ['0', '1'],
                true
            )
        ) {
            $_SESSION['admin_employee_error'] =
                'La demande est invalide.';

            $this->redirectToEmployees();
        }

        $employee =
            User::findEmployeeById(
                $employeeId
            );

        if ($employee === null) {
            $_SESSION['admin_employee_error'] =
                'Le compte employé est introuvable.';

            $this->redirectToEmployees();
        }

        $isActive =
            $requestedStatus === '1';

        $updated =
            User::setEmployeeActive(
                $employeeId,
                $isActive
            );

        if (!$updated) {
            $_SESSION['admin_employee_error'] =
                'Le statut du compte n’a pas pu être modifié.';
        } else {
            $_SESSION['admin_employee_success'] =
                $isActive
                ? 'Le compte employé a été réactivé.'
                : 'Le compte employé a été désactivé.';
        }

        $this->redirectToEmployees();
    }

    /* -------------------------------------------------- */
    /* statistiques */
    /* -------------------------------------------------- */

    /* Affiche les statistiques de commandes par menu. */
    public function statistics(): void
    {
        $this->requireAdmin(
            '/admin/statistics'
        );

        $statisticsError = null;
        $menuOrderStatistics = [];

        /* MariaDB prépare les données, puis MongoDB les stocke. */
        $sourceStatistics =
            OrderStatistics::getMenuOrderCounts();

        $synchronized =
            MongoOrderStatistics::synchronize(
                $sourceStatistics
            );

        if (!$synchronized) {
            $statisticsError =
                'Les statistiques MongoDB '
                . 'ne sont pas disponibles.';
        } else {
            /* Les données affichées sont relues directement depuis MongoDB. */
            $mongoStatistics =
                MongoOrderStatistics::getAll();

            if ($mongoStatistics === null) {
                $statisticsError =
                    'Les statistiques MongoDB '
                    . 'ne peuvent pas être chargées.';
            } else {
                $menuOrderStatistics =
                    $mongoStatistics;
            }
        }

        /* Chiffre d'affaires. */
        $revenueError = null;

        $revenueMenus =
            OrderStatistics::getMenusForFilter();

        $selectedMenuId = null;

        $menuIdValue =
            trim(
                (string) ($_GET['menu_id'] ?? '')
            );

        if ($menuIdValue !== '') {
            $validatedMenuId =
                filter_var(
                    $menuIdValue,
                    FILTER_VALIDATE_INT,
                    [
                        'options' => [
                            'min_range' => 1,
                        ],
                    ]
                );

            if ($validatedMenuId === false) {
                $revenueError =
                    'Le menu sélectionné est invalide.';
            } else {
                $selectedMenuId =
                    (int) $validatedMenuId;
            }
        }

        $startDate =
            trim(
                (string) ($_GET['start_date'] ?? '')
            );

        $endDate =
            trim(
                (string) ($_GET['end_date'] ?? '')
            );

        if (
            $startDate !== ''
            && !$this->isValidStatisticsDate(
                $startDate
            )
        ) {
            $revenueError =
                'La date de début est invalide.';
        }

        if (
            $endDate !== ''
            && !$this->isValidStatisticsDate(
                $endDate
            )
        ) {
            $revenueError =
                'La date de fin est invalide.';
        }

        if (
            $revenueError === null
            && $startDate !== ''
            && $endDate !== ''
            && $startDate > $endDate
        ) {
            $revenueError =
                'La date de début doit être '
                . 'antérieure à la date de fin.';
        }

        $revenueStatistics = [];

        if ($revenueError === null) {
            $revenueStatistics =
                OrderStatistics::getRevenueByMenu(
                    $selectedMenuId,
                    $startDate !== ''
                        ? $startDate
                        : null,
                    $endDate !== ''
                        ? $endDate
                        : null
                );
        }

        $openingHours =
            OpeningHour::getAll();

        $pageTitle =
            'Statistiques';

        $view =
            BASE_PATH
            . '/app/Views/admin/statistics.php';

        require BASE_PATH
            . '/app/Views/layouts/main.php';
    }

    /* Vérifie une date utilisée dans les statistiques. */
    private function isValidStatisticsDate(
        string $date
    ): bool {
        $dateObject =
            \DateTime::createFromFormat(
                'Y-m-d',
                $date
            );

        return $dateObject !== false
            && $dateObject->format('Y-m-d')
            === $date;
    }

    /* -------------------------------------------------- */
    /* sécurité */
    /* -------------------------------------------------- */

    /* Autorise uniquement l'administrateur. */
    private function requireAdmin(
        string $redirectPath
    ): array {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] =
                BASE_URL . $redirectPath;

            header(
                'Location: '
                    . BASE_URL
                    . '/login'
            );
            exit;
        }

        $user =
            User::findById(
                (int) $_SESSION['user']['id']
            );

        if ($user === null) {
            unset($_SESSION['user']);

            header(
                'Location: '
                    . BASE_URL
                    . '/login'
            );
            exit;
        }

        if ($user['role'] !== 'admin') {
            http_response_code(403);

            echo 'Accès refusé.';
            exit;
        }

        return $user;
    }

    /* Vérifie le jeton des formulaires administrateur. */
    private function validateCsrfToken(): void
    {
        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['admin_employee_csrf_token'])
            || !hash_equals(
                $_SESSION['admin_employee_csrf_token'],
                $csrfToken
            )
        ) {
            $_SESSION['admin_employee_error'] =
                'Le formulaire a expiré. '
                . 'Veuillez recommencer.';

            $this->redirectToEmployees();
        }
    }

    /* Vérifie les règles de sécurité du mot de passe. */
    private function isPasswordSecure(
        string $password
    ): bool {
        return strlen($password) >= 10
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }

    /* Conserve l'adresse mail après une erreur. */
    private function redirectEmployeeError(
        array $formData,
        string $message
    ): never {
        $_SESSION['admin_employee_form_data'] =
            $formData;

        $_SESSION['admin_employee_error'] =
            $message;

        $this->redirectToEmployees();
    }

    /* Retourne vers la gestion des employés. */
    private function redirectToEmployees(): never
    {
        header(
            'Location: '
                . BASE_URL
                . '/admin/employees'
        );
        exit;
    }
}
