<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Carrito.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/Pedido.php';

class CarritoController
{
    public function index() // Ver contenido del carrito
    {
        $carrito = new Carrito();
        $items = $carrito->obtener();

        $productoModel = new Producto();
        $productos = [];

        // Convertimos IDs a datos reales del producto
        foreach ($items as $id => $cantidad) {
            $p = $productoModel->getProductoById($id);
            if ($p) {
                $p['cantidad'] = $cantidad;
                $productos[] = $p;
            }
        }

        require_once __DIR__ . '/../vista/carrito.php';
    }

    public function ver()
    {
        $this->index();
    }

    public function agregar($idProducto, $cantidad = 1)
    {
        $carrito = new Carrito();
        $carrito->agregar($idProducto, $cantidad);

        header("Location: /carrito");
        exit;
    }


    public function quitar($idProducto)
    {
        $carrito = new Carrito();
        $carrito->quitar($idProducto);

        header("Location: /carrito");
        exit;
    }

    public function vaciar()
    {
        $carrito = new Carrito();
        $carrito->vaciar();

        header("Location: ?controller=carrito&action=ver");
        exit;
    }

    public function finalizarCompra()
    {
        // Solo clientes pueden finalizar compra
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereCliente();

        $carrito = new Carrito();
        $items = $carrito->obtener();

        if (empty($items)) {
            header("Location: ?controller=carrito&action=ver");
            exit;
        }

        $productoModel = new Producto();
        $itemsCompra = [];

        // Preparar items para la compra
        foreach ($items as $idProducto => $cantidad) {
            $itemsCompra[] = [
                'idproducto' => $idProducto,
                'cantidad' => $cantidad
            ];
        }

        // Crear la compra con estado inicial "iniciada" (1)
        $pedidoModel = new Pedido();
        $usuarioId = AuthMiddleware::usuarioId();
        
        try {
            $idCompra = $pedidoModel->crear($usuarioId, $itemsCompra);
            
            // Vaciar el carrito después de crear la compra
            $carrito->vaciar();
            
            // Redirigir a mis pedidos con mensaje de éxito
            $_SESSION['mensaje_exito'] = "¡Compra realizada exitosamente! N° de pedido: #$idCompra";
            header("Location: ?controller=pedido&action=misPedidos");
            exit;
            
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al procesar la compra: " . $e->getMessage();
            header("Location: ?controller=carrito&action=ver");
            exit;
        }
    }
}
