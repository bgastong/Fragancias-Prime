<?php

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/MailService.php';

class AuthController
{

    public function login()
    {
        require_once __DIR__ . '/../model/Usuario.php';
        $usuarioModel = new Usuario();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $user = $_POST['usuario'];
            $pass = $_POST['clave'];
            $recordarme = isset($_POST['recordarme']);

            $resultado = $usuarioModel->verificarLogin($user, $pass);

            if ($resultado) {

                session_start();
                $_SESSION['usuario'] = $resultado;

                // Cookie por 7 días
                if ($recordarme) {
                    setcookie('user_saved', $user, time() + 60 * 60 * 24 * 7, "/");
                } else {
                    setcookie('user_saved', '', time() - 3600, "/");
                }

                header("Location: ?controller=home&action=index");
                exit;
            }

            // Error → volver a la vista con mensaje
            $error = "Usuario o contraseña incorrecta";
        }

        require_once __DIR__ . '/../vista/login.php';
    }

    //Proceso el envio del form
    public function procesarLogin()
    {
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
        if ($usuario) {
            $_SESSION['usuario'] = [
                'idusuario' => $usuario['usuario'],
                'usnombre' => $usuario['usnombre'],
                'usmail' => $usuario['usmail'],
            ];

            //cookies
            if ($recordar) {
                //guardo el nombre de us x 30d.
                setcookie('recoradar_usuario', $usuario['usnombre'], time() + 60 * 60 * 24 * 30, "/");
            } else {
                //si descarma recordar elimino cookie.
                setcookie('recordar_usuario', '', time() - 3600, '/');
            }

            //mando al home
            header('Location: ?controller=home&action=index');

            exit;
        } else {
            // Volvemos al login con error
            header('Location: ?controller=auth&action=login&error=1');
            exit;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        header('Location: ?controller=auth&action=login');
        exit;
    }

    public function registro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['usuario'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $clave = $_POST['clave'] ?? '';
            $clave2 = $_POST['clave2'] ?? '';

            // Validaciones básicas
            if (empty($nombre) || empty($email) || empty($clave) || empty($clave2)) {
                $error = 'Todos los campos son obligatorios';
            } elseif ($clave !== $clave2) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($clave) < 4) {
                $error = 'La contraseña debe tener al menos 4 caracteres';
            } else {
                $usuarioModel = new Usuario();
                $resultado = $usuarioModel->crearUsuario($nombre, $clave, $email);

                if (isset($resultado['success']) && $resultado['success']) {
                    // Usuario creado exitosamente - enviar email de bienvenida
                    try {
                        // Verificar si el autoload de Composer está disponible
                        $autoloadPath = ROOT_PATH . '/vendor/autoload.php';
                        if (file_exists($autoloadPath)) {
                            require_once $autoloadPath;
                            
                            $mailService = new MailService();
                            $emailEnviado = $mailService->enviarBienvenida($email, $nombre);
                            
                            if ($emailEnviado) {
                                $exito = 'Usuario creado exitosamente. Te hemos enviado un email de bienvenida. Ya puedes iniciar sesión.';
                            } else {
                                $exito = 'Usuario creado exitosamente. Ya puedes iniciar sesión.';
                            }
                        } else {
                            $exito = 'Usuario creado exitosamente. Ya puedes iniciar sesión.';
                        }
                    } catch (Exception $e) {
                        error_log("Error al enviar email de bienvenida: " . $e->getMessage());
                        $exito = 'Usuario creado exitosamente. Ya puedes iniciar sesión.';
                    }
                } else {
                    $error = $resultado['error'];
                }
            }
        }

        require_once __DIR__ . '/../vista/registro.php';
    }
}
