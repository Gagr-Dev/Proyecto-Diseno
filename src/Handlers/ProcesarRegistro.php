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

        // Registrar en bitácora
        require_once __DIR__ . '/../Infrastructure/RepositorioBitacora.php';
        $nuevoId = (int)$conexion->lastInsertId();
        registrarBitacora($conexion, $_SESSION['usuario_id'] ?? $nuevoId, 'Personal registrado', 'Usuario', $nuevoId, [
            'nombre' => trim($postData['primer_nombre'] ?? '') . ' ' . trim($postData['primer_apellido'] ?? ''),
            'username' => trim($postData['username'] ?? '')
        ]);

        return ['exito' => true, 'mensajes' => ["Usuario registrado exitosamente."]];
    } catch (PDOException $e) {
        
        if ($e->getCode() == 23000) {
            return ['exito' => false, 'mensajes' => ["La cédula o el nombre de usuario ya están registrados en el sistema."]];
        }
        
        return ['exito' => false, 'mensajes' => ["Error interno del servidor."]];
    }
}