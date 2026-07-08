<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría — Club de Bolas Criollas</title>
    <meta name="description" content="Bitácora del sistema — trazabilidad de acciones y cambios realizados por el personal.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <style>
        .badge-accion {
            font-size: 0.72rem;
            padding: 4px 10px;
            letter-spacing: 0.03em;
        }
        .detalle-json {
            font-size: 0.75rem;
            color: var(--text-muted);
            max-width: 280px;
            word-break: break-word;
        }
        .detalle-json .detalle-key {
            color: var(--blue-primary);
            font-weight: 600;
        }
        .detalle-json .detalle-val {
            color: var(--text-white);
        }
        .filtro-card {
            background: var(--bg-card-alt) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius-lg) !important;
        }
        .tabla-auditoria tr {
            transition: background 0.15s ease;
        }
        .tabla-auditoria tr:hover {
            background: rgba(37, 99, 235, 0.06) !important;
        }
        .hora-registro {
            font-family: 'Inter', monospace;
            font-size: 0.78rem;
            white-space: nowrap;
        }
        .hora-registro .fecha {
            color: var(--text-muted);
        }
        .hora-registro .hora {
            color: var(--blue-primary);
            font-weight: 600;
        }
        .empleado-cell {
            white-space: nowrap;
        }
        .empleado-cell .nombre {
            font-weight: 600;
            color: var(--text-white);
        }
        .empleado-cell .rol {
            font-size: 0.7rem;
            color: var(--text-subtle);
        }
        .contador-resultados {
            font-size: 0.8rem;
            color: var(--text-muted);
            letter-spacing: 0.02em;
        }
        .ip-cell {
            font-family: 'Inter', monospace;
            font-size: 0.75rem;
            color: var(--text-subtle);
        }
    </style>
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="/index.php?ruta=dashboard" class="text-white text-decoration-none me-3 fs-5" title="Volver al Dashboard">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <a class="navbar-brand fw-bold mb-0" href="/index.php?ruta=dashboard">
                    <i class="bi bi-shop me-2"></i>Club Mamá Guille
                </a>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-light me-3" style="font-size: 0.85rem;">
                    <i class="bi bi-person-circle me-1"></i> 
                    <?= htmlspecialchars($_SESSION['primer_nombre'] . ' ' . $_SESSION['primer_apellido']) ?> 
                </span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold"><i class="bi bi-shield-lock-fill me-2" style="color: var(--text-muted);"></i><span class="gradient-text">Auditoría del Sistema</span></h2>
                <p class="text-muted">Bitácora de todas las acciones realizadas por el personal. Trazabilidad completa.</p>
            </div>
            <div class="text-end">
                <span class="contador-resultados">
                    <i class="bi bi-list-check me-1"></i>
                    <?= count($registrosBitacora) ?> registro<?= count($registrosBitacora) !== 1 ? 's' : '' ?> encontrado<?= count($registrosBitacora) !== 1 ? 's' : '' ?>
                </span>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtro-card p-3 mb-4">
            <form method="GET" action="/index.php" class="row g-2 align-items-end" id="formFiltros">
                <input type="hidden" name="ruta" value="auditoria">
                
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1"><i class="bi bi-calendar3 me-1"></i>Desde</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_desde" 
                           value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1"><i class="bi bi-calendar3 me-1"></i>Hasta</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_hasta" 
                           value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1"><i class="bi bi-person me-1"></i>Empleado</label>
                    <select class="form-select form-select-sm" name="usuario_id">
                        <option value="">Todos</option>
                        <?php foreach ($empleadosFiltro as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($filtros['usuario_id'] == $emp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1"><i class="bi bi-diagram-3 me-1"></i>Módulo</label>
                    <select class="form-select form-select-sm" name="modulo">
                        <option value="">Todos</option>
                        <?php foreach ($modulosFiltro as $mod): ?>
                            <option value="<?= htmlspecialchars($mod) ?>" <?= ($filtros['modulo'] === $mod) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mod) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i>Filtrar
                    </button>
                    <a href="/index.php?ruta=auditoria" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla de registros -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 tabla-auditoria">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4" style="min-width: 150px;">Fecha y Hora</th>
                                <th style="min-width: 140px;">Empleado</th>
                                <th style="min-width: 130px;">Acción</th>
                                <th>Módulo</th>
                                <th>Detalles</th>
                                <th class="text-center pe-4">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registrosBitacora)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-shield-check" style="font-size: 2.5rem; color: var(--text-subtle);"></i>
                                        <p class="text-muted mt-3 mb-0">No hay registros de auditoría<?= !empty(array_filter($filtros)) ? ' con los filtros aplicados' : ' todavía' ?>.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrosBitacora as $reg): ?>
                                    <?php
                                        // Determinar el color del badge según la acción
                                        $accion = $reg['accion'];
                                        $badgeClass = 'bg-secondary';
                                        $badgeIcon = 'bi-circle-fill';
                                        
                                        if (str_contains($accion, 'creado') || str_contains($accion, 'registrad') || str_contains($accion, 'Combo creado')) {
                                            $badgeClass = 'bg-success'; $badgeIcon = 'bi-plus-circle-fill';
                                        } elseif (str_contains($accion, 'editado') || str_contains($accion, 'Entrada') || str_contains($accion, 'reactivado')) {
                                            $badgeClass = 'bg-primary'; $badgeIcon = 'bi-pencil-fill';
                                        } elseif (str_contains($accion, 'desactivado') || str_contains($accion, 'cancelado') || str_contains($accion, 'Merma') || str_contains($accion, 'Salida')) {
                                            $badgeClass = 'bg-danger'; $badgeIcon = 'bi-exclamation-triangle-fill';
                                        } elseif (str_contains($accion, 'Venta') || str_contains($accion, 'Compra') || str_contains($accion, 'liquidada')) {
                                            $badgeClass = 'bg-warning'; $badgeIcon = 'bi-currency-dollar';
                                        } elseif (str_contains($accion, 'sesión') || str_contains($accion, 'Inicio')) {
                                            $badgeClass = 'bg-info'; $badgeIcon = 'bi-box-arrow-in-right';
                                        } elseif (str_contains($accion, 'Resultado') || str_contains($accion, 'partido')) {
                                            $badgeClass = 'bg-warning'; $badgeIcon = 'bi-trophy-fill';
                                        } elseif (str_contains($accion, 'Deuda registrada')) {
                                            $badgeClass = 'bg-danger'; $badgeIcon = 'bi-exclamation-circle-fill';
                                        }

                                        // Decodificar detalles JSON
                                        $detalles = !empty($reg['detalles']) ? json_decode($reg['detalles'], true) : null;

                                        // Mapeo de tablas a nombres de módulo legibles
                                        $nombresModulo = [
                                            'Productos' => 'Inventario',
                                            'Venta' => 'Punto de Venta',
                                            'Movimiento_Inventario' => 'Contabilidad',
                                            'Combos' => 'Combos',
                                            'Usuario' => 'Personal',
                                            'Evento' => 'Eventos',
                                            'Torneo_Partido' => 'Torneos',
                                            'Categorias' => 'Inventario',
                                            'Deudores' => 'Deudores',
                                            'Cuentas_Por_Cobrar' => 'Deudores',
                                        ];
                                        $moduloLegible = $nombresModulo[$reg['tabla_afectada']] ?? $reg['tabla_afectada'] ?? '—';
                                    ?>
                                    <tr>
                                        <td class="ps-4 hora-registro">
                                            <span class="fecha"><?= date('d/m/Y', strtotime($reg['fecha_hora'])) ?></span><br>
                                            <span class="hora"><?= date('h:i:s A', strtotime($reg['fecha_hora'])) ?></span>
                                        </td>
                                        <td class="empleado-cell">
                                            <span class="nombre"><?= htmlspecialchars($reg['nombre_empleado'] ?? 'Sistema') ?></span><br>
                                            <span class="rol"><?= htmlspecialchars($reg['rol_nombre'] ?? '') ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-accion <?= $badgeClass ?>">
                                                <i class="bi <?= $badgeIcon ?> me-1" style="font-size: 0.6rem;"></i><?= htmlspecialchars($accion) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold" style="font-size: 0.82rem;"><?= htmlspecialchars($moduloLegible) ?></span>
                                            <?php if ($reg['registro_id']): ?>
                                                <br><span class="text-muted" style="font-size: 0.7rem;">ID: <?= $reg['registro_id'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="detalle-json">
                                            <?php if ($detalles): ?>
                                                <?php foreach ($detalles as $key => $val): ?>
                                                    <span class="detalle-key"><?= htmlspecialchars($key) ?>:</span>
                                                    <span class="detalle-val"><?= htmlspecialchars(is_array($val) ? json_encode($val) : $val) ?></span>
                                                    <?php if ($key !== array_key_last($detalles)): ?><span class="text-muted"> · </span><?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4 ip-cell">
                                            <?= htmlspecialchars($reg['ip_direccion'] ?? '—') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>Se muestran los últimos 200 registros. Usa los filtros para refinar la búsqueda.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
