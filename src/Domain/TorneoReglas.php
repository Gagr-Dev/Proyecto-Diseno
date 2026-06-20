<?php

// Validar que los datos del formulario vengan completos
function validarDatosTorneo(array $datos): array {
    $errores = [];
    
    if (empty(trim($datos['nombre_torneo'] ?? ''))) {
        $errores[] = "El nombre del torneo es obligatorio.";
    }
    
    // Validar que hayan llegado los arreglos (arrays) de los partidos
    if (empty($datos['equipo_local']) || empty($datos['equipo_visitante']) || empty($datos['fecha_juego'])) {
        $errores[] = "Debe generar al menos un partido en el fixture.";
    } else {
        // Asegurarnos de que nadie haya alterado el HTML y falte alguna fecha
        $c1 = count($datos['equipo_local']);
        $c2 = count($datos['equipo_visitante']);
        $c3 = count($datos['fecha_juego']);
        
        if ($c1 !== $c2 || $c2 !== $c3) {
            $errores[] = "Los datos de los partidos están incompletos o corruptos.";
        }
    }
    
    return $errores;
}

// Extraer una lista única de los equipos que van a jugar
function obtenerEquiposUnicos(array $locales, array $visitantes): array {
    // Unimos los que juegan de local y de visitante en una sola lista gigante
    $todosLosEquipos = array_merge($locales, $visitantes);
    
    // Eliminamos los duplicados (ej. si "Los Compadres" juegan 5 veces, queda 1 sola vez)
    // y reordenamos las llaves del array
    return array_values(array_unique($todosLosEquipos));
}