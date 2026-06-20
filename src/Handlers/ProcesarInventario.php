<?php
require_once __DIR__ . '/../Infrastructure/Database.php';

function manejarNuevoProducto($datos) {
    $conexion = obtenerConexion();
    
    // Transformar campos vacíos en NULL para los precios opcionales
    $precio_combo = !empty($datos['precio_combo_5']) ? $datos['precio_combo_5'] : null;
    $precio_caja = !empty($datos['precio_caja_36']) ? $datos['precio_caja_36'] : null;
    
    $sql = "INSERT INTO Productos (categoria_id, nombre, stock_unidades_total, precio_unidad, precio_combo_5, precio_caja_36) 
            VALUES (:categoria_id, :nombre, :stock, :precio_unidad, :precio_combo, :precio_caja)";
            
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':categoria_id' => $datos['categoria_id'],
        ':nombre' => $datos['nombre'],
        ':stock' => (int)$datos['stock_inicial'],
        ':precio_unidad' => $datos['precio_unidad'],
        ':precio_combo' => $precio_combo,
        ':precio_caja' => $precio_caja
    ]);
}

function manejarEdicionProducto($datos) {
    $conexion = obtenerConexion();
    
    $precio_combo = !empty($datos['precio_combo_5']) ? $datos['precio_combo_5'] : null;
    $precio_caja = !empty($datos['precio_caja_36']) ? $datos['precio_caja_36'] : null;
    
    $sql = "UPDATE Productos 
            SET categoria_id = :categoria_id, nombre = :nombre, 
                precio_unidad = :precio_unidad, precio_combo_5 = :precio_combo, precio_caja_36 = :precio_caja 
            WHERE id = :id";
            
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':categoria_id' => $datos['categoria_id'],
        ':nombre' => $datos['nombre'],
        ':precio_unidad' => $datos['precio_unidad'],
        ':precio_combo' => $precio_combo,
        ':precio_caja' => $precio_caja,
        ':id' => $datos['producto_id']
    ]);
}

function manejarEntradaStock($datos) {
    $conexion = obtenerConexion();
    
    $cajas = (int)($datos['cantidad_cajas'] ?? 0);
    $unidades = (int)($datos['cantidad_unidades'] ?? 0);
    
    // Cálculo de conversión: 1 caja = 36 unidades
    $total_ingreso = ($cajas * 36) + $unidades;
    
    if ($total_ingreso > 0) {
        $sql = "UPDATE Productos SET stock_unidades_total = stock_unidades_total + :ingreso WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        return $stmt->execute([
            ':ingreso' => $total_ingreso,
            ':id' => $datos['producto_id']
        ]);
    }
    return false;
}

function manejarSalidaStock($datos) {
    $conexion = obtenerConexion();
    
    $cajas = (int)($datos['cantidad_cajas'] ?? 0);
    $unidades = (int)($datos['cantidad_unidades'] ?? 0);
    
    // Cálculo de conversión: 1 caja = 36 unidades
    $total_salida = ($cajas * 36) + $unidades;
    
    if ($total_salida > 0) {
        // GREATEST(0, ...) evita que el inventario quede en negativo
        $sql = "UPDATE Productos 
                SET stock_unidades_total = GREATEST(0, stock_unidades_total - :salida) 
                WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        return $stmt->execute([
            ':salida' => $total_salida,
            ':id' => $datos['producto_id']
        ]);
    }
    return false;
}