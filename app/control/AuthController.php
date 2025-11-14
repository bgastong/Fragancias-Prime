<?php

require_once __DIR__ . '/../model/Usuario.php';

class AuthController{

    public function login(){
        //Si esta logeado lo mando a lhome
        if(isset($_SESSION['usuario'])){
            header('Location: ?controller=home&action=index');
            exit;
        }
        
        //Muestro error
        $error = $_GET['error'] ?? null;

        //Relleno si existe cookie
        $usuarioRecordado = $_COOKIE['recordar_usuario'] ?? '';

        require __DIR__ . '/../vista/login.php';
    }

    //Proceso el envio del form
    public function procesarLogin(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        $nombre = $_POST['usuario'] ?? '';
        $clave  = $_POST['clave'] ?? '';
        $recordar = isset($_POST['recordar']); // checkbox

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->verificarLogin($nombre, $clave);

        //creo sesion
        if($usuario){
            $_SESSION['usuario'] = [
                'idusuario' => $usuario['usuario'],
                'usnombre' => $usuario['usnombre'],
                'usmail' => $usuario['usmail'],
            ];

        //cookies
        if($recordar){
            //guardo el nombre de us x 30d.
            setcookie('recoradar_usuario', $usuario['usnombre'], time() + 60*60*24*30, "/");
        } else {
            //si descarma recordar elimino cookie.
            setcookie('recordar_usuario', '', time() - 3600, '/');
        }

        //mando al home
        header('Location: ?controller=home&action=index');

        exit;
        }else{
            // Volvemos al login con error
            header('Location: ?controller=auth&action=login&error=1');
            exit;
        }
    }

    public function logout(){
        session_unset();
        session_destroy();

        header('Location: ?controller=auth&action=login');
        exit;
    }
}
