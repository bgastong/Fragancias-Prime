<?php 

require_once __DIR__ . '/../config/Database.php';

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