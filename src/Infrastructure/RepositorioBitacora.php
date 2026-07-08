<?php

/**
 * RepositorioBitacora.php
 * Funciones centrales para el registro y consulta de la Bitácora del Sistema (Auditoría).
 */

/**
 * Registra una acción en la bitácora del sistema.
 *
 * @param PDO    $conexion      Conexión PDO activa.
 * @param int    $usuarioId     ID del usuario que ejecuta la acción.
 * @param string $accion        Descripción corta de la acción (ej: "Producto creado").
 * @param string $tablaAfectada Nombre de la tabla afectada (ej: "Productos").
 * @param int|null $registroId  ID del registro afectado (nullable).
 * @param array  $detalles      Detalles adicionales en formato asociativo (se guarda como JSON).
 */
function registrarBitacora(PDO $conexion, int $usuarioId, string $accion, ?string $tablaAfectada = null, ?int $registroId = null, array $detalles = []): void {
    try {
        $sql = "INSERT INTO Bitacora_Sistema (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_direccion)
                VALUES (:usuario_id, :accion, :tabla_afectada, :registro_id, :detalles, :ip)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':usuario_id'     => $usuarioId,
            ':accion'         => $accion,
            ':tabla_afectada' => $tablaAfectada,
            ':registro_id'    => $registroId,
            ':detalles'       => !empty($detalles) ? json_encode($detalles, JSON_UNESCAPED_UNICODE) : null,
            ':ip'             => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (PDOException $e) {
        // La bitácora nunca debe romper la operación principal
        error_log("Error al registrar bitácora: " . $e->getMessage());
    }
}

/**
 * Obtiene los registros de la bitácora con filtros opcionales.
 *
 * @param PDO   $conexion Conexión PDO activa.
 * @param array $filtros  Filtros opcionales: fecha_desde, fecha_hasta, usuario_id, modulo.
 * @param int   $limite   Cantidad máxima de registros a devolver.
 * @return array Lista de registros de auditoría.
 */
function obtenerBitacora(PDO $conexion, array $filtros = [], int $limite = 200): array {
    $where = [];
    $params = [];

    if (!empty($filtros['fecha_desde'])) {
        $where[] = "b.fecha_hora >= :fecha_desde";
        $params[':fecha_desde'] = $filtros['fecha_desde'] . ' 00:00:00';
    }

    if (!empty($filtros['fecha_hasta'])) {
        $where[] = "b.fecha_hora <= :fecha_hasta";
        $params[':fecha_hasta'] = $filtros['fecha_hasta'] . ' 23:59:59';
    }

    if (!empty($filtros['usuario_id'])) {
        $where[] = "b.usuario_id = :usuario_id";
        $params[':usuario_id'] = (int)$filtros['usuario_id'];
    }

    if (!empty($filtros['modulo'])) {
        $where[] = "b.tabla_afectada = :modulo";
        $params[':modulo'] = $filtros['modulo'];
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT b.*, 
                   CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS nombre_empleado,
                   u.username,
                   r.nombre AS rol_nombre
            FROM Bitacora_Sistema b
            LEFT JOIN Usuario u ON b.usuario_id = u.id
            LEFT JOIN Rol r ON u.rol_id = r.id
            {$whereClause}
            ORDER BY b.fecha_hora DESC
            LIMIT :limite";

    $stmt = $conexion->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene la lista de empleados que tienen registros en la bitácora (para filtro).
 */
function obtenerEmpleadosBitacora(PDO $conexion): array {
    $sql = "SELECT DISTINCT u.id, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS nombre_completo
            FROM Bitacora_Sistema b
            INNER JOIN Usuario u ON b.usuario_id = u.id
            ORDER BY nombre_completo ASC";
    return $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene las tablas únicas registradas en la bitácora (para filtro de módulo).
 */
function obtenerModulosBitacora(PDO $conexion): array {
    $sql = "SELECT DISTINCT tabla_afectada FROM Bitacora_Sistema WHERE tabla_afectada IS NOT NULL ORDER BY tabla_afectada ASC";
    return $conexion->query($sql)->fetchAll(PDO::FETCH_COLUMN);
}
