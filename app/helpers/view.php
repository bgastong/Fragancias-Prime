<?php
// Helper para renderizar vistas. El controlador debe preparar los datos
// del header (por ejemplo llamando a `obtenerDatosHeaderDesdeBD`) y pasarlos
// a la vista en la variable `datosHeader`.

function render($viewPath, $vars = [])
{
    // Extraer variables para la vista
    if (is_array($vars) && !empty($vars)) {
        extract($vars, EXTR_OVERWRITE);
    }
    // Incluir la vista
    require $viewPath;
}
