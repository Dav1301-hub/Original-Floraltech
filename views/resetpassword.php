<?php
// Obtener mensajes de sesión si existen
$error = isset($_SESSION['reset_error']) ? $_SESSION['reset_error'] : '';
$success = isset($_SESSION['reset_success']) ? $_SESSION['reset_success'] : '';
// Limpiar los mensajes después de mostrarlos
unset($_SESSION['reset_error']);
unset($_SESSION['reset_success']);

$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f8f9fa;">
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-75 shadow-lg rounded overflow-hidden">
        <!-- Sección del Logo -->
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center" style="background:#e8f4f8;">
            <img src="/Original-Floraltech/assets/images/logoepymes.png" alt="Logo" class="mb-4" style="width:220px;">
            <p class="text-center px-3" style="font-size:1.2rem;">"Crea una nueva contraseña segura para tu cuenta."</p>
        </div>

        <!-- Formulario -->
        <div class="col-md-6 bg-white d-flex flex-column justify-content-center align-items-center" style="padding: 40px 0;">
            <div class="shadow rounded p-4" style="width:100%; max-width:400px; background:#fff;">
                <h2 class="mb-3 text-center fw-bold">Restablecer Contraseña</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if (!empty($token) && empty($success)): ?>
                <form method="POST" action="index.php?ctrl=login&action=updatePassword">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <input type="password" class="form-control mb-3" name="password" placeholder="Nueva contraseña" required minlength="6">
                    <input type="password" class="form-control mb-3" name="confirm_password" placeholder="Confirmar contraseña" required minlength="6">
                    <button type="submit" class="btn btn-primary w-100 py-2">Cambiar contraseña</button>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">El enlace de recuperación no es válido o ha expirado.</div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="index.php?ctrl=login&action=index">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>