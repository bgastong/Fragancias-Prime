<?php 

require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private $db;

    public function __construct() {
        $database = new DataBase();
        $this->db = $database->getConnection();
    }

    //Buscamos por user, retornamos, si no encuentra retorna nulo.
    public function BuscarNombre($nombre){
        $sql = 'SELECT * FROM usuario WHERE usnombre = :nombre';
        $consulta = $this->db->prepare($sql);
        $consulta->execute;
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    //Buscamos por id, retornamos, si no encuentra retorna nulo.
    public function BuscarId($idusuario){
        $sql = 'SELECT * FROM usuario WHERE idusuario = :id';
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':id', $idusuario, PDO::PARAM_INT);
        $consulta->execute;
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    //Listamos todos los usuarios
    public function ListarUsuarios(){
        $sql = 'SELECT * FROM  usuario';
        $consulta = $this->db->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //Verificamos login con clave hasheada. (devolvemos datos true, null si es false)
    public function verificarLogin($nombre, $claveIngresada)
    {
        $usuario = $this->BuscarNombre($nombre);

        // No existe usuario o está deshabilitado
        if (!$usuario || !is_null($usuario['usdeshabilitado'])) {
            return null;
        }

        // Verificamos la clave usando password_verify
        if (password_verify($claveIngresada, $usuario['uspass'])) {
            return $usuario;
        }

        return null;
    }

    //Creo usuario con la pass hasheada.
    public function CrearUsuario($nombre, $clave, $email){
        $hash = password_hash($clave, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (usnombre, uspass, usmail, usdeshabilitado)
                VALUES (:nombre, :pass, :mail, NULL)";

        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':pass', $hash, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $email, PDO::PARAM_STR);

        if ($consulta->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }
}