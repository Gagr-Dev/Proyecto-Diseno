<?php
function guardarUsuarioBD(PDO $conexion, array $datosUsuario): bool {
    $sql = "INSERT INTO Usuario (nombre_completo, username, password_hash, rol_id) 
            VALUES (:nombre_completo, :username, :password_hash, :rol_id)";
            
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':nombre_completo' => $datosUsuario['nombre_completo'],
        ':username' => $datosUsuario['username'],
        ':password_hash' => $datosUsuario['password_hash'],
        ':rol_id' => $datosUsuario['rol_id']
    ]);
}

// Función: buscar en la base de datos un usuario activo por su username
function buscarUsuarioPorUsername(PDO $conexion, string $username): ?array {
    $sql = "SELECT id, nombre_completo, username, password_hash, rol_id 
            FROM Usuario 
            WHERE username = :username AND activo = 1";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':username' => $username]);
    
    $usuario = $stmt->fetch();
    return $usuario ?: null; // Retorna el array del usuario o null si no existe
}