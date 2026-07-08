<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <script src="/JS/theme.js"></script>
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header">
        <i class="bi bi-person-plus-fill fs-1 d-block mb-2"></i>
        <h4>Registro de Empleado</h4>
        <p>Club de Bolas Criollas — Licorería</p>
    </div>
    <div class="auth-body">

        <?php if (isset($resultado) && !$resultado['exito']): ?>
            <div class="alert alert-danger alert-animated" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($resultado['mensaje']) ?>
            </div>
        <?php endif; ?>

        <form action="/index.php?ruta=login" method="POST" class="needs-validation" novalidate>
                    <?= campoCSRF() ?>
            
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-person me-1"></i>Usuario</label>
                <input type="text" name="username" class="form-control form-control-lg" placeholder="Tu nombre de usuario" required>
                <div class="invalid-feedback">Ingresa tu nombre de usuario.</div>
            </div>
            
            <div class="mb-4">
                <label class="form-label"><i class="bi bi-lock me-1"></i>Contraseña</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                <div class="invalid-feedback">Ingresa tu contraseña.</div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                    <i class="bi bi-check-circle me-2"></i>Crear Cuenta
                </button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p class="mb-0" style="color: var(--text-muted); font-size: 0.85rem;">
                ¿Ya tienes cuenta? <a href="/index.php?ruta=login" class="text-decoration-none fw-bold">Iniciar sesión</a>
            </p>
        </div>

    </div>
</div>

<script>
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