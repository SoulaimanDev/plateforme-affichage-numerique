<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Configuration SMTP si disponible, sinon mail() par défaut
        if (!empty($_ENV['SMTP_HOST'])) {
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['SMTP_HOST'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['SMTP_USER'];
            $this->mailer->Password = $_ENV['SMTP_PASS'];
            $this->mailer->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $_ENV['SMTP_PORT'] ?? 587;

            // utf-8
            $this->mailer->CharSet = 'UTF-8';
        }

        // Expéditeur par défaut
        $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? ('no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Plume Vision CMS';

        try {
            $this->mailer->setFrom($fromEmail, $fromName);
        } catch (Exception $e) {
            // Ignorer si format invalide, fallback
        }
    }

    /**
     * Envoie l'email de réinitialisation
     */
    public function sendResetLink($to, $link)
    {
        $subject = "Réinitialisation de votre mot de passe - Plume Vision";

        $message = "
        <html>
        <head>
            <title>Réinitialisation de mot de passe</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                .footer { font-size: 12px; color: #666; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Bonjour,</h2>
                <p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte Plume Vision.</p>
                <p>Pour définir un nouveau mot de passe, cliquez sur le bouton ci-dessous :</p>
                
                <p style='text-align: center;'>
                    <a href='{$link}' class='button'>Réinitialiser mon mot de passe</a>
                </p>
                
                <p>Ce lien est valable pendant 1 heure.</p>
                <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.</p>
                
                <div class='footer'>
                    <p>Cordialement,<br>L'équipe Plume Vision</p>
                    <p><small>Si le bouton ne fonctionne pas, copiez-collez ce lien : {$link}</small></p>
                </div>
            </div>
        </body>
        </html>
        ";

        try {
            // Reset recipients pour éviter les doublons si instance réutilisée
            $this->mailer->clearAddresses();

            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;
            $this->mailer->AltBody = strip_tags($message); // Version texte brute

            // LOGGING (Garder le log de backup)
            $this->logEmail($to, $subject, $link, 'SMTP (Attempting)');

            $this->mailer->send();

            $this->logEmail($to, $subject, $link, 'SMTP (Success)');
            return true;
        } catch (Exception $e) {
            $this->logEmail($to, $subject, $link, 'SMTP Error: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Log l'email dans un fichier pour debug
     */
    private function logEmail($to, $subject, $link, $status = 'Sent')
    {
        $logFile = __DIR__ . '/../../storage/logs/mail_debug.log';
        $date = date('Y-m-d H:i:s');
        $logContent = "[$date] [$status] TO: $to | SUBJECT: $subject | LINK: $link" . PHP_EOL;
        file_put_contents($logFile, $logContent, FILE_APPEND);
    }
}
