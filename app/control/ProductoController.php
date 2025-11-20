<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../model/Producto.php';

class ProductoController
{
    public function index() // Listo todos los productos
    {
        $productoModel = new Producto();
        $productos = $productoModel->listarTodos();

        require_once __DIR__ . '/../vista/productos.php';
    }

    public function ver($id) // Detalle de un producto
    {
        $productoModel = new Producto();
        $producto = $productoModel->getProductoById($id);

        if (!$producto) {
            echo "Producto no encontrado.";
            exit;
        }

        require_once __DIR__ . '/../vista/producto.php';
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

    public function crear()
    {
        // Solo admin puede crear productos
        AuthMiddleware::requiereAutenticacion();
        RoleMiddleware::requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el formulario
            $datos = [
                'pronombre' => $_POST['pronombre'] ?? '',
                'prodetalle' => $_POST['prodetalle'] ?? '',
                'procantstock' => $_POST['procantstock'] ?? 0,
                'precio' => $_POST['precio'] ?? 0,
                'orden' => $_POST['orden'] ?? 0,
                'subtitulo' => $_POST['subtitulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen' => ''
            ];

            // Validar campos requeridos
            if (empty($datos['pronombre']) || empty($datos['prodetalle'])) {
                $_SESSION['mensaje_error'] = "Nombre y detalle son obligatorios";
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
                }
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

    public function editar()
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
                'pronombre' => $_POST['pronombre'] ?? '',
                'prodetalle' => $_POST['prodetalle'] ?? '',
                'procantstock' => $_POST['procantstock'] ?? 0,
                'precio' => $_POST['precio'] ?? 0,
                'orden' => $_POST['orden'] ?? 0,
                'subtitulo' => $_POST['subtitulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen' => $producto['imagen'] ?? ''
            ];

            // Validar campos requeridos
            if (empty($datos['pronombre']) || empty($datos['prodetalle'])) {
                $_SESSION['mensaje_error'] = "Nombre y detalle son obligatorios";
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

                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid() . '.' . $extension;
                $rutaDestino = $uploadDir . $nombreArchivo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $datos['imagen'] = $nombreArchivo;
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

    public function eliminar()
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
            // Eliminar imagen física si existe
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

