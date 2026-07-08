<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combos — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="/index.php?ruta=inventario" class="text-white text-decoration-none me-3 fs-5" title="Volver a Inventario">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <a class="navbar-brand fw-bold mb-0" href="/index.php?ruta=dashboard">
                    <i class="bi bi-shop me-2"></i>Club Mamá Guille
                </a>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-light me-3" style="font-size: 0.85rem;"><i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['primer_nombre']) ?></span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold"><i class="bi bi-star-fill me-2" style="color: var(--warning);"></i><span class="gradient-text">Combos y Promociones</span></h2>
                <p class="text-muted">Crea promociones para vender en el punto de venta.</p>
            </div>
            <button class="btn btn-warning shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoCombo">
                <i class="bi bi-plus-circle me-1"></i> Crear Combo
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Combo</th>
                                <th>Tipo</th>
                                <th>Receta (Componentes)</th>
                                <th>Precio</th>
                                <th class="text-center">Stock Disponible</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaCombos)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No hay combos registrados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($listaCombos as $c): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td>
                                            <?php if (($c['tipo_consumo'] ?? 'Para Llevar') === 'Para Llevar'): ?>
                                                <span class="badge bg-secondary"><i class="bi bi-bag me-1"></i>Para Llevar</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark"><i class="bi bi-shop me-1"></i>Local</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <ul class="mb-0 text-muted small ps-3">
                                                <?php foreach($c['receta'] as $r): ?>
                                                    <li><?= $r['cantidad_necesaria'] ?>x <?= htmlspecialchars($r['nombre']) ?> <small>(Disp: <?= $r['stock_unidades_total'] ?>)</small></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                        <td class="fw-bold" style="color: var(--success);">$<?= number_format($c['precio'], 2) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-6"><?= $c['potencial'] ?> Disp.</span>
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

    <div class="modal fade" id="modalNuevoCombo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="/index.php?ruta=guardar_combo_nuevo" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-star-fill me-2"></i>Crear Combo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre del Combo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Tipo Consumo</label>
                            <select name="tipo_consumo" class="form-select" required>
                                <option value="Para Llevar">Para Llevar</option>
                                <option value="Local">Local</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Receta del Combo</h6>
                    <div id="lista-componentes">
                        <div class="row mb-2 componente-row">
                            <div class="col-8">
                                <select name="componente_id[]" class="form-select" required>
                                    <option value="">Seleccione producto del inventario...</option>
                                    <?php foreach($productosReceta as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="componente_cant[]" class="form-control" placeholder="Cant." min="1" value="1" required>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger btn-eliminar-fila" disabled><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarFilaCombo()">
                        <i class="bi bi-plus-circle me-1"></i>Añadir otro producto a la receta
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Guardar Combo</button>
                </div>
            </form>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.agregarFilaCombo = function() {
            const container = document.getElementById('lista-componentes');
            const row = container.querySelector('.componente-row').cloneNode(true);
            row.querySelector('select').value = '';
            row.querySelector('input').value = '1';
            const btnEliminar = row.querySelector('.btn-eliminar-fila');
            btnEliminar.disabled = false;
            btnEliminar.onclick = function() { row.remove(); };
            container.appendChild(row);
        };


    </script>
</body>
</html>