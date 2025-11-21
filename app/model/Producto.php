<?php

require_once __DIR__ . '/../config/database.php';


class Producto extends DataBase
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = $this->getConnection();
    }

    public function getProductosSlider()
    {
        $sql = "SELECT
                    sh.id          AS slider_id,
                    sh.orden       AS orden,
                    p.idproducto   AS idproducto,
                    p.pronombre,
                    p.prodetalle,
                    p.procantstock,
                    sh.imagen,
                    sh.precio,
                    sh.subtitulo,
                    sh.descripcion
                FROM slider_home sh
                INNER JOIN producto p
                    ON sh.producto_id = p.idproducto
                ORDER BY sh.orden ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodos() // Para listado publico
    {
        $sql = "SELECT * FROM producto ORDER BY idproducto DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarConPrecio() // Para panel admin
    {
        $sql = "SELECT p.*, sh.precio, sh.imagen, sh.orden, sh.subtitulo
                FROM producto p
                LEFT JOIN slider_home sh ON p.idproducto = sh.producto_id
                ORDER BY p.idproducto DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idProducto) // Obtener producto por ID
    {
        $sql = "SELECT p.*, sh.id as slider_id, sh.precio, sh.imagen, sh.orden, sh.subtitulo, sh.descripcion
                FROM producto p
                LEFT JOIN slider_home sh ON p.idproducto = sh.producto_id
                WHERE p.idproducto = :idProducto";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) //ABM - ALTA  
    {
        try {
            $this->conexion->beginTransaction();

            // Insertar en tabla producto
            $sql = "INSERT INTO producto (pronombre, prodetalle, procantstock) 
                    VALUES (:pronombre, :prodetalle, :procantstock)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':pronombre', $datos['pronombre']);
            $stmt->bindParam(':prodetalle', $datos['prodetalle']);
            $stmt->bindParam(':procantstock', $datos['procantstock'], PDO::PARAM_INT);
            $stmt->execute();

            $idProducto = $this->conexion->lastInsertId();

            // Insertar en slider_home si tiene precio e imagen
            if (!empty($datos['precio']) && !empty($datos['imagen'])) {
                $sqlSlider = "INSERT INTO slider_home (producto_id, precio, imagen, orden, subtitulo, descripcion) 
                            VALUES (:producto_id, :precio, :imagen, :orden, :subtitulo, :descripcion)";

                $stmtSlider = $this->conexion->prepare($sqlSlider);
                $stmtSlider->bindParam(':producto_id', $idProducto, PDO::PARAM_INT);
                $stmtSlider->bindParam(':precio', $datos['precio']);
                $stmtSlider->bindParam(':imagen', $datos['imagen']);
                $stmtSlider->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
                $stmtSlider->bindParam(':subtitulo', $datos['subtitulo']);
                $stmtSlider->bindParam(':descripcion', $datos['descripcion']);
                $stmtSlider->execute();
            }

            $this->conexion->commit();
            return $idProducto;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function editar($idProducto, $datos) // ABM - EDITAR
    {
        try {
            $this->conexion->beginTransaction();

            // Actualizar tabla producto
            $sql = "UPDATE producto 
                    SET pronombre = :pronombre, 
                        prodetalle = :prodetalle, 
                        procantstock = :procantstock
                    WHERE idproducto = :idProducto";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':pronombre', $datos['pronombre']);
            $stmt->bindParam(':prodetalle', $datos['prodetalle']);
            $stmt->bindParam(':procantstock', $datos['procantstock'], PDO::PARAM_INT);
            $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
            $stmt->execute();

            // Actualizar o insertar en slider_home
            $checkSlider = "SELECT id FROM slider_home WHERE producto_id = :producto_id";
            $stmtCheck = $this->conexion->prepare($checkSlider);
            $stmtCheck->bindParam(':producto_id', $idProducto, PDO::PARAM_INT);
            $stmtCheck->execute();
            $sliderExists = $stmtCheck->fetch();

            if ($sliderExists) {
                // Actualizar
                $sqlSlider = "UPDATE slider_home 
                                SET precio = :precio, 
                                    orden = :orden, 
                                    subtitulo = :subtitulo, 
                                    descripcion = :descripcion";

                if (!empty($datos['imagen'])) {
                    $sqlSlider .= ", imagen = :imagen";
                }

                $sqlSlider .= " WHERE producto_id = :producto_id";

                $stmtSlider = $this->conexion->prepare($sqlSlider);
                $stmtSlider->bindParam(':precio', $datos['precio']);
                $stmtSlider->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
                $stmtSlider->bindParam(':subtitulo', $datos['subtitulo']);
                $stmtSlider->bindParam(':descripcion', $datos['descripcion']);
                $stmtSlider->bindParam(':producto_id', $idProducto, PDO::PARAM_INT);

                if (!empty($datos['imagen'])) {
                    $stmtSlider->bindParam(':imagen', $datos['imagen']);
                }

                $stmtSlider->execute();
            } else if (!empty($datos['precio']) && !empty($datos['imagen'])) {
                // Insertar nuevo
                $sqlSlider = "INSERT INTO slider_home (producto_id, precio, imagen, orden, subtitulo, descripcion) 
                                VALUES (:producto_id, :precio, :imagen, :orden, :subtitulo, :descripcion)";

                $stmtSlider = $this->conexion->prepare($sqlSlider);
                $stmtSlider->bindParam(':producto_id', $idProducto, PDO::PARAM_INT);
                $stmtSlider->bindParam(':precio', $datos['precio']);
                $stmtSlider->bindParam(':imagen', $datos['imagen']);
                $stmtSlider->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
                $stmtSlider->bindParam(':subtitulo', $datos['subtitulo']);
                $stmtSlider->bindParam(':descripcion', $datos['descripcion']);
                $stmtSlider->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    // Eliminar producto y su entrada en slider_home
    public function eliminar($idProducto)
    {
        try {
            // Verificar si el producto esta en pedidos NO enviados (estado 1: iniciada o 2: aceptada)
            $sqlCheck = "SELECT COUNT(*) as total 
                        FROM compraitem ci
                        INNER JOIN compra c ON ci.idcompra = c.idcompra
                        INNER JOIN compraestado ce ON c.idcompra = ce.idcompra
                        WHERE ci.idproducto = :idProducto 
                        AND ce.idcompraestadotipo IN (1, 2)
                        AND ce.cefechafin IS NULL";
            $stmtCheck = $this->conexion->prepare($sqlCheck);
            $stmtCheck->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
            $stmtCheck->execute();
            $resultado = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($resultado['total'] > 0) {
                throw new Exception("No se puede eliminar el producto porque esta en pedidos pendientes de envio.");
            }

            $this->conexion->beginTransaction();

            // Eliminar items de pedidos enviados/cancelados (estado 3 y 4)
            $sqlDeleteItems = "DELETE ci FROM compraitem ci
                                INNER JOIN compra c ON ci.idcompra = c.idcompra
                                INNER JOIN compraestado ce ON c.idcompra = ce.idcompra
                                WHERE ci.idproducto = :idProducto 
                                AND ce.idcompraestadotipo IN (3, 4)
                                AND ce.cefechafin IS NULL";
            $stmtDeleteItems = $this->conexion->prepare($sqlDeleteItems);
            $stmtDeleteItems->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
            $stmtDeleteItems->execute();

            // Eliminar de slider_home
            $sqlSlider = "DELETE FROM slider_home WHERE producto_id = :producto_id";
            $stmtSlider = $this->conexion->prepare($sqlSlider);
            $stmtSlider->bindParam(':producto_id', $idProducto, PDO::PARAM_INT);
            $stmtSlider->execute();

            // Eliminar de producto
            $sql = "DELETE FROM producto WHERE idproducto = :idProducto";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $e;
        }
    }

    public function getProductoById($idProducto)
    {
        return $this->obtenerPorId($idProducto);
    }


    // Descontar stock de un producto
    public function descontarStock($idProducto, $cantidad)
    {
        $sql = "UPDATE producto 
                SET procantstock = procantstock - :cantidad 
                WHERE idproducto = :idProducto AND procantstock >= :cantidad";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0; // true si se actualizo, false si no hay stock
    }


    // Restaurar stock de un producto
    public function restaurarStock($idProducto, $cantidad)
    {
        $sql = "UPDATE producto 
                SET procantstock = procantstock + :cantidad 
                WHERE idproducto = :idProducto";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Verifico si hay stock suficiente
    public function verificarStock($idProducto, $cantidadRequerida)
    {
        $sql = "SELECT procantstock FROM producto WHERE idproducto = :idProducto";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $producto && $producto['procantstock'] >= $cantidadRequerida;
    }
}
