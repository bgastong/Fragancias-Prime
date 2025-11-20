<?php
require_once __DIR__ . '/app/model/Usuario.php';

$usuarioModel = new Usuario();

$resultado = $usuarioModel->crearUsuario("gaston", "1234", "gaston@test.com");

if (isset($resultado['success']) && $resultado['success']) {
    echo "Usuario creado exitosamente con ID: " . $resultado['id'];
} else {
    echo "Error: " . $resultado['error'];
}