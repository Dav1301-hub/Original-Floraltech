<?php
// Obtener mensajes de sesión si existen
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';
$logout_success = isset($_SESSION['logout_success']) ? $_SESSION['logout_success'] : '';
$login_success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : '';
$forgot_success = isset($_SESSION['forgot_success']) ? $_SESSION['forgot_success'] : '';

// Obtener información de intentos fallidos
$attempts_remaining = isset($_SESSION['attempts_remaining']) ? $_SESSION['attempts_remaining'] : null;
$max_attempts = isset($_SESSION['max_attempts']) ? $_SESSION['max_attempts'] : 3;
$account_locked = isset($_SESSION['account_locked']) ? $_SESSION['account_locked'] : false;
$lockout_time = isset($_SESSION['lockout_time']) ? $_SESSION['lockout_time'] : null;

// Limpiar los mensajes después de mostrarlos
unset($_SESSION['login_error']);
unset($_SESSION['register_success']);
unset($_SESSION['logout_success']);
unset($_SESSION['login_success']);
unset($_SESSION['forgot_success']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body style="background:#f8f9fa;">
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" style="padding: 40px 0;">
    <div class="row w-75 shadow-lg rounded overflow-hidden">
        <!-- Sección del Logo -->
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center" style="background:#b5b4b4; padding: 40px 0;">
            <img src="/Original-Floraltech/assets/images/logoepymes.png" alt="Logo" class="mb-4" style="width:320px;">
            <p class="text-center px-3" style="font-size:1.2rem;">"Gestiona tu floristería con facilidad, ¡deja que la tecnología florezca contigo!"</p>
        </div>

        <!-- Sección de Inicio de Sesión -->
        <div class="col-md-6 bg-white d-flex flex-column justify-content-center align-items-center" style="padding: 40px 0;">
            <div class="shadow rounded p-4" style="width:100%; max-width:400px; background:#fff;">
                <h2 class="mb-1 text-center fw-bold" style="font-size:2rem;">Inicia tu Sesión</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if (!empty($logout_success)): ?>
                    <div class="alert alert-info text-center"><?= htmlspecialchars($logout_success) ?></div>
                <?php endif; ?>

                <?php if (!empty($login_success)): ?>
                    <div class="alert alert-success text-center"><?= htmlspecialchars($login_success) ?></div>
                <?php endif; ?>

                <?php if (!empty($forgot_success)): ?>
                    <div class="alert alert-info text-center"><?= htmlspecialchars($forgot_success) ?></div>
                <?php endif; ?>

                
                <?php if ($attempts_remaining !== null && $attempts_remaining > 0 && !$account_locked): ?>
                    <div class="alert alert-warning text-center">
                        <strong>¡Atención!</strong><br>
                        Te quedan <?= $attempts_remaining ?> intento(s) antes de que tu cuenta se bloquee.
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($account_locked): ?>
                    <div class="alert alert-danger text-center">
                        <strong>¡Cuenta Bloqueada!</strong><br>
                        Has excedido el número máximo de intentos. Contacta al administrador.
                    </div>
                <?php endif; ?>


                <form method="POST" action="index.php?ctrl=login&action=authenticate">
                    <input type="text" class="form-control rounded mb-3" placeholder="Usuario o Email" name="username" required>
                    <input type="password" class="form-control rounded mb-3" placeholder="Contraseña" name="password" required>
                    <button type="submit" class="btn w-100 py-2" style="background:#2293c3; color:#fff; font-size:1.2rem; font-weight:bold;">Iniciar sesión</button>
                </form>

                <div class="text-center mt-3">
                    <a href="index.php?ctrl=login&action=forgot" style="color:#1877f2; text-decoration:none;">¿Olvidaste tu cuenta?</a>
                </div>
                <hr>
                <button class="btn btn-success w-100" type="button" onclick="window.location.href='index.php?ctrl=register&action=index'" style="font-weight:bold;">Crear Nuevo Usuario</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>