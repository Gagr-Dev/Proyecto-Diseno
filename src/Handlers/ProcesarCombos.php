<?php

function manejarNuevoCombo($datos) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    $conexion = obtenerConexion();
    try {
        $conexion->beginTransaction();
        
        $sqlCombo = "INSERT INTO Productos (categoria_id, nombre, precio_unidad, stock_unidades_total, estado, es_combo, tipo_consumo) VALUES (1, :nombre, :precio, 0, 'Activo', 1, :tipo_consumo)";
        $stmtCombo = $conexion->prepare($sqlCombo);
        $stmtCombo->execute([
            ':nombre' => trim($datos['nombre']),
            ':precio' => (float)$datos['precio'],
            ':tipo_consumo' => $datos['tipo_consumo'] ?? 'Para Llevar'
        ]);
        $comboId = $conexion->lastInsertId();

        $sqlDetalle = "INSERT INTO Recetas (producto_padre_id, producto_id, cantidad_necesaria) VALUES (:cid, :pid, :cant)";
        $stmtDetalle = $conexion->prepare($sqlDetalle);

        $productosIds = $datos['componente_id'] ?? [];
        $cantidades = $datos['componente_cant'] ?? [];

        for ($i = 0; $i < count($productosIds); $i++) {
            if (!empty($productosIds[$i]) && $cantidades[$i] > 0) {
                $stmtDetalle->execute([
                    ':cid' => $comboId,
                    ':pid' => (int)$productosIds[$i],
                    ':cant' => (int)$cantidades[$i]
                ]);
            }
        }
        $conexion->commit();

        // Registrar en bitácora
        require_once __DIR__ . '/../Infrastructure/RepositorioBitacora.php';
        registrarBitacora($conexion, $_SESSION['usuario_id'], 'Combo creado', 'Combos', (int)$comboId, [
            'nombre' => trim($datos['nombre']),
            'precio' => (float)$datos['precio'],
            'componentes' => count($productosIds)
        ]);

        return true;
    } catch (Exception $e) {
        $conexion->rollBack();
        return false;
    }
}
