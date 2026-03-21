<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Licorería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Acceso al Sistema</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($resultado) && !$resultado['exito']): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($resultado['mensaje']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="/index.php?ruta=login" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="username" class="form-control" required>
                            <div class="invalid-feedback">Ingresa tu nombre de usuario.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                            <div class="invalid-feedback">Ingresa tu contraseña.</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="mb-0">¿Nuevo empleado? <a href="/index.php?ruta=registro" class="text-decoration-none">Registrar usuario</a></p>
                    </div>

                </div>
            </div>
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