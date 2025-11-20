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
                    0 as total
                FROM compra c
                LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
                    AND ce.cefechafin IS NULL
                LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
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
                    0 as total
                FROM compra c
                INNER JOIN usuario u ON c.idusuario = u.idusuario
                LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
                    AND ce.cefechafin IS NULL
                LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
                WHERE ce.idcompraestadotipo IN (1, 2)
                GROUP BY c.idcompra, c.cofecha, u.usnombre, u.usmail, ce.idcompraestadotipo, cet.cetdescripcion
                ORDER BY c.cofecha DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        // Obtener un pedido por su ID
    public function obtenerPorId($idCompra)
    {
        $sql = "SELECT c.*, u.usnombre, u.usmail,
                    ce.idcompraestadotipo, cet.cetdescripcion as estado
                FROM compra c
                INNER JOIN usuario u ON c.idusuario = u.idusuario
                LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
                    AND ce.cefechafin IS NULL
                LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo
                WHERE c.idcompra = :idCompra";

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
            $this->db->rollBack();
            throw $e;
        }
    }


    // Actualizar estado de un pedido
    public function actualizarEstado($idCompra, $nuevoEstadoTipo)
    {
        try {
            $this->db->beginTransaction();

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

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
