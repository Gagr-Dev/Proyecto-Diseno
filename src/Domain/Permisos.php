<?php

/**
 * Permisos.php
 * Definición centralizada de acceso a módulos según el rol del usuario.
 * 
 * Roles:
 * 1 = Super Administrador
 * 2 = Cajero
 * 3 = Supervisor de Eventos
 * 4 = Contador
 * 5 = Administrador
 */

function tieneAcceso(string $modulo, int $rolId): bool {
    // Si es Super Administrador, tiene acceso a TODO.
    if ($rolId === 1) {
        return true;
    }

    $accesos = [
        // Módulos
        'dashboard' => [1, 2, 3, 4, 5],
        'pos' => [1, 2, 5],
        'inventario' => [1, 2, 4, 5], 
        'combos' => [1, 5],
        'eventos' => [1, 3, 5],
        'contabilidad' => [1, 4, 5],
        'deudores' => [1, 2, 4, 5],
        'personal' => [1], // Solo Super Admin
        'auditoria' => [1], // Solo Super Admin
        
        // Acciones específicas (Opcional, para refinar permisos en vistas)
        'inventario_escritura' => [1, 4, 5], // El rol 2 (Cajero) no está, solo lee
        'crear_administrador' => [1], // Solo Super Admin puede crear otros admins
        'crear_empleado' => [1, 5] // Admin puede crear empleados (pero no Admins)
    ];

    if (!array_key_exists($modulo, $accesos)) {
        return false;
    }

    return in_array($rolId, $accesos[$modulo]);
}
