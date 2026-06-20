<?php

function guardarTorneoCompleto(PDO $conexion, array $datos): bool {
    try {
        $conexion->beginTransaction();

        $sqlTorneo = "INSERT INTO Torneo (nombre) VALUES (:nombre)";
        $stmtTorneo = $conexion->prepare($sqlTorneo);
        $stmtTorneo->execute([':nombre' => $datos['nombre_torneo']]);
        
        $torneoId = $conexion->lastInsertId();

        $mapaEquipos = []; 
        $sqlEquipo = "INSERT INTO Torneo_Equipo (torneo_id, nombre_equipo) VALUES (:torneo_id, :nombre_equipo)";
        $stmtEquipo = $conexion->prepare($sqlEquipo);

        foreach ($datos['equipos_unicos'] as $nombreEquipo) {
            $stmtEquipo->execute([
                ':torneo_id' => $torneoId,
                ':nombre_equipo' => $nombreEquipo
            ]);
            $mapaEquipos[$nombreEquipo] = $conexion->lastInsertId();
        }

        $sqlPartido = "INSERT INTO Torneo_Partido 
                       (torneo_id, equipo_local_id, equipo_visitante_id, fecha_hora) 
                       VALUES (:torneo_id, :local_id, :visitante_id, :fecha)";
        $stmtPartido = $conexion->prepare($sqlPartido);

        $totalPartidos = count($datos['equipo_local']);
        
        for ($i = 0; $i < $totalPartidos; $i++) {
            $nombreLocal = $datos['equipo_local'][$i];
            $nombreVisitante = $datos['equipo_visitante'][$i];
            $fecha = $datos['fecha_juego'][$i];

            $stmtPartido->execute([
                ':torneo_id' => $torneoId,
                ':local_id' => $mapaEquipos[$nombreLocal], 
                ':visitante_id' => $mapaEquipos[$nombreVisitante],
                ':fecha' => $fecha
            ]);
        }

        $conexion->commit();
        return true;

    } catch (Exception $e) {
        $conexion->rollBack();
        throw $e; 
    }
}

function obtenerTorneoPorId(PDO $conexion, int $idTorneo): ?array {
    $sql = "SELECT id, nombre, estado, fecha_creacion FROM Torneo WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $idTorneo]);
    $resultado = $stmt->fetch();
    return $resultado ?: null;
}

function obtenerPartidosTorneo(PDO $conexion, int $idTorneo): array {
    $sql = "SELECT 
                p.id as partido_id, 
                p.fecha_hora, 
                p.estado as estado_partido, 
                p.ganador_id,
                el.id as local_id, 
                el.nombre_equipo as local_nombre,
                ev.id as visitante_id, 
                ev.nombre_equipo as visitante_nombre
            FROM Torneo_Partido p
            JOIN Torneo_Equipo el ON p.equipo_local_id = el.id
            JOIN Torneo_Equipo ev ON p.equipo_visitante_id = ev.id
            WHERE p.torneo_id = :torneo_id
            ORDER BY p.fecha_hora ASC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':torneo_id' => $idTorneo]);
    return $stmt->fetchAll();
}

function obtenerTodosLosTorneos(PDO $conexion): array {
    $sql = "SELECT id, nombre, estado, fecha_creacion FROM Torneo ORDER BY fecha_creacion DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}