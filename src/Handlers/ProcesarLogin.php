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
    
    // 1. Buscamos al usuario en la BD 
    $usuarioBD = buscarUsuarioPorUsername($conexion, $username);

    if (!$usuarioBD) {
        return ['exito' => false, 'mensaje' => 'Usuario no encontrado o inactivo.'];
    }

    // 2. Verificamos la contraseña 
    if (!verificarPassword($password, $usuarioBD['password_hash'])) {
        return ['exito' => false, 'mensaje' => 'Contraseña incorrecta.'];
    }

    // 3. Todo está bien, iniciamos la sesión y guardamos los datos del usuario
    session_start();
    $_SESSION['usuario_id'] = $usuarioBD['id'];
    $_SESSION['nombre'] = $usuarioBD['nombre_completo'];
    $_SESSION['rol_id'] = $usuarioBD['rol_id'];

    return ['exito' => true, 'mensaje' => 'Login exitoso.'];
}