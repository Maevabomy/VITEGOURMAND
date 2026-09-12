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
    /* création d'un compte employé */
    /* -------------------------------------------------- */

    /* Informe l'employé de la création de son compte. */
    public static function sendEmployeeAccountCreatedEmail(
        string $email
    ): bool {
        $subject =
            'Création de votre compte Vite & Gourmand';

        $message =
            self::buildEmployeeAccountCreatedMessage();

        return self::send(
            $email,
            $subject,
            $message
        );
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
    /* suivi d'une commande */
    /* -------------------------------------------------- */

    /* Informe le client que sa commande est acceptée. */
    public static function sendOrderAcceptedEmail(
        string $email,
        array $order
    ): bool {
        $subject =
            'Votre commande '
            . $order['order_number']
            . ' est acceptée';

        $message = self::buildOrderAcceptedMessage($order);

        return self::send(
            $email,
            $subject,
            $message
        );
    }

    /* Informe le client du délai de retour du matériel. */
    public static function sendEquipmentReturnEmail(
        string $email,
        array $order
    ): bool {
        $subject =
            'Retour du matériel - commande '
            . $order['order_number'];

        $message = self::buildEquipmentReturnMessage($order);

        return self::send(
            $email,
            $subject,
            $message
        );
    }

    /* Invite le client à laisser un avis après la prestation. */
    public static function sendOrderCompletedEmail(
        string $email,
        array $order,
        string $orderDetailLink
    ): bool {
        $subject =
            'Votre commande '
            . $order['order_number']
            . ' est terminée';

        $message = self::buildOrderCompletedMessage(
            $order,
            $orderDetailLink
        );

        return self::send(
            $email,
            $subject,
            $message
        );
    }

    /* Confirme l'annulation d'une commande. */
    public static function sendOrderCancelledEmail(
        string $email,
        array $order
    ): bool {
        $subject =
            'Annulation de votre commande '
            . $order['order_number'];

        $message = self::buildOrderCancelledMessage($order);

        return self::send(
            $email,
            $subject,
            $message
        );
    }

    /* -------------------------------------------------- */
    /* formulaire de contact */
    /* -------------------------------------------------- */

    /* Transmet une demande de contact à l'entreprise. */
    public static function sendContactRequestEmail(
        string $email,
        string $contactSubject,
        string $contactMessage
    ): bool {
        $subject =
            'Nouveau message de contact - '
            . $contactSubject;

        $message =
            self::buildContactRequestMessage(
                $email,
                $contactSubject,
                $contactMessage
            );

        $recipientEmail = trim(
            (string) getenv('CONTACT_RECIPIENT_EMAIL')
        );

        if ($recipientEmail === '') {
            return false;
        }

        return self::send(
            $recipientEmail,
            $subject,
            $message
        );
    }

    /* -------------------------------------------------- */
    /* envoi du mail */
    /* -------------------------------------------------- */

    /* Envoie le mail avec Brevo en production ou avec PHP en local. */
    private static function send(
        string $recipient,
        string $subject,
        string $message
    ): bool {
        $apiKey = trim(
            (string) getenv('BREVO_API_KEY')
        );

        $senderEmail = trim(
            (string) getenv('BREVO_SENDER_EMAIL')
        );

        /* Utilise l'API Brevo lorsque les variables d'environnement sont disponibles. */
        if ($apiKey !== '' && $senderEmail !== '') {
            $curl = curl_init(
                'https://api.brevo.com/v3/smtp/email'
            );

            if ($curl === false) {
                self::saveMailToLog(
                    $recipient,
                    $subject,
                    $message,
                    false
                );

                return false;
            }

            $data = [
                'sender' => [
                    'name' => 'Vite & Gourmand',
                    'email' => $senderEmail,
                ],
                'to' => [
                    [
                        'email' => $recipient,
                    ],
                ],
                'subject' => $subject,
                'htmlContent' => $message,
            ];

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'api-key: ' . $apiKey,
                    'content-type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode(
                    $data,
                    JSON_UNESCAPED_UNICODE
                ),
            ]);

            $response = curl_exec($curl);

            $statusCode = curl_getinfo(
                $curl,
                CURLINFO_HTTP_CODE
            );

            curl_close($curl);

            $sent =
                $response !== false
                && in_array(
                    $statusCode,
                    [201, 202],
                    true
                );

            return $sent;
        }

        /* Garde l'ancien fonctionnement pour les tests locaux lorsque Brevo n'est pas configuré.*/
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

    /* Prépare le mail reçu après une demande de contact. */
    private static function buildContactRequestMessage(
        string $email,
        string $contactSubject,
        string $contactMessage
    ): string {
        $safeEmail = htmlspecialchars(
            $email,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeSubject = htmlspecialchars(
            $contactSubject,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeMessage = nl2br(
            htmlspecialchars(
                $contactMessage,
                ENT_QUOTES,
                'UTF-8'
            )
        );

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Nouveau message de contact</title>
            </head>
            <body>
                <h1>Nouveau message depuis le site</h1>

                <p>
                    <strong>Adresse mail :</strong>
                    ' . $safeEmail . '
                </p>

                <p>
                    <strong>Sujet :</strong>
                    ' . $safeSubject . '
                </p>

                <p>
                    <strong>Message :</strong>
                </p>

                <p>
                    ' . $safeMessage . '
                </p>
            </body>
            </html>
        ';
    }

    /* Prépare le mail envoyé au nouvel employé. */
    private static function buildEmployeeAccountCreatedMessage(): string
    {
        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Création de votre compte</title>
            </head>
            <body>
                <h1>Votre compte Vite & Gourmand a été créé</h1>

                <p>
                    Un compte professionnel a été créé pour vous.
                </p>

                <p>
                    Votre identifiant de connexion correspond
                    à l’adresse mail ayant reçu ce message.
                </p>

                <p>
                    Pour des raisons de sécurité, votre mot de passe
                    n’est pas communiqué par e-mail.
                </p>

                <p>
                    Veuillez vous rapprocher de l’administrateur
                    afin de l’obtenir.
                </p>

                <p>
                    Vite & Gourmand
                </p>
            </body>
            </html>
        ';
    }

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
            self::buildAbsoluteUrl($resetLink),
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

    /* Prépare le mail confirmant l'acceptation. */
    private static function buildOrderAcceptedMessage(
        array $order
    ): string {
        $safeFirstName = htmlspecialchars(
            $order['customer_first_name'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderNumber = htmlspecialchars(
            $order['order_number'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeMenuTitle = htmlspecialchars(
            $order['menu_title'],
            ENT_QUOTES,
            'UTF-8'
        );

        $formattedDate = date(
            'd/m/Y',
            strtotime($order['event_date'])
        );

        $formattedTime = date(
            'H\hi',
            strtotime($order['delivery_time'])
        );

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Commande acceptée</title>
            </head>
            <body>
                <h1>Bonjour ' . $safeFirstName . '</h1>

                <p>
                    Votre commande
                    <strong>' . $safeOrderNumber . '</strong>
                    a été acceptée par notre équipe.
                </p>

                <p>
                    <strong>Menu :</strong>
                    ' . $safeMenuTitle . '
                </p>

                <p>
                    <strong>Date de la prestation :</strong>
                    ' . $formattedDate . ' à ' . $formattedTime . '
                </p>

                <p>
                    Nous vous tiendrons informé de son avancement.
                </p>

                <p>
                    À bientôt,<br>
                    Julie et José
                </p>
            </body>
            </html>
        ';
    }

    /* Prépare le mail de retour du matériel. */
    private static function buildEquipmentReturnMessage(
        array $order
    ): string {
        $safeFirstName = htmlspecialchars(
            $order['customer_first_name'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderNumber = htmlspecialchars(
            $order['order_number'],
            ENT_QUOTES,
            'UTF-8'
        );

        $formattedDeadline = 'Non définie';

        if (!empty($order['equipment_return_deadline'])) {
            $formattedDeadline = date(
                'd/m/Y',
                strtotime(
                    $order['equipment_return_deadline']
                )
            );
        }

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Retour du matériel</title>
            </head>
            <body>
                <h1>Bonjour ' . $safeFirstName . '</h1>

                <p>
                    La prestation liée à votre commande
                    <strong>' . $safeOrderNumber . '</strong>
                    a été livrée.
                </p>

                <p>
                    Du matériel vous a été confié pour la prestation.
                    Il doit être restitué au plus tard le
                    <strong>' . $formattedDeadline . '</strong>.
                </p>

                <p>
                    En l’absence de restitution dans le délai prévu,
                    une pénalité pouvant atteindre 600 € pourra être
                    appliquée.
                </p>

                <p>
                    Pour organiser le retour, vous pouvez contacter
                    notre équipe.
                </p>

                <p>
                    À bientôt,<br>
                    Julie et José
                </p>
            </body>
            </html>
        ';
    }

    /* Prépare le mail envoyé à la fin de la commande. */
    private static function buildOrderCompletedMessage(
        array $order,
        string $orderDetailLink
    ): string {
        $safeFirstName = htmlspecialchars(
            $order['customer_first_name'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderNumber = htmlspecialchars(
            $order['order_number'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderDetailLink = htmlspecialchars(
            self::buildAbsoluteUrl($orderDetailLink),
            ENT_QUOTES,
            'UTF-8'
        );

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Commande terminée</title>
            </head>
            <body>
                <h1>Bonjour ' . $safeFirstName . '</h1>

                <p>
                    Votre commande
                    <strong>' . $safeOrderNumber . '</strong>
                    est maintenant terminée.
                </p>

                <p>
                    Nous espérons que la prestation vous a donné
                    entière satisfaction.
                </p>

                <p>
                    Vous pouvez partager votre expérience depuis
                    le détail de votre commande :
                </p>

                <p>
                    <a href="' . $safeOrderDetailLink . '">
                        Donner mon avis
                    </a>
                </p>

                <p>
                    Merci pour votre confiance,<br>
                    Julie et José
                </p>
            </body>
            </html>
        ';
    }

    /* Prépare le mail confirmant l'annulation. */
    private static function buildOrderCancelledMessage(
        array $order
    ): string {
        $safeFirstName = htmlspecialchars(
            $order['customer_first_name'],
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderNumber = htmlspecialchars(
            $order['order_number'],
            ENT_QUOTES,
            'UTF-8'
        );

        return '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Commande annulée</title>
            </head>
            <body>
                <h1>Bonjour ' . $safeFirstName . '</h1>

                <p>
                    Nous vous confirmons l’annulation de la commande
                    <strong>' . $safeOrderNumber . '</strong>.
                </p>

                <p>
                    Cette annulation a été enregistrée après votre
                    échange avec notre équipe.
                </p>

                <p>
                    Pour toute question, vous pouvez nous contacter.
                </p>

                <p>
                    Cordialement,<br>
                    Julie et José
                </p>
            </body>
            </html>
        ';
    }

    /* Transforme un chemin interne en adresse complète pour les mails. */
    private static function buildAbsoluteUrl(string $url): string
    {
        if (
            str_starts_with($url, 'http://')
            || str_starts_with($url, 'https://')
        ) {
            return $url;
        }

        $appUrl = rtrim(
            (string) getenv('APP_URL'),
            '/'
        );

        if ($appUrl === '') {
            return $url;
        }

        return $appUrl . '/' . ltrim($url, '/');
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
