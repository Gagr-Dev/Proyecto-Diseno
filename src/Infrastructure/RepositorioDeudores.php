<?php

/**
 * Obtiene la lista de deudores y calcula su deuda total pendiente.
 * @param PDO $conexion
 * @return array
 */
function obtenerTodosLosDeudores($conexion) {
    try {
        // Hacemos un LEFT JOIN con Cuentas_Por_Cobrar para sumar los montos pendientes
        $sql = "SELECT d.id, d.nombre_completo, d.cedula, d.telefono, 
                       IFNULL(SUM(c.monto), 0) AS deuda_total
                FROM Deudores d
                LEFT JOIN Cuentas_Por_Cobrar c ON d.id = c.deudor_id AND c.estado = 'Pendiente'
                GROUP BY d.id, d.nombre_completo, d.cedula, d.telefono
                ORDER BY d.nombre_completo ASC";
                
        $stmt = $conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener deudores: " . $e->getMessage());
        return [];
    }
}