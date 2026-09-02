<?php

namespace App\Controllers;

use App\Models\ContactRequest;
use App\Models\OpeningHour;
use App\Services\MailService;

class ContactController
{
    /*
    |--------------------------------------------------------------------------
    | affichage
    |--------------------------------------------------------------------------
    */

    /* Affiche le formulaire de contact. */
    public function index(): void
    {
        if (empty($_SESSION['contact_csrf_token'])) {
            $_SESSION['contact_csrf_token'] =
                bin2hex(random_bytes(32));
        }

        $csrfToken =
            $_SESSION['contact_csrf_token'];

        $errors =
            $_SESSION['contact_errors']
            ?? [];

        $success =
            $_SESSION['contact_success']
            ?? null;

        $formData =
            $_SESSION['contact_form_data']
            ?? [];

        unset(
            $_SESSION['contact_errors'],
            $_SESSION['contact_success'],
            $_SESSION['contact_form_data']
        );

        $openingHours =
            OpeningHour::getAll();

        $pageTitle =
            'Contact';

        $view =
            BASE_PATH
            . '/app/Views/contact/index.php';

        require BASE_PATH
            . '/app/Views/layouts/main.php';
    }

    /*
    |--------------------------------------------------------------------------
    | envoi
    |--------------------------------------------------------------------------
    */

    /* Vérifie et enregistre une demande de contact. */
    public function store(): void
    {
        $errors = [];

        $csrfToken =
            (string) ($_POST['csrf_token'] ?? '');

        if (
            empty($_SESSION['contact_csrf_token'])
            || !hash_equals(
                $_SESSION['contact_csrf_token'],
                $csrfToken
            )
        ) {
            $errors[] =
                'Le formulaire a expiré. '
                . 'Veuillez recommencer.';
        }

        $email =
            trim(
                (string) ($_POST['email'] ?? '')
            );

        /*
         * Supprime les retours à la ligne afin
         * d'éviter leur utilisation dans le sujet du mail.
         */
        $subject =
            preg_replace(
                '/[\r\n]+/',
                ' ',
                trim(
                    (string) ($_POST['subject'] ?? '')
                )
            ) ?? '';

        $message =
            trim(
                (string) ($_POST['message'] ?? '')
            );

        $formData = [
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        ];

        /* Vérifie l'adresse mail. */
        if ($email === '') {
            $errors[] =
                'L’adresse mail est obligatoire.';
        } elseif (
            strlen($email) > 190
            || !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] =
                'L’adresse mail n’est pas valide.';
        }

        /* Vérifie le titre. */
        if ($subject === '') {
            $errors[] =
                'Le titre est obligatoire.';
        } elseif (
            strlen($subject) < 3
            || strlen($subject) > 150
        ) {
            $errors[] =
                'Le titre doit contenir entre '
                . '3 et 150 caractères.';
        }

        /* Vérifie la description. */
        if ($message === '') {
            $errors[] =
                'La description est obligatoire.';
        } elseif (
            strlen($message) < 10
            || strlen($message) > 5000
        ) {
            $errors[] =
                'La description doit contenir entre '
                . '10 et 5 000 caractères.';
        }

        if (!empty($errors)) {
            unset(
                $_SESSION['contact_success']
            );

            $_SESSION['contact_errors'] =
                $errors;

            $_SESSION['contact_form_data'] =
                $formData;

            $this->redirectToContact();
        }

        $created =
            ContactRequest::create(
                $email,
                $subject,
                $message
            );

        if (!$created) {
            $_SESSION['contact_errors'] = [
                'Votre message n’a pas pu être '
                    . 'enregistré. Veuillez réessayer.',
            ];

            $_SESSION['contact_form_data'] =
                $formData;

            $this->redirectToContact();
        }

        /*
         * Le message reste enregistré dans MariaDB
         * même si le serveur local ne peut pas
         * réellement transmettre l'e-mail.
         *
         * MailService en conserve également une copie
         * dans storage/logs/emails.log pour les tests.
         */
        MailService::sendContactRequestEmail(
            $email,
            $subject,
            $message
        );

        unset(
            $_SESSION['contact_csrf_token'],
            $_SESSION['contact_errors'],
            $_SESSION['contact_form_data']
        );

        $_SESSION['contact_success'] =
            'Votre message a bien été envoyé. '
            . 'Nous vous répondrons dès que possible.';

        $this->redirectToContact();
    }

    /*
    |--------------------------------------------------------------------------
    | redirection
    |--------------------------------------------------------------------------
    */

    private function redirectToContact(): never
    {
        header(
            'Location: '
                . BASE_URL
                . '/contact'
        );

        exit;
    }
}
