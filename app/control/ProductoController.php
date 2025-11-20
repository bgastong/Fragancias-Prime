<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Producto.php';

class ProductoController
{
    public function ver()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: ?controller=home&action=index");
            exit;
        }

        $productoModel = new Producto();
        $producto = $productoModel->obtenerPorId($id);

        if (!$producto) {
            header("Location: ?controller=home&action=index");
            exit;
        }

        require_once __DIR__ . '/../vista/detalle_producto.php';
    }

    public function listar()
    {
        // Solo admin puede listar productos en el panel admin
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $productoModel = new Producto();
        $productos = $productoModel->listarConPrecio();

        require_once __DIR__ . '/../vista-admin/productos-listar.php';
    }

    public function crear() //ABM - ALTA
    {
        // Solo admin puede crear productos
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el formulario
            $datos = [
                'pronombre' => '', // Se llenara con la ruta de imagen
                'prodetalle' => $_POST['prodetalle'] ?? '',
                'procantstock' => $_POST['procantstock'] ?? 0,
                'precio' => $_POST['precio'] ?? 0,
                'orden' => $_POST['orden'] ?? 0,
                'subtitulo' => $_POST['subtitulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen' => ''
            ];

            // Validar campo requerido
            if (empty($datos['prodetalle'])) {
                $_SESSION['mensaje_error'] = "El nombre del producto es obligatorio";
                require_once __DIR__ . '/../vista-admin/productos-crear.php';
                return;
            }

            // Procesar imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/upload/productos/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid() . '.' . $extension;
                $rutaDestino = $uploadDir . $nombreArchivo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $datos['imagen'] = $nombreArchivo;
                    // Guardar la ruta completa en pronombre
                    $datos['pronombre'] = '/Fragancias Prime/public/upload/productos/' . $nombreArchivo;
                }
            } else {
                // Si no hay imagen, usar ruta vacia
                $datos['pronombre'] = '';
            }

            try {
                $productoModel = new Producto();
                $idProducto = $productoModel->crear($datos);
                
                $_SESSION['mensaje_exito'] = "Producto creado exitosamente con ID #$idProducto";
                header("Location: ?controller=producto&action=listar");
                exit;

            } catch (Exception $e) {
                $_SESSION['mensaje_error'] = "Error al crear producto: " . $e->getMessage();
                require_once __DIR__ . '/../vista-admin/productos-crear.php';
                return;
            }
        }

        // Mostrar formulario
        require_once __DIR__ . '/../vista-admin/productos-crear.php';
    }

    public function editar() // ABM - EDITAR
    {
        // Solo admin puede editar productos
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idProducto = $_GET['id'] ?? null;
        
        if (!$idProducto) {
            $_SESSION['mensaje_error'] = "ID de producto no especificado";
            header("Location: ?controller=producto&action=listar");
            exit;
        }

        $productoModel = new Producto();
        $producto = $productoModel->obtenerPorId($idProducto);

        if (!$producto) {
            $_SESSION['mensaje_error'] = "Producto no encontrado";
            header("Location: ?controller=producto&action=listar");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el formulario
            $datos = [
                'pronombre' => $producto['pronombre'] ?? '', // Mantener el valor actual, se actualizara solo si hay nueva imagen
                'prodetalle' => $_POST['prodetalle'] ?? '',
                'procantstock' => $_POST['procantstock'] ?? 0,
                'precio' => $_POST['precio'] ?? 0,
                'orden' => $_POST['orden'] ?? 0,
                'subtitulo' => $_POST['subtitulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen' => $producto['imagen'] ?? ''
            ];

            // Validar campo requerido
            if (empty($datos['prodetalle'])) {
                $_SESSION['mensaje_error'] = "El nombre del producto es obligatorio";
                require_once __DIR__ . '/../vista-admin/productos-editar.php';
                return;
            }

            // Procesar nueva imagen si se sube
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/upload/productos/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Eliminar imagen anterior
                if (!empty($producto['imagen']) && file_exists($uploadDir . $producto['imagen'])) {
                    unlink($uploadDir . $producto['imagen']);
                }

                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION); //
                $nombreArchivo = uniqid() . '.' . $extension;
                $rutaDestino = $uploadDir . $nombreArchivo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) { // Nueva imagen subida
                    $datos['imagen'] = $nombreArchivo; // Actualizar con nueva imagen
                    $datos['pronombre'] = '/Fragancias Prime/public/upload/productos/' . $nombreArchivo; // Actualizar ruta
                }
            }

            try {
                $productoModel->editar($idProducto, $datos);
                
                $_SESSION['mensaje_exito'] = "Producto actualizado exitosamente";
                header("Location: ?controller=producto&action=listar");
                exit;

            } catch (Exception $e) {
                $_SESSION['mensaje_error'] = "Error al actualizar producto: " . $e->getMessage();
                require_once __DIR__ . '/../vista-admin/productos-editar.php';
                return;
            }
        }

        // Mostrar formulario
        require_once __DIR__ . '/../vista-admin/productos-editar.php';
    }

    public function eliminar() //ABM - BAJA
    {
        // Solo admin puede eliminar productos
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        $idProducto = $_GET['id'] ?? null;
        
        if (!$idProducto) {
            $_SESSION['mensaje_error'] = "ID de producto no especificado";
            header("Location: ?controller=producto&action=listar");
            exit;
        }

        $productoModel = new Producto();
        $producto = $productoModel->obtenerPorId($idProducto);

        if (!$producto) {
            $_SESSION['mensaje_error'] = "Producto no encontrado";
            header("Location: ?controller=producto&action=listar");
            exit;
        }

        try {
            // Eliminar imagen fisica si existe
            if (!empty($producto['imagen'])) {
                $rutaImagen = ROOT_PATH . '/public/upload/productos/' . $producto['imagen'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            $productoModel->eliminar($idProducto);
            
            $_SESSION['mensaje_exito'] = "Producto eliminado exitosamente";

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al eliminar producto: " . $e->getMessage();
        }

        header("Location: ?controller=producto&action=listar");
        exit;
    }
}

