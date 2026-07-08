<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
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
                    <span class="badge bg-primary ms-1">Rol: <?= htmlspecialchars($_SESSION['rol_id']) ?></span>
                </span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-1">
                            <span class="gradient-text"><i class="bi bi-people-fill me-2"></i>Gestión de Personal</span>
                        </h2>
                        <p class="text-muted mb-0">Administra los empleados del sistema. Registra nuevo personal o desactiva perfiles.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoPersonal">
                            <i class="bi bi-person-plus-fill me-1"></i>Nuevo Personal
                        </button>
                    </div>
                </div>
                <hr class="divider-glow">
            </div>
        </div>

        <!-- Alertas de resultado -->
        <?= renderizarMensajesFlash() ?>


        <!-- Resumen rápido -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper bg-primary fs-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total de Personal</div>
                            <div class="fs-4 fw-bold text-white"><?= count($listaPersonal) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper bg-success fs-3">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Activos</div>
                            <div class="fs-4 fw-bold" style="color: var(--success);">
                                <?= count(array_filter($listaPersonal, fn($u) => $u['activo'] == 1)) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-wrapper bg-danger fs-3">
                            <i class="bi bi-person-x-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Inactivos</div>
                            <div class="fs-4 fw-bold" style="color: var(--red-primary);">
                                <?= count(array_filter($listaPersonal, fn($u) => $u['activo'] == 0)) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buscador -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscarPersonal" class="form-control" placeholder="Buscar por nombre, cédula o usuario...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filtroEstado" class="form-select">
                    <option value="todos">Todos los estados</option>
                    <option value="activo">Solo Activos</option>
                    <option value="inactivo">Solo Inactivos</option>
                </select>
            </div>
        </div>

        <!-- Tabla de personal -->
        <div class="card shadow-sm mb-5">
            <div class="card-header pt-3 pb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-table me-2 text-primary"></i>Listado de Personal</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" id="tablaPersonal">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-nowrap"><i class="bi bi-hash me-1"></i>ID</th>
                                <th class="text-nowrap"><i class="bi bi-person me-1"></i>Nombre Completo</th>
                                <th class="text-nowrap"><i class="bi bi-card-text me-1"></i>Cédula</th>
                                <th class="text-nowrap"><i class="bi bi-at me-1"></i>Usuario</th>
                                <th class="text-nowrap"><i class="bi bi-shield me-1"></i>Rol</th>
                                <th class="text-nowrap"><i class="bi bi-toggle-on me-1"></i>Estado</th>
                                <th class="text-nowrap"><i class="bi bi-calendar me-1"></i>Fecha Registro</th>
                                <th class="text-center text-nowrap"><i class="bi bi-gear me-1"></i>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaPersonal)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay personal registrado aún.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaPersonal as $persona): ?>
                                    <tr class="fila-personal" 
                                        data-nombre="<?= htmlspecialchars(strtolower($persona['primer_nombre'] . ' ' . ($persona['segundo_nombre'] ?? '') . ' ' . $persona['primer_apellido'] . ' ' . ($persona['segundo_apellido'] ?? ''))) ?>"
                                        data-cedula="<?= htmlspecialchars(strtolower($persona['cedula'])) ?>"
                                        data-username="<?= htmlspecialchars(strtolower($persona['username'])) ?>"
                                        data-estado="<?= $persona['activo'] ? 'activo' : 'inactivo' ?>">
                                        <td class="fw-bold text-muted">#<?= $persona['id'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width:36px; height:36px; background: <?= $persona['activo'] ? 'var(--blue-subtle)' : 'var(--red-subtle)' ?>; color: <?= $persona['activo'] ? 'var(--blue-primary)' : 'var(--red-primary)' ?>; font-size:0.85rem; font-weight:700;">
                                                    <?= strtoupper(substr($persona['primer_nombre'], 0, 1) . substr($persona['primer_apellido'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-white">
                                                        <?= htmlspecialchars($persona['primer_nombre'] . ' ' . ($persona['segundo_nombre'] ?? '')) ?>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <?= htmlspecialchars($persona['primer_apellido'] . ' ' . ($persona['segundo_apellido'] ?? '')) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($persona['cedula']) ?></span></td>
                                        <td class="text-muted"><?= htmlspecialchars($persona['username']) ?></td>
                                        <td>
                                            <?php if ($persona['rol_nombre'] === 'Administrador'): ?>
                                                <span class="badge bg-primary"><i class="bi bi-shield-fill me-1"></i><?= htmlspecialchars($persona['rol_nombre']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-info"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($persona['rol_nombre']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($persona['activo']): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?= date('d/m/Y', strtotime($persona['fecha_creacion'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($persona['id'] != $_SESSION['usuario_id']): ?>
                                                <?php if ($persona['activo']): ?>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmarCambioEstado(<?= $persona['id'] ?>, '<?= htmlspecialchars($persona['primer_nombre'] . ' ' . $persona['primer_apellido']) ?>', 'desactivar')">
                                                        <i class="bi bi-person-x me-1"></i>Desactivar
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-success"
                                                            onclick="confirmarCambioEstado(<?= $persona['id'] ?>, '<?= htmlspecialchars($persona['primer_nombre'] . ' ' . $persona['primer_apellido']) ?>', 'activar')">
                                                        <i class="bi bi-person-check me-1"></i>Reactivar
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Tu cuenta</span>
                                            <?php endif; ?>
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

    <!-- ============================================================ -->
    <!-- MODAL: REGISTRAR NUEVO PERSONAL                              -->
    <!-- ============================================================ -->
    <div class="modal fade" id="modalNuevoPersonal" tabindex="-1" aria-labelledby="modalNuevoPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalNuevoPersonalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Personal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="/index.php?ruta=registrar_personal" method="POST" class="needs-validation" novalidate>
                    <?= campoCSRF() ?>
                    <div class="modal-body">
                        
                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4" role="alert">
                            <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
                            <div class="small">
                                Completa todos los campos obligatorios (<span class="text-danger">*</span>) para registrar un nuevo empleado. 
                                El trabajador podrá iniciar sesión con el usuario y contraseña asignados.
                            </div>
                        </div>

                        <!-- Fila 1: Nombres -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Primer Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="primer_nombre" class="form-control" placeholder="Ej: Juan" required>
                                <div class="invalid-feedback">El primer nombre es obligatorio.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" name="segundo_nombre" class="form-control" placeholder="Ej: Carlos">
                            </div>
                        </div>

                        <!-- Fila 2: Apellidos -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Primer Apellido <span class="text-danger">*</span></label>
                                <input type="text" name="primer_apellido" class="form-control" placeholder="Ej: Pérez" required>
                                <div class="invalid-feedback">El primer apellido es obligatorio.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" name="segundo_apellido" class="form-control" placeholder="Ej: Gómez">
                            </div>
                        </div>

                        <hr class="my-3" style="border-color: var(--border-color);">

                        <!-- Fila 3: Cédula y Rol -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-card-text me-1"></i>Cédula <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="tipo_cedula" class="form-select shadow-sm" style="max-width: 80px;" required>
                                        <option value="V-">V-</option>
                                        <option value="E-">E-</option>
                                    </select>
                                    <input type="text" name="cedula_numero" class="form-control shadow-sm" placeholder="12345678" pattern="[0-9]+" required>
                                </div>
                                <div class="invalid-feedback d-block d-none" id="cedulaFeedback">Ingresa un número de cédula válido.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-shield me-1"></i>Rol <span class="text-danger">*</span></label>
                                <select name="rol_id" class="form-select" required>
                                    <option value="" disabled selected>Selecciona un rol</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <?php 
                                            // No se puede registrar un Administrador (5) ni Super Administrador (1) desde la UI
                                            if (in_array($rol['id'], [1, 5])) continue; 
                                        ?>
                                        <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?> — <?= htmlspecialchars($rol['descripcion']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Selecciona un rol válido.</div>
                            </div>
                        </div>

                        <hr class="my-3" style="border-color: var(--border-color);">

                        <!-- Fila 4: Credenciales -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-person me-1"></i>Usuario <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="Mínimo 4 caracteres" minlength="4" required>
                                <div class="invalid-feedback">El usuario debe tener al menos 4 caracteres.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-lock me-1"></i>Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
                                <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bi bi-check-circle me-1"></i>Registrar Personal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL: CONFIRMAR CAMBIO DE ESTADO                            -->
    <!-- ============================================================ -->
    <div class="modal fade" id="modalCambioEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="modalCambioEstadoHeader">
                    <h5 class="modal-title fw-bold text-white" id="modalCambioEstadoTitulo"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-3">
                        <i class="fs-1 mb-3 d-block" id="modalCambioEstadoIcono"></i>
                        <p class="mb-1 fw-semibold text-white" id="modalCambioEstadoMensaje"></p>
                        <p class="text-muted small" id="modalCambioEstadoDetalle"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <form id="formCambioEstado" method="POST" class="d-inline">
                    <?= campoCSRF() ?>
                        <input type="hidden" name="usuario_id" id="inputUsuarioId">
                        <input type="hidden" name="nueva_accion" id="inputNuevaAccion">
                        <button type="submit" class="btn fw-bold" id="btnConfirmarCambio"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================================
        // Búsqueda y filtro en tiempo real
        // ============================================================
        const inputBuscar = document.getElementById('buscarPersonal');
        const selectEstado = document.getElementById('filtroEstado');
        const filas = document.querySelectorAll('.fila-personal');

        function filtrarTabla() {
            const texto = inputBuscar.value.toLowerCase().trim();
            const estado = selectEstado.value;

            filas.forEach(fila => {
                const nombre = fila.dataset.nombre || '';
                const cedula = fila.dataset.cedula || '';
                const username = fila.dataset.username || '';
                const estadoFila = fila.dataset.estado || '';

                const coincideTexto = nombre.includes(texto) || cedula.includes(texto) || username.includes(texto);
                const coincideEstado = estado === 'todos' || estadoFila === estado;

                fila.style.display = (coincideTexto && coincideEstado) ? '' : 'none';
            });
        }

        inputBuscar.addEventListener('input', filtrarTabla);
        selectEstado.addEventListener('change', filtrarTabla);

        // ============================================================
        // Modal de confirmación para cambio de estado
        // ============================================================
        function confirmarCambioEstado(userId, nombreCompleto, accion) {
            const modal = document.getElementById('modalCambioEstado');
            const header = document.getElementById('modalCambioEstadoHeader');
            const titulo = document.getElementById('modalCambioEstadoTitulo');
            const icono = document.getElementById('modalCambioEstadoIcono');
            const mensaje = document.getElementById('modalCambioEstadoMensaje');
            const detalle = document.getElementById('modalCambioEstadoDetalle');
            const btnConfirmar = document.getElementById('btnConfirmarCambio');
            const form = document.getElementById('formCambioEstado');

            document.getElementById('inputUsuarioId').value = userId;
            document.getElementById('inputNuevaAccion').value = accion;

            if (accion === 'desactivar') {
                header.className = 'modal-header bg-danger';
                titulo.innerHTML = '<i class="bi bi-person-x-fill me-2"></i>Desactivar Personal';
                icono.className = 'bi bi-person-x-fill fs-1 mb-3 d-block text-danger';
                mensaje.textContent = '¿Deseas desactivar a ' + nombreCompleto + '?';
                detalle.textContent = 'El trabajador no podrá iniciar sesión en el sistema. Podrás reactivarlo en cualquier momento.';
                btnConfirmar.className = 'btn btn-danger fw-bold';
                btnConfirmar.innerHTML = '<i class="bi bi-person-x me-1"></i>Sí, Desactivar';
                form.action = '/index.php?ruta=cambiar_estado_personal';
            } else {
                header.className = 'modal-header bg-success';
                titulo.innerHTML = '<i class="bi bi-person-check-fill me-2"></i>Reactivar Personal';
                icono.className = 'bi bi-person-check-fill fs-1 mb-3 d-block text-success';
                mensaje.textContent = '¿Deseas reactivar a ' + nombreCompleto + '?';
                detalle.textContent = 'El trabajador podrá volver a iniciar sesión en el sistema.';
                btnConfirmar.className = 'btn btn-success fw-bold';
                btnConfirmar.innerHTML = '<i class="bi bi-person-check me-1"></i>Sí, Reactivar';
                form.action = '/index.php?ruta=cambiar_estado_personal';
            }

            new bootstrap.Modal(modal).show();
        }

        // ============================================================
        // Validación Bootstrap
        // ============================================================
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

</body>
</html>
