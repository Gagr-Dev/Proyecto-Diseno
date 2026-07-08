<?php

function obtenerProductosActivosPOS($conexion) {
    try {
        // Unimos productos normales y combos en una sola lista para el frontend
        $sql = "
            SELECT id, nombre, precio_unidad, stock_unidades_total AS stock, 'producto' AS tipo 
            FROM Productos 
            WHERE estado = 'Activo' AND es_combo = 0 AND stock_unidades_total > 0
            
            UNION ALL
            
            SELECT p.id, p.nombre, p.precio_unidad, 
                   IFNULL(MIN(FLOOR(ing.stock_unidades_total / r.cantidad_necesaria)), 0) AS stock, 
                   'combo' AS tipo 
            FROM Productos p
            JOIN Recetas r ON p.id = r.producto_padre_id
            JOIN Productos ing ON r.producto_id = ing.id
            WHERE p.estado = 'Activo' AND p.es_combo = 1
            GROUP BY p.id
            HAVING stock > 0
            
            ORDER BY nombre ASC
        ";
        $stmt = $conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al cargar productos POS: " . $e->getMessage());
        return [];
    }
}