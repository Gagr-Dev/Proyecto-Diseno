<?php

function procesarVentaPOS($datos, $usuarioId) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    require_once __DIR__ . '/../Infrastructure/RepositorioContabilidad.php';
    $conexion = obtenerConexion();

    $carrito = json_decode($datos['carrito_json'], true);
    $totalVenta = (float)$datos['total_venta'];

    if (empty($carrito) || $totalVenta <= 0) return false;

    try {
        $conexion->beginTransaction();

        $metodoPago = $datos['metodo_pago'] ?? 'Efectivo';
        $referenciaPago = !empty($datos['referencia_pago']) ? $datos['referencia_pago'] : null;
        $telefonoPago = !empty($datos['telefono_pago']) ? $datos['telefono_pago'] : null;

        // 1. Crear el ticket maestro
        $sqlVenta = "INSERT INTO Venta (usuario_id, total, metodo_pago, referencia_pago, telefono_pago) VALUES (:uid, :total, :metodo, :ref_pago, :tel_pago)";
        $stmtVenta = $conexion->prepare($sqlVenta);
        $stmtVenta->execute([
            ':uid' => $usuarioId, 
            ':total' => $totalVenta,
            ':metodo' => $metodoPago,
            ':ref_pago' => $referenciaPago,
            ':tel_pago' => $telefonoPago
        ]);
        $ventaId = $conexion->lastInsertId();

        // Preparar consultas para Productos Normales
        $stmtDetalleProd = $conexion->prepare("INSERT INTO Detalle_Venta (venta_id, producto_id, formato_venta, cantidad, subtotal) VALUES (:vid, :pid, 'Unidad', :cant, :subtotal)");
        $stmtStockProd = $conexion->prepare("UPDATE Productos SET stock_unidades_total = stock_unidades_total - :cant WHERE id = :pid");
        $stmtMovProd = $conexion->prepare("INSERT INTO Movimiento_Inventario (producto_id, cantidad_unidades, costo_unitario, tipo_movimiento, referencia_id, usuario_id) VALUES (:pid, :cant, :costo, 'Venta', :ref, :uid)");

        // Preparar consultas para Combos (ahora usan producto_id también)
        $stmtDetalleCombo = $conexion->prepare("INSERT INTO Detalle_Venta (venta_id, producto_id, formato_venta, cantidad, subtotal) VALUES (:vid, :cid, 'Unidad', :cant, :subtotal)");
        $stmtMovCombo = $conexion->prepare("INSERT INTO Movimiento_Inventario (producto_id, cantidad_unidades, costo_unitario, tipo_movimiento, referencia_id, usuario_id) VALUES (:cid, :cant, :costo, 'Venta', :ref, :uid)");

        // 2. Agregar y Validar Inventario Total Necesario
        $requerimientos = [];
        $stmtReceta = $conexion->prepare("SELECT producto_id, cantidad_necesaria FROM Recetas WHERE producto_padre_id = :cid");
        
        foreach ($carrito as $item) {
            $idReal = (int)$item['id_real'];
            $cant = (int)$item['cantidad'];
            
            if ($item['tipo'] === 'combo') {
                $stmtReceta->execute([':cid' => $idReal]);
                $receta = $stmtReceta->fetchAll(PDO::FETCH_ASSOC);
                foreach($receta as $ing) {
                    $pid = $ing['producto_id'];
                    $req = $cant * $ing['cantidad_necesaria'];
                    $requerimientos[$pid] = ($requerimientos[$pid] ?? 0) + $req;
                }
            } else {
                $requerimientos[$idReal] = ($requerimientos[$idReal] ?? 0) + $cant;
            }
        }

        $stmtCheck = $conexion->prepare("SELECT stock_unidades_total, nombre FROM Productos WHERE id = :pid FOR UPDATE");
        foreach ($requerimientos as $pid => $totalRequerido) {
            $stmtCheck->execute([':pid' => $pid]);
            $producto = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$producto || $producto['stock_unidades_total'] < $totalRequerido) {
                throw new Exception("Stock insuficiente para: " . ($producto['nombre'] ?? 'Producto Desconocido') . ". Se requieren $totalRequerido, hay {$producto['stock_unidades_total']}.");
            }
        }

        // 3. Procesar cada item del carrito (descontar y registrar movimientos)
        foreach ($carrito as $item) {
            $idReal = (int)$item['id_real'];
            $cant = (int)$item['cantidad'];
            $subtotal = (float)$item['subtotal'];
            $tipo = $item['tipo'];

            if ($tipo === 'combo') {
                $stmtDetalleCombo->execute([':vid' => $ventaId, ':cid' => $idReal, ':cant' => $cant, ':subtotal' => $subtotal]);
                
                // Descontar ingredientes físicos
                $stmtReceta->execute([':cid' => $idReal]);
                $receta = $stmtReceta->fetchAll(PDO::FETCH_ASSOC);
                foreach($receta as $ing) {
                    $cantIngrediente = $cant * $ing['cantidad_necesaria'];
                    $stmtStockProd->execute([':cant' => $cantIngrediente, ':pid' => $ing['producto_id']]);
                }

                // Calcular el costo real de adquisición del combo
                $costoUnitarioCombo = obtenerCostoPromedioCombo($conexion, $idReal);
                $stmtMovCombo->execute([':cid' => $idReal, ':cant' => $cant, ':costo' => $costoUnitarioCombo, ':ref' => $ventaId, ':uid' => $usuarioId]);
            } else {
                $stmtDetalleProd->execute([':vid' => $ventaId, ':pid' => $idReal, ':cant' => $cant, ':subtotal' => $subtotal]);
                $stmtStockProd->execute([':cant' => $cant, ':pid' => $idReal]);

                // Calcular el costo real de adquisición del producto
                $costoUnitarioProd = obtenerCostoPromedioProducto($conexion, $idReal);
                $stmtMovProd->execute([':pid' => $idReal, ':cant' => $cant, ':costo' => $costoUnitarioProd, ':ref' => $ventaId, ':uid' => $usuarioId]);
            }
        }

        $conexion->commit();

        // Registrar en bitácora
        require_once __DIR__ . '/../Infrastructure/RepositorioBitacora.php';
        registrarBitacora($conexion, $usuarioId, 'Venta procesada', 'Venta', (int)$ventaId, [
            'total' => $totalVenta,
            'metodo_pago' => $metodoPago,
            'items' => count($carrito)
        ]);

        return true;

    } catch (Exception $e) {
        $conexion->rollBack();
        error_log("Error al procesar Venta POS: " . $e->getMessage());
        return false;
    }
}