<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioController
{
    public function listar()
    {
        // Solo admin puede listar usuarios
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->listarTodos();

        require_once __DIR__ . '/../vista-admin/usuarios-listar.php';
    }

    public function editar()
    {
        // Solo admin puede editar usuarios
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idUsuario = $_GET['id'] ?? null;
        
        if (!$idUsuario) {
            $_SESSION['mensaje_error'] = "ID de usuario no especificado";
            header("Location: ?controller=usuario&action=listar");
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarUsuario(['idusuario' => $idUsuario]);

        if (!$usuario) {
            $_SESSION['mensaje_error'] = "Usuario no encontrado";
            header("Location: ?controller=usuario&action=listar");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevoRol = $_POST['idrol'] ?? null;
            
            if ($nuevoRol) {
                try {
                    // Eliminar roles actuales y asignar el nuevo
                    $usuarioModel->actualizarRol($idUsuario, $nuevoRol);
                    
                    $_SESSION['mensaje_exito'] = "Rol actualizado exitosamente";
                    header("Location: ?controller=usuario&action=listar");
                    exit;

                } catch (Exception $e) {
                    $_SESSION['mensaje_error'] = "Error al actualizar rol: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . '/../vista-admin/usuarios-editar.php';
    }

    public function deshabilitar()
    {
        // Solo admin puede deshabilitar usuarios
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idUsuario = $_GET['id'] ?? null;
        
        if (!$idUsuario) {
            $_SESSION['mensaje_error'] = "ID de usuario no especificado";
            header("Location: ?controller=usuario&action=listar");
            exit;
        }

        try {
            $usuarioModel = new Usuario();
            $usuarioModel->deshabilitar($idUsuario);
            
            $_SESSION['mensaje_exito'] = "Usuario deshabilitado exitosamente";

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al deshabilitar usuario: " . $e->getMessage();
        }

        header("Location: ?controller=usuario&action=listar");
        exit;
    }

    public function habilitar()
    {
        // Solo admin puede habilitar usuarios
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idUsuario = $_GET['id'] ?? null;
        
        if (!$idUsuario) {
            $_SESSION['mensaje_error'] = "ID de usuario no especificado";
            header("Location: ?controller=usuario&action=listar");
            exit;
        }

        try {
            $usuarioModel = new Usuario();
            $usuarioModel->habilitar($idUsuario);
            
            $_SESSION['mensaje_exito'] = "Usuario habilitado exitosamente";

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al habilitar usuario: " . $e->getMessage();
        }

        header("Location: ?controller=usuario&action=listar");
        exit;
    }
}
