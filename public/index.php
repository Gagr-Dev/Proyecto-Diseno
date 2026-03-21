<?php
// 1. Iniciamos la sesión en el nivel más alto de la aplicación
session_start();

// 2. Obtenemos la ruta solicitada, por defecto es 'login'
$ruta = $_GET['ruta'] ?? 'login';

// 3. Convertimos a minúsculas por seguridad
$ruta = strtolower($ruta);

// ------------------------------------------------------------------
// ENRUTADOR (FRONT CONTROLLER)
// ------------------------------------------------------------------

if ($ruta === 'login') {
    // Si ya está logueado, lo mandamos al dashboard
    if (isset($_SESSION['usuario_id'])) {
        header('Location: /index.php?ruta=dashboard');
        exit;
    }

    $resultado = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/../src/Handlers/ProcesarLogin.php';
        $resultado = manejarLogin($_POST);
        
        // Si el login fue exitoso, redirigimos
        if ($resultado['exito']) {
            header('Location: /index.php?ruta=dashboard');
            exit;
        }
    }
    require __DIR__ . '/../views/usuarios/login.php';

} elseif ($ruta === 'registro') {
    $resultado = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/../src/Handlers/ProcesarRegistro.php';
        $resultado = manejarRegistroUsuario($_POST);
    }
    require __DIR__ . '/../views/usuarios/registro.php';

} elseif ($ruta === 'dashboard') {
    // Protegemos la ruta: Si no hay sesión, lo devolvemos al login
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /index.php?ruta=login');
        exit;
    }
    // Vista temporal para confirmar el login exitoso
    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['nombre']) . "!</h1>";
    echo "<p>Tu ID de rol es: " . htmlspecialchars($_SESSION['rol_id']) . "</p>";
    echo "<a href='/index.php?ruta=logout' style='color: red;'>Cerrar Sesión</a>";
    echo "</div>";

} elseif ($ruta === 'logout') {
    session_destroy();
    header('Location: /index.php?ruta=login');
    exit;

} else {
    // Si escriben cualquier otra cosa en la URL
    http_response_code(404);
    echo "<h1 style='text-align: center; margin-top: 50px; font-family: sans-serif;'>Página no encontrada</h1>";
    echo "<p style='text-align: center;'><a href='/index.php?ruta=login'>Volver al inicio</a></p>";
}