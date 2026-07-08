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

function obtenerTodoElPersonal(PDO $conexion): array {
    $sql = "SELECT u.id, u.primer_nombre, u.segundo_nombre, u.primer_apellido, u.segundo_apellido,
                   u.cedula, u.username, u.rol_id, u.activo, u.fecha_creacion,
                   r.nombre AS rol_nombre
            FROM Usuario u
            INNER JOIN Rol r ON u.rol_id = r.id
            ORDER BY u.activo DESC, u.primer_nombre ASC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function cambiarEstadoUsuario(PDO $conexion, int $usuarioId, int $nuevoEstado): bool {
    $sql = "UPDATE Usuario SET activo = :activo WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':activo' => $nuevoEstado,
        ':id' => $usuarioId
    ]);
}

function obtenerRoles(PDO $conexion): array {
    $sql = "SELECT id, nombre, descripcion FROM Rol ORDER BY id ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}