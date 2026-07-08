<?php

// =========================================================
// FUNCIONES AYUDANTES PARA CALCULAR EL COSTO DE ADQUISICIÓN
// =========================================================

function obtenerCostoPromedioProducto($conexion, $productoId) {
    // 1. Intentar obtener el promedio de las compras al proveedor
    $stmt = $conexion->prepare("SELECT AVG(costo_unitario) FROM Movimiento_Inventario WHERE producto_id = :pid AND tipo_movimiento = 'Compra_Proveedor' AND costo_unitario > 0");
    $stmt->execute([':pid' => $productoId]);
    $costo = (float)$stmt->fetchColumn();
    
    if ($costo > 0) return $costo;
    
    // 2. Si no hay compras, buscar cualquier entrada de stock que tenga costo
    $stmt2 = $conexion->prepare("SELECT costo_unitario FROM Movimiento_Inventario WHERE producto_id = :pid AND tipo_movimiento = 'Entrada' AND costo_unitario > 0 ORDER BY id DESC LIMIT 1");
    $stmt2->execute([':pid' => $productoId]);
    $costo2 = (float)$stmt2->fetchColumn();
    
    if ($costo2 > 0) return $costo2;
    
    // 3. Fallback: 60% del precio de venta (asumiendo 40% de margen)
    $stmt3 = $conexion->prepare("SELECT precio_unidad FROM Productos WHERE id = :pid");
    $stmt3->execute([':pid' => $productoId]);
    $precio = (float)$stmt3->fetchColumn();
    
    return $precio * 0.60;
}

function obtenerCostoPromedioCombo($conexion, $comboId) {
    // Calcula el costo de un combo sumando el costo de las botellas que lo conforman
    $stmt = $conexion->prepare("SELECT producto_id, cantidad_necesaria FROM Recetas WHERE producto_padre_id = :cid");
    $stmt->execute([':cid' => $comboId]);
    $receta = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $costoTotal = 0;
    foreach ($receta as $item) {
        $costoTotal += obtenerCostoPromedioProducto($conexion, $item['producto_id']) * $item['cantidad_necesaria'];
    }
    return $costoTotal;
}

// =========================================================
// CACHÉ DE COSTOS (Pre-carga todos los costos en 1 query)
// =========================================================

function obtenerCacheCostos($conexion) {
    $cache = [];
    
    // 1. Costos promedio de compras al proveedor (1 sola query para todos)
    $sql = "SELECT producto_id, AVG(costo_unitario) as costo_avg
            FROM Movimiento_Inventario 
            WHERE tipo_movimiento = 'Compra_Proveedor' AND costo_unitario > 0
            GROUP BY producto_id";
    foreach ($conexion->query($sql) as $row) {
        $cache[(int)$row['producto_id']] = (float)$row['costo_avg'];
    }
    
    // 2. Para productos sin compras registradas, usar 60% del precio de venta
    $sql2 = "SELECT id, precio_unidad, es_combo FROM Productos WHERE estado = 'Activo'";
    foreach ($conexion->query($sql2) as $row) {
        $pid = (int)$row['id'];
        if (!isset($cache[$pid]) && empty($row['es_combo'])) {
            $cache[$pid] = (float)$row['precio_unidad'] * 0.60;
        }
    }
    
    // 3. Costos de combos (basado en recetas)
    $sqlCombos = "SELECT r.producto_padre_id, SUM(IFNULL(c.costo, p.precio_unidad * 0.60) * r.cantidad_necesaria) as costo_combo
                  FROM Recetas r
                  JOIN Productos p ON r.producto_id = p.id
                  LEFT JOIN (
                      SELECT producto_id, AVG(costo_unitario) as costo
                      FROM Movimiento_Inventario 
                      WHERE tipo_movimiento = 'Compra_Proveedor' AND costo_unitario > 0
                      GROUP BY producto_id
                  ) c ON r.producto_id = c.producto_id
                  GROUP BY r.producto_padre_id";
    foreach ($conexion->query($sqlCombos) as $row) {
        $cache[(int)$row['producto_padre_id']] = (float)$row['costo_combo'];
    }
    
    return $cache;
}

function obtenerCostoDesdeCacheado($cache, $productoId) {
    return $cache[(int)$productoId] ?? 0;
}

// =========================================================
// REPORTES PRINCIPALES
// =========================================================

