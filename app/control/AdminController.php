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
        require_once __DIR__ . '/../vista-admin/dashboard.php';
    }

    public function reportes()
    {
        echo "Pagina de reportes en construcción";
    }
}
