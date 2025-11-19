<?php

require_once __DIR__ . '/../config/Database.php';

class Producto
{
    private $db;

    public function __construct()
    {
        $database = new DataBase;
        $this->db = $database->getConnection();
    }

    public function ObtenerTodos()
    {
        $consulta = $this->db->query("SELECT * FROM productos");
        return $consulta->fetchAll();
    }

    public function getProductosSlider()
    {
        $sql = "SELECT
                sh.id          AS slider_id,
                sh.orden,
                p.idproducto   AS idproducto,
                p.pronombre,
                p.prodetalle,
                p.proprecio,
                p.proimagen    AS imagen
            FROM slider_home sh
            INNER JOIN producto p
                ON p.idproducto = sh.producto_id
            WHERE p.prodeshabilitado IS NULL
                OR p.prodeshabilitado = '0000-00-00 00:00:00'
            ORDER BY sh.orden ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
