<?php

require_once __DIR__ . '/../config/database.php';

class Pedido
{
    private $db;

    public function __construct()
    {
        $database = new DataBase();
        $this->db = $database->getConnection();
    }

    // Obtener pedidos por usuario
    public function obtenerPorUsuario($usuarioId)
    {
        $sql = "SELECT c.idcompra, c.cofecha, 
                    ce.idcompraestadotipo, cet.cetdescripcion as estado,
                    COALESCE(SUM(ci.cicantidad * sh.precio), 0) as total
                FROM compra c
                LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
                    AND ce.cefechafin IS NULL
                LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
                LEFT JOIN compraitem ci ON c.idcompra = ci.idcompra
                LEFT JOIN slider_home sh ON ci.idproducto = sh.producto_id
                WHERE c.idusuario = :usuarioId
                GROUP BY c.idcompra, c.cofecha, ce.idcompraestadotipo, cet.cetdescripcion
                ORDER BY c.cofecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPendientes()
    {
        $sql = "SELECT c.idcompra, c.cofecha, u.usnombre, u.usmail,
                    ce.idcompraestadotipo, cet.cetdescripcion as estado,
                    COALESCE(SUM(ci.cicantidad * sh.precio), 0) as total
                FROM compra c
                INNER JOIN usuario u ON c.idusuario = u.idusuario
                LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
                    AND ce.cefechafin IS NULL
                LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
                LEFT JOIN compraitem ci ON c.idcompra = ci.idcompra
                LEFT JOIN slider_home sh ON ci.idproducto = sh.producto_id
                WHERE ce.idcompraestadotipo IN (1, 2, 3)
                GROUP BY c.idcompra, c.cofecha, u.usnombre, u.usmail, ce.idcompraestadotipo, cet.cetdescripcion
                ORDER BY c.cofecha DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un pedido por su ID
    public function obtenerPorId($idCompra)
    {
        $sql = "SELECT c.*, u.usnombre, u.usmail,
                    ce.idcompraestadotipo, cet.cetdescripcion as estado,
                    COALESCE(SUM(ci.cicantidad * sh.precio), 0) as total
                FROM compra c
                INNER JOIN usuario u ON c.idusuario = u.idusuario
                LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
                    AND ce.cefechafin IS NULL
                LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
                LEFT JOIN compraitem ci ON c.idcompra = ci.idcompra
                LEFT JOIN slider_home sh ON ci.idproducto = sh.producto_id
                WHERE c.idcompra = :idCompra
                GROUP BY c.idcompra, c.idusuario, c.cofecha, u.usnombre, u.usmail, ce.idcompraestadotipo, cet.cetdescripcion";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Obtener los items de un pedido
    public function obtenerItems($idCompra)
    {
        $sql = "SELECT ci.*, p.pronombre, p.prodetalle
                FROM compraitem ci
                INNER JOIN producto p ON ci.idproducto = p.idproducto
                WHERE ci.idcompra = :idCompra";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener el historial completo de estados de un pedido
    public function obtenerHistorialEstados($idCompra)
    {
        $sql = "SELECT ce.idcompraestado, ce.idcompraestadotipo, 
                    cet.cetdescripcion, cet.cetdetalle,
                    ce.cefechaini, ce.cefechafin
                FROM compraestado ce
                INNER JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
                WHERE ce.idcompra = :idCompra
                ORDER BY ce.cefechaini ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Crear un nuevo pedido (compra)
    public function crear($usuarioId, $items)
    {
        try {
            $this->db->beginTransaction();

            // Insertar compra
            $sql = "INSERT INTO compra (idusuario, cofecha) 
                    VALUES (:usuarioId, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();

            $idCompra = $this->db->lastInsertId();

            // Insertar items de la compra
            $sqlItem = "INSERT INTO compraitem (idcompra, idproducto, cicantidad) 
                        VALUES (:idCompra, :idProducto, :cantidad)";

            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                $stmtItem->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
                $stmtItem->bindParam(':idProducto', $item['idproducto'], PDO::PARAM_INT);
                $stmtItem->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtItem->execute();
            }

            // Crear estado inicial: iniciada (1)
            $sqlEstado = "INSERT INTO compraestado (idcompra, idcompraestadotipo, cefechaini) 
                        VALUES (:idCompra, 1, NOW())";
            $stmtEstado = $this->db->prepare($sqlEstado);
            $stmtEstado->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
            $stmtEstado->execute();

            $this->db->commit();
            return $idCompra;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }


    // Actualizar estado de un pedido
    public function actualizarEstado($idCompra, $nuevoEstadoTipo)
    {
        try {
            $transaccionIniciada = false;
            
            // Solo iniciar transacción si no hay una activa
            if (!$this->db->inTransaction()) {
                $this->db->beginTransaction();
                $transaccionIniciada = true;
            }

            // Cerrar el estado actual
            $sqlCerrar = "UPDATE compraestado 
                        SET cefechafin = NOW() 
                        WHERE idcompra = :idCompra AND cefechafin IS NULL";
            $stmtCerrar = $this->db->prepare($sqlCerrar);
            $stmtCerrar->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
            $stmtCerrar->execute();

            // Crear el nuevo estado
            $sqlNuevo = "INSERT INTO compraestado (idcompra, idcompraestadotipo, cefechaini) 
                        VALUES (:idCompra, :estadoTipo, NOW())";
            $stmtNuevo = $this->db->prepare($sqlNuevo);
            $stmtNuevo->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
            $stmtNuevo->bindParam(':estadoTipo', $nuevoEstadoTipo, PDO::PARAM_INT);
            $stmtNuevo->execute();

            // Solo hacer commit si iniciamos la transacción aquí
            if ($transaccionIniciada) {
                $this->db->commit();
            }
            
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Crear pedido completo con validaciones y email
    public function crearPedidoCompleto($usuarioId, $items, $usuarioModel, $mailService)
    {
        try {
            // Crear el pedido
            $idCompra = $this->crear($usuarioId, $items);
            
            // Obtener datos del pedido para el email
            $pedido = $this->obtenerPorId($idCompra);
            $total = $pedido['total'] ?? 0;
            
            // Obtener datos del usuario
            $usuario = $usuarioModel->buscarId($usuarioId);
            
            // Enviar email si tiene email configurado
            if ($usuario && !empty($usuario['usmail'])) {
                $mailService->enviarCompraIniciada(
                    $usuario['usmail'],
                    $usuario['usnombre'],
                    $idCompra,
                    $total
                );
            }
            
            return ['exito' => true, 'idCompra' => $idCompra];
        } catch (Exception $e) {
            return ['exito' => false, 'mensaje' => $e->getMessage()];
        }
    }

    // Aceptar pedido (descuenta stock y envia email)
    public function aceptarPedido($idCompra, $productoModel, $usuarioModel, $mailService)
    {
        try {
            $this->db->beginTransaction();
            
            // Obtener items del pedido
            $items = $this->obtenerItems($idCompra);
            
            // Descontar stock de cada producto
            foreach ($items as $item) {
                $stockDescontado = $productoModel->descontarStock($item['idproducto'], $item['cicantidad']);
                if (!$stockDescontado) {
                    throw new Exception("Stock insuficiente para el producto ID: " . $item['idproducto']);
                }
            }
            
            // Cambiar estado a "aceptada" (2)
            $this->actualizarEstado($idCompra, 2);
            
            $this->db->commit();
            
            // Enviar email al cliente
            $pedido = $this->obtenerPorId($idCompra);
            $usuario = $usuarioModel->buscarId($pedido['idusuario']);
            
            if ($usuario && !empty($usuario['usmail'])) {
                $mailService->enviarCompraAceptada(
                    $usuario['usmail'],
                    $usuario['usnombre'],
                    $idCompra
                );
            }
            
            return ['exito' => true];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['exito' => false, 'mensaje' => $e->getMessage()];
        }
    }

    // Enviar pedido (actualiza estado y envia email)
    public function enviarPedido($idCompra, $usuarioModel, $mailService)
    {
        try {
            // Cambiar estado a "enviada" (3)
            $this->actualizarEstado($idCompra, 3);
            
            // Enviar email al cliente
            $pedido = $this->obtenerPorId($idCompra);
            $usuario = $usuarioModel->buscarId($pedido['idusuario']);
            
            if ($usuario && !empty($usuario['usmail'])) {
                $mailService->enviarCompraEnviada(
                    $usuario['usmail'],
                    $usuario['usnombre'],
                    $idCompra
                );
            }
            
            return ['exito' => true];
        } catch (Exception $e) {
            return ['exito' => false, 'mensaje' => $e->getMessage()];
        }
    }

    // Cancelar pedido (restaura stock si fue aceptado y envia email)
    public function cancelarPedido($idCompra, $productoModel, $usuarioModel, $mailService)
    {
        try {
            $this->db->beginTransaction(); 
            
            $pedido = $this->obtenerPorId($idCompra);
            
            // Si el pedido ya fue aceptado (estado >= 2), restaurar el stock
            if ($pedido['idcompraestadotipo'] >= 2) {
                $items = $this->obtenerItems($idCompra);
                
                foreach ($items as $item) {
                    $productoModel->restaurarStock($item['idproducto'], $item['cicantidad']);
                }
            }
            
            // Cambiar estado a "cancelada" (4)
            $this->actualizarEstado($idCompra, 4);
            
            $this->db->commit(); // Confirmo 
            
            // Enviar email al cliente
            $usuario = $usuarioModel->buscarId($pedido['idusuario']);
            
            if ($usuario && !empty($usuario['usmail'])) {
                $mailService->enviarCompraCancelada(
                    $usuario['usmail'],
                    $usuario['usnombre'],
                    $idCompra
                );
            }
            
            return ['exito' => true, 'stockRestaurado' => $pedido['idcompraestadotipo'] >= 2];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['exito' => false, 'mensaje' => $e->getMessage()];
        }
    }
}
