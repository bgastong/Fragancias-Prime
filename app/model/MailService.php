<?php

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;

class MailService
{
    private $transport;
    private $defaultFrom;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/mail.php';
        
        $options = new SmtpOptions([
            'name'              => $config['smtp']['name'],
            'host'              => $config['smtp']['host'],
            'port'              => $config['smtp']['port'],
            'connection_class'  => $config['smtp']['connection_class'],
            'connection_config' => $config['smtp']['connection_config'],
        ]);

        $this->transport   = new Smtp($options);
        $this->defaultFrom = $config['smtp']['from'];
    }

    /**
     * Enviar email con HTML
     */
    public function enviarBienvenida($toEmail, $nombreUsuario)
    {
        $subject = "¡Bienvenido a Fragancias Prime!";
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .button { display: inline-block; padding: 12px 30px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Fragancias Prime</h1>
                </div>
                <div class='content'>
                    <h2>¡Hola, {$nombreUsuario}!</h2>
                    <p>Te damos la bienvenida a <strong>Fragancias Prime</strong>, tu tienda de fragancias de confianza.</p>
                    <p>Tu cuenta ha sido creada exitosamente y ya puedes comenzar a explorar nuestro catálogo de productos exclusivos.</p>
                    <p>Disfruta de:</p>
                    <ul>
                        <li>Fragancias de las mejores marcas</li>
                        <li>Proceso de compra rapido y seguro</li>
                        <li>Seguimiento de tus pedidos</li>
                        <li>Ofertas exclusivas</li>
                    </ul>
                    <a href='http://localhost/Fragancias%20Prime/public/?controller=auth&action=login' class='button'>Iniciar Sesión</a>
                </div>
                <div class='footer'>
                    <p>© 2025 Fragancias Prime. Todos los derechos reservados.</p>
                    <p>Este es un email automatico, por favor no responder.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $textBody = "Hola {$nombreUsuario},\n\nTe damos la bienvenida a Fragancias Prime.\n\nTu cuenta ha sido creada exitosamente.\n\nSaludos,\nEquipo Fragancias Prime";

        return $this->enviar($toEmail, $subject, $htmlBody, $textBody);
    }

    /**
     * Enviar email genérico
     */
    public function enviar($toEmail, $subject, $htmlBody, $textBody = null)
    {
        try {
            $message = new Message();

            $message->addFrom(
                $this->defaultFrom['email'],
                $this->defaultFrom['name']
            );

            $message->addTo($toEmail)
                    ->setSubject($subject)
                    ->setEncoding('UTF-8');

            $message->getHeaders()->addHeaderLine('Content-Language', 'es-AR');

            $parts = [];

            if ($textBody !== null) {
                $textPart = new MimePart($textBody);
                $textPart->type     = 'text/plain; charset=UTF-8';
                $textPart->encoding = 'quoted-printable';
                $parts[] = $textPart;
            }

            $htmlPart = new MimePart($htmlBody);
            $htmlPart->type     = 'text/html; charset=UTF-8';
            $htmlPart->encoding = 'quoted-printable';
            $parts[] = $htmlPart;

            $body = new MimeMessage();
            $body->setParts($parts);    
            $message->setBody($body);

            $this->transport->send($message);
            return true;

        } catch (Exception $e) {
            error_log("Error al enviar email: " . $e->getMessage());
            return false;
        }
    }
}
