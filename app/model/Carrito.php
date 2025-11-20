<?php

class Carrito
{
    public function __construct()
    {
        // Inicializo el carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    public function agregar($idProducto, $cantidad = 1) // Agrego producto al carrito
    {
        if (!isset($_SESSION['carrito'][$idProducto])) { // Si no existe, inicializo
            $_SESSION['carrito'][$idProducto] = 0;
        }

        $_SESSION['carrito'][$idProducto] += $cantidad;
    }

    public function quitar($idProducto)
    {
        if (isset($_SESSION['carrito'][$idProducto])) {
            unset($_SESSION['carrito'][$idProducto]);
        }
    }

    public function vaciar()
    {
        $_SESSION['carrito'] = [];
    }

    public function obtener()
    {
        return $_SESSION['carrito'];
    }

    public function totalProductos()
    {
        return array_sum($_SESSION['carrito']);
    }

    public function estaVacio()
    {
        return empty($_SESSION['carrito']);
    }
}
