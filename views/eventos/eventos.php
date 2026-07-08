<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos y Torneos — Club de Bolas Criollas</title>
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
                    <i class="bi bi-trophy-fill me-2" style="color: var(--warning);"></i>Eventos y Torneos
                </a>
            </div>
            <span class="navbar-text d-none d-sm-inline" style="color: var(--text-muted); font-size: 0.85rem;">
                Módulo de Eventos
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        
        <?= renderizarMensajesFlash() ?>


        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h2 class="fw-bold mb-0"><span class="gradient-text">Gestión de Eventos y Torneos</span></h2>
            <div>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEvento">
                    <i class="bi bi-calendar-plus me-1"></i> Nuevo Evento Simple
                </button>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="eventosTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="torneos-tab" data-bs-toggle="tab" data-bs-target="#torneos" type="button" role="tab">
                    <i class="bi bi-trophy-fill me-1" style="color: var(--warning);"></i> Crear Torneo
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="registrados-tab" data-bs-toggle="tab" data-bs-target="#registrados" type="button" role="tab">
                    <i class="bi bi-folder-fill me-1"></i> Torneos Registrados
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="eventos-tab" data-bs-toggle="tab" data-bs-target="#eventos" type="button" role="tab">
                    <i class="bi bi-calendar-event me-1"></i> Eventos Generales
                </button>
            </li>
        </ul>

        <div class="tab-content" id="eventosTabsContent">
            
            <div class="tab-pane fade show active" id="torneos" role="tabpanel">
                <div class="row">
                    
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header fw-bold" style="background: var(--gradient-blue) !important; color: #fff !important;">
                                1. Registro de Equipos
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="text" id="nombreEquipo" class="form-control" placeholder="Ej: Los Compadres">
                                    <button class="btn btn-success" type="button" onclick="agregarEquipo()">Añadir</button>
                                </div>
                                
                                <ul class="list-group mb-3" id="listaEquipos"></ul>

                                <div class="d-grid">
                                    <button class="btn btn-warning fw-bold" type="button" onclick="generarFixture()">
                                        <i class="bi bi-shuffle"></i> 2. Generar Juegos Automáticos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <form action="/index.php?ruta=guardar_torneo" method="POST" id="formTorneo">
                    <?= campoCSRF() ?>
                            <div class="card shadow-sm">
                                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                                    <span>3. Calendario de Juegos</span>
                                    <span class="badge bg-danger" id="contadorEquipos">0 Equipos</span>
                                </div>
                                <div class="card-body p-0">
                                    
                                    <div class="p-3" style="border-bottom: 1px solid var(--border-color);">
                                        <label class="form-label fw-bold">Nombre del Torneo</label>
                                        <input type="text" name="nombre_torneo" class="form-control" required placeholder="Ej: Torneo Regular de Bolas Criollas 2026">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Juego</th>
                                                    <th>Equipo Local</th>
                                                    <th>VS</th>
                                                    <th>Equipo Visitante</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaJuegos">
                                                <tr>
                                                    <td colspan="6" class="text-muted py-4">Añade equipos y genera los juegos para ver el calendario.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary" id="btnGuardarTorneo" disabled>
                                        <i class="bi bi-save me-1"></i> Guardar Torneo en Base Datos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="tab-pane fade" id="registrados" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Torneo</th>
                                        <th>Fecha de Creación</th>
                                        <th>Estado Actual</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($torneosGuardados)): ?>
                                        <tr>
                                            <td colspan="5" class="py-4 text-muted">No hay ningún torneo guardado en el sistema todavía.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($torneosGuardados as $t): ?>
                                            <tr>
                                                <td class="fw-bold">#<?= $t['id'] ?></td>
                                                <td class="text-start ps-4 fw-bold"><?= htmlspecialchars($t['nombre']) ?></td>
                                                <td class="text-muted"><?= date('d/m/Y h:i A', strtotime($t['fecha_creacion'])) ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= htmlspecialchars($t['estado']) ?></span>
                                                </td>
                                                <td>
                                                    <a href="/index.php?ruta=ver_torneo&id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-eye-fill me-1"></i> Ver Evento
                                                    </a>
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

    <div class="tab-pane fade" id="eventos" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><i class="bi bi-calendar-check me-2"></i>Próximos Eventos</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered mt-3 align-middle text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre del Evento</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($eventosGenerales)): ?>
                                        <tr>
                                            <td colspan="6" class="py-4 text-muted">No hay eventos generales programados.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($eventosGenerales as $evento): ?>
                                            <tr>
                                                <td class="text-start fw-bold"><?= htmlspecialchars($evento['nombre']) ?></td>
                                                <td class="text-muted"><?= date('d/m/Y h:i A', strtotime($evento['fecha_inicio'])) ?></td>
                                                <td class="text-muted"><?= date('d/m/Y h:i A', strtotime($evento['fecha_fin'])) ?></td>
                                                <td class="text-start"><?= nl2br(htmlspecialchars($evento['descripcion'])) ?></td>
                                                <td>
                                                    <?php if ($evento['estado'] === 'Programado'): ?>
                                                        <span class="badge bg-success">Programado</span>
                                                    <?php elseif ($evento['estado'] === 'En Curso'): ?>
                                                        <span class="badge bg-info">En Curso</span>
                                                    <?php elseif ($evento['estado'] === 'Finalizado'): ?>
                                                        <span class="badge bg-secondary">Finalizado</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Cancelado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($evento['estado'] === 'Programado' || $evento['estado'] === 'En Curso'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-cancelar-evento" title="Cancelar Evento"
                                                                data-id="<?= $evento['id'] ?>"
                                                                data-nombre="<?= htmlspecialchars($evento['nombre']) ?>">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted"><i class="bi bi-dash"></i></span>
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

    <div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Registrar Evento Simple</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/index.php?ruta=crear_evento" method="POST">
                    <?= campoCSRF() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Evento</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Noche de Gaitas / Cervezazo">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Fecha y Hora de Inicio</label>
                                <input type="text" name="fecha_inicio" class="form-control flatpickr-datetime" placeholder="Seleccione fecha y hora" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Fecha y Hora de Fin</label>
                                <input type="text" name="fecha_fin" class="form-control flatpickr-datetime" placeholder="Seleccione fecha y hora" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de las promociones o agrupaciones musicales..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Cancelación de Evento -->
    <div class="modal fade" id="modalCancelarEvento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form action="/index.php?ruta=cancelar_evento" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <input type="hidden" name="evento_id" id="cancelar_evento_id">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fs-6"><i class="bi bi-exclamation-triangle-fill me-2"></i>Cancelar Evento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-x-circle-fill" style="font-size: 2.5rem; color: var(--red-primary);"></i>
                    <p class="mt-3 mb-2">¿Estás seguro de que deseas cancelar este evento?</p>
                    <h6 id="cancelar_evento_nombre" class="fw-bold mb-3" style="color: var(--red-primary);"></h6>
                    <small class="text-muted">Esta acción cambiará el estado del evento a "Cancelado" y no se podrá revertir.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Volver</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Sí, Cancelar Evento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación de Equipo -->
    <div class="modal fade" id="modalEliminarEquipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fs-6"><i class="bi bi-person-dash-fill me-2"></i>Quitar Equipo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash3-fill" style="font-size: 2.5rem; color: var(--red-primary);"></i>
                    <p class="mt-3 mb-2">¿Deseas quitar este equipo de la lista?</p>
                    <h6 id="eliminar_equipo_nombre" class="fw-bold" style="color: var(--red-primary);"></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Mantener</button>
                    <button type="button" class="btn btn-danger fw-bold" id="btnConfirmarEliminarEquipo"><i class="bi bi-trash3 me-1"></i>Sí, Quitar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    let equipos = [];

    function agregarEquipo() {
        const input = document.getElementById('nombreEquipo');
        const nombre = input.value.trim();

        if(nombre === '') return alert('Ingresa un nombre válido');
        if(equipos.includes(nombre)) return alert('Este equipo ya está registrado');
        
        equipos.push(nombre);
        input.value = '';
        actualizarLista();
    }

    let equipoIndexPendiente = null;

    function eliminarEquipo(index) {
        equipoIndexPendiente = index;
        document.getElementById('eliminar_equipo_nombre').textContent = equipos[index];
        new bootstrap.Modal(document.getElementById('modalEliminarEquipo')).show();
    }

    function confirmarEliminarEquipo() {
        if (equipoIndexPendiente !== null) {
            equipos.splice(equipoIndexPendiente, 1);
            equipoIndexPendiente = null;
            actualizarLista();
            bootstrap.Modal.getInstance(document.getElementById('modalEliminarEquipo')).hide();
        }
    }

    function actualizarLista() {
        const lista = document.getElementById('listaEquipos');
        const contador = document.getElementById('contadorEquipos');
        lista.innerHTML = '';
        
        equipos.forEach((equipo, index) => {
            lista.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${equipo}
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarEquipo(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
            `;
        });
        contador.innerText = `${equipos.length} Equipos`;
    }

    function generarFixture() {
        if(equipos.length < 2) return alert('Necesitas al menos 2 equipos para generar juegos.');
        
        const tabla = document.getElementById('tablaJuegos');
        tabla.innerHTML = '';
        let juegoNumero = 1;

        let participantes = [...equipos];

        if (participantes.length % 2 !== 0) {
            participantes.push('Descanso');
        }

        const totalJornadas = participantes.length - 1;
        const partidosPorJornada = participantes.length / 2;

        for (let jornada = 0; jornada < totalJornadas; jornada++) {
            
            tabla.innerHTML += `
                <tr style="background: var(--bg-elevated) !important;">
                    <td colspan="6" class="fw-bold py-2 text-uppercase text-center" style="color: var(--warning); letter-spacing: 0.1em;">Jornada ${jornada + 1}</td>
                </tr>
            `;

            for (let i = 0; i < partidosPorJornada; i++) {
                const local = participantes[i];
                const visitante = participantes[participantes.length - 1 - i];

                if (local !== 'Descanso' && visitante !== 'Descanso') {
                    tabla.innerHTML += `
                        <tr>
                            <td class="fw-bold text-muted">#${juegoNumero}</td>
                            <td class="text-end fw-bold">
                                ${local}
                                <input type="hidden" name="equipo_local[]" value="${local}">
                            </td>
                            <td class="fw-bold" style="color: var(--red-primary);">VS</td>
                            <td class="text-start fw-bold">
                                ${visitante}
                                <input type="hidden" name="equipo_visitante[]" value="${visitante}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm flatpickr-datetime" name="fecha_juego[]" placeholder="Fecha y hora" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    juegoNumero++;
                }
            }

            const ultimoEquipo = participantes.pop();
            participantes.splice(1, 0, ultimoEquipo);
        }
        
        document.getElementById('btnGuardarTorneo').disabled = false;
        
        // Inicializar flatpickr para los nuevos inputs generados
        flatpickr('.flatpickr-datetime', {
            locale: 'es',
            enableTime: true,
            dateFormat: 'Y-m-d H:i'
        });
    }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Revisar si hay un hash en la URL (ej: #registrados o #eventos)
            let hash = window.location.hash;
            if (hash) {
                // Buscar el botón de la pestaña que tiene ese data-bs-target
                let botonPestana = document.querySelector('button[data-bs-target="' + hash + '"]');
                if (botonPestana) {
                    // Instanciar y mostrar la pestaña usando la API nativa de Bootstrap
                    let instaciaPestana = new bootstrap.Tab(botonPestana);
                    instaciaPestana.show();
                }
            }

            // Modal de cancelar evento
            document.querySelectorAll('.btn-cancelar-evento').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('cancelar_evento_id').value = this.dataset.id;
                    document.getElementById('cancelar_evento_nombre').textContent = this.dataset.nombre;
                    new bootstrap.Modal(document.getElementById('modalCancelarEvento')).show();
                });
            });

            // Confirmar eliminación de equipo
            document.getElementById('btnConfirmarEliminarEquipo').addEventListener('click', confirmarEliminarEquipo);
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
</body>
</html>