<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold mb-0" href="/index.php?ruta=dashboard">
                <i class="bi bi-shop me-2"></i><span class="d-none d-sm-inline">Club Mamá Guille</span><span class="d-sm-none">CMG</span>
            </a>
            
            <div class="d-flex align-items-center">
                <span class="text-light me-3 d-none d-sm-inline" style="font-size: 0.85rem;">
                    <i class="bi bi-person-circle me-1"></i> 
                    <?= htmlspecialchars($_SESSION['primer_nombre']) ?> 
                    <span class="badge bg-primary ms-1">Rol: <?= htmlspecialchars($_SESSION['rol_id']) ?></span>
                </span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mt-md-5">
        
        <div class="row mb-3 mb-md-5">
            <div class="col-12">
                <h2 class="fw-bold mb-1 dashboard-title">
                    <span class="gradient-text">Panel de Control</span>
                </h2>
                <p class="text-muted mb-0">Bienvenido al sistema de gestión. Selecciona un módulo para comenzar.</p>
                <hr class="divider-glow">
            </div>
        </div>

        <div class="row g-3 g-md-4">
            
            <?php if (tieneAcceso('pos', (int)$_SESSION['rol_id'])): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="/index.php?ruta=pos" class="text-decoration-none">
                    <div class="card shadow-sm h-100 module-card text-center p-2 p-md-3">
                        <div class="card-body px-1 px-md-3">
                            <div class="icon-wrapper bg-success text-success fs-2">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold text-white module-card-title">Punto de Venta</h5>
                            <p class="card-text text-muted small d-none d-md-block">Control de ventas, facturación y proceso de caja.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <?php if (tieneAcceso('inventario', (int)$_SESSION['rol_id'])): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="/index.php?ruta=inventario" class="text-decoration-none">
                    <div class="card shadow-sm h-100 module-card text-center p-2 p-md-3">
                        <div class="card-body px-1 px-md-3">
                            <div class="icon-wrapper bg-primary text-primary fs-2">
                                <i class="bi bi-boxes"></i>
                            </div>
                            <h5 class="card-title fw-bold text-white module-card-title">Inventario</h5>
                            <p class="card-text text-muted small d-none d-md-block">Gestión de productos, categorías, stock y mermas.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <?php if (tieneAcceso('eventos', (int)$_SESSION['rol_id'])): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="/index.php?ruta=eventos" class="text-decoration-none">
                    <div class="card shadow-sm h-100 module-card text-center p-2 p-md-3">
                        <div class="card-body px-1 px-md-3">
                            <div class="icon-wrapper bg-warning text-warning fs-2">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold text-white module-card-title">Eventos y Torneos</h5>
                            <p class="card-text text-muted small d-none d-md-block">Bolas criollas, eventos especiales y fixture.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <?php if (tieneAcceso('contabilidad', (int)$_SESSION['rol_id'])): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="/index.php?ruta=contabilidad" class="text-decoration-none">
                    <div class="card shadow-sm h-100 module-card text-center p-2 p-md-3">
                        <div class="card-body px-1 px-md-3">
                            <div class="icon-wrapper bg-danger text-danger fs-2">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h5 class="card-title fw-bold text-white module-card-title">Contabilidad</h5>
                            <p class="card-text text-muted small d-none d-md-block">Gastos de inventario, ganancias y control de deudores.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <?php if (tieneAcceso('personal', (int)$_SESSION['rol_id'])): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="/index.php?ruta=personal" class="text-decoration-none">
                    <div class="card shadow-sm h-100 module-card text-center p-2 p-md-3">
                        <div class="card-body px-1 px-md-3">
                            <div class="icon-wrapper bg-info text-info fs-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold text-white module-card-title">Gestión de Personal</h5>
                            <p class="card-text text-muted small d-none d-md-block">Registro de empleados y desactivación de perfiles.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <?php if (tieneAcceso('auditoria', (int)$_SESSION['rol_id'])): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="/index.php?ruta=auditoria" class="text-decoration-none">
                    <div class="card shadow-sm h-100 module-card text-center p-2 p-md-3">
                        <div class="card-body px-1 px-md-3">
                            <div class="icon-wrapper bg-secondary text-secondary fs-2" style="background: rgba(124, 138, 160, 0.1) !important; color: var(--text-muted) !important;">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold text-white module-card-title">Auditoría</h5>
                            <p class="card-text text-muted small d-none d-md-block">Bitácora de acciones del sistema y trazabilidad.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

        </div>

        <!-- ============================================= -->
        <!-- RESUMEN DE ACTIVIDAD                          -->
        <!-- ============================================= -->
        <div class="row mt-4 mt-md-5 mb-3">
            <div class="col-12">
                <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i>Resumen de Actividad</h5>
                <hr class="divider-glow mt-2">
            </div>
        </div>

        <!-- Tarjetas KPI -->
        <div class="row g-3 mb-4">
            <!-- Ventas de Hoy -->
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper bg-success fs-3" style="margin-bottom:0;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Ventas Hoy</div>
                            <div class="fs-5 fw-bold" style="color: var(--success);">
                                $<?= number_format($resumenDashboard['ventas_hoy_monto'], 2) ?>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= $resumenDashboard['ventas_hoy_cantidad'] ?> transacción<?= $resumenDashboard['ventas_hoy_cantidad'] !== 1 ? 'es' : '' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Bajo -->
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper fs-3" style="margin-bottom:0; background: <?= count($resumenDashboard['stock_bajo']) > 0 ? 'var(--warning-subtle)' : 'var(--success-subtle)' ?>; color: <?= count($resumenDashboard['stock_bajo']) > 0 ? 'var(--warning)' : 'var(--success)' ?>;">
                            <i class="bi bi-<?= count($resumenDashboard['stock_bajo']) > 0 ? 'exclamation-triangle-fill' : 'check-circle-fill' ?>"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Stock Bajo</div>
                            <div class="fs-5 fw-bold" style="color: <?= count($resumenDashboard['stock_bajo']) > 0 ? 'var(--warning)' : 'var(--success)' ?>;">
                                <?= count($resumenDashboard['stock_bajo']) ?> producto<?= count($resumenDashboard['stock_bajo']) !== 1 ? 's' : '' ?>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= count($resumenDashboard['stock_bajo']) > 0 ? '≤ 5 unidades' : 'Todo en orden' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eventos y Torneos -->
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper bg-warning fs-3" style="margin-bottom:0;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Eventos / Torneos</div>
                            <div class="fs-5 fw-bold" style="color: var(--warning);">
                                <?= $resumenDashboard['eventos_activos'] + $resumenDashboard['torneos_activos'] ?> activo<?= ($resumenDashboard['eventos_activos'] + $resumenDashboard['torneos_activos']) !== 1 ? 's' : '' ?>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= $resumenDashboard['eventos_activos'] ?> evento<?= $resumenDashboard['eventos_activos'] !== 1 ? 's' : '' ?> · <?= $resumenDashboard['torneos_activos'] ?> torneo<?= $resumenDashboard['torneos_activos'] !== 1 ? 's' : '' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deudas Pendientes -->
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper fs-3" style="margin-bottom:0; background: <?= $resumenDashboard['deuda_pendiente'] > 0 ? 'var(--red-subtle)' : 'var(--success-subtle)' ?>; color: <?= $resumenDashboard['deuda_pendiente'] > 0 ? 'var(--red-primary)' : 'var(--success)' ?>;">
                            <i class="bi bi-<?= $resumenDashboard['deuda_pendiente'] > 0 ? 'credit-card-2-front-fill' : 'check-circle-fill' ?>"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Deudas Pendientes</div>
                            <div class="fs-5 fw-bold" style="color: <?= $resumenDashboard['deuda_pendiente'] > 0 ? 'var(--red-primary)' : 'var(--success)' ?>;">
                                $<?= number_format($resumenDashboard['deuda_pendiente'], 2) ?>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= $resumenDashboard['deuda_pendiente'] > 0 ? 'Por cobrar' : 'Sin deudas' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila inferior: Actividad Reciente + Alertas -->
        <div class="row g-3 mb-4 mb-md-5">
            
            <!-- Actividad Reciente (Bitácora) -->
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header pt-3 pb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Actividad Reciente</h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($resumenDashboard['ultimas_acciones'])): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                <span class="small">No hay actividad registrada aún.</span>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-nowrap">Usuario</th>
                                            <th class="text-nowrap">Acción</th>
                                            <th class="text-nowrap">Módulo</th>
                                            <th class="text-nowrap">Fecha / Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resumenDashboard['ultimas_acciones'] as $accion): ?>
                                            <tr>
                                                <td class="text-nowrap">
                                                    <i class="bi bi-person-circle me-1 text-muted"></i>
                                                    <?= htmlspecialchars($accion['nombre_empleado'] ?? 'Sistema') ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $accionTexto = $accion['accion'];
                                                        $badgeClass = 'bg-secondary';
                                                        if (stripos($accionTexto, 'venta') !== false || stripos($accionTexto, 'Venta') !== false) $badgeClass = 'bg-success';
                                                        elseif (stripos($accionTexto, 'activado') !== false || stripos($accionTexto, 'reactivado') !== false) $badgeClass = 'bg-success';
                                                        elseif (stripos($accionTexto, 'desactivado') !== false || stripos($accionTexto, 'eliminado') !== false || stripos($accionTexto, 'cancelado') !== false) $badgeClass = 'bg-danger';
                                                        elseif (stripos($accionTexto, 'creado') !== false || stripos($accionTexto, 'registrado') !== false || stripos($accionTexto, 'nuevo') !== false) $badgeClass = 'bg-primary';
                                                        elseif (stripos($accionTexto, 'editado') !== false || stripos($accionTexto, 'actualizado') !== false) $badgeClass = 'bg-warning';
                                                        elseif (stripos($accionTexto, 'sesión') !== false || stripos($accionTexto, 'login') !== false) $badgeClass = 'bg-info';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>" style="font-size: 0.72rem;"><?= htmlspecialchars($accionTexto) ?></span>
                                                </td>
                                                <td class="text-muted"><?= htmlspecialchars($accion['tabla_afectada'] ?? '—') ?></td>
                                                <td class="text-nowrap text-muted">
                                                    <?php
                                                        $fecha = new DateTime($accion['fecha_hora']);
                                                        echo $fecha->format('d/m/Y h:i A');
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($resumenDashboard['ultimas_acciones'])): ?>
                        <div class="card-footer text-end">
                            <?php if (tieneAcceso('auditoria', (int)$_SESSION['rol_id'])): ?>
                                <a href="/index.php?ruta=auditoria" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-shield-lock me-1"></i>Ver toda la bitácora
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel lateral: Alertas + Estado del equipo -->
            <div class="col-lg-4">
                <!-- Alertas Inteligentes -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header pt-3 pb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-bell-fill me-2" style="color: var(--warning);"></i>Alertas</h6>
                    </div>
                    <div class="card-body py-2">
                        <?php $hayAlertas = false; ?>

                        <?php if (count($resumenDashboard['stock_bajo']) > 0): ?>
                            <?php $hayAlertas = true; ?>
                            <?php foreach ($resumenDashboard['stock_bajo'] as $prod): ?>
                                <div class="d-flex align-items-center gap-2 py-2 border-bottom" style="border-color: var(--border-color) !important;">
                                    <span class="badge bg-warning" style="font-size: 0.65rem;">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </span>
                                    <div class="small flex-grow-1">
                                        <span class="fw-semibold"><?= htmlspecialchars($prod['nombre']) ?></span>
                                    </div>
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">
                                        <?= (int)$prod['stock_unidades_total'] ?> uds
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if ($resumenDashboard['deuda_pendiente'] > 0): ?>
                            <?php $hayAlertas = true; ?>
                            <div class="d-flex align-items-center gap-2 py-2 border-bottom" style="border-color: var(--border-color) !important;">
                                <span class="badge bg-danger" style="font-size: 0.65rem;">
                                    <i class="bi bi-credit-card-2-front-fill"></i>
                                </span>
                                <div class="small flex-grow-1">
                                    <span class="fw-semibold">Deudas por cobrar</span>
                                </div>
                                <span class="badge bg-danger" style="font-size: 0.7rem;">
                                    $<?= number_format($resumenDashboard['deuda_pendiente'], 2) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($resumenDashboard['torneos_activos'] > 0): ?>
                            <?php $hayAlertas = true; ?>
                            <div class="d-flex align-items-center gap-2 py-2 border-bottom" style="border-color: var(--border-color) !important;">
                                <span class="badge bg-info" style="font-size: 0.65rem;">
                                    <i class="bi bi-trophy-fill"></i>
                                </span>
                                <div class="small flex-grow-1">
                                    <span class="fw-semibold"><?= $resumenDashboard['torneos_activos'] ?> torneo<?= $resumenDashboard['torneos_activos'] !== 1 ? 's' : '' ?> en juego</span>
                                </div>
                                <a href="/index.php?ruta=eventos" class="badge bg-primary text-decoration-none" style="font-size: 0.7rem;">Ver</a>
                            </div>
                        <?php endif; ?>

                        <?php if (!$hayAlertas): ?>
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle-fill fs-2 d-block mb-2" style="color: var(--success);"></i>
                                <span class="text-muted small">Todo está en orden. Sin alertas pendientes.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estado del Equipo -->
                <div class="card shadow-sm">
                    <div class="card-header pt-3 pb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Equipo</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Personal Activo</span>
                            <span class="badge bg-success"><?= $resumenDashboard['personal_activo'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-muted">Personal Inactivo</span>
                            <span class="badge bg-danger"><?= $resumenDashboard['personal_inactivo'] ?></span>
                        </div>
                        <?php 
                            $totalPersonal = $resumenDashboard['personal_activo'] + $resumenDashboard['personal_inactivo'];
                            $porcentajeActivo = $totalPersonal > 0 ? round(($resumenDashboard['personal_activo'] / $totalPersonal) * 100) : 0;
                        ?>
                        <div class="progress" style="height: 6px; background: var(--bg-elevated);">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentajeActivo ?>%;" aria-valuenow="<?= $porcentajeActivo ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-muted text-end mt-1" style="font-size: 0.7rem;"><?= $porcentajeActivo ?>% activo</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php if (isset($_SESSION['login_reciente']) && $_SESSION['login_reciente'] === true): ?>
    <!-- Toast de Bienvenida -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Toastify({
                text: "¡Hola, <?= addslashes($_SESSION['primer_nombre'] ?? 'Usuario') ?>! Bienvenido al sistema.",
                duration: 4000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "#10b981", // success
                    borderRadius: "8px",
                    boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                    fontWeight: "600"
                }
            }).showToast();
        });
    </script>
    <?php unset($_SESSION['login_reciente']); ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>