<?php
// Validar que exista la sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php?ruta=login');
    exit;
}

require_once __DIR__ . '/../../src/Infrastructure/Database.php';
$conexion = obtenerConexion();

// Obtener los productos (Solo los Activos)
$sql = "SELECT p.*, c.nombre AS categoria 
        FROM Productos p 
        INNER JOIN Categorias c ON p.categoria_id = c.id 
        WHERE p.estado = 'Activo' AND p.es_combo = 0
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
    <title>Inventario — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/CSS/estilos_marca.css?v=2">
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
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
                <span class="text-light me-3" style="font-size: 0.85rem;"><i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['primer_nombre']) ?></span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
       <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-boxes me-2" style="color: var(--blue-primary);"></i><span class="gradient-text">Gestión de Inventario</span></h2>
                <p class="text-muted mb-0">Administra tus productos, categorías y stock.</p>
            </div>
            
            <div class="d-grid gap-2 d-sm-flex">
                <?php if (tieneAcceso('inventario_escritura', (int)$_SESSION['rol_id'])): ?>
                <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                </button>
                <?php endif; ?>
                
                <a href="/index.php?ruta=combos" class="btn btn-warning shadow-sm fw-bold text-center">
                    <i class="bi bi-star-fill me-1"></i> Combos y Promociones
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaInventario">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th>Categoría</th>
                                <th class="text-center">Unidades/Caja</th>
                                <th class="text-center">Stock (Cajas)</th>
                                <th class="text-center">Stock (Unds)</th>
                                <th class="text-end">Precio (U)</th>
                                <?php if (tieneAcceso('inventario_escritura', (int)$_SESSION['rol_id'])): ?>
                                <th class="text-center">Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $p): ?>
                                <tr>
                                    <td class="ps-4 fw-bold producto-nombre-td"><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($p['categoria']) ?></span></td>
                                    <td class="text-center fw-bold text-muted"><?= $p['unidades_por_caja'] ?></td>
                                    <td class="text-center fw-bold" style="color: var(--warning);">
                                        <?= floor($p['stock_unidades_total'] / max(1, $p['unidades_por_caja'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $p['stock_unidades_total'] > 10 ? 'bg-success' : 'bg-danger' ?> fs-6">
                                            <?= $p['stock_unidades_total'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold" style="color: var(--success);">$<?= number_format($p['precio_unidad'], 2) ?></td>
                                    <?php if (tieneAcceso('inventario_escritura', (int)$_SESSION['rol_id'])): ?>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-nowrap">
                                            <button class="btn btn-sm btn-success btn-entrada" data-bs-toggle="modal" data-bs-target="#modalEntrada" data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-caja="<?= $p['unidades_por_caja'] ?>" title="Añadir Stock"><i class="bi bi-box-arrow-in-down"></i></button>
                                            <button class="btn btn-sm btn-warning btn-salida" data-bs-toggle="modal" data-bs-target="#modalSalida" data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-caja="<?= $p['unidades_por_caja'] ?>" title="Restar Stock / Merma"><i class="bi bi-box-arrow-up"></i></button>
                                            <button class="btn btn-sm btn-outline-primary btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarProducto" data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-categoria="<?= $p['categoria_id'] ?>" data-preciou="<?= $p['precio_unidad'] ?>" data-precioc5="<?= $p['precio_combo_5'] ?>" data-precioc36="<?= $p['precio_caja_36'] ?>" data-caja="<?= $p['unidades_por_caja'] ?>" title="Editar Producto"><i class="bi bi-pencil-square"></i></button>
                                            <button class="btn btn-sm btn-outline-danger btn-eliminar" data-bs-toggle="modal" data-bs-target="#modalEliminarProducto" data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" title="Eliminar Producto"><i class="bi bi-trash3-fill"></i></button>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="modalNuevoProducto" tabindex="-1">
        <div class="modal-dialog">
            <form action="/index.php?ruta=guardar_producto" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            
                            <div class="input-group">
                                <select name="categoria_id" class="form-select" required>
                                    <option value="" selected disabled>Seleccione...</option> 
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria" title="Crear nueva categoría">
                                    <i class="bi bi-plus-lg fw-bold"></i>
                                </button>
                            </div>
                            
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-bold">Stock Inicial (Unds)</label>
                            <input type="number" name="stock_inicial" class="form-control" value="0" min="0" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-warning">Unidades por Caja</label>
                            <input type="number" name="unidades_por_caja" class="form-control" value="1" min="1" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Para calcular stock en cajas</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold" style="color: var(--success) !important;">Precio Unidad ($)</label>
                            <input type="number" step="0.01" name="precio_unidad" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold" style="color: var(--success) !important;">Precio x5 ($)</label>
                            <input type="number" step="0.01" name="precio_combo_5" class="form-control" placeholder="Opcional">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold" style="color: var(--success) !important;">Precio Caja ($)</label>
                            <input type="number" step="0.01" name="precio_caja_36" class="form-control" placeholder="Opcional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Nueva Categoría -->
    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form action="/index.php?ruta=guardar_categoria" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fs-6"><i class="bi bi-tags-fill me-2"></i>Nueva Categoría</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Alerta de error si ya existe -->
                    <?php if (isset($_GET['error_cat']) && $_GET['error_cat'] === 'existe'): ?>
                        <div class="alert alert-danger p-2 small mb-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Esta categoría ya existe.
                        </div>
                    <?php endif; ?>
                    
                    <label class="form-label fw-bold">Nombre de la Categoría</label>
                    <input type="text" name="nombre_categoria" class="form-control" placeholder="Ej: Vinos, Snacks..." required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">Atrás</button>
                    <button type="submit" class="btn btn-success fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Producto -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1">
        <div class="modal-dialog">
            <form action="/index.php?ruta=editar_producto" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <input type="hidden" name="producto_id" id="edit_producto_id">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Producto</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="categoria_id" id="edit_categoria" class="form-select" required>
                            <option value="" disabled>Seleccione...</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-warning">Unidades por Caja</label>
                        <input type="number" name="unidades_por_caja" id="edit_unidades_caja" class="form-control" min="1" required>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold" style="color: var(--success) !important;">Precio Unidad ($)</label>
                            <input type="number" step="0.01" name="precio_unidad" id="edit_precio_u" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold" style="color: var(--success) !important;">Precio x5 ($)</label>
                            <input type="number" step="0.01" name="precio_combo_5" id="edit_precio_c5" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold" style="color: var(--success) !important;">Precio Caja ($)</label>
                            <input type="number" step="0.01" name="precio_caja_36" id="edit_precio_c36" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Entrada Stock -->
    <div class="modal fade" id="modalEntrada" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form action="/index.php?ruta=agregar_entrada" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <input type="hidden" name="producto_id" id="entrada_producto_id">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fs-6"><i class="bi bi-box-arrow-in-down me-2"></i>Añadir Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 id="entrada_nombre" class="fw-bold mb-3"></h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted" id="entrada_label_cajas">Cajas</label>
                            <input type="number" name="cantidad_cajas" class="form-control text-center" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Unidades Sueltas</label>
                            <input type="number" name="cantidad_unidades" class="form-control text-center" min="0" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Procesar Ingreso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Salida Stock -->
    <div class="modal fade" id="modalSalida" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form action="/index.php?ruta=restar_inventario" method="POST" class="modal-content">
                    <?= campoCSRF() ?>
                <input type="hidden" name="producto_id" id="salida_producto_id">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fs-6"><i class="bi bi-box-arrow-up me-2"></i>Restar Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 id="salida_nombre" class="fw-bold mb-3"></h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted" id="salida_label_cajas">Cajas</label>
                            <input type="number" name="cantidad_cajas" class="form-control text-center" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Unidades Sueltas</label>
                            <input type="number" name="cantidad_unidades" class="form-control text-center" min="0" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Procesar Salida</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulario oculto para eliminar producto (SweetAlert) -->
    <form id="formEliminarOculto" action="/index.php?ruta=eliminar_producto" method="POST" style="display:none;">
        <?= campoCSRF() ?>
        <input type="hidden" name="producto_id" id="delete_producto_id_hidden">
    </form>

    <!-- jQuery, DataTables & SweetAlert2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar DataTables
        if (document.getElementById('tablaInventario')) {
            $('#tablaInventario').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                    lengthMenu: "Mostrar _MENU_"
                },
                pageLength: 25,
                order: [[1, 'asc'], [0, 'asc']] // Ordenar por Categoría, luego por Producto
            });
        }
        
        // Modal de Entrada
        document.querySelectorAll('.btn-entrada').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('entrada_producto_id').value = this.dataset.id;
                document.getElementById('entrada_nombre').textContent = this.dataset.nombre;
                const upc = this.dataset.caja || '1';
                document.getElementById('entrada_label_cajas').textContent = 'Cajas (+' + upc + 'u)';
            });
        });

        // Modal de Salida
        document.querySelectorAll('.btn-salida').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('salida_producto_id').value = this.dataset.id;
                document.getElementById('salida_nombre').textContent = this.dataset.nombre;
                const upc = this.dataset.caja || '1';
                document.getElementById('salida_label_cajas').textContent = 'Cajas (-' + upc + 'u)';
            });
        });

        // Modal de Edición
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_producto_id').value = this.dataset.id;
                document.getElementById('edit_nombre').value = this.dataset.nombre;
                document.getElementById('edit_categoria').value = this.dataset.categoria;
                document.getElementById('edit_precio_u').value = this.dataset.preciou;
                document.getElementById('edit_precio_c5').value = this.dataset.precioc5;
                document.getElementById('edit_precio_c36').value = this.dataset.precioc36;
                document.getElementById('edit_unidades_caja').value = this.dataset.caja;
            });
        });

        // Modal de Eliminar (SweetAlert2)
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                
                Swal.fire({
                    title: '¿Eliminar Producto?',
                    html: `¿Estás seguro de que deseas eliminar <strong>${nombre}</strong>?<br><small class="text-muted mt-2 d-block">El producto dejará de aparecer en el inventario y punto de venta.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash3-fill"></i> Sí, Eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete_producto_id_hidden').value = id;
                        document.getElementById('formEliminarOculto').submit();
                    }
                });
            });
        });

        // ========================================================
        // CONTROL Y VALIDACIÓN DE CATEGORÍAS
        // ========================================================

        // 1. Validación en el cliente (Evita enviar el formulario si ya existe en el select)
        const formCategoria = document.querySelector('#modalNuevaCategoria form');
        if (formCategoria) {
            formCategoria.addEventListener('submit', function(e) {
                const inputNombre = this.querySelector('input[name="nombre_categoria"]');
                const nombreNuevo = inputNombre.value.trim().toLowerCase();
                
                // Obtenemos los nombres de las categorías que ya están cargadas en el select de productos
                const opciones = document.querySelectorAll('select[name="categoria_id"] option');
                let duplicado = false;
                
                opciones.forEach(opt => {
                    if (opt.text.trim().toLowerCase() === nombreNuevo) {
                        duplicado = true;
                    }
                });
                
                if (duplicado) {
                    e.preventDefault(); // Detiene el envío del formulario
                    alert('¡Esta categoría ya existe! Elige un nombre diferente.');
                }
            });
        }

        // 2. Comportamiento automático según los parámetros de la URL de redirección
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('success_cat')) {
            // Si se creó con éxito, reabre automáticamente el menú de registrar producto
            const modalProducto = new bootstrap.Modal(document.getElementById('modalNuevoProducto'));
            modalProducto.show();
        } else if (urlParams.has('error_cat') && urlParams.get('error_cat') === 'existe') {
            // Si hubo error desde el servidor, mantén abierto el modal de categoría para corregirlo
            const modalCategoria = new bootstrap.Modal(document.getElementById('modalNuevaCategoria'));
            modalCategoria.show();
        }
        
    });
    </script>
</body>
</html>