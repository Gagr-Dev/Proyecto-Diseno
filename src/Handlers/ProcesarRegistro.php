<?php
require_once __DIR__ . '/../Domain/UsuarioReglas.php';
require_once __DIR__ . '/../Infrastructure/Database.php';
require_once __DIR__ . '/../Infrastructure/RepositorioUsuario.php';

function manejarRegistroUsuario(array $postData) {
    // 1. Validar (Mundo Puro)
    $errores = validarDatosRegistro($postData);
    
    if (!empty($errores)) {
        // Retornamos los errores a la vista
        return ['exito' => false, 'mensajes' => $errores];
    }
    
    // 2. Preparar Datos
    $datosLimpios = prepararUsuarioParaBD($postData);
    
    // 3. Guardar en BD 
    $conexion = obtenerConexion();
    try {
        guardarUsuarioBD($conexion, $datosLimpios);
        return ['exito' => true, 'mensajes' => ["Usuario registrado exitosamente."]];
    } catch (PDOException $e) {
        // Si el usuario ya existe (por la restricción UNIQUE en SQL)
        if ($e->getCode() == 23000) {
            return ['exito' => false, 'mensajes' => ["El nombre de usuario ya está en uso."]];
        }
        return ['exito' => false, 'mensajes' => ["Error interno del servidor."]];
    }
}