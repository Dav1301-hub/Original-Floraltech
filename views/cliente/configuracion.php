<?php
// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 5) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

// Incluir archivos de configuración
require_once 'models/conexion.php';
require_once 'controllers/cconfig.php';

$usuario = $_SESSION['user'];
$mensaje = '';
$tipo_mensaje = '';

// Conectar a la base de datos
$conn = new conexion();
$db = $conn->get_conexion();

// Procesar actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre_completo = $_POST['nombre_completo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $naturaleza = $_POST['naturaleza'] ?? '';
        $nueva_clave = $_POST['nueva_clave'] ?? '';
        $confirmar_clave = $_POST['confirmar_clave'] ?? '';
        
        // Validaciones
        if (empty($nombre_completo)) {
            throw new Exception('El nombre completo es requerido');
        }
        
        // Si se quiere cambiar la contraseña
        if (!empty($nueva_clave)) {
            if ($nueva_clave !== $confirmar_clave) {
                throw new Exception('Las contraseñas no coinciden');
            }
            if (strlen($nueva_clave) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
        }
        
        // Actualizar datos del usuario
        if (!empty($nueva_clave)) {
            $clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usu SET nombre_completo = ?, telefono = ?, naturaleza = ?, clave = ? WHERE idusu = ?");
            $stmt->execute([$nombre_completo, $telefono, $naturaleza, $clave_hash, $usuario['idusu']]);
        } else {
            $stmt = $db->prepare("UPDATE usu SET nombre_completo = ?, telefono = ?, naturaleza = ? WHERE idusu = ?");
            $stmt->execute([$nombre_completo, $telefono, $naturaleza, $usuario['idusu']]);
        }
        
        // Actualizar también la tabla de clientes
        $stmt = $db->prepare("UPDATE cli SET nombre = ?, telefono = ?, direccion = ? WHERE email = ?");
        $stmt->execute([$nombre_completo, $telefono, $naturaleza, $usuario['email']]);
        
        // Actualizar la sesión
        $_SESSION['user']['nombre_completo'] = $nombre_completo;
        $_SESSION['user']['telefono'] = $telefono;
        $_SESSION['user']['naturaleza'] = $naturaleza;
        
        $mensaje = 'Perfil actualizado correctamente';
        $tipo_mensaje = 'success';
        
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}

// Obtener datos actuales del usuario
try {
    $stmt = $db->prepare("SELECT * FROM usu WHERE idusu = ?");
    $stmt->execute([$usuario['idusu']]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $datos_usuario = $usuario;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Cuenta - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Estilizado -->
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fas fa-seedling"></i>
                FloralTech
            </div>
            <div class="navbar-user">
                <div class="user-info">
                    <p class="user-name"><?= htmlspecialchars($usuario['nombre_completo']) ?></p>
                    <p class="user-welcome">Configuración de Cuenta</p>
                </div>
                <a href="index.php?ctrl=cliente&action=dashboard" class="logout-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </nav>

        <!-- Saludo Principal Estilizado -->
        <div class="welcome-card card">
            <div class="card-body">
                <div class="welcome-header">
                    <h2><i class="fas fa-user-cog"></i> Configuración de Cuenta</h2>
                    <p>Actualiza tu información personal y preferencias</p>
                </div>
            </div>
        </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario de configuración -->
            <div class="content-grid" style="grid-template-columns: 1fr;">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-edit"></i> Información Personal
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="configForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_completo" class="form-label">
                                        <i class="fas fa-user"></i> Nombre Completo
                                    </label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                           value="<?= htmlspecialchars($datos_usuario['nombre_completo'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email
                                    </label>
                                    <input type="email" class="form-control" id="email" 
                                           value="<?= htmlspecialchars($datos_usuario['email'] ?? '') ?>" disabled>
                                    <div class="form-text">El email no se puede modificar</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone"></i> Teléfono
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= htmlspecialchars($datos_usuario['telefono'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-at"></i> Nombre de Usuario
                                    </label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?= htmlspecialchars($datos_usuario['username'] ?? '') ?>" disabled>
                                    <div class="form-text">El nombre de usuario no se puede modificar</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="naturaleza" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Dirección
                                </label>
                                <textarea class="form-control" id="naturaleza" name="naturaleza" rows="2" 
                                          placeholder="Ingresa tu dirección completa"><?= htmlspecialchars($datos_usuario['naturaleza'] ?? '') ?></textarea>
                            </div>

                            <!-- Cambio de contraseña -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <i class="fas fa-lock"></i> Cambiar Contraseña
                                    <small class="text-muted">(Opcional - Dejar en blanco para mantener la actual)</small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nueva_clave" class="form-label">
                                                <i class="fas fa-key"></i> Nueva Contraseña
                                            </label>
                                            <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" 
                                                   minlength="6" placeholder="Mínimo 6 caracteres">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="confirmar_clave" class="form-label">
                                                <i class="fas fa-check-double"></i> Confirmar Contraseña
                                            </label>
                                            <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" 
                                                   minlength="6" placeholder="Repetir nueva contraseña">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="config-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dashboard-cliente.js"></script>
    <script>
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 3000);
    </script>
</body>
</html>
