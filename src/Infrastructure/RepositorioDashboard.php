<?php

/**
 * RepositorioDashboard.php
 * Funciones para obtener datos resumidos del sistema para el Panel de Control.
 * 
 * Nota: Las acciones del Super Administrador (rol_id=1) se ocultan
 * SIEMPRE en el dashboard para todos los usuarios, manteniendo la trazabilidad privada.
 */

/**
 * Obtiene todas las métricas necesarias para el dashboard.
 * @param PDO $conexion  Conexión activa a la base de datos.
 */
function obtenerResumenDashboard(PDO $conexion): array {
    $resumen = [
        'ventas_hoy_monto'    => 0.00,
        'ventas_hoy_cantidad' => 0,
        'stock_bajo'          => [],
        'eventos_activos'     => 0,
        'torneos_activos'     => 0,
        'deuda_pendiente'     => 0.00,
        'ultimas_acciones'    => [],
        'personal_activo'     => 0,
        'personal_inactivo'   => 0,
        'ventas_semana'       => [],
    ];

    try {
        // 1. Ventas del día (monto total y cantidad de transacciones)
        $sqlVentas = "SELECT IFNULL(SUM(total), 0) AS monto, COUNT(*) AS cantidad
                      FROM Venta
                      WHERE DATE(fecha_hora) = CURDATE()";
        $datosVentas = $conexion->query($sqlVentas)->fetch(PDO::FETCH_ASSOC);
        $resumen['ventas_hoy_monto']    = (float) $datosVentas['monto'];
        $resumen['ventas_hoy_cantidad'] = (int) $datosVentas['cantidad'];

        // 2. Productos con stock bajo (≤ 5 unidades, solo activos y no combos)
        $sqlStock = "SELECT id, nombre, stock_unidades_total
                     FROM Productos
                     WHERE estado = 'Activo' AND (es_combo = 0 OR es_combo IS NULL) AND stock_unidades_total <= 5
                     ORDER BY stock_unidades_total ASC
                     LIMIT 10";
        $resumen['stock_bajo'] = $conexion->query($sqlStock)->fetchAll(PDO::FETCH_ASSOC);

        // 3. Eventos activos (Programados + En Curso)
        $sqlEventos = "SELECT COUNT(*) FROM Evento WHERE estado IN ('Programado', 'En Curso')";
        $resumen['eventos_activos'] = (int) $conexion->query($sqlEventos)->fetchColumn();

        // 4. Torneos activos (Pendiente + En Curso)
        $sqlTorneos = "SELECT COUNT(*) FROM Torneo WHERE estado IN ('Pendiente', 'En Curso')";
        $resumen['torneos_activos'] = (int) $conexion->query($sqlTorneos)->fetchColumn();

        // 5. Deuda pendiente total
        $sqlDeuda = "SELECT IFNULL(SUM(monto), 0) FROM Cuentas_Por_Cobrar WHERE estado = 'Pendiente'";
        $resumen['deuda_pendiente'] = (float) $conexion->query($sqlDeuda)->fetchColumn();

        // 6. Últimas 5 acciones de la bitácora
        //    SIEMPRE ocultar las acciones del Super Admin (rol_id=1)
        $sqlBitacora = "SELECT b.accion, b.tabla_afectada, b.fecha_hora,
                               CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS nombre_empleado
                        FROM Bitacora_Sistema b
                        LEFT JOIN Usuario u ON b.usuario_id = u.id
                        WHERE (u.rol_id != 1 OR u.rol_id IS NULL)
                        ORDER BY b.fecha_hora DESC
                        LIMIT 5";
        $resumen['ultimas_acciones'] = $conexion->query($sqlBitacora)->fetchAll(PDO::FETCH_ASSOC);

        // 7. Personal activo/inactivo (excluir Super Admins siempre)
        $sqlPersonal = "SELECT 
                            SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) AS activos,
                            SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) AS inactivos
                        FROM Usuario
                        WHERE rol_id != 1";
        $datosPersonal = $conexion->query($sqlPersonal)->fetch(PDO::FETCH_ASSOC);
        $resumen['personal_activo']   = (int) ($datosPersonal['activos'] ?? 0);
        $resumen['personal_inactivo'] = (int) ($datosPersonal['inactivos'] ?? 0);

    } catch (PDOException $e) {
        error_log("Error Resumen Dashboard: " . $e->getMessage());
    }

    return $resumen;
}
