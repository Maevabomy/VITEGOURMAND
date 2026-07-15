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
    /* confirmation de commande */
    /* -------------------------------------------------- */

    /* Envoie le récapitulatif après l'enregistrement. */
    public static function sendOrderConfirmationEmail(
        string $email,
        array $confirmation
    ): bool {
        $subject =
            'Confirmation de votre commande '
            . $confirmation['order_number'];

        $message = self::buildOrderConfirmationMessage(
            $confirmation
        );

        return self::send(
            $email,
            $subject,
            $message
        );
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

    /* Prépare le mail de confirmation de commande. */
    private static function buildOrderConfirmationMessage(
        array $confirmation
    ): string {
        $safeFirstName = htmlspecialchars(
            $confirmation['customer_first_name'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderNumber = htmlspecialchars(
            $confirmation['order_number'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeMenuTitle = htmlspecialchars(
            $confirmation['menu_title'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeAddress = htmlspecialchars(
            $confirmation['delivery_address'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safePostalCode = htmlspecialchars(
            $confirmation['delivery_postal_code'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeCity = htmlspecialchars(
            $confirmation['delivery_city'],
            ENT_QUOTES,
            'UTF-8'
        );

        $formattedDate = date(
            'd/m/Y',
            strtotime($confirmation['event_date'])
        );

        $formattedMenuPrice = number_format(
            (float) $confirmation['menu_price'],
            2,
            ',',
            ' '
        );

        $formattedDeliveryPrice = number_format(
            (float) $confirmation['delivery_price'],
            2,
            ',',
            ' '
        );

        $formattedTotalPrice = number_format(
            (float) $confirmation['total_price'],
            2,
            ',',
            ' '
        );

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Confirmation de commande</title>
            </head>
            <body>
                <h1>Commande confirmée</h1>

                <p>
                    Bonjour ' . $safeFirstName . ',
                </p>

                <p>
                    Votre commande a bien été enregistrée par
                    Vite & Gourmand.
                </p>

                <h2>Récapitulatif</h2>

                <p>
                    <strong>Numéro de commande :</strong>
                    ' . $safeOrderNumber . '
                </p>

                <p>
                    <strong>Menu :</strong>
                    ' . $safeMenuTitle . '
                </p>

                <p>
                    <strong>Nombre de personnes :</strong>
                    ' . (int) $confirmation['people_count'] . '
                </p>

                <p>
                    <strong>Date de la prestation :</strong>
                    ' . $formattedDate . '
                </p>

                <p>
                    <strong>Heure de livraison :</strong>
                    ' . htmlspecialchars(
            $confirmation['delivery_time'],
            ENT_QUOTES,
            'UTF-8'
        ) . '
                </p>

                <p>
                    <strong>Adresse :</strong><br>
                    ' . $safeAddress . '<br>
                    ' . $safePostalCode . ' ' . $safeCity . '
                </p>

                <p>
                    <strong>Prix du menu :</strong>
                    ' . $formattedMenuPrice . ' €
                </p>

                <p>
                    <strong>Frais de livraison :</strong>
                    ' . $formattedDeliveryPrice . ' €
                </p>

                <p>
                    <strong>Total :</strong>
                    ' . $formattedTotalPrice . ' €
                </p>

                <p>
                    <strong>Statut :</strong>
                    En attente
                </p>

                <p>
                    Notre équipe examinera prochainement votre commande.
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