function obtenerResumenContabilidad($conexion) {
    $resumen = [
        'inversion_compras' => 0.00, // Dinero invertido en stock (No resta ganancia)
        'costo_ventas' => 0.00,      // Costo de la mercancía que YA se vendió
        'merma_cantidad' => 0,
        'merma_costo' => 0.00,       // Pérdida pura
        'ingresos_totales' => 0.00,
        'impuestos_estimados' => 0.00,
        'ganancia_neta' => 0.00,
        'futura_ganancia' => 0.00
    ];

    try {
        // Pre-cargar caché de costos (1 vez, pocas queries)
        $cache = obtenerCacheCostos($conexion);

        // 1. Inversión en Compras (Informativo, es dinero en los estantes)
        $sqlGasto = "SELECT IFNULL(SUM(cantidad_unidades * costo_unitario), 0) FROM Movimiento_Inventario WHERE tipo_movimiento = 'Compra_Proveedor'";
        $resumen['inversion_compras'] = (float) $conexion->query($sqlGasto)->fetchColumn();

        // 2. Mermas (Pérdidas Reales)
        $sqlMerma = "SELECT IFNULL(SUM(cantidad_unidades), 0) as cantidad, IFNULL(SUM(cantidad_unidades * costo_unitario), 0) as costo FROM Movimiento_Inventario WHERE tipo_movimiento = 'Ajuste_Merma'";
        $datosMerma = $conexion->query($sqlMerma)->fetch(PDO::FETCH_ASSOC);
        $resumen['merma_cantidad'] = (int) $datosMerma['cantidad'];
        $resumen['merma_costo'] = (float) $datosMerma['costo'];

        // 3. Ingresos Totales y Costo de Ventas (COGS) — agregado por producto
        $sqlVentas = "SELECT dv.producto_id, SUM(dv.cantidad) as total_cant, SUM(dv.subtotal) as total_sub
                      FROM Detalle_Venta dv
                      JOIN Productos p ON dv.producto_id = p.id
                      GROUP BY dv.producto_id";
        foreach ($conexion->query($sqlVentas) as $row) {
            $resumen['ingresos_totales'] += (float)$row['total_sub'];
            $costoUnit = obtenerCostoDesdeCacheado($cache, $row['producto_id']);
            $resumen['costo_ventas'] += $costoUnit * (int)$row['total_cant'];
        }

        // 4. Impuestos
        if ($resumen['ingresos_totales'] > 0) {
            $resumen['impuestos_estimados'] = $resumen['ingresos_totales'] - ($resumen['ingresos_totales'] / 1.16);
        }

        // 5. Ganancia Neta Real (Ingresos - Costo Adquisición - Mermas)
        $resumen['ganancia_neta'] = $resumen['ingresos_totales'] - $resumen['costo_ventas'] - $resumen['merma_costo'];

        // 6. Futura Ganancia
        $sqlFutura = "SELECT IFNULL(SUM(stock_unidades_total * precio_unidad), 0) FROM Productos WHERE estado = 'Activo'";
        $resumen['futura_ganancia'] = (float) $conexion->query($sqlFutura)->fetchColumn();

        return $resumen;
    } catch (PDOException $e) {
        error_log("Error Resumen Contabilidad: " . $e->getMessage());
        return $resumen;
    }
}

function obtenerRegistroMensual($conexion) {
    try {
        // Pre-cargar caché de costos
        $cache = obtenerCacheCostos($conexion);
        $meses = []; 

        // Sumar Mermas por Mes
        $sqlMermas = "SELECT DATE_FORMAT(fecha_hora, '%Y-%m') AS mes, SUM(cantidad_unidades * costo_unitario) as mermas
                      FROM Movimiento_Inventario WHERE tipo_movimiento = 'Ajuste_Merma' GROUP BY mes";
        foreach ($conexion->query($sqlMermas) as $row) {
            $mes = $row['mes'];
            if (!isset($meses[$mes])) $meses[$mes] = ['ingresos' => 0, 'costo_ventas' => 0, 'mermas' => 0];
            $meses[$mes]['mermas'] += (float)$row['mermas'];
        }

        // Sumar Ingresos y Costo de Ventas por Mes — agregado por (mes, producto)
        $sqlVentas = "SELECT DATE_FORMAT(v.fecha_hora, '%Y-%m') AS mes, dv.producto_id, 
                             SUM(dv.cantidad) as total_cant, SUM(dv.subtotal) as total_sub
                      FROM Detalle_Venta dv
                      JOIN Venta v ON dv.venta_id = v.id
                      JOIN Productos p ON dv.producto_id = p.id
                      WHERE v.fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                      GROUP BY mes, dv.producto_id";
        foreach ($conexion->query($sqlVentas) as $row) {
            $mes = $row['mes'];
            if (!isset($meses[$mes])) $meses[$mes] = ['ingresos' => 0, 'costo_ventas' => 0, 'mermas' => 0];
            $meses[$mes]['ingresos'] += (float)$row['total_sub'];
            $costoUnit = obtenerCostoDesdeCacheado($cache, $row['producto_id']);
            $meses[$mes]['costo_ventas'] += $costoUnit * (int)$row['total_cant'];
        }

        // Formatear el array final
        $resultado = [];
        foreach ($meses as $mes => $datos) {
            $resultado[] = [
                'mes' => $mes,
                'ingresos' => $datos['ingresos'],
                'costo_ventas' => $datos['costo_ventas'],
                'mermas' => $datos['mermas'],
                'ganancia_neta' => $datos['ingresos'] - $datos['costo_ventas'] - $datos['mermas']
            ];
        }
        usort($resultado, function($a, $b) { return strcmp($b['mes'], $a['mes']); });
        return array_slice($resultado, 0, 12); // Últimos 12 meses máximo
    } catch (PDOException $e) {
        error_log("Error Registro Mensual: " . $e->getMessage());
        return [];
    }
}

