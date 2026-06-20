<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Eventos y Torneos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/index.php?ruta=dashboard">
                <i class="bi bi-arrow-left-circle me-2"></i>Volver al Panel
            </a>
            <span class="navbar-text text-white">
                Módulo de Eventos
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'exito'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>¡Éxito!</strong> El torneo se ha organizado y registrado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['msg'] === 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error del Sistema:</strong> No se pudo guardar el torneo. Asegúrate de haber ejecutado el código SQL en tu base de datos para crear las tablas de Torneo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Gestión de Eventos y Torneos</h2>
            <div>
                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#modalEvento">
                    <i class="bi bi-calendar-plus me-1"></i> Nuevo Evento Simple
                </button>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="eventosTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="torneos-tab" data-bs-toggle="tab" data-bs-target="#torneos" type="button" role="tab">
                    <i class="bi bi-trophy-fill text-warning me-1"></i> Crear Torneo
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="registrados-tab" data-bs-toggle="tab" data-bs-target="#registrados" type="button" role="tab">
                    <i class="bi bi-folder-fill text-primary me-1"></i> Torneos Registrados
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
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-primary text-white fw-bold">
                                1. Registro de Equipos
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="text" id="nombreEquipo" class="form-control" placeholder="Ej: Los Compadres">
                                    <button class="btn btn-success" type="button" onclick="agregarEquipo()">Añadir</button>
                                </div>
                                
                                <ul class="list-group mb-3" id="listaEquipos"></ul>

                                <div class="d-grid">
                                    <button class="btn btn-warning fw-bold text-dark" type="button" onclick="generarFixture()">
                                        <i class="bi bi-shuffle"></i> 2. Generar Juegos Automáticos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <form action="/index.php?ruta=guardar_torneo" method="POST" id="formTorneo">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                                    <span>3. Calendario de Juegos</span>
                                    <span class="badge bg-danger" id="contadorEquipos">0 Equipos</span>
                                </div>
                                <div class="card-body p-0">
                                    
                                    <div class="p-3 bg-light border-bottom">
                                        <label class="form-label fw-bold">Nombre del Torneo</label>
                                        <input type="text" name="nombre_torneo" class="form-control" required placeholder="Ej: Torneo Regular de Bolas Criollas 2026">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                                            <thead class="table-light">
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
                                <div class="card-footer bg-white text-end">
                                    <button type="submit" class="btn btn-primary" id="btnGuardarTorneo" disabled>
                                        <i class="bi bi-save me-1"></i> Guardar Torneo en Base de Datos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="tab-pane fade" id="registrados" role="tabpanel">
                <div class="card shadow-sm border-0">
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
                                                <td class="text-start ps-4 fw-bold text-secondary"><?= htmlspecialchars($t['nombre']) ?></td>
                                                <td class="text-muted"><?= date('d/m/Y h:i A', strtotime($t['fecha_creacion'])) ?></td>
                                                <td>
                                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($t['estado']) ?></span>
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
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Próximos Eventos</h5>
                        <table class="table table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre del Evento</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Noche de Gaitas</td>
                                    <td>2026-11-15 20:00</td>
                                    <td>2026-11-16 02:00</td>
                                    <td>Evento con música en vivo y promociones en licores.</td>
                                    <td><span class="badge bg-success">Programado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Registrar Evento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/index.php?ruta=crear_evento" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Evento</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Fecha y Hora de Inicio</label>
                                <input type="datetime-local" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Fecha y Hora de Fin</label>
                                <input type="datetime-local" name="fecha_fin" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción (Opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Evento</button>
                    </div>
                </form>
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

    function eliminarEquipo(index) {
        equipos.splice(index, 1);
        actualizarLista();
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
                <tr class="table-dark">
                    <td colspan="6" class="fw-bold py-2 text-uppercase text-center">Jornada ${jornada + 1}</td>
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
                            <td class="text-danger fw-bold">VS</td>
                            <td class="text-start fw-bold">
                                ${visitante}
                                <input type="hidden" name="equipo_visitante[]" value="${visitante}">
                            </td>
                            <td>
                                <input type="datetime-local" class="form-control form-control-sm" name="fecha_juego[]" required>
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
    }
    </script>
</body>
</html>