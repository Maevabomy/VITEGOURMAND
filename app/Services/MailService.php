<?php

namespace App\Services;

class MailService
{
    /* -------------------------------------------------- */
    /* mail de bienvenue */
    /* -------------------------------------------------- */

    /* Envoie le message prévu après la création du compte. */
    public static function sendWelcomeEmail(
        string $email,
        string $firstName
    ): bool {
        $subject = 'Bienvenue chez Vite & Gourmand';

        $message = self::buildWelcomeMessage($firstName);

        return self::send($email, $subject, $message);
    }

    /* -------------------------------------------------- */
    /* réinitialisation du mot de passe */
    /* -------------------------------------------------- */

    /* Envoie le lien permettant de choisir un nouveau mot de passe. */
    public static function sendPasswordResetEmail(
        string $email,
        string $firstName,
        string $resetLink
    ): bool {
        $subject = 'Réinitialisation de votre mot de passe';

        $message = self::buildPasswordResetMessage(
            $firstName,
            $resetLink
        );

        return self::send($email, $subject, $message);
    }

    /* -------------------------------------------------- */
    /* envoi du mail */
    /* -------------------------------------------------- */

    /* Tente l'envoi et conserve une copie locale en cas d'échec. */
    private static function send(
        string $recipient,
        string $subject,
        string $message
    ): bool {
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Vite & Gourmand <contact@vite-et-gourmand.fr>',
        ];

        $sent = @mail(
            $recipient,
            $subject,
            $message,
            implode("\r\n", $headers)
        );

        /* Conserve une copie du mail pour les tests locaux. */
        self::saveMailToLog(
            $recipient,
            $subject,
            $message,
            $sent
        );

        return $sent;
    }

    /* -------------------------------------------------- */
    /* contenu du mail */
    /* -------------------------------------------------- */

    /* Prépare le contenu du message de bienvenue. */
    private static function buildWelcomeMessage(string $firstName): string
    {
        $safeFirstName = htmlspecialchars(
            $firstName,
            ENT_QUOTES,
            'UTF-8'
        );

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Bienvenue chez Vite & Gourmand</title>
            </head>
            <body>
                <h1>Bienvenue ' . $safeFirstName . '</h1>

                <p>
                    Votre compte Vite & Gourmand a bien été créé.
                </p>

                <p>
                    Vous pouvez maintenant vous connecter afin de préparer
                    et suivre vos prochaines commandes.
                </p>

                <p>
                    À bientôt,<br>
                    Julie et José
                </p>
            </body>
            </html>
        ';
    }

    /* Prépare le mail contenant le lien de réinitialisation. */
    private static function buildPasswordResetMessage(
        string $firstName,
        string $resetLink
    ): string {
        $safeFirstName = htmlspecialchars(
            $firstName,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeResetLink = htmlspecialchars(
            $resetLink,
            ENT_QUOTES,
            'UTF-8'
        );

        return '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Réinitialisation du mot de passe</title>
        </head>
        <body>
            <h1>Bonjour ' . $safeFirstName . '</h1>

            <p>
                Une demande de réinitialisation de votre mot de passe
                a été effectuée.
            </p>

            <p>
                <a href="' . $safeResetLink . '">
                    Choisir un nouveau mot de passe
                </a>
            </p>

            <p>
                Ce lien est valable pendant une heure.
            </p>

            <p>
                Si vous n’êtes pas à l’origine de cette demande,
                vous pouvez ignorer ce message.
            </p>

            <p>
                À bientôt,<br>
                Julie et José
            </p>
        </body>
        </html>
    ';
    }

    /* -------------------------------------------------- */
    /* copie locale */
    /* -------------------------------------------------- */

    /* Enregistre le mail lorsque XAMPP ne peut pas l'envoyer. */
    private static function saveMailToLog(
        string $recipient,
        string $subject,
        string $message,
        bool $sent
    ): void {
        $logDirectory = BASE_PATH . '/storage/logs';

        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0775, true);
        }

        $content = PHP_EOL;
        $content .= 'Statut : ' . ($sent ? 'accepté par PHP' : 'échec') . PHP_EOL;
        $content .= 'Date : ' . date('Y-m-d H:i:s') . PHP_EOL;
        $content .= 'Destinataire : ' . $recipient . PHP_EOL;
        $content .= 'Sujet : ' . $subject . PHP_EOL;
        $content .= 'Message :' . PHP_EOL;
        $readableMessage = preg_replace_callback(
            '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is',
            static function (array $matches): string {
                $linkText = trim(strip_tags($matches[2]));
                $linkUrl = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');

                return $linkText . ' : ' . $linkUrl;
            },
            $message
        );

        $content .= html_entity_decode(
            strip_tags($readableMessage ?? $message),
            ENT_QUOTES,
            'UTF-8'
        ) . PHP_EOL;
        $content .= str_repeat('-', 60) . PHP_EOL;

        file_put_contents(
            $logDirectory . '/emails.log',
            $content,
            FILE_APPEND
        );
    }
}
