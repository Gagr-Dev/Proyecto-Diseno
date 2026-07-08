<?php
require_once __DIR__ . '/../Infrastructure/RepositorioBitacora.php';

function manejarMovimientoContable($datos, $usuarioId) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    $conexion = obtenerConexion();

    $productoId = (int)$datos['producto_id'];
    $cantidadIngresada = (int)$datos['cantidad'];
    $costoIngresado = (float)$datos['costo_unitario'];
    $tipoMovimiento = $datos['tipo_movimiento']; 
    $formato = $datos['formato'] ?? 'Unidad'; // Recibimos el nuevo campo del formulario

    try {
        $conexion->beginTransaction();

        // 1. Consultar cuántas unidades trae la caja de este producto
        $sqlProd = "SELECT unidades_por_caja FROM Productos WHERE id = :id";
        $stmtProd = $conexion->prepare($sqlProd);
        $stmtProd->execute([':id' => $productoId]);
        $unidadesPorCaja = (int)$stmtProd->fetchColumn();

        if ($unidadesPorCaja <= 0) $unidadesPorCaja = 1; // Seguridad

        // 2. Normalizar cantidades y costos a UNIDADES para la base de datos
        if ($formato === 'Caja') {
            $cantidadTotalUnidades = $cantidadIngresada * $unidadesPorCaja;
            // Si la caja costó $36 y trae 36 cervezas, el costo unitario a guardar es $1.00
            $costoRealPorUnidad = $costoIngresado / $unidadesPorCaja; 
        } else {
            $cantidadTotalUnidades = $cantidadIngresada;
            $costoRealPorUnidad = $costoIngresado;
        }

        // 3. Guardar el registro financiero
        $sqlMovimiento = "INSERT INTO Movimiento_Inventario 
                (producto_id, cantidad_unidades, costo_unitario, tipo_movimiento, usuario_id) 
                VALUES (:producto_id, :cantidad, :costo_unitario, :tipo_movimiento, :usuario_id)";
        
        $stmtMov = $conexion->prepare($sqlMovimiento);
        $stmtMov->execute([
            ':producto_id' => $productoId,
            ':cantidad' => $cantidadTotalUnidades,
            ':costo_unitario' => $costoRealPorUnidad,
            ':tipo_movimiento' => $tipoMovimiento,
            ':usuario_id' => $usuarioId
        ]);

        // 4. Actualizar el inventario físico
        if ($tipoMovimiento === 'Compra_Proveedor') {
            $sqlStock = "UPDATE Productos SET stock_unidades_total = stock_unidades_total + :cantidad WHERE id = :producto_id";
        } else if ($tipoMovimiento === 'Ajuste_Merma') {
            $sqlStock = "UPDATE Productos SET stock_unidades_total = stock_unidades_total - :cantidad WHERE id = :producto_id";
        }

        $stmtStock = $conexion->prepare($sqlStock);
        $stmtStock->execute([
            ':cantidad' => $cantidadTotalUnidades,
            ':producto_id' => $productoId
        ]);

        $conexion->commit();

        // Registrar en bitácora
        $tipoTexto = ($tipoMovimiento === 'Compra_Proveedor') ? 'Compra a proveedor registrada' : 'Merma registrada';
        registrarBitacora($conexion, $usuarioId, $tipoTexto, 'Movimiento_Inventario', (int)$conexion->lastInsertId(), [
            'producto_id' => $productoId,
            'cantidad' => $cantidadTotalUnidades,
            'costo_unitario' => $costoRealPorUnidad,
            'formato' => $formato
        ]);

        return true;

    } catch (PDOException $e) {
        $conexion->rollBack();
        error_log("Error al procesar contabilidad/inventario: " . $e->getMessage());
        return false;
    }
}


