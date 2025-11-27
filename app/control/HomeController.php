<?php

require_once __DIR__ . '/../model/Producto.php';

class HomeController
{
    public function index()
    {
        $producto = new Producto();
        $productosSlider = $producto->getProductosSlider();

        require_once __DIR__ . '/../helpers/view.php';
        require_once __DIR__ . '/../helpers/header.php';

        // Construir menú desde el controlador (dinámico sin usar BD)
        $usuario = $_SESSION['usuario'] ?? null;
        $menuIzquierdo = [
            ['key' => 'inicio', 'label' => 'Inicio', 'href' => '?controller=home&action=index', 'icon' => 'bi-house'],
            ['key' => 'buscar', 'label' => 'Buscar', 'href' => '?controller=producto&action=buscar', 'icon' => 'bi-search'],
        ];

        $datosHeader = obtenerDatosHeaderDesdeControlador($usuario, $menuIzquierdo);

        render(__DIR__ . '/../vista/home.php', ['productosSlider' => $productosSlider, 'datosHeader' => $datosHeader]);
    }

    public function accesoDenegado()
    {
        require_once __DIR__ . '/../helpers/view.php';
        require_once __DIR__ . '/../helpers/header.php';

        $usuario = $_SESSION['usuario'] ?? null;
        $menuIzquierdo = obtenerMenuPorDefecto($usuario);
        $datosHeader = obtenerDatosHeaderDesdeControlador($usuario, $menuIzquierdo);
        render(__DIR__ . '/../vista/acceso-denegado.php', ['datosHeader' => $datosHeader]);
    }
}
