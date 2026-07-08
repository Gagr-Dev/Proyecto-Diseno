<?php
require_once __DIR__ . '/../Infrastructure/Database.php';
require_once __DIR__ . '/../Infrastructure/RepositorioBitacora.php';

function manejarNuevoProducto($datos) {
    $conexion = obtenerConexion();
    $precio_combo = !empty($datos['precio_combo_5']) ? $datos['precio_combo_5'] : null;
    $precio_caja = !empty($datos['precio_caja_36']) ? $datos['precio_caja_36'] : null;
    
    $unidades_por_caja = !empty($datos['unidades_por_caja']) ? (int)$datos['unidades_por_caja'] : 1;
    
    $sql = "INSERT INTO Productos (categoria_id, nombre, stock_unidades_total, precio_unidad, precio_combo_5, precio_caja_36, unidades_por_caja) 
            VALUES (:categoria_id, :nombre, :stock, :precio_unidad, :precio_combo, :precio_caja, :unidades_caja)";
            
    $stmt = $conexion->prepare($sql);
    $resultado = $stmt->execute([
        ':categoria_id' => $datos['categoria_id'],
        ':nombre' => $datos['nombre'],
        ':stock' => (int)$datos['stock_inicial'],
        ':precio_unidad' => $datos['precio_unidad'],
        ':precio_combo' => $precio_combo,
        ':precio_caja' => $precio_caja,
        ':unidades_caja' => $unidades_por_caja
    ]);

    if ($resultado) {
        registrarBitacora($conexion, $_SESSION['usuario_id'], 'Producto creado', 'Productos', (int)$conexion->lastInsertId(), [
            'nombre' => $datos['nombre'],
            'stock_inicial' => (int)$datos['stock_inicial'],
            'precio_unidad' => $datos['precio_unidad']
        ]);
    }
    return $resultado;
}

function manejarEdicionProducto($datos) {
    $conexion = obtenerConexion();
    $precio_combo = !empty($datos['precio_combo_5']) ? $datos['precio_combo_5'] : null;
    $precio_caja = !empty($datos['precio_caja_36']) ? $datos['precio_caja_36'] : null;
    
    $unidades_por_caja = !empty($datos['unidades_por_caja']) ? (int)$datos['unidades_por_caja'] : 1;
    
    $sql = "UPDATE Productos 
            SET categoria_id = :categoria_id, nombre = :nombre, 
                precio_unidad = :precio_unidad, precio_combo_5 = :precio_combo, precio_caja_36 = :precio_caja,
                unidades_por_caja = :unidades_caja
            WHERE id = :id";
            
    $stmt = $conexion->prepare($sql);
    $resultado = $stmt->execute([
        ':categoria_id' => $datos['categoria_id'],
        ':nombre' => $datos['nombre'],
        ':precio_unidad' => $datos['precio_unidad'],
        ':precio_combo' => $precio_combo,
        ':precio_caja' => $precio_caja,
        ':unidades_caja' => $unidades_por_caja,
        ':id' => $datos['producto_id']
    ]);

    if ($resultado) {
        registrarBitacora($conexion, $_SESSION['usuario_id'], 'Producto editado', 'Productos', (int)$datos['producto_id'], [
            'nombre' => $datos['nombre'],
            'precio_unidad' => $datos['precio_unidad']
        ]);
    }
    return $resultado;
}

function manejarEntradaStock($datos) {
    $conexion = obtenerConexion();
    $cajas = (int)($datos['cantidad_cajas'] ?? 0);
    $unidades = (int)($datos['cantidad_unidades'] ?? 0);
    
    // Obtener unidades_por_caja del producto
    $stmtProd = $conexion->prepare("SELECT unidades_por_caja FROM Productos WHERE id = :id");
    $stmtProd->execute([':id' => $datos['producto_id']]);
    $unidadesPorCaja = (int)$stmtProd->fetchColumn() ?: 1;
    
    $total_ingreso = ($cajas * $unidadesPorCaja) + $unidades;
    
    if ($total_ingreso > 0) {
        $sql = "UPDATE Productos SET stock_unidades_total = stock_unidades_total + :ingreso WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([':ingreso' => $total_ingreso, ':id' => $datos['producto_id']]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Entrada de stock', 'Productos', (int)$datos['producto_id'], [
                'cajas' => $cajas,
                'unidades' => $unidades,
                'unidades_por_caja' => $unidadesPorCaja,
                'total_ingresado' => $total_ingreso
            ]);
        }
        return $resultado;
    }
    return false;
}

function manejarSalidaStock($datos) {
    $conexion = obtenerConexion();
    $cajas = (int)($datos['cantidad_cajas'] ?? 0);
    $unidades = (int)($datos['cantidad_unidades'] ?? 0);
    
    // Obtener unidades_por_caja del producto
    $stmtProd = $conexion->prepare("SELECT unidades_por_caja FROM Productos WHERE id = :id");
    $stmtProd->execute([':id' => $datos['producto_id']]);
    $unidadesPorCaja = (int)$stmtProd->fetchColumn() ?: 1;
    
    $total_salida = ($cajas * $unidadesPorCaja) + $unidades;
    
    if ($total_salida > 0) {
        $sql = "UPDATE Productos SET stock_unidades_total = GREATEST(0, stock_unidades_total - :salida) WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([':salida' => $total_salida, ':id' => $datos['producto_id']]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Salida de stock', 'Productos', (int)$datos['producto_id'], [
                'cajas' => $cajas,
                'unidades' => $unidades,
                'unidades_por_caja' => $unidadesPorCaja,
                'total_retirado' => $total_salida
            ]);
        }
        return $resultado;
    }
    return false;
}

function manejarEliminarProducto($datos) {
    $conexion = obtenerConexion();
    try {
        // Solo cambiamos el estado a Inactivo para no dañar los reportes contables del pasado
        $sql = "UPDATE Productos SET estado = 'Inactivo' WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([':id' => (int)$datos['producto_id']]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Producto desactivado', 'Productos', (int)$datos['producto_id'], []);
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log("Error al eliminar producto: " . $e->getMessage());
        return false;
    }
}


function manejarNuevaCategoria($datos) {
    $conexion = obtenerConexion();
    try {
        $sql = "INSERT INTO Categorias (nombre) VALUES (:nombre)";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([':nombre' => trim($datos['nombre_categoria'])]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Categoría creada', 'Categorias', (int)$conexion->lastInsertId(), [
                'nombre' => trim($datos['nombre_categoria'])
            ]);
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log("Error al crear categoría: " . $e->getMessage());
        return false;
    }
}