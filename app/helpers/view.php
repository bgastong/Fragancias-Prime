<?php
// Helper para renderizar vistas. El controlador debe preparar los datos
// del header (por ejemplo llamando a `obtenerDatosHeaderDesdeBD`) y pasarlos
// a la vista en la variable `datosHeader`.

if (!function_exists('base_url')) {
    function base_url()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptName = str_replace('\\', '/', $scriptName);
        $base = preg_replace('#/[^/]*$#', '', $scriptName);
        $base = rtrim((string) $base, '/');

        return $base === '' ? '/' : $base;
    }
}

if (!function_exists('asset_url')) {
    function asset_url($path)
    {
        $path = ltrim((string) $path, '/');
        $base = base_url();

        return $base === '/' ? '/' . $path : $base . '/' . $path;
    }
}

if (!function_exists('public_asset_url_if_exists')) {
    function public_asset_url_if_exists($path)
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, 'data:image/')) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^.*?/public/#', '', $path);
        $path = ltrim($path, '/');

        $candidatos = [];
        if (str_starts_with($path, 'img/') || str_starts_with($path, 'upload/')) {
            $candidatos[] = $path;
        } else {
            $candidatos[] = 'upload/productos/' . basename($path);
            $candidatos[] = 'img/' . basename($path);
        }

        foreach (array_unique($candidatos) as $candidato) {
            if (file_exists(ROOT_PATH . '/public/' . $candidato)) {
                return asset_url($candidato);
            }
        }

        $fileName = basename($path);
        if ($fileName !== '') {
            $imagenesDir = ROOT_PATH . '/public/img';
            if (is_dir($imagenesDir)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imagenesDir));
                foreach ($iterator as $file) {
                    if ($file->isFile() && strcasecmp($file->getFilename(), $fileName) === 0) {
                        $rutaPublica = str_replace('\\', '/', substr($file->getPathname(), strlen(ROOT_PATH . '/public/') ));
                        return asset_url($rutaPublica);
                    }
                }
            }
        }

        return null;
    }
}

if (!function_exists('placeholder_image_url')) {
    function placeholder_image_url($label = 'Imagen no disponible')
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">'
            . '<rect width="800" height="600" fill="#f3f4f6"/>'
            . '<rect x="180" y="120" width="440" height="300" rx="24" fill="#e5e7eb"/>'
            . '<circle cx="320" cy="240" r="44" fill="#cbd5e1"/>'
            . '<path d="M250 390l78-84 58 60 42-44 126 68H250z" fill="#cbd5e1"/>'
            . '<text x="400" y="500" text-anchor="middle" fill="#6b7280" font-family="Arial, sans-serif" font-size="28">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
    }
}

if (!function_exists('product_image_url')) {
    function product_image_url($producto, $fallbackLabel = 'Imagen no disponible')
    {
        if (is_array($producto) && !empty($producto['imagen'])) {
            $url = public_asset_url_if_exists($producto['imagen']);
            if ($url !== null) {
                return $url;
            }
        }

        if (is_array($producto) && !empty($producto['pronombre'])) {
            $url = public_asset_url_if_exists($producto['pronombre']);
            if ($url !== null) {
                return $url;
            }
        }

        if (is_array($producto)) {
            $texto = strtolower(trim(implode(' ', [
                (string) ($producto['subtitulo'] ?? ''),
                (string) ($producto['prodetalle'] ?? ''),
                (string) ($producto['descripcion'] ?? ''),
            ])));

            $candidatos = [];
            if (str_contains($texto, 'azzaro')) {
                $candidatos = [
                    'img/Azzaro/azzaro-pour-homme.webp',
                    'img/Azzaro/azzaro-azul.avif',
                    'img/Azzaro/azzaro-gris.jpg',
                    'img/Azzaro/azzaro-marron.jpg',
                    'img/Azzaro/portada azzaro.jpg',
                ];
            }

            foreach ($candidatos as $candidato) {
                $rutaFisica = ROOT_PATH . '/public/' . $candidato;
                if (file_exists($rutaFisica)) {
                    return asset_url($candidato);
                }
            }
        }

        return placeholder_image_url($fallbackLabel);
    }
}

function render($viewPath, $vars = [])
{
    // Extraer variables para la vista
    if (is_array($vars) && !empty($vars)) {
        extract($vars, EXTR_OVERWRITE);
    }
    // Incluir la vista
    require $viewPath;
}
