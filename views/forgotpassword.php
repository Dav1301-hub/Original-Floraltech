<?php
// Obtener mensajes de sesión si existen
$error = isset($_SESSION['forgot_error']) ? $_SESSION['forgot_error'] : '';
$success = isset($_SESSION['forgot_success']) ? $_SESSION['forgot_success'] : '';
// Limpiar los mensajes después de mostrarlos
unset($_SESSION['forgot_error']);
unset($_SESSION['forgot_success']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f8f9fa url('assets/images/composicion-de-flores-y-conos-de-waffle.jpg') no-repeat center center fixed; background-size: cover;">
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-75 shadow-lg rounded overflow-hidden">
        <!-- Sección del Logo -->
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center" style="background:rgba(249,214,223,0.65); padding: 40px 0;">
            <img src="assets/images/logoepymes.png" alt="Logo" class="mb-4" style="width:220px;">
            <p class="text-center px-3" style="font-size:1.2rem; color: #333;">"Recupera el acceso a tu cuenta de manera fácil y segura."</p>
        </div>

        <!-- Formulario -->
        <div class="col-md-6 bg-white d-flex flex-column justify-content-center align-items-center" style="padding: 40px 0;">
            <div class="shadow rounded p-4" style="width:100%; max-width:400px; background:#fff;">
                <h2 class="mb-1 text-center fw-bold">Recuperar Contraseña</h2>
                <p class="mb-3 text-center text-muted">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?ctrl=login&action=sendRecovery">
                    <input type="email" class="form-control rounded mb-3" placeholder="Correo electrónico" name="email" required>
                    <button type="submit" class="btn btn-primary w-100 py-2">Enviar instrucciones</button>
                </form>

                <div class="text-center mt-3">
                    <a href="index.php?ctrl=login&action=index">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>