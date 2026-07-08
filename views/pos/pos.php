<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <style>
        @media (min-width: 992px) {
            body { overflow: hidden; }
            .lista-productos { height: calc(100vh - 52px); overflow-y: auto; overflow-x: hidden; }
            .ticket-bg { height: calc(100vh - 52px); overflow-y: auto; }
        }
        @media (max-width: 991px) {
            body { overflow-x: hidden; }
            .lista-productos { max-height: 55vh; overflow-y: auto; border-bottom: 1px solid var(--border-color); }
            .ticket-bg { min-height: 45vh; }
        }
    </style>
    <script src="/JS/theme.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-1 shadow-sm sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex align-items-center">
                <a href="/index.php?ruta=dashboard" class="text-white text-decoration-none me-3 fs-5" title="Volver al Dashboard">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <a class="navbar-brand fw-bold fs-5 mb-0" href="/index.php?ruta=dashboard">
                    <i class="bi bi-cart-check-fill me-2 text-success" style="color: var(--success) !important;"></i>Punto de Venta
                </a>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-light d-none d-sm-inline" style="font-size: 0.8rem;">
                    <span class="badge bg-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Caja Activa</span>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            
            <div class="col-lg-8 p-3 p-lg-4 lista-productos">
                
                <?= renderizarMensajesFlash() ?>

                <!-- Buscador Select2 -->
                <div class="mb-3">
                    <select id="buscador_productos" class="form-select w-100">
                        <option value=""></option>
                        <?php foreach($productosPOS as $p): ?>
                            <option value="<?= $p['tipo'] ?>_<?= $p['id'] ?>" 
                                    data-id_unico="<?= $p['tipo'] ?>_<?= $p['id'] ?>"
                                    data-nombre="<?= htmlspecialchars(addslashes($p['nombre'])) ?>"
                                    data-precio="<?= $p['precio_unidad'] ?>"
                                    data-stock="<?= $p['stock'] ?>"
                                    data-tipo="<?= $p['tipo'] ?>"
                                    data-id_real="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?> - $<?= number_format($p['precio_unidad'], 2) ?> (Disp: <?= $p['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-lg-3">
                    <?php if(empty($productosPOS)): ?>
                        <div class="col-12 text-muted">No hay productos ni combos con stock disponible para vender.</div>
                    <?php else: ?>
                        <?php foreach($productosPOS as $p): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm producto-card <?= $p['tipo'] === 'combo' ? 'border-warning border-2' : '' ?>" 
                                     onclick="agregarAlCarrito('<?= $p['tipo'] ?>_<?= $p['id'] ?>', '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= $p['precio_unidad'] ?>, <?= $p['stock'] ?>, '<?= $p['tipo'] ?>', <?= $p['id'] ?>)">
                                    
                                    <?php if($p['tipo'] === 'combo'): ?>
                                        <span class="badge bg-warning position-absolute top-0 start-50 translate-middle-x mt-1 shadow-sm" style="font-size: 0.6rem;"><i class="bi bi-star-fill"></i> COMBO</span>
                                    <?php endif; ?>
                                    
                                    <div class="card-body text-center p-2 p-lg-3 d-flex flex-column justify-content-between mt-2">
                                        <h6 class="card-title fw-bold mb-1 lh-sm" style="font-size: 0.85rem; color: var(--text-white);"><?= htmlspecialchars($p['nombre']) ?></h6>
                                        <div class="mt-2">
                                            <h5 class="fw-bold mb-0 fs-6" style="color: var(--success);">$<?= number_format($p['precio_unidad'], 2) ?></h5>
                                            <span class="badge bg-secondary mt-1" style="font-size: 0.7rem;">Disp: <?= $p['stock'] ?> unds</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4 p-3 p-lg-4 ticket-bg shadow-sm d-flex flex-column">
                <h5 class="fw-bold mb-3 pb-2" style="border-bottom: 1px solid var(--border-color); color: var(--text-white);">
                    <i class="bi bi-receipt me-1"></i> Detalle de Venta
                </h5>
                
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text fw-bold" style="color: var(--blue-primary) !important;">Tasa BCV (Bs)</span>
                    <input type="number" id="tasa_bcv" class="form-control fw-bold text-end" value="36.50" step="0.01" onchange="actualizarTotales()">
                </div>

                <div class="flex-grow-1 mb-3" style="border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden;">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center" style="width: 90px;">Cant.</th>
                                <th class="text-end">Sub ($)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tabla-carrito">
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">
                                    <lottie-player src="/assets/lotties/empty_cart.json?v=2" background="transparent" speed="1" style="width: 120px; height: 120px; margin: 0 auto; opacity: 0.6;" loop autoplay></lottie-player>
                                    <span class="d-block mt-2">El carrito está vacío</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-auto pt-3" style="border-top: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between mb-1">
                        <h6 class="text-muted mb-0">Total Dólares</h6>
                        <h4 class="fw-bold mb-0" style="color: var(--success);">$<span id="total_usd">0.00</span></h4>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="text-muted mb-0">Total Bolívares</h6>
                        <h5 class="fw-bold mb-0" style="color: var(--blue-primary);">Bs <span id="total_bs">0.00</span></h5>
                    </div>

                    <button type="button" id="btn-cobrar" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm py-2" disabled style="background: var(--gradient-red) !important; border: none !important;" data-bs-toggle="modal" data-bs-target="#modalPago">
                        <i class="bi bi-cash-coin me-2"></i> Procesar Pago
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Confirmar Eliminar Item del Carrito -->


    <!-- Modal de Pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="/index.php?ruta=procesar_venta" method="POST" id="form-venta" class="modal-content">
                    <?= campoCSRF() ?>
                <input type="hidden" name="carrito_json" id="input_carrito_json">
                <input type="hidden" name="total_venta" id="input_total_venta">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i>Confirmar Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h4 class="text-center mb-4 fw-bold" style="color: var(--success);">Total: $<span id="modal_total_usd">0.00</span></h4>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select form-select-lg" onchange="toggleCamposPago()" required>
                            <option value="Efectivo" selected>Efectivo (Divisas / Dólares)</option>
                            <option value="Pago Móvil">Pago Móvil</option>
                            <option value="Punto de Venta">Punto de Venta</option>
                        </select>
                    </div>

                    <div id="campos_pago_movil" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono (Pago Móvil)</label>
                            <input type="text" name="telefono_pago" id="telefono_pago" class="form-control" placeholder="Ej: 04141234567">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Referencia (Últimos 4 o 6 dígitos)</label>
                            <input type="text" name="referencia_pago" id="referencia_pago" class="form-control" placeholder="Ej: 9452">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Confirmar Venta</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#buscador_productos').select2({
                theme: 'bootstrap-5',
                placeholder: '🔍 Buscar producto por nombre...',
                allowClear: true,
                width: '100%'
            });

            $('#buscador_productos').on('select2:select', function (e) {
                var data = e.params.data.element.dataset;
                agregarAlCarrito(data.id_unico, data.nombre, data.precio, data.stock, data.tipo, data.id_real);
                // Limpiar select después de agregar
                $(this).val(null).trigger('change');
            });
        });

        function toggleCamposPago() {
            const metodo = document.getElementById('metodo_pago').value;
            const campos = document.getElementById('campos_pago_movil');
            const ref = document.getElementById('referencia_pago');
            const tel = document.getElementById('telefono_pago');
            
            if (metodo === 'Pago Móvil') {
                campos.style.display = 'block';
                ref.required = true;
                tel.required = true;
            } else {
                campos.style.display = 'none';
                ref.required = false;
                tel.required = false;
                ref.value = '';
                tel.value = '';
            }
        }

        let carrito = [];

        function agregarAlCarrito(id_unico, nombre, precio, stockMaximo, tipo, id_real) {
            let itemExistente = carrito.find(item => item.id_unico === id_unico);
            
            if (itemExistente) {
                if (itemExistente.cantidad < stockMaximo) {
                    itemExistente.cantidad++;
                    itemExistente.subtotal = itemExistente.cantidad * itemExistente.precio;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock Insuficiente',
                        text: '¡No puedes vender más unidades de las que hay en el inventario!',
                        confirmButtonColor: '#0d6efd'
                    });
                }
            } else {
                carrito.push({
                    id_unico: id_unico,
                    id_real: id_real,
                    tipo: tipo,
                    nombre: nombre,
                    precio: parseFloat(precio),
                    cantidad: 1,
                    subtotal: parseFloat(precio),
                    stockMax: stockMaximo
                });
            }
            renderizarCarrito();
        }

        function cambiarCantidad(index, operacion) {
            let item = carrito[index];
            if (operacion === 1 && item.cantidad < item.stockMax) {
                item.cantidad++;
            } else if (operacion === -1 && item.cantidad > 1) {
                item.cantidad--;
            }
            item.subtotal = item.cantidad * item.precio;
            renderizarCarrito();
        }

        function eliminarItem(index) {
            carrito.splice(index, 1);
            renderizarCarrito();
        }

        function renderizarCarrito() {
            const tbody = document.getElementById('tabla-carrito');
            let html = '';
            let totalUSD = 0;

            if (carrito.length === 0) {
                tbody.innerHTML = `<tr>
                    <td colspan="4" class="text-center py-5 text-muted small">
                        <lottie-player src="/assets/lotties/empty_cart.json?v=2" background="transparent" speed="1" style="width: 120px; height: 120px; margin: 0 auto; opacity: 0.6;" loop autoplay></lottie-player>
                        <span class="d-block mt-2">El carrito está vacío</span>
                    </td>
                </tr>`;
                document.getElementById('btn-cobrar').disabled = true;
            } else {
                carrito.forEach((item, index) => {
                    totalUSD += item.subtotal;
                    let badgeCombo = item.tipo === 'combo' ? '<i class="bi bi-star-fill" style="color: var(--warning);"></i> ' : '';
                    
                    html += `
                        <tr>
                            <td class="text-truncate" style="max-width: 110px; font-size:0.8rem;" title="${item.nombre}">
                                ${badgeCombo}${item.nombre}
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary px-2" type="button" onclick="cambiarCantidad(${index}, -1)">-</button>
                                    <input type="text" class="form-control text-center px-0" value="${item.cantidad}" readonly style="font-size:0.8rem;">
                                    <button class="btn btn-outline-secondary px-2" type="button" onclick="cambiarCantidad(${index}, 1)">+</button>
                                </div>
                            </td>
                            <td class="text-end fw-bold" style="font-size:0.85rem; color: var(--success);">$${item.subtotal.toFixed(2)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger border-0 px-1" onclick="eliminarItem(${index})"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
                document.getElementById('btn-cobrar').disabled = false;
            }

            document.getElementById('total_usd').innerText = totalUSD.toFixed(2);
            document.getElementById('input_total_venta').value = totalUSD.toFixed(2);
            document.getElementById('input_carrito_json').value = JSON.stringify(carrito);
            document.getElementById('modal_total_usd').innerText = totalUSD.toFixed(2);
            
            actualizarTotales();
        }

        function actualizarTotales() {
            let totalUSD = parseFloat(document.getElementById('total_usd').innerText) || 0;
            let tasa = parseFloat(document.getElementById('tasa_bcv').value) || 0;
            let totalBs = totalUSD * tasa;
            
            document.getElementById('total_bs').innerText = totalBs.toFixed(2);
        }
    </script>
</body>
</html>