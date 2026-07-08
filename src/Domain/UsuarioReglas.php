<?php
function validarDatosRegistro(array $datos): array {
    $errores = [];
    
    $cedula = isset($datos['cedula_numero']) 
        ? trim($datos['cedula_numero']) 
        : trim($datos['cedula'] ?? '');

    if (empty($cedula)) {
        $errores[] = "La cédula es obligatoria.";
    }
    if (empty(trim($datos['primer_nombre'] ?? ''))) {
        $errores[] = "El primer nombre es obligatorio.";
    }
    if (empty(trim($datos['primer_apellido'] ?? ''))) {
        $errores[] = "El primer apellido es obligatorio.";
    }
    if (empty(trim($datos['username'] ?? '')) || strlen(trim($datos['username'])) < 4) {
        $errores[] = "El usuario debe tener al menos 4 caracteres.";
    }
    if (empty($datos['password'] ?? '') || strlen($datos['password']) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if (empty($datos['rol_id'] ?? '')) {
        $errores[] = "Debe seleccionar un rol válido.";
    }
    
    return $errores;
}

function prepararUsuarioParaBD(array $datosCrudos): array {
    $cedula = isset($datosCrudos['cedula_numero']) 
        ? ($datosCrudos['tipo_cedula'] ?? 'V-') . trim($datosCrudos['cedula_numero']) 
        : trim($datosCrudos['cedula'] ?? '');

    return [
     
        'cedula' => strtoupper($cedula),
        'primer_nombre' => trim($datosCrudos['primer_nombre'] ?? ''),
        'primer_apellido' => trim($datosCrudos['primer_apellido'] ?? ''),
        
        
        'segundo_nombre' => !empty(trim($datosCrudos['segundo_nombre'] ?? '')) ? trim($datosCrudos['segundo_nombre']) : null,
        'segundo_apellido' => !empty(trim($datosCrudos['segundo_apellido'] ?? '')) ? trim($datosCrudos['segundo_apellido']) : null,
        
        'username' => trim($datosCrudos['username'] ?? ''),
        'password_hash' => password_hash($datosCrudos['password'] ?? '', PASSWORD_BCRYPT),
        'rol_id' => (int) ($datosCrudos['rol_id'] ?? 0)
    ];
}