function editarMovimientoContable($datos, $usuarioId) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    $conexion = obtenerConexion();

    $idMovimiento = (int)$datos['id_movimiento'];
    $nuevoProductoId = (int)$datos['producto_id'];
    $cantidadIngresada = (int)$datos['cantidad'];
    $costoIngresado = (float)$datos['costo_unitario'];
    $formato = $datos['formato'] ?? 'Unidad';

    try {
        $conexion->beginTransaction();

        // 1. Obtener el movimiento antiguo para revertir su efecto en el inventario
        $stmtViejo = $conexion->prepare("SELECT * FROM Movimiento_Inventario WHERE id = :id");
        $stmtViejo->execute([':id' => $idMovimiento]);
        $movViejo = $stmtViejo->fetch(PDO::FETCH_ASSOC);

        if (!$movViejo) throw new Exception("Movimiento no encontrado.");

        // 2. Revertir el stock del movimiento antiguo
        if ($movViejo['tipo_movimiento'] === 'Compra_Proveedor') {
            $sqlRevertir = "UPDATE Productos SET stock_unidades_total = stock_unidades_total - :cant WHERE id = :prod";
        } else {
            $sqlRevertir = "UPDATE Productos SET stock_unidades_total = stock_unidades_total + :cant WHERE id = :prod";
        }
        $stmtRev = $conexion->prepare($sqlRevertir);
        $stmtRev->execute([':cant' => $movViejo['cantidad_unidades'], ':prod' => $movViejo['producto_id']]);

        // 3. Calcular nuevas unidades basadas en el formato (Caja/Unidad)
        $stmtProd = $conexion->prepare("SELECT unidades_por_caja FROM Productos WHERE id = :id");
        $stmtProd->execute([':id' => $nuevoProductoId]);
        $unidadesPorCaja = (int)$stmtProd->fetchColumn();
        if ($unidadesPorCaja <= 0) $unidadesPorCaja = 1;

        if ($formato === 'Caja') {
            $cantidadTotalUnidades = $cantidadIngresada * $unidadesPorCaja;
            $costoRealPorUnidad = $costoIngresado / $unidadesPorCaja; 
        } else {
            $cantidadTotalUnidades = $cantidadIngresada;
            $costoRealPorUnidad = $costoIngresado;
        }

        // 4. Aplicar el nuevo stock
        if ($movViejo['tipo_movimiento'] === 'Compra_Proveedor') {
            $sqlAplicar = "UPDATE Productos SET stock_unidades_total = stock_unidades_total + :cant WHERE id = :prod";
        } else {
            $sqlAplicar = "UPDATE Productos SET stock_unidades_total = stock_unidades_total - :cant WHERE id = :prod";
        }
        $stmtApl = $conexion->prepare($sqlAplicar);
        $stmtApl->execute([':cant' => $cantidadTotalUnidades, ':prod' => $nuevoProductoId]);

        // 5. Actualizar el registro financiero
        $sqlActualizar = "UPDATE Movimiento_Inventario 
                          SET producto_id = :producto_id, cantidad_unidades = :cantidad, costo_unitario = :costo, usuario_id = :usuario_id
                          WHERE id = :id";
        $stmtAct = $conexion->prepare($sqlActualizar);
        $stmtAct->execute([
            ':producto_id' => $nuevoProductoId,
            ':cantidad' => $cantidadTotalUnidades,
            ':costo' => $costoRealPorUnidad,
            ':usuario_id' => $usuarioId,
            ':id' => $idMovimiento
        ]);

        $conexion->commit();

        // Registrar en bitácora
        registrarBitacora($conexion, $usuarioId, 'Movimiento contable editado', 'Movimiento_Inventario', $idMovimiento, [
            'producto_id' => $nuevoProductoId,
            'nueva_cantidad' => $cantidadTotalUnidades,
            'nuevo_costo' => $costoRealPorUnidad
        ]);

        return true;

    } catch (Exception $e) {
        $conexion->rollBack();
        error_log("Error al editar contabilidad: " . $e->getMessage());
        return false;
    }
}