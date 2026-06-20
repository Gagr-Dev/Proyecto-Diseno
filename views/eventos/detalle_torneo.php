<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Torneo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/index.php?ruta=eventos">
                <i class="bi bi-arrow-left-circle me-2"></i>Volver a Eventos
            </a>
            <span class="navbar-text text-white">
                Administración del Torneo
            </span>
        </div>
    </nav>

    <div class="container mt-5">
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i><?= htmlspecialchars($torneo['nombre']) ?></h3>
                    <div class="mt-2 text-light opacity-75">
                        <i class="bi bi-calendar3 me-1"></i> Creado el: <?= date('d/m/Y h:i A', strtotime($torneo['fecha_creacion'])) ?>
                    </div>
                </div>
                <div class="text-end">
                    <?php if($torneo['estado'] === 'Pendiente'): ?>
                        <span class="badge bg-secondary fs-6 mb-2">Estado: Pendiente</span>
                    <?php elseif($torneo['estado'] === 'En Curso'): ?>
                        <span class="badge bg-info text-dark fs-6 mb-2">Estado: En Curso</span>
                    <?php else: ?>
                        <span class="badge bg-success fs-6 mb-2"><i class="bi bi-check-circle-fill me-1"></i>Torneo Terminado</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body p-0">
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
                                        <td class="text-muted fw-bold">
                                            <?= date('d/m/Y', strtotime($partido['fecha_hora'])) ?> <br>
                                            <span class="text-primary"><?= date('h:i A', strtotime($partido['fecha_hora'])) ?></span>
                                        </td>
                                        
                                        <td class="fw-bold fs-5 <?= ($partido['ganador_id'] == $partido['local_id']) ? 'text-success' : '' ?>">
                                            <?= htmlspecialchars($partido['local_nombre']) ?>
                                            <?= ($partido['ganador_id'] == $partido['local_id']) ? '<i class="bi bi-trophy-fill text-warning ms-1"></i>' : '' ?>
                                        </td>
                                        
                                        <td class="text-danger fw-bold fs-5">VS</td>
                                        
                                        <td class="fw-bold fs-5 <?= ($partido['ganador_id'] == $partido['visitante_id']) ? 'text-success' : '' ?>">
                                            <?= htmlspecialchars($partido['visitante_nombre']) ?>
                                            <?= ($partido['ganador_id'] == $partido['visitante_id']) ? '<i class="bi bi-trophy-fill text-warning ms-1"></i>' : '' ?>
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
                                                <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Registrado</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>