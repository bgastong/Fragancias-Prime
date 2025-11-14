<?php
require_once __DIR__ . '/app/model/Usuario.php';

$usuarioModel = new Usuario();

$id = $usuarioModel->crearUsuario("gaston", "1234", "gaston@test.com");

echo "Usuario creado con ID: " . $id;