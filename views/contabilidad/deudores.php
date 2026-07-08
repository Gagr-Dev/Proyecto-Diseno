<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deudores — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="/index.php?ruta=contabilidad" class="text-white text-decoration-none me-3 fs-5" title="Volver a Contabilidad">
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
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold"><i class="bi bi-people-fill me-2" style="color: var(--warning);"></i><span class="gradient-text">Gestión de Deudores</span></h2>
                <p class="text-muted">Control de clientes fijos y cuentas por cobrar (Fiado).</p>
            </div>
            
            <button class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoDeudor">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Deudor
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Nombre Completo</th>
                                <th>Cédula</th>
                                <th>Teléfono</th>
                                <th class="text-end">Deuda Total Pendiente</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deudores)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay deudores registrados actualmente.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deudores as $deudor): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?= htmlspecialchars($deudor['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($deudor['cedula']) ?: 'N/A' ?></td>
                                        <td><?= htmlspecialchars($deudor['telefono']) ?: 'N/A' ?></td>
                                        <td class="text-end fw-bold" style="color: var(--red-primary);">
                                            $<?= number_format($deudor['deuda_total'], 2) ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button class="btn btn-sm btn-primary me-1" title="Añadir Deuda" 
                                                    data-bs-toggle="modal" data-bs-target="#modalNuevaDeuda"
                                                    data-id="<?= $deudor['id'] ?>" 
                                                    data-nombre="<?= htmlspecialchars($deudor['nombre_completo']) ?>">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                            
                                            <button class="btn btn-sm btn-success" title="Registrar Pago Completo"
                                                    data-bs-toggle="modal" data-bs-target="#modalPagarDeuda"
                                                    data-id="<?= $deudor['id'] ?>" 
                                                    data-nombre="<?= htmlspecialchars($deudor['nombre_completo']) ?>"
                                                    data-deuda="<?= $deudor['deuda_total'] ?>"
                                                    <?= $deudor['deuda_total'] <= 0 ? 'disabled' : '' ?>>
                                                <i class="bi bi-currency-dollar"></i> Pagar
                                            </button>
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

    <div class="modal fade" id="modalNuevoDeudor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/index.php?ruta=guardar_deudor" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-person-plus"></i> Registrar Nuevo Deudor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" class="form-control" name="nombre_completo" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Cédula</label>
                                <input type="text" class="form-control" name="cedula">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" class="form-control" name="telefono">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Deudor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevaDeuda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/index.php?ruta=guardar_deuda" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Añadir Deuda</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="deudor_id" id="input_nueva_deuda_id">
                        <p>Fiando mercancía a: <strong id="texto_nueva_deuda_nombre" style="color: var(--blue-primary);"></strong></p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto ($)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="monto" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción / Concepto</label>
                            <input type="text" class="form-control" name="descripcion" placeholder="Ej: 2 Cajas de Polar" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Deuda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPagarDeuda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/index.php?ruta=pagar_deuda" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-header bg-warning border-bottom-0">
                        <h5 class="modal-title fw-bold" style="color: #000 !important;"><i class="bi bi-exclamation-triangle"></i> Liquidar Deuda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center pb-4">
                        <input type="hidden" name="deudor_id" id="input_pagar_deuda_id">
                        <i class="bi bi-currency-dollar" style="font-size: 3rem; color: var(--success);"></i>
                        <h4 class="mt-3">¿Confirmar pago total?</h4>
                        <p class="text-muted mb-1">Se marcarán como pagadas todas las cuentas pendientes de:</p>
                        <h5 id="texto_pagar_deuda_nombre" class="fw-bold mb-3"></h5>
                        <div class="alert alert-danger d-inline-block px-4">
                            Monto a liquidar: <strong class="fs-5">$<span id="texto_pagar_deuda_monto"></span></strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold">Confirmar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Interceptar Modal de Añadir Deuda
            const modalNuevaDeuda = document.getElementById('modalNuevaDeuda');
            modalNuevaDeuda.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                document.getElementById('input_nueva_deuda_id').value = button.getAttribute('data-id');
                document.getElementById('texto_nueva_deuda_nombre').textContent = button.getAttribute('data-nombre');
            });

            // Interceptar Modal de Pagar
            const modalPagarDeuda = document.getElementById('modalPagarDeuda');
            modalPagarDeuda.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                document.getElementById('input_pagar_deuda_id').value = button.getAttribute('data-id');
                document.getElementById('texto_pagar_deuda_nombre').textContent = button.getAttribute('data-nombre');
                document.getElementById('texto_pagar_deuda_monto').textContent = button.getAttribute('data-deuda');
            });
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