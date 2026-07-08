<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Club de Bolas Criollas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/CSS/estilos_marca.css">
    <script src="/JS/theme.js"></script>
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header text-center">
        <i class="bi bi-shop fs-1 d-block mb-2"></i>
        <h4>Club de Bolas Criollas</h4>
        <p>Sistema de Gestión — Licorería</p>
    </div>
    <div class="auth-body">
        
        <div class="alert-container">
            <?php if (isset($resultado) && !$resultado['exito']): ?>
                <div class="alert alert-danger d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?= htmlspecialchars($resultado['mensaje']) ?></div>
                </div>
            <?php endif; ?>
            <?= renderizarMensajesFlash() ?>
        </div>

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
                    <i class="bi bi-box-arrow-in-right me-2"></i>Entrar al Sistema
                </button>
            </div>
        </form>



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