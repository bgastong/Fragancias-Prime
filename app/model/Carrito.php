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

    // Agregar producto con validacion de stock
    public function agregarConValidacion($idProducto, $cantidad, $productoModel)
    {
        if ($idProducto <= 0 || $cantidad <= 0) {
            return ['exito' => false, 'mensaje' => 'Datos invalidos'];
        }

        $producto = $productoModel->obtenerPorId($idProducto);
        
        if (!$producto) {
            return ['exito' => false, 'mensaje' => 'Producto no encontrado'];
        }
        
        if ($producto['procantstock'] < $cantidad) {
            return ['exito' => false, 'mensaje' => "Stock insuficiente. Solo hay {$producto['procantstock']} unidades disponibles"];
        }

        // Verificar cantidad total en carrito
        $cantidadActual = $_SESSION['carrito'][$idProducto] ?? 0;
        $cantidadTotal = $cantidadActual + $cantidad;
        
        if ($producto['procantstock'] < $cantidadTotal) {
            return ['exito' => false, 'mensaje' => "No puedes agregar mas. Stock: {$producto['procantstock']}, en carrito: {$cantidadActual}"];
        }

        $this->agregar($idProducto, $cantidad);
        return ['exito' => true, 'mensaje' => 'Producto agregado al carrito'];
    }

    // Validar todo el carrito antes de finalizar compra
    public function validarStock($productoModel)
    {
        $items = $this->obtener();
        
        if (empty($items)) {
            return ['exito' => false, 'mensaje' => 'El carrito esta vacio'];
        }

        foreach ($items as $idProducto => $cantidad) {
            $producto = $productoModel->obtenerPorId($idProducto);
            
            if (!$producto) {
                return ['exito' => false, 'mensaje' => "Producto ID {$idProducto} no encontrado"];
            }
            
            if ($producto['procantstock'] < $cantidad) {
                return ['exito' => false, 'mensaje' => "Stock insuficiente para '{$producto['prodetalle']}'. Solo hay {$producto['procantstock']} unidades disponibles"];
            }
        }

        return ['exito' => true];
    }

    // Preparar items para compra
    public function prepararItemsCompra()
    {
        $items = $this->obtener();
        $itemsCompra = [];

        foreach ($items as $idProducto => $cantidad) {
            $itemsCompra[] = [
                'idproducto' => $idProducto,
                'cantidad' => $cantidad
            ];
        }

        return $itemsCompra;
    }
}
