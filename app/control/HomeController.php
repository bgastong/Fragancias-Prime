<?php

require_once __DIR__ . '/../model/Producto.php';

class HomeController
{
    public function index()
    {
        // Si está logueado, enviamos datos a la vista
        $usuario = $_SESSION['usuario'] ?? null;

        require __DIR__ . '/../vista/home.php';
    }
}