<?php

require_once __DIR__ . '/AuthMiddleware.php';

/*verificamos que el usuario tenga los permisos necesarios segun su rol*/
class RoleMiddleware
{
    public static function tieneRol($rolRequerido)
    {
        $usuario = AuthMiddleware::usuarioActual();
        
        if (!$usuario) {
            return false;
        }

        // obtengo roles del usuario desde la base de datos
        require_once __DIR__ . '/../model/Usuario.php';
        $usuarioModel = new Usuario();
        $roles = $usuarioModel->obtenerRolesUsuario($usuario['idusuario']);

        foreach ($roles as $rol) { // recorrer roles del usuario
            if ($rol['rodescripcion'] === $rolRequerido) { // comparar con rol requerido
                return true;
            }
        }

        return false;
    }

    //verifico si el uss tiene rol permitido
    public static function tieneAlgunRol($rolesPermitidos)
    {
        foreach ($rolesPermitidos as $rol) { // verificar cada rol
            if (self::tieneRol($rol)) { // si tiene el rol
                return true;
            }
        }
        return false;
    }

    //verifico que el usuario tiene rol requerido
    public static function requiereRol($rolRequerido)
    {
        AuthMiddleware::requiereAutenticacion(); // primero autenticacion

        if (!self::tieneRol($rolRequerido)) { // si no tiene el rol
            header('Location: ?controller=home&action=accesoDenegado'); // redirijo
            exit;
        }
    }

    //verifico que el uss tiene algun rol permitido
    public static function requiereAlgunRol($rolesPermitidos)
    {
        AuthMiddleware::requiereAutenticacion();

        if (!self::tieneAlgunRol($rolesPermitidos)) {
            header('Location: ?controller=home&action=accesoDenegado');
            exit;
        }
    }


    public static function esAdmin()
    {
        return self::tieneRol('admin');
    }

    public static function esCliente()
    {
        return self::tieneRol('cliente');
    }


    public static function requiereAdmin()
    {
        self::requiereRol('admin');
    }

    public static function requiereCliente()
    {
        self::requiereRol('cliente');
    }
}
