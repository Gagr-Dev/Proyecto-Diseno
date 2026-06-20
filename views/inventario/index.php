<?php
// Validar que exista la sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php?ruta=login');
    exit;
}

require_once __DIR__ . '/../../src/Infrastructure/Database.php';
$conexion = obtenerConexion();

// Obtener los productos con el nombre de su categoría
$sql = "SELECT p.*, c.nombre AS categoria 
        FROM Productos p 
        INNER JOIN Categorias c ON p.categoria_id = c.id 
        WHERE p.estado = 'Activo'
        ORDER BY c.nombre, p.nombre";
$stmt = $conexion->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías para los formularios
$stmtCat = $conexion->query("SELECT * FROM Categorias ORDER BY nombre");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Gestión de Licorería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/index.php?ruta=dashboard">
                <i class="bi bi-shop me-2"></i>Licorería App
            </a>
            <div class="d-flex align-items-center">
                <a href="/index.php?ruta=dashboard" class="btn btn-sm btn-outline-light me-3">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Panel
                </a>
                <span class="text-light me-3">
                    <i class="bi bi-person-circle me-1"></i> 
                    <?= htmlspecialchars($_SESSION['primer_nombre'] ?? 'Usuario') ?> 
                </span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-danger border-0">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-dark"><i class="bi bi-boxes text-primary me-2"></i>Control de Inventario</h2>
                <p class="text-muted">Gestiona el stock de cervezas, cigarros y chucherías.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Producto
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Producto</th>
                                <th class="py-3">Categoría</th>
                                <th class="py-3 text-center">Stock Físico Real</th>
                                <th class="py-3 text-end">Precio (Und)</th>
                                <th class="py-3 text-end">Combo 5</th>
                                <th class="py-3 text-end">Caja (36)</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $p): ?>
                                <tr>
                                    <td class="px-4 fw-medium"><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categoria']) ?></span></td>
                                    
                                    <td class="text-center">
                                        <?php 
                                        $total_unidades = $p['stock_unidades_total'];
                                        
                                        // LÓGICA DE LAS 36 UNIDADES PARA CERVEZAS (o productos con precio de caja)
                                        if (!empty($p['precio_caja_36'])) {
                                            $cajas = floor($total_unidades / 36);
                                            $sueltas = $total_unidades % 36;
                                            
                                            if ($cajas > 0) {
                                                echo "<span class='badge bg-primary fs-6 me-1'>{$cajas} Cajas</span>";
                                            }
                                            if ($sueltas > 0 || $total_unidades == 0) {
                                                $color = ($total_unidades < 10) ? 'danger' : 'info text-dark';
                                                echo "<span class='badge bg-{$color} fs-6'>{$sueltas} Unds</span>";
                                            }
                                        } else {
                                            // Lógica para unidades simples
                                            $color = ($total_unidades < 5) ? 'danger' : 'success';
                                            echo "<span class='badge bg-{$color} fs-6'>{$total_unidades} Unds</span>";
                                        }
                                        ?>
                                    </td>

                                    <td class="text-end">$<?= number_format($p['precio_unidad'], 2) ?></td>
                                    <td class="text-end text-muted">
                                        <?= $p['precio_combo_5'] ? '$'.number_format($p['precio_combo_5'], 2) : '-' ?>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        <?= $p['precio_caja_36'] ? '$'.number_format($p['precio_caja_36'], 2) : '-' ?>
                                    </td>
                                    
                                    <td class="px-4 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-success btn-entrada" 
                                                data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
                                                data-bs-toggle="modal" data-bs-target="#modalEntrada" title="Agregar Entrada">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-warning btn-salida" 
                                                data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
                                                data-bs-toggle="modal" data-bs-target="#modalSalida" title="Registrar Merma/Salida">
                                            <i class="bi bi-box-arrow-up"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-primary btn-editar ms-1" 
                                                data-id="<?= $p['id'] ?>" 
                                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                data-categoria="<?= $p['categoria_id'] ?>"
                                                data-preciou="<?= $p['precio_unidad'] ?>"
                                                data-precioc5="<?= $p['precio_combo_5'] ?>"
                                                data-precioc36="<?= $p['precio_caja_36'] ?>"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($productos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No hay productos registrados en el inventario.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoProducto" tabindex="-1">
        <div class="modal-dialog">
            <form action="/index.php?ruta=guardar_producto" method="POST" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Registrar Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Inicial (Unidades Sueltas Totales)</label>
                        <input type="number" name="stock_inicial" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label class="form-label">Precio Unit.</label>
                            <input type="number" step="0.01" name="precio_unidad" class="form-control" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Combo 5</label>
                            <input type="number" step="0.01" name="precio_combo_5" class="form-control" placeholder="Opcional">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Caja (36)</label>
                            <input type="number" step="0.01" name="precio_caja_36" class="form-control" placeholder="Opcional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <form action="/index.php?ruta=editar_producto" method="POST" class="modal-content">
                <input type="hidden" name="producto_id" id="edit_producto_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria_id" id="edit_categoria" class="form-select" required>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label class="form-label">Precio Unit.</label>
                            <input type="number" step="0.01" name="precio_unidad" id="edit_precio_u" class="form-control" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Combo 5</label>
                            <input type="number" step="0.01" name="precio_combo_5" id="edit_precio_c5" class="form-control">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Caja (36)</label>
                            <input type="number" step="0.01" name="precio_caja_36" id="edit_precio_c36" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalEntrada" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form action="/index.php?ruta=agregar_entrada" method="POST" class="modal-content">
                <input type="hidden" name="producto_id" id="entrada_producto_id">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fs-6"><i class="bi bi-box-arrow-in-down me-2"></i>Agregar Inventario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold mb-3 text-center text-success" id="entrada_nombre"></p>
                    <div class="mb-3">
                        <label class="form-label">Cajas Completas (x36)</label>
                        <input type="number" name="cantidad_cajas" class="form-control form-control-lg text-center" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unidades Sueltas</label>
                        <input type="number" name="cantidad_unidades" class="form-control form-control-lg text-center" value="0" min="0">
                    </div>
                    <small class="text-muted text-center d-block">El sistema sumará automáticamente todo a las unidades totales.</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Registrar Entrada</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalSalida" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form action="/index.php?ruta=restar_inventario" method="POST" class="modal-content border-warning">
                <input type="hidden" name="producto_id" id="salida_producto_id">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fs-6"><i class="bi bi-box-arrow-up me-2"></i>Registrar Salida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold mb-3 text-center text-warning-emphasis" id="salida_nombre"></p>
                    <div class="mb-3">
                        <label class="form-label">Cajas a retirar (x36)</label>
                        <input type="number" name="cantidad_cajas" class="form-control form-control-lg text-center" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unidades sueltas a retirar</label>
                        <input type="number" name="cantidad_unidades" class="form-control form-control-lg text-center" value="0" min="0">
                    </div>
                    <small class="text-muted text-center d-block">Ideal para botellas rotas, vencidas o consumo interno.</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Restar del Inventario</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Lógica para llenar el modal de Entrada
        document.querySelectorAll('.btn-entrada').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('entrada_producto_id').value = this.dataset.id;
                document.getElementById('entrada_nombre').textContent = this.dataset.nombre;
            });
        });

        // Lógica para llenar el modal de Salida/Merma
        document.querySelectorAll('.btn-salida').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('salida_producto_id').value = this.dataset.id;
                document.getElementById('salida_nombre').textContent = this.dataset.nombre;
            });
        });

        // Lógica para llenar el modal de Edición
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_producto_id').value = this.dataset.id;
                document.getElementById('edit_nombre').value = this.dataset.nombre;
                document.getElementById('edit_categoria').value = this.dataset.categoria;
                document.getElementById('edit_precio_u').value = this.dataset.preciou;
                document.getElementById('edit_precio_c5').value = this.dataset.precioc5;
                document.getElementById('edit_precio_c36').value = this.dataset.precioc36;
            });
        });
        
    });
    </script>
</body>
</html>