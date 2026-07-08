<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contabilidad — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css?v=3">
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
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2" style="color: var(--red-primary);"></i><span class="gradient-text">Panel de Contabilidad</span></h2>
                <p class="text-muted mb-0">Resumen financiero general basado en el inventario y las ventas registradas.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($_SESSION['rol_id'] == 1): ?>
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCompra"><i class="bi bi-box-arrow-in-down me-1"></i> Registrar Compra</button>
                    <button class="btn btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#modalMerma"><i class="bi bi-trash3 me-1"></i> Registrar Merma</button>
                <?php endif; ?>
                <a href="/index.php?ruta=deudores" class="btn btn-warning fw-bold shadow-sm"><i class="bi bi-people"></i> Deudores</a>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
            <div class="col">
                <div class="card shadow-sm border-4 h-100" style="border-left-color: var(--success) !important;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3" style="color: var(--success); font-size: 0.8rem;"><i class="bi bi-cash-stack me-1"></i> Ingresos Brutos</h6>
                        <h4 class="fw-bold mb-0">$<?= number_format($resumen['ingresos_totales'], 2) ?></h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Por ventas realizadas</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-4 h-100" style="border-left-color: var(--blue-primary) !important;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3" style="color: var(--blue-primary); font-size: 0.8rem;" title="Inversión total en almacén: $<?= number_format($resumen['inversion_compras'], 2) ?>"><i class="bi bi-box-seam me-1"></i> Costo de Ventas</h6>
                        <h4 class="fw-bold mb-0">$<?= number_format($resumen['costo_ventas'], 2) ?></h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Costo de mercancía entregada</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-4 h-100" style="border-left-color: var(--red-primary) !important;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3" style="color: var(--red-primary); font-size: 0.8rem;"><i class="bi bi-exclamation-triangle me-1"></i> Pérdidas (Merma)</h6>
                        <h4 class="fw-bold mb-0" style="color: var(--red-primary);">-$<?= number_format($resumen['merma_costo'], 2) ?></h4>
                        <small class="text-muted" style="font-size: 0.75rem;"><?= $resumen['merma_cantidad'] ?> unidades perdidas</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-4 h-100" style="border-left-color: var(--warning) !important;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3" style="color: var(--warning); font-size: 0.8rem;"><i class="bi bi-bank me-1"></i> Impuestos (16%)</h6>
                        <h4 class="fw-bold mb-0">$<?= number_format($resumen['impuestos_estimados'], 2) ?></h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Impuesto retenido estimado</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <?php 
                    $colorGanancia = $resumen['ganancia_neta'] >= 0 ? 'var(--success)' : 'var(--red-primary)'; 
                ?>
                <div class="card shadow-sm border-4 h-100" style="border-left-color: <?= $colorGanancia ?> !important;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3" style="color: <?= $colorGanancia ?>; font-size: 0.8rem;"><i class="bi bi-graph-up-arrow me-1"></i> Ganancia Neta</h6>
                        <h4 class="fw-bold mb-0" style="color: <?= $colorGanancia ?>;">
                            <?= $resumen['ganancia_neta'] >= 0 ? '+' : '-' ?>$<?= number_format(abs($resumen['ganancia_neta']), 2) ?>
                        </h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Ingresos - Costo - Mermas</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-4" style="border-left-color: var(--info) !important;">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <h5 class="mb-0 fw-bold me-3"><i class="bi bi-stars me-2" style="color: var(--info);"></i>Ganancia Proyectada a Futuro</h5>
                            <span class="text-muted small d-none d-md-block" style="border-left: 1px solid var(--border-color); padding-left: 1rem;">Valor total estimado al vender el inventario físico actual</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <h4 class="fw-bold mb-0" style="color: var(--info);">+$<?= number_format($resumen['futura_ganancia'], 2) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pt-3 pb-2">
                        <h5 class="fw-bold"><i class="bi bi-calendar-check me-2"></i>Registro Mensual General</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center datatable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Mes (Año-Mes)</th>
                                        <th style="color: var(--success);">Total Ingresos</th>
                                        <th style="color: var(--blue-primary);" title="Costo de Adquisición de la Mercancía Vendida">Costo de Ventas (COGS)</th>
                                        <th style="color: var(--red-primary);">Total Mermas</th>
                                        <th>Ganancia Neta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($registroMensual)): ?>
                                        <tr><td colspan="5" class="py-3 text-muted">No hay registros financieros.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($registroMensual as $mes): ?>
                                            <tr>
                                                <td class="fw-bold"><?= htmlspecialchars($mes['mes']) ?></td>
                                                <td style="color: var(--success);">$<?= number_format($mes['ingresos'], 2) ?></td>
                                                <td style="color: var(--blue-primary);">$<?= number_format($mes['costo_ventas'], 2) ?></td>
                                                <td style="color: var(--red-primary);">-$<?= number_format($mes['mermas'], 2) ?></td>
                                                <td class="fw-bold" style="color: <?= $mes['ganancia_neta'] >= 0 ? 'var(--success)' : 'var(--red-primary)' ?>;">
                                                    $<?= number_format($mes['ganancia_neta'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pt-3 pb-2">
                        <h5 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de Movimientos</h5>
                        <small class="text-muted">Registro detallado de Ventas, Compras de mercancía y Mermas.</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 datatable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-3">Fecha</th>
                                        <th>Tipo</th>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th>Monto (Ingreso / Gasto)</th>
                                        <th style="color: var(--success);">Ganancia Real</th>
                                        <?php if ($_SESSION['rol_id'] == 1): ?>
                                            <th class="text-center">Acción</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($historial)): ?>
                                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay movimientos recientes.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($historial as $mov): ?>
                                            <tr>
                                                <td class="ps-3 text-muted small"><?= htmlspecialchars($mov['fecha_hora']) ?></td>
                                                <td>
                                                    <?php if ($mov['tipo_movimiento'] == 'Compra_Proveedor'): ?>
                                                        <span class="badge bg-primary">Compra</span>
                                                    <?php elseif ($mov['tipo_movimiento'] == 'Ajuste_Merma'): ?>
                                                        <span class="badge bg-danger">Merma</span>
                                                    <?php elseif ($mov['tipo_movimiento'] == 'Venta'): ?>
                                                        <span class="badge bg-success mb-1">Venta POS</span><br>
                                                        <?php if (!empty($mov['metodo_pago'])): ?>
                                                            <small class="text-muted"><i class="bi bi-wallet2"></i> <?= htmlspecialchars($mov['metodo_pago']) ?></small>
                                                            <?php if ($mov['metodo_pago'] === 'Pago Móvil'): ?>
                                                                <br><small class="text-muted" style="font-size: 0.7rem;">Ref: <?= htmlspecialchars($mov['referencia_pago']) ?> | Tel: <?= htmlspecialchars($mov['telefono_pago']) ?></small>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?= htmlspecialchars($mov['tipo_movimiento']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-bold text-truncate" style="max-width: 150px;"><?= htmlspecialchars($mov['producto_nombre']) ?></td>
                                                <td><?= $mov['cantidad'] ?>u</td>
                                                
                                                <td class="fw-bold">
                                                    <?php if($mov['tipo_movimiento'] == 'Compra_Proveedor'): ?>
                                                        <span style="color: var(--blue-primary);">$<?= number_format($mov['monto_total'], 2) ?> <small class="fw-normal text-muted">(Inversión)</small></span>
                                                    <?php elseif($mov['tipo_movimiento'] == 'Ajuste_Merma'): ?>
                                                        <span style="color: var(--red-primary);">-$<?= number_format($mov['monto_total'], 2) ?> <small class="fw-normal text-muted">(Pérdida)</small></span>
                                                    <?php else: ?>
                                                        <span style="color: var(--success);">+$<?= number_format($mov['monto_total'], 2) ?> <small class="fw-normal text-muted">(Ingreso)</small></span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="fw-bold" style="color: <?= $mov['ganancia'] >= 0 ? 'var(--success)' : 'var(--red-primary)' ?>;">
                                                    <?php if($mov['tipo_movimiento'] == 'Compra_Proveedor'): ?>
                                                        <span class="text-muted fw-normal">-</span>
                                                    <?php else: ?>
                                                        <?= $mov['ganancia'] >= 0 ? '+' : '-' ?>$<?= number_format(abs($mov['ganancia']), 2) ?>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <?php if ($_SESSION['rol_id'] == 1): ?>
                                                    <td class="text-center">
                                                        <?php if ($mov['tipo_movimiento'] == 'Compra_Proveedor' || $mov['tipo_movimiento'] == 'Ajuste_Merma'): ?>
                                                            <button class="btn btn-sm btn-outline-secondary btn-editar-movimiento"
                                                                data-bs-toggle="modal" data-bs-target="#modalEditarMov"
                                                                data-id="<?= $mov['id'] ?>"
                                                                data-producto="<?= $mov['producto_id'] ?>"
                                                                data-cantidad="<?= $mov['cantidad'] ?>"
                                                                data-costo="<?= $mov['monto_total'] ?>">
                                                                <i class="bi bi-pencil-square"></i> Editar
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-muted small">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalCompra" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/index.php?ruta=registrar_movimiento_contable" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Registrar Gasto de Reposición</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="tipo_movimiento" value="Compra_Proveedor">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Producto Adquirido</label>
                                <select class="form-select" name="producto_id" required>
                                    <option value="">Seleccione un producto...</option>
                                    <?php foreach ($productos as $prod): ?>
                                        <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Formato</label>
                                <select class="form-select" name="formato" required>
                                    <option value="Unidad">Unidad</option>
                                    <option value="Caja">Caja</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Cantidad Ingresada</label>
                                <input type="number" class="form-control" name="cantidad" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Costo ($)</label>
                                <input type="number" step="0.01" class="form-control" name="costo_unitario" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Compra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMerma" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/index.php?ruta=registrar_movimiento_contable" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Registrar Merma (Pérdida)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="tipo_movimiento" value="Ajuste_Merma">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Producto Perdido</label>
                                <select class="form-select" name="producto_id" required>
                                    <option value="">Seleccione un producto...</option>
                                    <?php foreach ($productos as $prod): ?>
                                        <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Formato</label>
                                <select class="form-select" name="formato" required>
                                    <option value="Unidad">Unidad</option>
                                    <option value="Caja">Caja</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Cantidad Perdida</label>
                                <input type="number" class="form-control" name="cantidad" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Costo ($)</label>
                                <input type="number" step="0.01" class="form-control" name="costo_unitario" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Registrar Merma</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarMov" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/index.php?ruta=editar_movimiento_contable" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title">Modificar Registro</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_movimiento" id="edit_id_movimiento">
                        <p class="text-muted small mb-3">El sistema ajustará el stock físico automáticamente.</p>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Producto</label>
                                <select class="form-select" name="producto_id" id="edit_producto_id" required>
                                    <?php foreach ($productos as $prod): ?>
                                        <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Formato</label>
                                <select class="form-select" name="formato" required>
                                    <option value="Unidad" selected>Unidad</option>
                                    <option value="Caja">Caja</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nueva Cantidad</label>
                                <input type="number" class="form-control" name="cantidad" id="edit_cantidad" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nuevo Costo Total ($)</label>
                                <input type="number" step="0.01" class="form-control" name="costo_unitario" id="edit_costo" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEditar = document.getElementById('modalEditarMov');
            if(modalEditar){
                modalEditar.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    document.getElementById('edit_id_movimiento').value = button.getAttribute('data-id');
                    document.getElementById('edit_producto_id').value = button.getAttribute('data-producto');
                    document.getElementById('edit_cantidad').value = button.getAttribute('data-cantidad');
                    document.getElementById('edit_costo').value = button.getAttribute('data-costo');
                });
            }
        });
    </script>
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('.flatpickr-date', {
                locale: 'es',
                dateFormat: 'Y-m-d'
            });
            flatpickr('.flatpickr-datetime', {
                locale: 'es',
                enableTime: true,
                dateFormat: 'Y-m-d H:i'
            });
        });
    </script>
    <!-- DataTables JS y jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                    lengthMenu: "Mostrar _MENU_"
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
            });
        });
    </script>
</body>
</html>