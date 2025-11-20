<?php

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/MailService.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['usuario'] ?? '');
            $clave = $_POST['clave'] ?? '';
            $recordar = isset($_POST['recordar']);

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->verificarLogin($nombre, $clave);

            if ($usuario) {
                $_SESSION['usuario'] = [
                    'idusuario' => $usuario['idusuario'],
                    'usnombre' => $usuario['usnombre'],
                    'usmail' => $usuario['usmail'],
                ];

                if ($recordar) {
                    setcookie('recordar_usuario', $usuario['usnombre'], time() + 60 * 60 * 24 * 30, "/");
                } else {
                    setcookie('recordar_usuario', '', time() - 3600, '/');
                }

                header('Location: ?controller=home&action=index');
                exit;
            } else {
                $error = "Usuario o contrasena incorrectos";
            }
        }

        require_once __DIR__ . '/../vista/login.php';
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

            // Validaciones basicas
            if (empty($nombre) || empty($email) || empty($clave) || empty($clave2)) {
                $error = 'Todos los campos son obligatorios';
            } elseif ($clave !== $clave2) {
                $error = 'Las contrasenas no coinciden';
            } elseif (strlen($clave) < 4) {
                $error = 'La contrasena debe tener al menos 4 caracteres';
            } else {
                $usuarioModel = new Usuario();
                $resultado = $usuarioModel->crearUsuario($nombre, $clave, $email);

                if (isset($resultado['success']) && $resultado['success']) {
                    // Usuario creado exitosamente - envio email de bienvenida
                    try {
                        // VERIFICO EL COMPOSER AUTOCARGA
                        $autoloadPath = ROOT_PATH . '/vendor/autoload.php';
                        if (file_exists($autoloadPath)) {
                            require_once $autoloadPath;
                            
                            $mailService = new MailService();
                            $emailEnviado = $mailService->enviarBienvenida($email, $nombre);
                            
                            if ($emailEnviado) {
                                $exito = 'Usuario creado exitosamente. Te hemos enviado un email de bienvenida. Ya puedes iniciar sesion.';
                            } else {
                                $exito = 'Usuario creado exitosamente. Ya puedes iniciar sesion.';
                            }
                        } else {
                            $exito = 'Usuario creado exitosamente. Ya puedes iniciar sesion.';
                        }
                    } catch (Exception $e) {
                        error_log("Error al enviar email de bienvenida: " . $e->getMessage());
                        $exito = 'Usuario creado exitosamente. Ya puedes iniciar sesion.';
                    }
                } else {
                    $error = $resultado['error'];
                }
            }
        }

        require_once __DIR__ . '/../vista/registro.php';
    }
}
