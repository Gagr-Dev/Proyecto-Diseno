<?php
// Función: Compara la contraseña en texto plano con el hash de la BD
function verificarPassword(string $passwordIngresada, string $hashGuardado): bool {
    return password_verify($passwordIngresada, $hashGuardado);
}