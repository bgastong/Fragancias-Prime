<?php

/*Verificamos que el usuario este logg*/
class AuthMiddleware
{
    public static function verificarSesion() // Verifica si el usuario está logeado
    {
        if (session_status() === PHP_SESSION_NONE) { // Verificp si la sesión no ha sido iniciada
            session_start();
        }

        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']); // Verificp si el usuario está en sesión
    }

    /*Debe estar autenticado si no redirige al login*/
    public static function requiereAutenticacion()
    {
        if (!self::verificarSesion()) {
            // Guardar la URL a la que intentaba acceder
            $_SESSION['url_anterior'] = $_SERVER['REQUEST_URI']; 
            
            header('Location: ?controller=auth&action=login');
            exit;
        }
    }

    /*No debe estar logueado si no redirige al home*/
    public static function soloInvitados()
    {
        if (self::verificarSesion()) { // si está logueado
            header('Location: ?controller=home&action=index');
            exit;
        }
    }

    /*obtengo el usuario*/
    public static function usuarioActual()
    {
        if (self::verificarSesion()) {
            return $_SESSION['usuario'];
        }
        return null;
    }

    /*obtengo id*/
    public static function usuarioId()
    {
        $usuario = self::usuarioActual();
        return $usuario['idusuario'] ?? null;
    }
}
