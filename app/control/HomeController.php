<?php

require_once __DIR__ . '/../model/Producto.php';

class HomeController
{
    public function index() {
        $productoModel = new Producto();
        $sliderProductos = $productoModel->getProductosSlider();

        require_once __DIR__ . '/../vista/home.php';
    }
}
    