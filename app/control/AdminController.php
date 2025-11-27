<?php

require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class AdminController
{
    public function __construct()
    {
        // Verificar que sea admin en cada accion
        RoleMiddleware::requiereAdmin();
    }

    public function dashboard()
    {
        require_once __DIR__ . '/../helpers/view.php';
        require_once __DIR__ . '/../helpers/header.php';
        $usuario = $_SESSION['usuario'] ?? null;
        $menu = obtenerMenuPorDefecto($usuario);
        $datosHeader = obtenerDatosHeaderDesdeControlador($usuario, $menu);
        render(__DIR__ . '/../vista-admin/dashboard.php', ['datosHeader' => $datosHeader, 'esVistaAdmin' => true]);
    }
}
