<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Carrito.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/Pedido.php';
require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/MailService.php';

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

    public function agregar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProducto = intval($_POST['idproducto'] ?? 0);
            $cantidad = intval($_POST['cantidad'] ?? 1);

            $carrito = new Carrito();
            $productoModel = new Producto();
            
            $resultado = $carrito->agregarConValidacion($idProducto, $cantidad, $productoModel);
            
            if ($resultado['exito']) {
                $_SESSION['mensaje_exito'] = $resultado['mensaje'];
            } else {
                $_SESSION['mensaje_error'] = $resultado['mensaje'];
            }
        }

        header("Location: ?controller=carrito&action=ver");
        exit;
    }


    public function quitar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProducto = intval($_POST['idproducto'] ?? 0);

            if ($idProducto > 0) {
                $carrito = new Carrito();
                $carrito->quitar($idProducto);
            }
        }

        header("Location: ?controller=carrito&action=ver");
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
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereCliente();

        $carrito = new Carrito();
        $productoModel = new Producto();
        
        // Validar stock
        $validacion = $carrito->validarStock($productoModel);
        if (!$validacion['exito']) {
            $_SESSION['mensaje_error'] = $validacion['mensaje'];
            header("Location: ?controller=carrito&action=ver");
            exit;
        }

        // Preparar items
        $itemsCompra = $carrito->prepararItemsCompra();
        
        // Crear pedido completo
        $pedidoModel = new Pedido();
        $usuarioModel = new Usuario();
        $mailService = new MailService();
        $usuarioId = AuthMiddleware::usuarioId();
        
        $resultado = $pedidoModel->crearPedidoCompleto($usuarioId, $itemsCompra, $usuarioModel, $mailService);
        
        if ($resultado['exito']) {
            $carrito->vaciar();
            $_SESSION['mensaje_exito'] = "Compra realizada exitosamente! N de pedido: #{$resultado['idCompra']}";
            header("Location: ?controller=pedido&action=misPedidos");
        } else {
            $_SESSION['mensaje_error'] = "Error al procesar la compra: {$resultado['mensaje']}";
            header("Location: ?controller=carrito&action=ver");
        }
        
        exit;
    }
}
