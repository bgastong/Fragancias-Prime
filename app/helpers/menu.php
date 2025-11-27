<?php
// Helper para construir el menú principal dinamicamente
// Uso: require_once __DIR__ . '/helpers/menu.php';
// $menu = getMenuItems($usuario);

require_once __DIR__ . '/../config/database.php';

function getMenuItems($usuario = null)
{
    // Items base por defecto
    $items = [
        ['key' => 'inicio', 'label' => 'Inicio', 'href' => '?controller=home&action=index', 'icon' => 'bi-house'],
    ];

    try {
        // Si existe la tabla `menu`, usamos el modelo Menu para construir el menú según rol
        require_once __DIR__ . '/../model/Menu.php';
        $menuModel = new Menu();

        if ($menuModel->tableExists()) {
            $menuRows = [];

            // Si hay usuario, intentar obtener sus roles e incluir solo items permitidos
            if ($usuario) {
                // Cargar modelo Usuario para obtener roles del usuario
                require_once __DIR__ . '/../model/Usuario.php';
                $usuarioModel = new Usuario();
                $roles = $usuarioModel->obtenerRolesUsuario($usuario['idusuario']);

                $roleIds = [];
                foreach ($roles as $r) {
                    if (isset($r['idrol'])) $roleIds[] = (int)$r['idrol'];
                }

                        if (!empty($roleIds)) {
                            $menuRows = $menuModel->getByRoleIds($roleIds);
                        } else {
                            $menuRows = $menuModel->getAll();
                        }
            } else {
                // Usuario invitado: obtener items públicos (menurol o visible_para='all'/'anon')
                $menuRows = $menuModel->getPublicItems();
            }
                    // Convertir filas de menu a estructura de items usada por las vistas
                    if (!empty($menuRows)) {
                        $items = []; // reemplazar items base por items desde BD

                        // Determinar roles del usuario por nombre para comparar con visible_para
                        $userRoleNames = [];
                        if ($usuario) {
                            // obtenemos roles como nombres (rodescripcion) si están disponibles
                            require_once __DIR__ . '/../model/Usuario.php';
                            $um = new Usuario();
                            $rrows = $um->obtenerRolesUsuario($usuario['idusuario']);
                            foreach ($rrows as $rr) {
                                if (isset($rr['rodescripcion'])) $userRoleNames[] = strtolower(trim($rr['rodescripcion']));
                                elseif (isset($rr['rol_nombre'])) $userRoleNames[] = strtolower(trim($rr['rol_nombre']));
                            }
                        }

                        foreach ($menuRows as $row) {
                            // Saltar items deshabilitados (seguro)
                            if (isset($row['medeshabilitado']) && (int)$row['medeshabilitado'] !== 0) continue;

                            // Tipo: por defecto consideramos 'left' para el menú principal
                            $tipo = isset($row['tipo']) && $row['tipo'] ? strtolower($row['tipo']) : 'left';
                            if ($tipo !== 'left') continue; // solo left items aquí

                            // Validar visible_para
                            $visible = isset($row['visible_para']) ? strtolower(trim($row['visible_para'])) : 'all';
                            $allow = false;
                            if ($visible === 'all') {
                                $allow = true;
                            } elseif ($visible === 'anon') {
                                $allow = !$usuario;
                            } elseif ($visible === 'user' || $visible === 'cliente') {
                                $allow = (bool)$usuario; // si está autenticado
                            } else {
                                // soportar lista CSV: 'admin,cliente'
                                $parts = array_map('trim', explode(',', $visible));
                                foreach ($parts as $p) {
                                    if ($p === '') continue;
                                    if ($p === 'user' && $usuario) { $allow = true; break; }
                                    if ($usuario && in_array($p, $userRoleNames, true)) { $allow = true; break; }
                                }
                            }

                            if (!$allow) continue;

                            $href = '#';
                            if (!empty($row['medescripcion'])) {
                                $href = $row['medescripcion'];
                            } elseif (!empty($row['href'])) {
                                $href = $row['href'];
                            }

                            $items[] = [
                                'key' => 'menu_' . $row['idmenu'],
                                'label' => $row['menombre'],
                                'href' => $href,
                                'icon' => isset($row['icon']) ? $row['icon'] : null,
                                'parent' => $row['idpadre'] ?? null,
                            ];
                        }
                    }
        } else {
            // Si no existe tabla menu: fallback anterior (categorías si existen)
            $db = new DataBase();
            $conn = $db->getConnection();

            $stmtCheck = $conn->query("SHOW TABLES LIKE 'categoria'");
            $tableExists = $stmtCheck->fetch();

            if ($tableExists) {
                $stmt = $conn->prepare('SELECT idcategoria, catnombre FROM categoria WHERE catactivo = 1 ORDER BY catorden ASC');
                $stmt->execute();
                $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($cats) {
                    $items = [];
                    foreach ($cats as $cat) {
                        $items[] = [
                            'key' => 'cat_' . $cat['idcategoria'],
                            'label' => $cat['catnombre'],
                            'href' => '?controller=producto&action=listar&categoria=' . $cat['idcategoria'],
                            'icon' => null,
                        ];
                    }
                }
            }
        }

    } catch (Exception $e) {
        // Si falla la conexion o el modelo, devolver menu base
    }

    // Agregar panel admin si el usuario es admin (comodín adicional)
    if ($usuario && class_exists('RoleMiddleware') && RoleMiddleware::esAdmin()) {
        $items[] = ['key' => 'admin', 'label' => 'Panel', 'href' => '?controller=admin&action=dashboard', 'icon' => 'bi-speedometer2'];
    }

    return $items;
}
