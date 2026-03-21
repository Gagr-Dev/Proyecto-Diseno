<?php
// Función: Solo evalúa datos y retorna errores si los hay
function validarDatosRegistro(array $datos): array {
    $errores = [];
    
    if (empty(trim($datos['nombre_completo']))) {
        $errores[] = "El nombre completo es obligatorio.";
    }
    if (empty(trim($datos['username'])) || strlen($datos['username']) < 4) {
        $errores[] = "El usuario debe tener al menos 4 caracteres.";
    }
    if (empty($datos['password']) || strlen($datos['password']) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if (empty($datos['rol_id'])) {
        $errores[] = "Debe seleccionar un rol válido.";
    }
    
    return $errores;
}

// Función: Transforma los datos crudos en el formato exacto para la BD
function prepararUsuarioParaBD(array $datosCrudos): array {
    return [
        'nombre_completo' => trim($datosCrudos['nombre_completo']),
        'username' => trim($datosCrudos['username']),
        // Encriptamos la contraseña aquí para que la BD solo reciba el hash
        'password_hash' => password_hash($datosCrudos['password'], PASSWORD_BCRYPT),
        'rol_id' => (int) $datosCrudos['rol_id']
    ];
}