function obtenerHistorialMovimientos($conexion) {
    try {
        // Pre-cargar caché de costos
        $cache = obtenerCacheCostos($conexion);
        $historial = [];

        // 1. Mermas y Compras (últimas 25)
        $sqlMov = "SELECT m.id, m.fecha_hora, m.tipo_movimiento, m.cantidad_unidades, m.costo_unitario, m.producto_id,
                          p.nombre AS prod_nombre
                   FROM Movimiento_Inventario m
                   LEFT JOIN Productos p ON m.producto_id = p.id
                   WHERE m.tipo_movimiento IN ('Compra_Proveedor', 'Ajuste_Merma')
                   ORDER BY m.fecha_hora DESC LIMIT 25";
        foreach ($conexion->query($sqlMov) as $row) {
            $monto = $row['cantidad_unidades'] * $row['costo_unitario'];
            $historial[] = [
                'id' => $row['id'],
                'fecha_hora' => $row['fecha_hora'],
                'tipo_movimiento' => $row['tipo_movimiento'],
                'producto_nombre' => $row['prod_nombre'] ?? 'Desconocido',
                'cantidad' => $row['cantidad_unidades'],
                'monto_total' => $monto,
                'ganancia' => ($row['tipo_movimiento'] == 'Ajuste_Merma') ? -$monto : 0, 
                'producto_id' => $row['producto_id']
            ];
        }

        // 2. Ventas (últimas 25, usando caché)
        $sqlVentas = "SELECT dv.id, v.fecha_hora, dv.cantidad, dv.subtotal, dv.producto_id, p.es_combo,
                             p.nombre AS prod_nombre,
                             v.metodo_pago, v.referencia_pago, v.telefono_pago
                      FROM Detalle_Venta dv
                      JOIN Venta v ON dv.venta_id = v.id
                      LEFT JOIN Productos p ON dv.producto_id = p.id
                      ORDER BY v.fecha_hora DESC LIMIT 25";
        foreach ($conexion->query($sqlVentas) as $row) {
            $costoUnit = obtenerCostoDesdeCacheado($cache, $row['producto_id']);
            $costoBase = $costoUnit * $row['cantidad'];

            $historial[] = [
                'id' => 'v_'.$row['id'], // ID virtual para evitar cruces en el modal
                'fecha_hora' => $row['fecha_hora'],
                'tipo_movimiento' => 'Venta',
                'producto_nombre' => $row['prod_nombre'] ?? 'Desconocido',
                'cantidad' => $row['cantidad'],
                'monto_total' => (float)$row['subtotal'], 
                'ganancia' => (float)$row['subtotal'] - $costoBase,
                'producto_id' => $row['producto_id'],
                'metodo_pago' => $row['metodo_pago'],
                'referencia_pago' => $row['referencia_pago'],
                'telefono_pago' => $row['telefono_pago']
            ];
        }

        // Unir, ordenar y retornar los últimos 50
        usort($historial, function($a, $b) { return strtotime($b['fecha_hora']) - strtotime($a['fecha_hora']); });
        return array_slice($historial, 0, 50);

    } catch (PDOException $e) {
        error_log("Error Historial: " . $e->getMessage());
        return [];
    }
}

function obtenerProductosParaContabilidad($conexion) {
    try {
        $sql = "SELECT id, nombre FROM Productos WHERE estado = 'Activo' ORDER BY nombre ASC";
        $stmt = $conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return [];
    }
}