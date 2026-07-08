<?php

function obtenerCombosConDisponibilidad($conexion) {
    try {
        $sqlCombos = "SELECT id, nombre, precio_unidad AS precio, stock_unidades_total AS stock_reservado, estado, tipo_consumo FROM Productos WHERE es_combo = 1 AND estado = 'Activo'";
        $stmtCombos = $conexion->query($sqlCombos);
        $combos = $stmtCombos->fetchAll(PDO::FETCH_ASSOC);

        // A cada combo le calculamos su receta y su potencial de armado
        foreach ($combos as &$combo) {
            $sqlReceta = "SELECT r.producto_id, p.nombre, r.cantidad_necesaria, p.stock_unidades_total 
                          FROM Recetas r
                          JOIN Productos p ON r.producto_id = p.id
                          WHERE r.producto_padre_id = :cid";
            $stmtReceta = $conexion->prepare($sqlReceta);
            $stmtReceta->execute([':cid' => $combo['id']]);
            $receta = $stmtReceta->fetchAll(PDO::FETCH_ASSOC);

            $combo['receta'] = $receta;
            
            // Lógica matemática para saber cuántos combos potenciales se pueden armar
            $potencial = null;
            foreach ($receta as $item) {
                // Dividimos el stock actual entre lo que pide la receta
                $posibles = floor($item['stock_unidades_total'] / $item['cantidad_necesaria']);
                if ($potencial === null || $posibles < $potencial) {
                    $potencial = $posibles; // El límite lo pone el producto del que haya menos cantidad
                }
            }
            $combo['potencial'] = $potencial !== null ? $potencial : 0;
        }
        return $combos;
    } catch (PDOException $e) {
        error_log("Error combos: " . $e->getMessage());
        return [];
    }
}

function obtenerProductosParaReceta($conexion) {
    $stmt = $conexion->query("SELECT id, nombre, stock_unidades_total FROM Productos WHERE estado = 'Activo' ORDER BY nombre ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}