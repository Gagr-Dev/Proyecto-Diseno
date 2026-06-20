<?php
require_once __DIR__ . '/../Domain/UsuarioReglas.php';
require_once __DIR__ . '/../Infrastructure/Database.php';
require_once __DIR__ . '/../Infrastructure/RepositorioUsuario.php';

function manejarRegistroUsuario(array $postData) {
    // 1. Valida Datos
    $errores = validarDatosRegistro($postData);
    
    if (!empty($errores)) {
        
        return ['exito' => false, 'mensajes' => $errores];
    }
    
    
    $datosLimpios = prepararUsuarioParaBD($postData);
    
    
    $conexion = obtenerConexion();
    try {
        guardarUsuarioBD($conexion, $datosLimpios);
        return ['exito' => true, 'mensajes' => ["Usuario registrado exitosamente."]];
    } catch (PDOException $e) {
        
        if ($e->getCode() == 23000) {
            return ['exito' => false, 'mensajes' => ["La cédula o el nombre de usuario ya están registrados en el sistema."]];
        }
        
        return ['exito' => false, 'mensajes' => ["Error interno del servidor."]];
    }
}