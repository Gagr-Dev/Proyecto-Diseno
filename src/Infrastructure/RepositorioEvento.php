<?php


function guardarEventoGlobal(PDO $conexion, array $datos): bool {
    $sql = "INSERT INTO Evento (nombre, fecha_inicio, fecha_fin, descripcion, estado) 
            VALUES (:nombre, :fecha_inicio, :fecha_fin, :descripcion, 'Programado')";
    $stmt = $conexion->prepare($sql);
    
    return $stmt->execute([
        ':nombre' => $datos['nombre'],
        ':fecha_inicio' => $datos['fecha_inicio'],
        ':fecha_fin' => $datos['fecha_fin'],
        ':descripcion' => $datos['descripcion'] ?? ''
    ]);
}

function obtenerEventosGenerales(PDO $conexion): array {
    // 1. Auto-actualizar estados según la fecha y hora del servidor de Base de Datos.
    // Ignoramos los eventos que ya fueron "Cancelados" manualmente.
    $sqlActualizar = "UPDATE Evento 
                      SET estado = CASE 
                          WHEN NOW() < fecha_inicio THEN 'Programado'
                          WHEN NOW() BETWEEN fecha_inicio AND fecha_fin THEN 'En Curso'
                          WHEN NOW() > fecha_fin THEN 'Finalizado'
                      END 
                      WHERE estado != 'Cancelado'";
    $conexion->query($sqlActualizar);

    // 2. Extraer la lista de eventos actualizada
    $sql = "SELECT * FROM Evento ORDER BY fecha_inicio ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function cancelarEventoGlobal(PDO $conexion, int $idEvento): bool {
    $sql = "UPDATE Evento SET estado = 'Cancelado' WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([':id' => $idEvento]);
}