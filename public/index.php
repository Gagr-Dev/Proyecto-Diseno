<?php
session_start();

$ruta = strtolower($_GET['ruta'] ?? 'login');

require_once __DIR__ . '/../src/Domain/Permisos.php';
require_once __DIR__ . '/../src/Infrastructure/FlashMessages.php';
require_once __DIR__ . '/../src/Infrastructure/CSRF.php';

// Validación Global de CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        agregarMensajeFlash('error', 'Token de seguridad inválido o expirado. Por favor, intenta de nuevo.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php?ruta=dashboard'));
        exit;
    }
}

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
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioDashboard.php';
        
        $conexion = obtenerConexion();
        $resumenDashboard = obtenerResumenDashboard($conexion);
        
        require __DIR__ . '/../views/dashboard/dashboard.php';
        break;

    case 'logout':
        session_destroy();
        header('Location: /index.php?ruta=login');
        exit;

    case 'inventario':
        if (!isset($_SESSION['usuario_id'])) { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('inventario', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        // NUEVA RUTA DE INVENTARIO
        require __DIR__ . '/../views/inventario/inventario.php';
        break;

    // ==========================================
    // MÓDULO DE COMBOS Y PROMOCIONES
    // ==========================================
    case 'combos':
        if (!isset($_SESSION['usuario_id'])) { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('combos', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioCombos.php';
        $conexion = obtenerConexion();
        
        $listaCombos = obtenerCombosConDisponibilidad($conexion);
        $productosReceta = obtenerProductosParaReceta($conexion);
        
        // NUEVA RUTA DE COMBOS
        require __DIR__ . '/../views/combos/combos.php';
        break;

    case 'guardar_combo_nuevo':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /index.php?ruta=login'); exit; }
        require_once __DIR__ . '/../src/Handlers/ProcesarCombos.php';
        manejarNuevoCombo($_POST);
        header('Location: /index.php?ruta=combos');
        exit;


    // ==========================================
    // MÓDULO DE PUNTO DE VENTA (POS)
    // ==========================================
    case 'pos':
        if (!isset($_SESSION['usuario_id'])) { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('pos', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioPOS.php';

        $conexion = obtenerConexion();
        $productosPOS = obtenerProductosActivosPOS($conexion);

        // NUEVA RUTA DE POS
        require __DIR__ . '/../views/pos/pos.php';
        break;

    case 'procesar_venta':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        require_once __DIR__ . '/../src/Handlers/ProcesarPOS.php';
        
        if (procesarVentaPOS($_POST, $_SESSION['usuario_id'])) {
            agregarMensajeFlash('exito', 'Operación realizada con éxito.');
        header('Location: /index.php?ruta=pos');
        } else {
            agregarMensajeFlash('error', 'Ocurrió un error al procesar la solicitud.');
        header('Location: /index.php?ruta=pos');
        }
        exit;

    case 'eventos':
        if (!isset($_SESSION['usuario_id'])) { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('eventos', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioTorneo.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioEvento.php'; 
        
        $conexion = obtenerConexion();
        $torneosGuardados = obtenerTodosLosTorneos($conexion);
        $eventosGenerales = obtenerEventosGenerales($conexion); 
        
        require __DIR__ . '/../views/eventos/eventos.php';
        break;
    
    case 'crear_evento':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login');
            exit;
        }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioEvento.php';
        
        $conexion = obtenerConexion();
        
        $datosEvento = [
            'nombre' => trim($_POST['nombre']),
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'descripcion' => trim($_POST['descripcion'])
        ];
        
        if (guardarEventoGlobal($conexion, $datosEvento)) {
            agregarMensajeFlash('exito', 'Evento creado correctamente.');
        header('Location: /index.php?ruta=eventos');
        } else {
            agregarMensajeFlash('error', 'Ocurrió un error al procesar la solicitud.');
        header('Location: /index.php?ruta=eventos');
        }
        exit;
        break;
    
    case 'cancelar_evento':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login');
            exit;
        }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioEvento.php';
        
        $conexion = obtenerConexion();
        $idEvento = (int)$_POST['evento_id'];
        
        if (cancelarEventoGlobal($conexion, $idEvento)) {
            // Registrar en bitácora
            require_once __DIR__ . '/../src/Infrastructure/RepositorioBitacora.php';
            registrarBitacora($conexion, $_SESSION['usuario_id'], 'Evento cancelado', 'Evento', $idEvento, []);

            agregarMensajeFlash('exito', 'Evento cancelado exitosamente.');
        header('Location: /index.php?ruta=eventos');
        } else {
            agregarMensajeFlash('error', 'Ocurrió un error al procesar la solicitud.');
        header('Location: /index.php?ruta=eventos');
        }
        exit;
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
                agregarMensajeFlash('exito', 'Operación realizada con éxito.');
        header('Location: /index.php?ruta=eventos');
            } else {
                agregarMensajeFlash('error', 'Ocurrió un error al procesar la solicitud.');
        header('Location: /index.php?ruta=eventos');
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
        $tablaPosiciones = obtenerTablaPosiciones($conexion, $idTorneo);

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

        $sqlPartido = "UPDATE Torneo_Partido SET estado = 'Jugado', ganador_id = :ganador_id WHERE id = :partido_id";
        $stmtPartido = $conexion->prepare($sqlPartido);
        $stmtPartido->execute([':ganador_id' => $ganadorId, ':partido_id' => $partidoId]);

        $sqlFaltantes = "SELECT COUNT(*) FROM Torneo_Partido WHERE torneo_id = :torneo_id AND estado = 'Pendiente'";
        $stmtFaltantes = $conexion->prepare($sqlFaltantes);
        $stmtFaltantes->execute([':torneo_id' => $torneoId]);
        $partidosPendientes = $stmtFaltantes->fetchColumn();

        if ($partidosPendientes == 0) {
            $sqlTorneo = "UPDATE Torneo SET estado = 'Terminado' WHERE id = :torneo_id";
        } else {
            $sqlTorneo = "UPDATE Torneo SET estado = 'En Curso' WHERE id = :torneo_id";
        }
        $stmtTorneo = $conexion->prepare($sqlTorneo);
        $stmtTorneo->execute([':torneo_id' => $torneoId]);

        // Registrar en bitácora
        require_once __DIR__ . '/../src/Infrastructure/RepositorioBitacora.php';
        registrarBitacora($conexion, $_SESSION['usuario_id'], 'Resultado de partido registrado', 'Torneo_Partido', $partidoId, [
            'torneo_id' => $torneoId,
            'ganador_id' => $ganadorId
        ]);

        header("Location: /index.php?ruta=ver_torneo&id=" . $torneoId);
        exit;
        break;

    case 'guardar_producto':
    case 'editar_producto':
    case 'agregar_entrada':
    case 'restar_inventario':
    case 'eliminar_producto':
    case 'guardar_categoria':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('inventario_escritura', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Handlers/ProcesarInventario.php';
        
        if ($ruta === 'guardar_producto') {
            manejarNuevoProducto($_POST);
            header('Location: /index.php?ruta=inventario');
            exit;
        } elseif ($ruta === 'editar_producto') {
            manejarEdicionProducto($_POST);
            header('Location: /index.php?ruta=inventario');
            exit;
        } elseif ($ruta === 'agregar_entrada') {
            manejarEntradaStock($_POST);
            header('Location: /index.php?ruta=inventario');
            exit;
        } elseif ($ruta === 'restar_inventario') { 
            manejarSalidaStock($_POST);
            header('Location: /index.php?ruta=inventario');
            exit;
        } elseif ($ruta === 'eliminar_producto') {
            manejarEliminarProducto($_POST);
            header('Location: /index.php?ruta=inventario');
            exit;
        } elseif ($ruta === 'guardar_categoria') { 
            require_once __DIR__ . '/../src/Infrastructure/Database.php';
            $conexion = obtenerConexion();
            $nombre_cat = trim($_POST['nombre_categoria'] ?? '');

            // Validar si la categoría ya existe (insensible a mayúsculas/minúsculas)
            $stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM Categorias WHERE LOWER(nombre) = LOWER(:nombre)");
            $stmtCheck->execute([':nombre' => $nombre_cat]);
            $existe = $stmtCheck->fetchColumn();

            if ($existe > 0) {
                // Redirecciona informando del error
                agregarMensajeFlash('error', 'La categoría ya existe.');
                header('Location: /index.php?ruta=inventario');
            } else {
                manejarNuevaCategoria($_POST);
                // Redirecciona informando del éxito para abrir el modal de productos
                agregarMensajeFlash('exito', 'Categoría creada con éxito.');
                header('Location: /index.php?ruta=inventario');
            }
            exit;
        }

    // ==========================================
    // MÓDULO DE CONTABILIDAD
    // ==========================================
    case 'contabilidad':
        if (!isset($_SESSION['usuario_id'])) { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('contabilidad', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioContabilidad.php';
        
        $conexion = obtenerConexion();
        $resumen = obtenerResumenContabilidad($conexion);
        $productos = obtenerProductosParaContabilidad($conexion);
        $historial = obtenerHistorialMovimientos($conexion);  
        $registroMensual = obtenerRegistroMensual($conexion); 
        
        // NUEVA RUTA DE CONTABILIDAD
        require __DIR__ . '/../views/contabilidad/contabilidad.php'; 
        break;

    case 'registrar_movimiento_contable':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        require_once __DIR__ . '/../src/Handlers/ProcesarContabilidad.php';
        $exito = manejarMovimientoContable($_POST, $_SESSION['usuario_id']);
        header('Location: /index.php?ruta=contabilidad&msg=' . ($exito ? 'exito' : 'error'));
        exit;

    case 'editar_movimiento_contable': 
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        require_once __DIR__ . '/../src/Handlers/ProcesarContabilidad.php';
        $exito = editarMovimientoContable($_POST, $_SESSION['usuario_id']);
        header('Location: /index.php?ruta=contabilidad&msg=' . ($exito ? 'editado' : 'error'));
        exit;

    // ==========================================
    // MÓDULO DE DEUDORES
    // ==========================================
    case 'deudores':
        if (!isset($_SESSION['usuario_id'])) { header('Location: /index.php?ruta=login'); exit; }
        if (!tieneAcceso('deudores', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioDeudores.php';
        
        $conexion = obtenerConexion();
        $deudores = obtenerTodosLosDeudores($conexion);
        
        require __DIR__ . '/../views/contabilidad/deudores.php';
        break;
        
    case 'guardar_deudor':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        require_once __DIR__ . '/../src/Handlers/ProcesarDeudores.php';
        manejarNuevoDeudor($_POST);
        header('Location: /index.php?ruta=deudores'); 
        exit;

    case 'guardar_deuda':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        require_once __DIR__ . '/../src/Handlers/ProcesarDeudores.php';
        manejarNuevaDeuda($_POST);
        header('Location: /index.php?ruta=deudores'); 
        exit;

    case 'pagar_deuda':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        require_once __DIR__ . '/../src/Handlers/ProcesarDeudores.php';
        manejarPagoDeuda($_POST);
        header('Location: /index.php?ruta=deudores'); 
        exit;

    // ==========================================
    // MÓDULO DE GESTIÓN DE PERSONAL
    // ==========================================
    case 'personal':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login'); exit;
        }
        // Solo administradores pueden acceder
        if (!tieneAcceso('personal', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioUsuario.php';
        
        $conexion = obtenerConexion();
        $listaPersonal = obtenerTodoElPersonal($conexion);
        $roles = obtenerRoles($conexion);
        
        require __DIR__ . '/../views/personal/personal.php';
        break;

    case 'registrar_personal':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        if (!tieneAcceso('personal', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Handlers/ProcesarRegistro.php';
        $resultado = manejarRegistroUsuario($_POST);
        
        if ($resultado['exito']) {
            agregarMensajeFlash('exito', 'Personal registrado exitosamente.');
        header('Location: /index.php?ruta=personal');
        } else {
            agregarMensajeFlash('error', implode(' ', $resultado['mensajes']));
            header('Location: /index.php?ruta=personal');
        }
        exit;

    case 'cambiar_estado_personal':
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?ruta=login'); exit;
        }
        if (!tieneAcceso('personal', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioUsuario.php';
        
        $conexion = obtenerConexion();
        $usuarioId = (int)$_POST['usuario_id'];
        $accion = $_POST['nueva_accion'] ?? '';
        
        // Evitar que el admin se desactive a sí mismo
        if ($usuarioId === (int)$_SESSION['usuario_id']) {
            agregarMensajeFlash('error', 'Ocurrió un error al procesar la solicitud.');
        header('Location: /index.php?ruta=personal');
            exit;
        }
        
        $nuevoEstado = ($accion === 'activar') ? 1 : 0;
        
        if (cambiarEstadoUsuario($conexion, $usuarioId, $nuevoEstado)) {
            // Registrar en bitácora
            require_once __DIR__ . '/../src/Infrastructure/RepositorioBitacora.php';
            $accionTexto = ($accion === 'activar') ? 'Personal reactivado' : 'Personal desactivado';
            registrarBitacora($conexion, $_SESSION['usuario_id'], $accionTexto, 'Usuario', $usuarioId, [
                'nuevo_estado' => $nuevoEstado
            ]);

            $msg = ($accion === 'activar') ? 'activado' : 'desactivado';
            header('Location: /index.php?ruta=personal&msg=' . $msg);
        } else {
            agregarMensajeFlash('error', 'Ocurrió un error al procesar la solicitud.');
        header('Location: /index.php?ruta=personal');
        }
        exit;

    // ==========================================
    // MÓDULO DE AUDITORÍA
    // ==========================================
    case 'auditoria':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?ruta=login'); exit;
        }
        // Solo administradores pueden acceder
        if (!tieneAcceso('auditoria', (int)$_SESSION['rol_id'])) { header('Location: /index.php?ruta=dashboard'); exit; }
        
        require_once __DIR__ . '/../src/Infrastructure/Database.php';
        require_once __DIR__ . '/../src/Infrastructure/RepositorioBitacora.php';
        
        $conexion = obtenerConexion();
        
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'usuario_id'  => $_GET['usuario_id'] ?? '',
            'modulo'      => $_GET['modulo'] ?? ''
        ];
        
        $registrosBitacora = obtenerBitacora($conexion, $filtros);
        $empleadosFiltro = obtenerEmpleadosBitacora($conexion);
        $modulosFiltro = obtenerModulosBitacora($conexion);
        
        require __DIR__ . '/../views/auditoria/auditoria.php';
        break;

    case '500':
        http_response_code(500);
        echo "<h1 style='text-align:center; margin-top:50px;'>Error 500 - Error Interno del Servidor</h1>";
        break;

    default:
        http_response_code(404);
        // NUEVA RUTA DEL ERROR 404
        require __DIR__ . '/../views/errores/404.php';
        break;
}