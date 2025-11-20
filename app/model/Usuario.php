<?php 

require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    //Buscamos por user, retornamos, si no encuentra retorna nulo.
    public function buscarNombre($nombre){
        $sql = 'SELECT * FROM usuario WHERE usnombre = :nombre';
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    //Buscamos por id, retornamos, si no encuentra retorna nulo.
    public function buscarId($idusuario){
        $sql = 'SELECT * FROM usuario WHERE idusuario = :id';
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':id', $idusuario, PDO::PARAM_INT);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    //Buscamos por email, retornamos, si no encuentra retorna nulo.
    public function buscarEmail($email){
        $sql = 'SELECT * FROM usuario WHERE usmail = :email';
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    //Listamos todos los usuarios
    public function listarUsuarios(){
        $sql = 'SELECT * FROM  usuario';
        $consulta = $this->db->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //Verificamos login con clave hasheada. (devolvemos datos true, null si es false)
    public function verificarLogin($nombre, $claveIngresada)
    {
        $usuario = $this->buscarNombre($nombre);

        // No existe usuario o esta deshabilitado
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
    public function crearUsuario($nombre, $clave, $email){
        // uss ya existe??
        if ($this->buscarNombre($nombre)) {
            return ['error' => 'El nombre de usuario ya está en uso'];
        }

        // mail ya existe?
        if ($this->buscarEmail($email)) {
            return ['error' => 'El email ya está registrado'];
        }

        // validacion mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'El formato del email no es válido'];
        }

        $hash = password_hash($clave, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (usnombre, uspass, usmail, usdeshabilitado)
                VALUES (:nombre, :pass, :mail, NULL)";

        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':pass', $hash, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $email, PDO::PARAM_STR);

        if ($consulta->execute()) {
            $idUsuario = $this->db->lastInsertId();
            
            $this->asignarRol($idUsuario, 2);// rol cliente por defecto
            
            return ['success' => true, 'id' => $idUsuario];
        }

        return ['error' => 'No se pudo crear el usuario'];
    }


    public function obtenerRolesUsuario($idusuario)
    {
        $sql = "SELECT r.* 
                FROM rol r
                INNER JOIN usuariorol ur ON r.idrol = ur.idrol
                WHERE ur.idusuario = :idusuario";
        
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function asignarRol($idusuario, $idrol)
    {
        $sql = "INSERT INTO usuariorol (idusuario, idrol) 
                VALUES (:idusuario, :idrol)";
        
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        $consulta->bindValue(':idrol', $idrol, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    public function listarTodos()
    {
        $sql = "SELECT u.*, r.rodescripcion as rol_nombre, r.idrol
                FROM usuario u
                LEFT JOIN usuariorol ur ON u.idusuario = ur.idusuario
                LEFT JOIN rol r ON ur.idrol = r.idrol
                ORDER BY u.idusuario DESC";
        
        $consulta = $this->db->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarUsuario($parametro)
    {
        if (isset($parametro['idusuario'])) {
            return $this->buscarId($parametro['idusuario']);
        }
        return null;
    }

    public function actualizarRol($idusuario, $idrol)
    {
        // Eliminar roles actuales
        $sqlDelete = "DELETE FROM usuariorol WHERE idusuario = :idusuario";
        $consultaDelete = $this->db->prepare($sqlDelete);
        $consultaDelete->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        $consultaDelete->execute();

        // Asignar nuevo rol
        return $this->asignarRol($idusuario, $idrol);
    }

    public function deshabilitar($idusuario)
    {
        $sql = "UPDATE usuario SET usdeshabilitado = NOW() WHERE idusuario = :idusuario";
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public function habilitar($idusuario)
    {
        $sql = "UPDATE usuario SET usdeshabilitado = NULL WHERE idusuario = :idusuario";
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public function actualizarContrasena($idusuario, $nuevaContrasena)
    {
        $passHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET uspass = :pass WHERE idusuario = :idusuario";
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':pass', $passHash, PDO::PARAM_STR);
        $consulta->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public function actualizarEmail($idusuario, $nuevoEmail)
    {
        $sql = "UPDATE usuario SET usmail = :email WHERE idusuario = :idusuario";
        $consulta = $this->db->prepare($sql);
        $consulta->bindValue(':email', $nuevoEmail, PDO::PARAM_STR);
        $consulta->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
        return $consulta->execute();
    }

    // Cambiar contrasena con validaciones
    public function cambiarContrasenaConValidacion($idusuario, $passActual, $passNueva, $passConfirmar)
    {
        // Validaciones
        if (empty($passActual) || empty($passNueva) || empty($passConfirmar)) {
            return ['exito' => false, 'mensaje' => 'Todos los campos son obligatorios'];
        }

        if ($passNueva !== $passConfirmar) {
            return ['exito' => false, 'mensaje' => 'Las contrasenas nuevas no coinciden'];
        }

        if (strlen($passNueva) < 4) {
            return ['exito' => false, 'mensaje' => 'La contrasena debe tener al menos 4 caracteres'];
        }

        // Verificar contrasena actual
        $usuario = $this->buscarId($idusuario);
        
        if (!$usuario || !password_verify($passActual, $usuario['uspass'])) {
            return ['exito' => false, 'mensaje' => 'La contrasena actual es incorrecta'];
        }

        // Actualizar contrasena
        if ($this->actualizarContrasena($idusuario, $passNueva)) {
            return ['exito' => true];
        }

        return ['exito' => false, 'mensaje' => 'Error al actualizar la contrasena'];
    }

    // Cambiar email con validaciones
    public function cambiarEmailConValidacion($idusuario, $emailNuevo, $password)
    {
        // Validaciones
        if (empty($emailNuevo) || empty($password)) {
            return ['exito' => false, 'mensaje' => 'Todos los campos son obligatorios'];
        }

        if (!filter_var($emailNuevo, FILTER_VALIDATE_EMAIL)) {
            return ['exito' => false, 'mensaje' => 'El email no es valido'];
        }

        // Verificar contrasena
        $usuario = $this->buscarId($idusuario);
        
        if (!$usuario || !password_verify($password, $usuario['uspass'])) {
            return ['exito' => false, 'mensaje' => 'La contrasena es incorrecta'];
        }

        // Verificar que el email no este en uso
        $emailExiste = $this->buscarEmail($emailNuevo);
        
        if ($emailExiste && $emailExiste['idusuario'] != $idusuario) {
            return ['exito' => false, 'mensaje' => 'Este email ya esta en uso'];
        }

        // Actualizar email
        if ($this->actualizarEmail($idusuario, $emailNuevo)) {
            return ['exito' => true];
        }

        return ['exito' => false, 'mensaje' => 'Error al actualizar el email'];
    }
}
