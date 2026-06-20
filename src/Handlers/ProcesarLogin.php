<?php

require_once __DIR__ . '/../Domain/AutenticacionReglas.php';
require_once __DIR__ . '/../Infrastructure/Database.php';
require_once __DIR__ . '/../Infrastructure/RepositorioUsuario.php';

function manejarLogin(array $postData): array {
    $username = trim($postData['username'] ?? '');
    $password = $postData['password'] ?? '';

    if (empty($username) || empty($password)) {
        return ['exito' => false, 'mensaje' => 'Por favor, completa todos los campos.'];
    }

    $conexion = obtenerConexion();
    
    
    $usuarioBD = buscarUsuarioPorUsername($conexion, $username);

    if (!$usuarioBD) {
        return ['exito' => false, 'mensaje' => 'Usuario no encontrado o inactivo.'];
    }

   
    if (!verificarPassword($password, $usuarioBD['password_hash'])) {
        return ['exito' => false, 'mensaje' => 'Contraseña incorrecta.'];
    }

    
    $_SESSION['usuario_id'] = $usuarioBD['id'];
    $_SESSION['primer_nombre'] = $usuarioBD['primer_nombre'];
    $_SESSION['primer_apellido'] = $usuarioBD['primer_apellido'];
    $_SESSION['rol_id'] = $usuarioBD['rol_id'];

    return ['exito' => true, 'mensaje' => 'Login exitoso.'];
}