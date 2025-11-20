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


    // mail de bienvenida
    public function enviarBienvenida($toEmail, $nombreUsuario)
    {
        $subject = "Bienvenido a Fragancias Prime";
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
                .header { background: #000; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>Fragancias Prime</h2>
                </div>
                <div class='content'>
                    <h3>Hola, {$nombreUsuario}!</h3>
                    <p>Tu cuenta ha sido creada exitosamente.</p>
                    <p>Ya puedes iniciar sesion y explorar nuestro catalogo de fragancias.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Fragancias Prime</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $textBody = "Hola {$nombreUsuario},\n\nTu cuenta en Fragancias Prime ha sido creada exitosamente.\n\nSaludos,\nEquipo Fragancias Prime";

        return $this->enviar($toEmail, $subject, $htmlBody, $textBody);
    }

    
    // Email de compra iniciada
    public function enviarCompraIniciada($toEmail, $nombreUsuario, $idCompra, $total)
    {
        $subject = "Pedido #$idCompra recibido";
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
                .header { background: #000; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>Fragancias Prime</h2>
                </div>
                <div class='content'>
                    <h3>¡Hola, {$nombreUsuario}!</h3>
                    <p>Recibimos tu pedido <strong>#$idCompra</strong></p>
                    <p>Total: <strong>\$$total</strong></p>
                    <p>Te notificaremos cuando cambie el estado.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Fragancias Prime</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $textBody = "Hola {$nombreUsuario},\n\nRecibimos tu pedido #{$idCompra}.\nTotal: \${$total}\n\nSaludos,\nEquipo Fragancias Prime";

        return $this->enviar($toEmail, $subject, $htmlBody, $textBody);
    }

    // Email de compra aceptada
    public function enviarCompraAceptada($toEmail, $nombreUsuario, $idCompra)
    {
        $subject = "Pedido #$idCompra aceptado";
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>Fragancias Prime</h2>
                </div>
                <div class='content'>
                    <h3>Hola, {$nombreUsuario}!</h3>
                    <p>Tu pedido <strong>#$idCompra</strong> ha sido aceptado.</p>
                    <p>Pronto estara en camino.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Fragancias Prime</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $textBody = "Hola {$nombreUsuario},\n\nTu pedido #{$idCompra} ha sido aceptado.\n\nSaludos,\nEquipo Fragancias Prime";

        return $this->enviar($toEmail, $subject, $htmlBody, $textBody);
    }

    // Email de compra enviada
    public function enviarCompraEnviada($toEmail, $nombreUsuario, $idCompra)
    {
        $subject = "Pedido #$idCompra enviado";
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>Fragancias Prime</h2>
                </div>
                <div class='content'>
                    <h3>Hola, {$nombreUsuario}!</h3>
                    <p>Tu pedido <strong>#$idCompra</strong> esta en camino.</p>
                    <p>Gracias por tu compra!</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Fragancias Prime</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $textBody = "Hola {$nombreUsuario},\n\nTu pedido #{$idCompra} esta en camino.\n\nSaludos,\nEquipo Fragancias Prime";

        return $this->enviar($toEmail, $subject, $htmlBody, $textBody);
    }

    // Email de compra cancelada
    public function enviarCompraCancelada($toEmail, $nombreUsuario, $idCompra)
    {
        $subject = "Pedido #$idCompra cancelado";
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>Fragancias Prime</h2>
                </div>
                <div class='content'>
                    <h3>Hola, {$nombreUsuario}!</h3>
                    <p>Tu pedido <strong>#$idCompra</strong> ha sido cancelado.</p>
                    <p>Si tenes consultas, contactanos.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Fragancias Prime</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $textBody = "Hola {$nombreUsuario},\n\nTu pedido #{$idCompra} ha sido cancelado.\n\nSaludos,\nEquipo Fragancias Prime";

        return $this->enviar($toEmail, $subject, $htmlBody, $textBody);
    }

    
    // Enviar email
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
