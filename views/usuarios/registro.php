<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario - Licorería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Registrar Nuevo Empleado</h4>
                </div>
                <div class="card-body">
                    
                    <?php if (isset($resultado)): ?>
                        <div class="alert <?= $resultado['exito'] ? 'alert-success' : 'alert-danger' ?>">
                            <ul class="mb-0">
                                <?php foreach ($resultado['mensajes'] as $msg): ?>
                                    <li><?= htmlspecialchars($msg) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/index.php?ruta=registro" method="POST" class="needs-validation" novalidate>
    
    <div class="mb-3">
        <label class="form-label">Nombre Completo</label>
        <input type="text" name="nombre_completo" class="form-control" 
               pattern="^[a-zA-ZÀ-ÿ\s]+$" minlength="5" maxlength="100" required>
        <div class="invalid-feedback">
            Ingresa un nombre válido (solo letras, mínimo 5 caracteres).
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Nombre de Usuario (Username)</label>
        <input type="text" name="username" class="form-control" 
               pattern="[a-zA-Z0-9_]+" minlength="4" maxlength="50" required>
        <div class="invalid-feedback">
            Mínimo 4 caracteres. Usa solo letras, números y guiones bajos (sin espacios).
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" 
               minlength="6" required>
        <div class="invalid-feedback">
            La contraseña debe tener al menos 6 caracteres.
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Rol del Sistema</label>
        <select name="rol_id" class="form-select" required>
            <option value="" disabled selected>Seleccione un rol...</option>
            <option value="1">Supervisor de Evento</option>
            <option value="2">Cajero</option>
        </select>
        <div class="invalid-feedback">
            Por favor, selecciona un rol de la lista.
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">Registrar Usuario</button>
        
        <div class="mt-4 text-center">
    <p class="mb-0">¿Ya tienes una cuenta? <a href="/index.php?ruta=login" class="text-decoration-none">Inicia sesión aquí</a></p>
</div>
    </div>
</form>

<script>
    // Script nativo de Bootstrap para activar las validaciones visuales
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

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>