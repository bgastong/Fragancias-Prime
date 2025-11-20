<?php

require_once __DIR__ . '/../model/Producto.php';

class HomeController
{
    public function index()
    {
        $producto = new Producto();
        $productosSlider = $producto->getProductosSlider();

        require_once __DIR__ . '/../vista/home.php';
    }

    public function accesoDenegado()
    {
        require_once __DIR__ . '/../vista/acceso-denegado.php';
    }
}
