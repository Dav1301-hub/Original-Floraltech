<?php
// Obtener errores o éxitos de sesión si existen
$error = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : '';
$success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';
// Limpiar mensajes después de mostrarlos
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Nuevo Usuario - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <!-- Cargar reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body style="background:#f8f9fa;">
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-4">
    <div class="row w-100 shadow-lg rounded overflow-hidden" style="max-width: 1000px;">
        <!-- Logo -->
        <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center" style="background:#f9d6df; padding: 40px 20px; min-height: 500px;">
            <img src="../assets/images/logoepymes.png" class="mb-4" style="width:280px; max-width: 80%;">
            <p class="text-center px-3" style="font-size: 1.1rem; color: #333;">"Regístrate y gestiona tu florería de manera fácil y eficiente, ¡haz que la tecnología trabaje para ti!"</p>
        </div>

        <!-- Formulario -->
        <div class="col-lg-6 bg-white d-flex flex-column justify-content-center align-items-center" style="padding: 30px 20px; min-height: 500px;">
            <div class="w-100" style="max-width: 420px;">
                <h2 class="mb-4 text-center fw-bold" style="font-size:1.8rem; color: #2293c3;">Crear Cuenta</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?ctrl=register&action=create" id="registerForm">
                    <!-- Campo oculto para tipo de usuario (siempre Cliente = 5) -->
                    <input type="hidden" name="tpusu_idtpusu" value="5">
                    
                    <!-- Información básica -->
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <input type="text" class="form-control rounded" placeholder="Nombre de usuario" name="username" required>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control rounded" placeholder="Nombre completo" name="nombre_completo" required>
                        </div>
                    </div>
                    
                    <!-- Información de contacto -->
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <input type="email" class="form-control rounded" placeholder="Correo electrónico" name="email" required>
                        </div>
                        <div class="col">
                            <input type="tel" class="form-control rounded" placeholder="Teléfono" name="telefono" required>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <input type="text" class="form-control rounded" placeholder="Dirección completa" name="direccion" required>
                    </div>

                    <!-- Contraseñas -->
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <input type="password" class="form-control rounded" placeholder="Contraseña" name="password" required minlength="6">
                        </div>
                        <div class="col">
                            <input type="password" class="form-control rounded" placeholder="Confirmar contraseña" name="password_confirm" required minlength="6">
                        </div>
                    </div>

                    <!-- reCAPTCHA v2 -->
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="6LdL6bwrAAAAAGd47QZb40LsI4gzPFDcfP3btLvM"></div>
                        <div id="recaptcha-error" class="text-danger small mt-1" style="display: none;">
                            Por favor, completa el reCAPTCHA
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 py-2 mb-3" style="background:#2293c3; color:#fff; font-size:1.2rem; font-weight:bold;">Registrarse</button>
                </form>

                <hr class="my-3">
                <button class="btn btn-success w-100" type="button" onclick="window.location.href='index.php?ctrl=login&action=index'" style="font-weight:bold;">Iniciar Sesión</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Validación del formulario con reCAPTCHA
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const recaptchaResponse = grecaptcha.getResponse();
    
    if (!recaptchaResponse) {
        e.preventDefault();
        document.getElementById('recaptcha-error').style.display = 'block';
        
        // Scroll al reCAPTCHA
        document.querySelector('.g-recaptcha').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        
        return false;
    }
    
    // Ocultar mensaje de error si existe
    document.getElementById('recaptcha-error').style.display = 'none';
});

// Resetear mensaje de error cuando el usuario interactúa con reCAPTCHA
grecaptcha.ready(function() {
    const recaptchaWidget = document.querySelector('.g-recaptcha');
    if (recaptchaWidget) {
        grecaptcha.render(recaptchaWidget, {
            'sitekey': '6LdL6bwrAAAAAGd47QZb40LsI4gzPFDcfP3btLvM',
            'callback': function() {
                // Ocultar mensaje de error cuando se complete el reCAPTCHA
                document.getElementById('recaptcha-error').style.display = 'none';
            },
            'expired-callback': function() {
                // Mostrar mensaje si el reCAPTCHA expira
                document.getElementById('recaptcha-error').style.display = 'block';
            },
            'error-callback': function() {
                // Mostrar mensaje si hay error en reCAPTCHA
                document.getElementById('recaptcha-error').style.display = 'block';
            }
        });
    }
});
</script>
</body>
</html>