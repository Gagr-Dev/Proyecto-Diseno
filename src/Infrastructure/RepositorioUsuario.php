<?php
function guardarUsuarioBD(PDO $conexion, array $datosUsuario): bool {
    $sql = "INSERT INTO Usuario 
            (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, username, password_hash, rol_id) 
            VALUES 
            (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :cedula, :username, :password_hash, :rol_id)";
            
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':primer_nombre' => $datosUsuario['primer_nombre'],
        ':segundo_nombre' => $datosUsuario['segundo_nombre'],
        ':primer_apellido' => $datosUsuario['primer_apellido'],
        ':segundo_apellido' => $datosUsuario['segundo_apellido'],
        ':cedula' => $datosUsuario['cedula'],
        ':username' => $datosUsuario['username'],
        ':password_hash' => $datosUsuario['password_hash'],
        ':rol_id' => $datosUsuario['rol_id']
    ]);
}

function buscarUsuarioPorUsername(PDO $conexion, string $username): ?array {
    
    $sql = "SELECT id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, username, password_hash, rol_id 
            FROM Usuario 
            WHERE username = :username AND activo = 1";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':username' => $username]);
    
    $usuario = $stmt->fetch();
    return $usuario ?: null; 
}