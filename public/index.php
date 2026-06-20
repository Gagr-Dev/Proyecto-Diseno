<?php
session_start();

$ruta = strtolower($_GET['ruta'] ?? 'login');

// ------------------------------------------------------------------
// ENRUTADOR (FRONT CONTROLLER)
// ------------------------------------------------------------------

switch ($ruta) {
    case 'login':
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=dashboard');
            exit;
        }

        $resultado = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/Handlers/ProcesarLogin.php';
            $resultado = manejarLogin($_POST);
            
            if ($resultado['exito']) {
                header('Location: /index.php?ruta=dashboard');
                exit;
            }
        }
        require __DIR__ . '/../views/usuarios/login.php';
        break;

    case 'registro':
        $resultado = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/Handlers/ProcesarRegistro.php';
            $resultado = manejarRegistroUsuario($_POST);
        }
        require __DIR__ . '/../views/usuarios/registro.php';
        break;

    case 'dashboard':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login');
            exit;
        }
        require __DIR__ . '/../views/dashboard.php';
        break;

    case 'logout':
        session_destroy();
        header('Location: /index.php?ruta=login');
        exit;

    case 'inventario':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login');
            exit;
        }
        require __DIR__ . '/../views/inventario/index.php';
        break;

    case 'eventos':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login');
            exit;
        }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioTorneo.php';
        
        $conexion = obtenerConexion();
        $torneosGuardados = obtenerTodosLosTorneos($conexion);
        
        require __DIR__ . '/../views/eventos/eventos.php';
        break;

    case 'guardar_torneo':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/Handlers/ProcesarTorneo.php';
            $resultado = manejarRegistroTorneo($_POST);
            
            if ($resultado['exito']) {
                header('Location: /index.php?ruta=eventos&msg=exito');
            } else {
                header('Location: /index.php?ruta=eventos&msg=error');
            }
            exit;
        }
        break;

    case 'ver_torneo':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login');
            exit;
        }
        
        $idTorneo = (int)($_GET['id'] ?? 0);

        

        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioTorneo.php';
        
        $conexion = obtenerConexion();
        
        $torneo = obtenerTorneoPorId($conexion, $idTorneo);
        $partidos = obtenerPartidosTorneo($conexion, $idTorneo);

        if (!$torneo) {
            echo "Torneo no encontrado.";
            exit;
        }

        require __DIR__ . '/../views/eventos/detalle_torneo.php';
        break;

    case 'guardar_resultado':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login');
            exit;
        }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        $conexion = obtenerConexion();
        
        $partidoId = (int)$_POST['partido_id'];
        $ganadorId = (int)$_POST['ganador_id'];
        $torneoId = (int)$_POST['torneo_id'];

        // 1. Guardamos el resultado del partido como 'Jugado' (según la DB)
        $sqlPartido = "UPDATE Torneo_Partido SET estado = 'Jugado', ganador_id = :ganador_id WHERE id = :partido_id";
        $stmtPartido = $conexion->prepare($sqlPartido);
        $stmtPartido->execute([':ganador_id' => $ganadorId, ':partido_id' => $partidoId]);

        // 2. VERIFICACIÓN DEL ESTADO GLOBAL DEL TORNEO
        // Contamos cuántos partidos siguen 'Pendientes' en este torneo
        $sqlFaltantes = "SELECT COUNT(*) FROM Torneo_Partido WHERE torneo_id = :torneo_id AND estado = 'Pendiente'";
        $stmtFaltantes = $conexion->prepare($sqlFaltantes);
        $stmtFaltantes->execute([':torneo_id' => $torneoId]);
        $partidosPendientes = $stmtFaltantes->fetchColumn();

        if ($partidosPendientes == 0) {
            // Si ya no quedan partidos pendientes, el torneo terminó
            $sqlTorneo = "UPDATE Torneo SET estado = 'Terminado' WHERE id = :torneo_id";
        } else {
            // Si aún quedan partidos, el torneo está a la mitad (En Curso)
            $sqlTorneo = "UPDATE Torneo SET estado = 'En Curso' WHERE id = :torneo_id";
        }
        $stmtTorneo = $conexion->prepare($sqlTorneo);
        $stmtTorneo->execute([':torneo_id' => $torneoId]);

        // Redirigimos de vuelta a la vista
        header("Location: /index.php?ruta=ver_torneo&id=" . $torneoId);
        exit;
        break;

    case 'guardar_producto':
    case 'editar_producto':
    case 'agregar_entrada':
    case 'restar_inventario': 
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login');
            exit;
        }
        
        require_once __DIR__ . '/../src/Handlers/ProcesarInventario.php';
        
        if ($ruta === 'guardar_producto') {
            manejarNuevoProducto($_POST);
        } elseif ($ruta === 'editar_producto') {
            manejarEdicionProducto($_POST);
        } elseif ($ruta === 'agregar_entrada') {
            manejarEntradaStock($_POST);
        } elseif ($ruta === 'restar_inventario') { 
            manejarSalidaStock($_POST);
        }
        
        header('Location: /index.php?ruta=inventario');
        exit;

    case '500':
        http_response_code(500);
        echo "<h1 style='text-align:center; margin-top:50px;'>Error 500 - Error Interno del Servidor</h1>";
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
        break;
}