<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Licorería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        
        .module-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
        .icon-wrapper {
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/index.php?ruta=dashboard">
                <i class="bi bi-shop me-2"></i>Licorería App
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3">
                    <i class="bi bi-person-circle me-1"></i> 
                    <?= htmlspecialchars($_SESSION['primer_nombre'] . ' ' . $_SESSION['primer_apellido']) ?> 
                    <span class="badge bg-secondary ms-1">Rol: <?= htmlspecialchars($_SESSION['rol_id']) ?></span>
                </span>
                <a href="/index.php?ruta=logout" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">Panel de Control</h2>
                <p class="text-muted">Selecciona el módulo al que deseas acceder.</p>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-6 col-lg-3">
                <a href="/index.php?ruta=pos" class="text-decoration-none text-dark">
                    <div class="card shadow-sm border-0 h-100 module-card text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper bg-success bg-opacity-10 text-success fs-2">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold">Punto de Venta</h5>
                            <p class="card-text text-muted small">Control de ventas, facturación y proceso de caja.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a href="/index.php?ruta=inventario" class="text-decoration-none text-dark">
                    <div class="card shadow-sm border-0 h-100 module-card text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary fs-2">
                                <i class="bi bi-boxes"></i>
                            </div>
                            <h5 class="card-title fw-bold">Inventario</h5>
                            <p class="card-text text-muted small">Gestión de productos, categorías, stock y mermas.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a href="/index.php?ruta=eventos" class="text-decoration-none text-dark">
                    <div class="card shadow-sm border-0 h-100 module-card text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper bg-warning bg-opacity-10 text-warning fs-2">
                                <i class="bi bi-calendar-star-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold">Control de Eventos</h5>
                            <p class="card-text text-muted small">Planificación, asignación y seguimiento de eventos.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a href="/index.php?ruta=contabilidad" class="text-decoration-none text-dark">
                    <div class="card shadow-sm border-0 h-100 module-card text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper bg-danger bg-opacity-10 text-danger fs-2">
                                <i class="bi bi-calculator-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold">Contabilidad</h5>
                            <p class="card-text text-muted small">Gastos de inventario, ganancias y control de deudores.</p>
                        </div>
                    </div>
                </a>
            </div>

        </div> <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-activity me-2"></i>Resumen de Actividad</h5>
                    </div>
                    <div class="card-body pb-4">
                        <div class="alert alert-info border-0 mb-0 d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                            <div>
                                En el futuro, aquí puedes inyectar gráficas de ventas, alertas de stock bajo o eventos próximos conectándolo con tu base de datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>