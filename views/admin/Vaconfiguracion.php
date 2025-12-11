<?php
// Vista de configuración (incluida en el dashboard admin)

require_once __DIR__ . '/../../models/conexion.php';
$mensaje_exito = '';
$mensaje_error = '';

$conexion = (new conexion())->get_conexion();
$id_admin = $_SESSION['user']['idusu'] ?? ($_SESSION['user_id'] ?? null);

// Cargar usuario admin
try {
    $stmt_admin = $conexion->prepare("SELECT idusu, nombre_completo, email, username, telefono FROM usu WHERE idusu = :id LIMIT 1");
    $stmt_admin->execute([':id' => $id_admin]);
    $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
    if (!$admin) {
        $stmt_admin = $conexion->query("SELECT idusu, nombre_completo, email, username, telefono FROM usu WHERE tpusu_idtpusu = 1 LIMIT 1");
        $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC) ?: [
            'nombre_completo' => '',
            'email' => '',
            'username' => '',
            'telefono' => ''
        ];
        if ($admin && empty($id_admin)) {
            $id_admin = $admin['idusu'];
        }
        $mensaje_error = $mensaje_error ?: 'Usuario de sesión no encontrado, mostrando el primer administrador disponible.';
    }
} catch (Exception $e) {
    $mensaje_error = 'Error al obtener datos del usuario: ' . $e->getMessage();
    $admin = [
        'nombre_completo' => '',
        'email' => '',
        'username' => '',
        'telefono' => ''
    ];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['actualizar_personal'])) {
            $nombre = trim($_POST['nombre_completo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            if ($nombre === '') {
                throw new Exception('El nombre completo es requerido');
            }
            $stmt_update = $conexion->prepare("UPDATE usu SET nombre_completo=:n, telefono=:t WHERE idusu=:id");
            $stmt_update->execute([
                ':n' => $nombre,
                ':t' => $telefono,
                ':id' => $id_admin
            ]);
            $mensaje_exito = 'Información personal actualizada';
        }

        if (isset($_POST['actualizar_contrasena'])) {
            $pass1 = $_POST['nueva_contrasena'] ?? '';
            $pass2 = $_POST['confirmar_contrasena'] ?? '';
            if ($pass1 === '' || $pass2 === '') {
                throw new Exception('Ambos campos de contraseña son requeridos');
            }
            if ($pass1 !== $pass2) {
                throw new Exception('Las contraseñas no coinciden');
            }
            if (strlen($pass1) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $stmt_update = $conexion->prepare("UPDATE usu SET clave = ? WHERE idusu = ?");
            $stmt_update->execute([$hash, $id_admin]);
            $mensaje_exito = 'Contraseña actualizada';
        }

    } catch (Exception $e) {
        $mensaje_error = $e->getMessage();
    }

    if ($mensaje_exito) {
        try {
            $stmt_admin = $conexion->prepare("SELECT nombre_completo, email, username, telefono FROM usu WHERE idusu = :id LIMIT 1");
            $stmt_admin->execute([':id' => $id_admin]);
            $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC) ?: $admin;
        } catch (Exception $e) {}
    }
}
?>

<div class="container-fluid py-4" style="background:#fff; min-height: 100vh;">
    <div class="p-4 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color: #fff;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <p class="mb-1 text-white-50 small" style="color: #ffff">Ajustes de cuenta</p>
                <h2 class="fw-bold mb-0" style="color: #ffff">Configuracion</h2>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25">Usuario: <?= htmlspecialchars($admin['username'] ?? '') ?></span>
                <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25">Rol: Administrador</span>
            </div>
        </div>
    </div>

    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form class="mx-auto px-3 w-100" method="POST">
        <div class="row g-4">
            <div class="col-12 col-lg-6 d-flex">
                <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width:44px;height:44px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="mb-0">Información personal</h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" name="nombre_completo" value="<?= htmlspecialchars($admin['nombre_completo']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
                            <small class="text-muted">El email no se puede modificar</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($admin['telefono']) ?>">
                        </div>
                        <button class="btn btn-primary w-100 mt-2" name="actualizar_personal"><i class="fas fa-save me-2"></i>Guardar cambios</button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 d-flex">
                <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width:44px;height:44px;">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h5 class="mb-0">Contraseña</h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" name="nueva_contrasena" placeholder="Minimo 6 caracteres">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" name="confirmar_contrasena" placeholder="Repetir contraseña">
                        </div>
                        <div class="small text-muted mb-2">Deja los campos vacíos para mantener la actual.</div>
                        <button class="btn btn-warning w-100 mt-2 text-dark" name="actualizar_contrasena"><i class="fas fa-save me-2"></i>Actualizar contraseña</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nuevaContrasena = document.querySelector('input[name="nueva_contrasena"]');
    const confirmarContrasena = document.querySelector('input[name="confirmar_contrasena"]');
    function validarContrasenas() {
        if (nuevaContrasena.value || confirmarContrasena.value) {
            if (nuevaContrasena.value !== confirmarContrasena.value) {
                confirmarContrasena.setCustomValidity('Las contraseñas no coinciden');
            } else if (nuevaContrasena.value.length < 6) {
                nuevaContrasena.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            } else {
                nuevaContrasena.setCustomValidity('');
                confirmarContrasena.setCustomValidity('');
            }
        } else {
            nuevaContrasena.setCustomValidity('');
            confirmarContrasena.setCustomValidity('');
        }
    }
    nuevaContrasena.addEventListener('input', validarContrasenas);
    confirmarContrasena.addEventListener('input', validarContrasenas);
    setTimeout(function() {
        document.querySelectorAll('.alert-success, .alert-danger').forEach(function(alert) {
            if (alert.classList.contains('alert-dismissible')) {
                const instance = new bootstrap.Alert(alert);
                instance.close();
            }
        });
    }, 5000);
});
</script>
