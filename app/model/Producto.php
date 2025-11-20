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

    public function listarTodos()
    {
        $sql = "SELECT * FROM producto ORDER BY idproducto DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarConPrecio()
    {
        $sql = "SELECT p.*, sh.precio, sh.imagen, sh.orden, sh.subtitulo
                FROM producto p
                LEFT JOIN slider_home sh ON p.idproducto = sh.producto_id
                ORDER BY p.idproducto DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idProducto)
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

    public function crear($datos)
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

    public function editar($idProducto, $datos)
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

    public function eliminar($idProducto)
    {
        try {
            $this->conexion->beginTransaction();

            // Eliminar de slider_home primero
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
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function getProductoById($idProducto)
    {
        return $this->obtenerPorId($idProducto);
    }
}

