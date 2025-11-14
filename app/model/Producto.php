<?php 

require_once '/../config/database.php';

class Producto {
    private $db;
    
    public function __construct() {
        $database = new DataBase;
        $this->db = $database->getConnection();
    }

    public function ObtenerTodos(){
        $consulta = $this->db->query("SELECT * FROM productos");
        return $consulta->fetchAll();
    }
}