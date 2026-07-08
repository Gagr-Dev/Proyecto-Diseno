<?php

/**
 * Agrega un mensaje flash a la sesión.
 * @param string $tipo Tipo de mensaje (ej. 'exito', 'error', 'advertencia', 'info')
 * @param string $mensaje El texto del mensaje a mostrar
 */
function agregarMensajeFlash($tipo, $mensaje) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    $_SESSION['flash_messages'][] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

/**
 * Obtiene y limpia todos los mensajes flash de la sesión actual.
 * Debe ser llamado por la vista justo antes de renderizar los mensajes.
 * @return array Arreglo de mensajes flash
 */
function obtenerMensajesFlash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $mensajes = $_SESSION['flash_messages'] ?? [];
    $_SESSION['flash_messages'] = []; // Limpiar después de leer
    return $mensajes;
}

/**
 * Renderiza los mensajes flash usando Toastify JS.
 * @return string HTML de los scripts para Toastify
 */
function renderizarMensajesFlash() {
    $mensajes = obtenerMensajesFlash();
    if (empty($mensajes)) return '';
    
    $html = '<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">';
    $html .= '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>';
    $html .= '<script>document.addEventListener("DOMContentLoaded", function() {';
    
    foreach ($mensajes as $msg) {
        $color = '#3b82f6'; // info
        switch ($msg['tipo']) {
            case 'exito':
                $color = '#10b981'; // success
                break;
            case 'error':
                $color = '#ef4444'; // danger
                break;
            case 'advertencia':
                $color = '#f59e0b'; // warning
                break;
        }
        
        $mensajeSeguro = addslashes($msg['mensaje']);
        
        $html .= <<<HTML
            Toastify({
                text: "{$mensajeSeguro}",
                duration: 4000,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: "{$color}",
                    borderRadius: "8px",
                    boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)",
                    fontWeight: "600"
                }
            }).showToast();
HTML;
    }
    
    $html .= '});</script>';
    
    return $html;
}
