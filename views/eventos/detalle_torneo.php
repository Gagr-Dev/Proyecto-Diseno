<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Torneo — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
           <a class="navbar-brand fw-bold" href="/index.php?ruta=eventos#registrados">
                <i class="bi bi-arrow-left-circle me-2"></i>Volver a Eventos
            </a>
            <span class="navbar-text" style="color: var(--text-muted); font-size: 0.85rem;">
                Administración del Torneo
            </span>
        </div>
    </nav>

    <div class="container mt-5">
        
        <div class="card shadow-sm mb-4" style="overflow: hidden;">
            <div class="p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="background: var(--gradient-brand) !important;">
                <div>
                    <h3 class="mb-0 fw-bold" style="color: #fff !important;"><i class="bi bi-trophy-fill me-2" style="color: var(--warning);"></i><?= htmlspecialchars($torneo['nombre']) ?></h3>
                    <div class="mt-2" style="color: rgba(255,255,255,0.7);">
                        <i class="bi bi-calendar3 me-1"></i> Creado el: <?= date('d/m/Y h:i A', strtotime($torneo['fecha_creacion'])) ?>
                    </div>
                </div>
                <div class="text-end mt-3 mt-md-0">
                    <?php if($torneo['estado'] === 'Pendiente'): ?>
                        <span class="badge bg-secondary fs-6">Estado: Pendiente</span>
                    <?php elseif($torneo['estado'] === 'En Curso'): ?>
                        <span class="badge bg-info fs-6">Estado: En Curso</span>
                    <?php else: ?>
                        <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill me-1"></i>Torneo Terminado</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body p-0">
                <!-- Pestañas para cambiar entre vistas -->
                <ul class="nav nav-tabs px-3 pt-3" id="torneoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="partidos-tab" data-bs-toggle="tab" data-bs-target="#partidos-pane" type="button" role="tab">
                            <i class="bi bi-calendar-event me-2"></i>Calendario de Partidos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas-pane" type="button" role="tab" style="color: var(--success) !important;">
                            <i class="bi bi-bar-chart-line-fill me-2"></i>Tabla de Posiciones
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="torneoTabsContent">
                    
                    <!-- PANE 1: CALENDARIO DE PARTIDOS -->
                    <div class="tab-pane fade show active" id="partidos-pane" role="tabpanel" aria-labelledby="partidos-tab">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha y Hora del Partido</th>
                                        <th>Equipo Local</th>
                                        <th>VS</th>
                                        <th>Equipo Visitante</th>
                                        <th>Estado</th>
                                        <th>Definir Ganador</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($partidos)): ?>
                                        <tr>
                                            <td colspan="6" class="py-5 text-muted">No hay partidos registrados para este torneo.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($partidos as $partido): ?>
                                            <tr>
                                                <td class="fw-bold">
                                                    <span class="text-muted"><?= date('d/m/Y', strtotime($partido['fecha_hora'])) ?></span> <br>
                                                    <span style="color: var(--blue-primary);"><?= date('h:i A', strtotime($partido['fecha_hora'])) ?></span>
                                                </td>
                                                
                                                <td class="fw-bold fs-5 <?= ($partido['ganador_id'] == $partido['local_id']) ? '' : '' ?>" style="<?= ($partido['ganador_id'] == $partido['local_id']) ? 'color: var(--success);' : '' ?>">
                                                    <?= htmlspecialchars($partido['local_nombre']) ?>
                                                    <?= ($partido['ganador_id'] == $partido['local_id']) ? '<i class="bi bi-trophy-fill ms-1" style="color: var(--warning);"></i>' : '' ?>
                                                </td>
                                                
                                                <td class="fw-bold fs-5" style="color: var(--red-primary);">VS</td>
                                                
                                                <td class="fw-bold fs-5" style="<?= ($partido['ganador_id'] == $partido['visitante_id']) ? 'color: var(--success);' : '' ?>">
                                                    <?= htmlspecialchars($partido['visitante_nombre']) ?>
                                                    <?= ($partido['ganador_id'] == $partido['visitante_id']) ? '<i class="bi bi-trophy-fill ms-1" style="color: var(--warning);"></i>' : '' ?>
                                                </td>
                                                
                                                <td>
                                                    <?php if($partido['estado_partido'] === 'Pendiente'): ?>
                                                        <span class="badge bg-secondary">Pendiente</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Jugado</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php if($partido['estado_partido'] === 'Pendiente'): ?>
                                                        <form action="/index.php?ruta=guardar_resultado" method="POST" class="d-flex justify-content-center gap-2">
                    <?= campoCSRF() ?>
                                                            <input type="hidden" name="partido_id" value="<?= $partido['partido_id'] ?>">
                                                            <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">
                                                            
                                                            <select name="ganador_id" class="form-select form-select-sm w-auto" required>
                                                                <option value="" disabled selected>Seleccionar Ganador...</option>
                                                                <option value="<?= $partido['local_id'] ?>"><?= htmlspecialchars($partido['local_nombre']) ?></option>
                                                                <option value="<?= $partido['visitante_id'] ?>"><?= htmlspecialchars($partido['visitante_nombre']) ?></option>
                                                            </select>
                                                            
                                                            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="fw-bold" style="color: var(--success);"><i class="bi bi-check-circle-fill me-1"></i>Registrado</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- PANE 2: TABLA DE POSICIONES (ESTADÍSTICAS) -->
                    <div class="tab-pane fade" id="estadisticas-pane" role="tabpanel" aria-labelledby="estadisticas-tab">
                        <div class="p-3 d-flex align-items-center justify-content-between" style="background: var(--bg-elevated); border-bottom: 1px solid var(--border-color);">
                            <span class="fw-bold text-uppercase" style="color: var(--warning); letter-spacing: 0.1em;"><i class="bi bi-table me-2"></i>Clasificación oficial del torneo</span>
                            <span class="badge bg-warning">Tradición Criolla</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 80px;">Pos</th>
                                        <th class="text-start ps-4">Equipo</th>
                                        <th style="width: 100px;">JJ</th>
                                        <th style="width: 100px;">JG</th>
                                        <th style="width: 100px;">JP</th>
                                        <th style="width: 120px; color: var(--warning);">AVG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tablaPosiciones)): ?>
                                        <tr>
                                            <td colspan="6" class="py-5 text-muted">No hay estadísticas disponibles todavía.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $pos = 1; foreach ($tablaPosiciones as $fila): ?>
                                            <tr class="<?= ($pos <= 3) ? 'fw-semibold' : '' ?>">
                                                <td class="font-monospace fs-5">
                                                    <?php if($pos == 1): ?>
                                                        <span class="badge bg-warning" style="color: #000 !important;">
                                                            <i class="bi bi-award-fill"></i> 1º
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted"><?= $pos ?>º</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-start ps-4 fs-5 text-uppercase fw-bold">
                                                    <?= htmlspecialchars($fila['nombre']) ?>
                                                </td>
                                                <td class="fs-5 text-muted"><?= $fila['jj'] ?></td>
                                                <td class="fs-5 fw-bold" style="color: var(--success);"><?= $fila['jg'] ?></td>
                                                <td class="fs-5" style="color: var(--red-primary);"><?= $fila['jp'] ?></td>
                                                <td class="fw-bold font-monospace fs-5" style="background: var(--bg-elevated); color: var(--warning);">
                                                    <?= $fila['avg'] ?>
                                                </td>
                                            </tr>
                                        <?php $pos++; endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> 
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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