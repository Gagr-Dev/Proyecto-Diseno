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
        <div class="col-md-8"> <div class="card shadow-sm">
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
                            <label class="form-label">Cédula de Identidad</label>
                            <input type="text" name="cedula" class="form-control" 
                                   pattern="^[VEve]?-?[0-9]{6,10}$" required placeholder="Ej: V-12345678 o 12345678">
                            <div class="invalid-feedback">
                                Ingresa una cédula válida.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Primer Nombre</label>
                                <input type="text" name="primer_nombre" class="form-control" 
                                       pattern="^[a-zA-ZÀ-ÿ\s]+$" minlength="2" maxlength="50" required>
                                <div class="invalid-feedback">El primer nombre es obligatorio.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Segundo Nombre (Opcional)</label>
                                <input type="text" name="segundo_nombre" class="form-control" 
                                       pattern="^[a-zA-ZÀ-ÿ\s]*$" maxlength="50">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Primer Apellido</label>
                                <input type="text" name="primer_apellido" class="form-control" 
                                       pattern="^[a-zA-ZÀ-ÿ\s]+$" minlength="2" maxlength="50" required>
                                <div class="invalid-feedback">El primer apellido es obligatorio.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Segundo Apellido (Opcional)</label>
                                <input type="text" name="segundo_apellido" class="form-control" 
                                       pattern="^[a-zA-ZÀ-ÿ\s]*$" maxlength="50">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3 text-secondary">Credenciales de Acceso</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de Usuario (Username)</label>
                                <input type="text" name="username" class="form-control" 
                                       pattern="[a-zA-Z0-9_]+" minlength="4" maxlength="50" required>
                                <div class="invalid-feedback">Mínimo 4 caracteres (letras, números, guiones bajos).</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                                <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Rol del Sistema</label>
                            <select name="rol_id" class="form-select" required>
                                <option value="" disabled selected>Seleccione un rol...</option>
                                <option value="1">Supervisor de Evento</option>
                                <option value="2">Cajero</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un rol.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                            <div class="mt-4 text-center">
                                <p class="mb-0">¿Ya tienes una cuenta? <a href="/index.php?ruta=login" class="text-decoration-none">Inicia sesión aquí</a></p>
                            </div>
                        </div>
                    </form>

<script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault() http_response_code(404);
       
        require __DIR__ . '/../views/404.php';
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