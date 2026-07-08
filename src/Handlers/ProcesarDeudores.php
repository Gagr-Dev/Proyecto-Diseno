<?php
require_once __DIR__ . '/../Infrastructure/RepositorioBitacora.php';

function manejarNuevoDeudor($datos) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    $conexion = obtenerConexion();
    
    try {
        $sql = "INSERT INTO Deudores (nombre_completo, cedula, telefono) VALUES (:nombre, :cedula, :telefono)";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([
            ':nombre' => trim($datos['nombre_completo']),
            ':cedula' => trim($datos['cedula']),
            ':telefono' => trim($datos['telefono'])
        ]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Deudor registrado', 'Deudores', (int)$conexion->lastInsertId(), [
                'nombre' => trim($datos['nombre_completo'])
            ]);
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log("Error al crear deudor: " . $e->getMessage());
        return false;
    }
}

function manejarNuevaDeuda($datos) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    $conexion = obtenerConexion();
    
    try {
        $sql = "INSERT INTO Cuentas_Por_Cobrar (deudor_id, monto, descripcion) VALUES (:deudor_id, :monto, :descripcion)";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([
            ':deudor_id' => (int)$datos['deudor_id'],
            ':monto' => (float)$datos['monto'],
            ':descripcion' => trim($datos['descripcion'])
        ]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Deuda registrada', 'Cuentas_Por_Cobrar', (int)$conexion->lastInsertId(), [
                'deudor_id' => (int)$datos['deudor_id'],
                'monto' => (float)$datos['monto'],
                'descripcion' => trim($datos['descripcion'])
            ]);
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log("Error al añadir deuda: " . $e->getMessage());
        return false;
    }
}

function manejarPagoDeuda($datos) {
    require_once __DIR__ . '/../Infrastructure/Database.php';
    $conexion = obtenerConexion();
    
    try {
        // Actualizamos todas las deudas pendientes de este cliente a 'Pagado'
        $sql = "UPDATE Cuentas_Por_Cobrar 
                SET estado = 'Pagado', fecha_pago = NOW() 
                WHERE deudor_id = :deudor_id AND estado = 'Pendiente'";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([':deudor_id' => (int)$datos['deudor_id']]);

        if ($resultado) {
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Deuda liquidada', 'Cuentas_Por_Cobrar', (int)$datos['deudor_id'], [
                'deudor_id' => (int)$datos['deudor_id']
            ]);
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log("Error al pagar deuda: " . $e->getMessage());
        return false;
    }
}