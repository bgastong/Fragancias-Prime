<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Pedido.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/MailService.php';

class PedidoController
{
    public function misPedidos()
    {
        // Requiere estar logueado y ser cliente
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereCliente();

        $pedidoModel = new Pedido();
        $usuarioId = AuthMiddleware::usuarioId();
        
        $pedidos = $pedidoModel->obtenerPorUsuario($usuarioId);

        require_once __DIR__ . '/../vista/mis-pedidos.php';
    }

    public function pendientes()
    {
        // Solo admin puede ver pedidos pendientes
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->obtenerPendientes();

        require_once __DIR__ . '/../vista/pedidos-pendientes.php';
    }

    public function detalle()
    {
        AuthMiddleware::requiereAutenticacion();

        $idCompra = $_GET['id'] ?? null;
        if (!$idCompra) {
            die("ID de pedido no especificado");
        }

        $pedidoModel = new Pedido();
        $pedido = $pedidoModel->obtenerPorId($idCompra);

        if (!$pedido) {
            die("Pedido no encontrado");
        }

        // Verificar que el usuario sea dueno del pedido o sea admin
        $usuarioId = AuthMiddleware::usuarioId();
        if ($pedido['idusuario'] != $usuarioId && !RoleMiddleware::esAdmin()) {
            header("Location: ?controller=home&action=accesoDenegado");
            exit;
        }

        $items = $pedidoModel->obtenerItems($idCompra);

        require_once __DIR__ . '/../vista/detalle-pedido.php';
    }

    public function aceptar()
    {
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idCompra = $_GET['id'] ?? null;
        if (!$idCompra) {
            die("ID de pedido no especificado");
        }

        $pedidoModel = new Pedido();
        $productoModel = new Producto();
        $usuarioModel = new Usuario();
        $mailService = new MailService();
        
        $resultado = $pedidoModel->aceptarPedido($idCompra, $productoModel, $usuarioModel, $mailService);
        
        if ($resultado['exito']) {
            $_SESSION['mensaje_exito'] = "Pedido #$idCompra aceptado y stock descontado exitosamente";
        } else {
            $_SESSION['mensaje_error'] = "Error al aceptar pedido: {$resultado['mensaje']}";
        }

        header("Location: ?controller=pedido&action=pendientes");
        exit;
    }

    public function enviar()
    {
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idCompra = $_GET['id'] ?? null;
        if (!$idCompra) {
            die("ID de pedido no especificado");
        }

        $pedidoModel = new Pedido();
        $usuarioModel = new Usuario();
        $mailService = new MailService();
        
        $resultado = $pedidoModel->enviarPedido($idCompra, $usuarioModel, $mailService);
        
        if ($resultado['exito']) {
            $_SESSION['mensaje_exito'] = "Pedido #$idCompra marcado como enviado";
        } else {
            $_SESSION['mensaje_error'] = "Error al enviar pedido: {$resultado['mensaje']}";
        }

        header("Location: ?controller=pedido&action=pendientes");
        exit;
    }

    public function cancelar()
    {
        AuthMiddleware::requiereAutenticacion();

        $idCompra = $_GET['id'] ?? null;
        if (!$idCompra) {
            die("ID de pedido no especificado");
        }

        $pedidoModel = new Pedido();
        $pedido = $pedidoModel->obtenerPorId($idCompra);

        if (!$pedido) {
            die("Pedido no encontrado");
        }

        // Verificar permisos
        $usuarioId = AuthMiddleware::usuarioId();
        $esAdmin = RoleMiddleware::esAdmin();
        $esDueno = $pedido['idusuario'] == $usuarioId;

        if (!$esAdmin && (!$esDueno || $pedido['idcompraestadotipo'] != 1)) {
            $_SESSION['mensaje_error'] = "No tienes permiso para cancelar este pedido";
            header("Location: ?controller=pedido&action=misPedidos");
            exit;
        }

        // Cancelar pedido
        $productoModel = new Producto();
        $usuarioModel = new Usuario();
        $mailService = new MailService();
        
        $resultado = $pedidoModel->cancelarPedido($idCompra, $productoModel, $usuarioModel, $mailService);
        
        if ($resultado['exito']) {
            $mensaje = "Pedido #$idCompra cancelado";
            if ($resultado['stockRestaurado']) {
                $mensaje .= " y stock restaurado";
            }
            $_SESSION['mensaje_exito'] = $mensaje;
        } else {
            $_SESSION['mensaje_error'] = "Error al cancelar pedido: {$resultado['mensaje']}";
        }

        if ($esAdmin) {
            header("Location: ?controller=pedido&action=pendientes");
        } else {
            header("Location: ?controller=pedido&action=misPedidos");
        }
        exit;
    }
}
