<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Pedido.php';

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
        // Requiere rol de depósito o admin
        AuthMiddleware::requiereAutenticacion();
        
        if (!RoleMiddleware::esDeposito() && !RoleMiddleware::esAdmin()) {
            header("Location: ?controller=home&action=accesoDenegado");
            exit;
        }

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

        // Verificar que el usuario sea dueño del pedido o sea admin/deposito
        $usuarioId = AuthMiddleware::usuarioId();
        if ($pedido['idusuario'] != $usuarioId && 
            !RoleMiddleware::esAdmin() && 
            !RoleMiddleware::esDeposito()) {
            header("Location: ?controller=home&action=accesoDenegado");
            exit;
        }

        $items = $pedidoModel->obtenerItems($idCompra);

        require_once __DIR__ . '/../vista/detalle-pedido.php';
    }

    public function aceptar()
    {
        // Solo admin o depósito pueden aceptar pedidos
        AuthMiddleware::requiereAutenticacion();
        
        if (!RoleMiddleware::esAdmin() && !RoleMiddleware::esDeposito()) {
            header("Location: ?controller=home&action=accesoDenegado");
            exit;
        }

        $idCompra = $_GET['id'] ?? null;
        if (!$idCompra) {
            die("ID de pedido no especificado");
        }

        $pedidoModel = new Pedido();
        
        try {
            // Cambiar estado a "aceptada" (2)
            $pedidoModel->actualizarEstado($idCompra, 2);
            $_SESSION['mensaje_exito'] = "Pedido #$idCompra aceptado exitosamente";
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al aceptar pedido: " . $e->getMessage();
        }

        header("Location: ?controller=pedido&action=pendientes");
        exit;
    }

    public function enviar()
    {
        //solo admin o deposito
        AuthMiddleware::requiereAutenticacion();
        
        if (!RoleMiddleware::esAdmin() && !RoleMiddleware::esDeposito()) {
            header("Location: ?controller=home&action=accesoDenegado");
            exit;
        }

        $idCompra = $_GET['id'] ?? null;
        if (!$idCompra) {
            die("ID de pedido no especificado");
        }

        $pedidoModel = new Pedido();
        
        try {
            //enviado (3)
            $pedidoModel->actualizarEstado($idCompra, 3);
            $_SESSION['mensaje_exito'] = "Pedido #$idCompra marcado como enviado";
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al enviar pedido: " . $e->getMessage();
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

        // Admin puede cancelar cualquier pedido
        // Cliente solo puede cancelar sus propios pedidos en estado "iniciada" (1)
        $usuarioId = AuthMiddleware::usuarioId();
        $esAdmin = RoleMiddleware::esAdmin();
        $esDeposito = RoleMiddleware::esDeposito();
        $esDueno = $pedido['idusuario'] == $usuarioId;

        if (!$esAdmin && !$esDeposito && (!$esDueno || $pedido['idcompraestadotipo'] != 1)) {
            $_SESSION['mensaje_error'] = "No tienes permiso para cancelar este pedido";
            header("Location: ?controller=pedido&action=misPedidos");
            exit;
        }

        try {
            // Cambiar estado a "cancelada" (4)
            $pedidoModel->actualizarEstado($idCompra, 4);
            $_SESSION['mensaje_exito'] = "Pedido #$idCompra cancelado";
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al cancelar pedido: " . $e->getMessage();
        }

        if ($esAdmin || $esDeposito) {
            header("Location: ?controller=pedido&action=pendientes");
        } else {
            header("Location: ?controller=pedido&action=misPedidos");
        }
        exit;
    }
}
