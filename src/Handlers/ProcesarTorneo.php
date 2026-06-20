<?php
require_once __DIR__ . '/../Domain/TorneoReglas.php';
require_once __DIR__ . '/../Infrastructure/Database.php';
require_once __DIR__ . '/../Infrastructure/RepositorioTorneo.php';

function manejarRegistroTorneo(array $postData): array {
    // 1. Validar los datos
    $errores = validarDatosTorneo($postData);
    if (!empty($errores)) {
        return ['exito' => false, 'mensajes' => $errores];
    }

    // 2. Extraer equipos sin repetir
    $equiposUnicos = obtenerEquiposUnicos($postData['equipo_local'], $postData['equipo_visitante']);

    // 3. Estructurar la data para mandarla a la Base de Datos
    $datosEstructurados = [
        'nombre_torneo' => trim($postData['nombre_torneo']),
        'equipos_unicos' => $equiposUnicos,
        'equipo_local' => $postData['equipo_local'],
        'equipo_visitante' => $postData['equipo_visitante'],
        'fecha_juego' => $postData['fecha_juego']
    ];

    // 4. Conectar y Guardar
    $conexion = obtenerConexion();
    try {
        guardarTorneoCompleto($conexion, $datosEstructurados);
        return ['exito' => true, 'mensajes' => ["Torneo organizado y guardado exitosamente."]];
    } catch (Exception $e) {
        return ['exito' => false, 'mensajes' => ["Error al guardar en base de datos: " . $e->getMessage()]];
    }
}