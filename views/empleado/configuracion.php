<?php
if (!isset($_SESSION['user']) || !in_array((int)($_SESSION['user']['tpusu_idtpusu'] ?? 0), [2, 3], true)) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

require_once 'models/conexion.php';

$usuario = $_SESSION['user'];
$mensaje = '';
$tipo_mensaje = '';

$conn = new conexion();
$db = $conn->get_conexion();

try {
    $stmt = $db->prepare("SELECT * FROM usu WHERE idusu = ?");
    $stmt->execute([$usuario['idusu']]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $datos_usuario = $usuario;
}

// URL del avatar: desde DB (avatar_data) o legacy path
$avatar_actual_url = null;
if (!empty($datos_usuario['avatar_data'])) {
    $avatar_actual_url = 'avatar.php?tipo=usu&id=' . $usuario['idusu'];
} elseif (!empty($datos_usuario['avatar']) && file_exists(__DIR__ . '/../../' . $datos_usuario['avatar'])) {
    $avatar_actual_url = $datos_usuario['avatar'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $naturaleza = trim($_POST['naturaleza'] ?? '');
        $nueva_clave = $_POST['nueva_clave'] ?? '';
        $confirmar_clave = $_POST['confirmar_clave'] ?? '';

        if (empty($nombre_completo)) {
            throw new Exception('El nombre completo es requerido');
        }

        if (!empty($nueva_clave)) {
            if ($nueva_clave !== $confirmar_clave) {
                throw new Exception('Las contraseñas no coinciden');
            }
            if (strlen($nueva_clave) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
        }

        // Avatar: guardar en base de datos (avatar_data, avatar_tipo), no en archivos
        $avatar_data = null;
        $avatar_tipo = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                throw new Exception('La foto no debe exceder 2MB');
            }
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('Solo se permiten imágenes JPG o PNG');
            }
            $avatar_data = file_get_contents($file['tmp_name']);
            $avatar_tipo = $mimeType;
        }

        $params = [$nombre_completo, $telefono, $naturaleza];
        $sets = "nombre_completo = ?, telefono = ?, naturaleza = ?";
        if (!empty($nueva_clave)) {
            $sets .= ", clave = ?";
            $params[] = password_hash($nueva_clave, PASSWORD_DEFAULT);
        }
        if ($avatar_data !== null) {
            $sets .= ", avatar_data = ?, avatar_tipo = ?, avatar = NULL";
            $params[] = $avatar_data;
            $params[] = $avatar_tipo;
        }
        $params[] = $usuario['idusu'];
        $stmt = $db->prepare("UPDATE usu SET $sets WHERE idusu = ?");
        $stmt->execute($params);

        $_SESSION['user']['nombre_completo'] = $nombre_completo;
        $_SESSION['user']['telefono'] = $telefono;
        $_SESSION['user']['naturaleza'] = $naturaleza;

        $datos_usuario['nombre_completo'] = $nombre_completo;
        $datos_usuario['telefono'] = $telefono;
        $datos_usuario['naturaleza'] = $naturaleza;
        if ($avatar_data !== null) {
            $avatar_actual_url = 'avatar.php?tipo=usu&id=' . $usuario['idusu'];
            $datos_usuario['avatar_data'] = true;
        }

        $mensaje = 'Perfil actualizado correctamente';
        $tipo_mensaje = 'success';
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}

$navbar_volver_url = 'index.php?ctrl=empleado&action=dashboard';
$navbar_volver_text = 'Volver al Dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .config-page { --cfg-accent: #0d9488; --cfg-radius: 16px; --cfg-shadow: 0 4px 20px rgba(13, 148, 136, 0.08); }
        .config-page .page-title-card {
            background: linear-gradient(135deg, #fff 0%, #f0fdfa 100%);
            border: 1px solid rgba(13, 148, 136, 0.2);
            border-radius: var(--cfg-radius);
            box-shadow: var(--cfg-shadow);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--cfg-accent);
        }
        .config-page .page-title-card h1 { font-size: 1.35rem; font-weight: 700; color: #134e4a; margin: 0 0 0.25rem 0; }
        .config-page .page-title-card p { margin: 0; color: #5eead4; font-size: 0.9rem; }
        .config-page .config-card {
            background: #fff;
            border: 1px solid rgba(13, 148, 136, 0.15);
            border-radius: var(--cfg-radius);
            box-shadow: var(--cfg-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .config-page .config-card .card-header {
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            border-bottom: 1px solid rgba(13, 148, 136, 0.15);
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: #134e4a;
            font-size: 1rem;
        }
        .config-page .config-card .card-header i { color: var(--cfg-accent); margin-right: 0.5rem; }
        .config-page .config-card .card-body { padding: 1.25rem 1.5rem; }
        .config-page .form-label { font-weight: 600; color: #134e4a; }
        .config-page .form-control { border-radius: 10px; border: 1px solid rgba(13, 148, 136, 0.25); }
        .config-page .form-control:focus { border-color: var(--cfg-accent); box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15); }
        .config-page .btn-guardar {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        }
        .config-page .btn-guardar:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4); }
        .config-page .config-center { width: 100%; max-width: 100%; }
        .config-page .avatar-wrap {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--cfg-accent);
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .config-page .avatar-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .config-page .avatar-wrap .avatar-placeholder { font-size: 3rem; color: var(--cfg-accent); opacity: 0.6; }
    </style>
</head>
<body class="empleado-theme">
    <div class="dashboard-container config-page">
        <?php include __DIR__ . '/partials/navbar_empleado.php'; ?>

        <div class="main-content">
            <div class="config-center w-100">
                <div class="page-title-card">
                    <h1><i class="fas fa-cog me-2" style="color: var(--cfg-accent);"></i> Configuración de Cuenta</h1>
                    <p>Actualiza tu información personal</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="configForm" enctype="multipart/form-data">
                    <div class="config-card">
                        <div class="card-header">
                            <i class="fas fa-camera"></i> Foto de perfil
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-wrap">
                                <?php if (!empty($avatar_actual_url)): ?>
                                    <img src="<?= htmlspecialchars($avatar_actual_url) ?>?v=<?= time() ?>" alt="Mi foto">
                                <?php else: ?>
                                    <span class="avatar-placeholder"><i class="fas fa-user"></i></span>
                                <?php endif; ?>
                            </div>
                            <label class="form-label d-block mb-2">Elige una imagen (JPG o PNG, máx. 2MB)</label>
                            <input type="file" class="form-control form-control-sm mx-auto" name="avatar" accept="image/jpeg,image/png,image/jpg" style="max-width: 280px;">
                        </div>
                    </div>

                    <div class="config-card">
                        <div class="card-header">
                            <i class="fas fa-user-edit"></i> Información Personal
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre_completo" class="form-label"><i class="fas fa-user me-1 text-muted"></i> Nombre completo</label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($datos_usuario['nombre_completo'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label"><i class="fas fa-envelope me-1 text-muted"></i> Email</label>
                                    <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($datos_usuario['email'] ?? '') ?>" disabled>
                                    <small class="text-muted">No se puede modificar</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label"><i class="fas fa-phone me-1 text-muted"></i> Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($datos_usuario['telefono'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="username" class="form-label"><i class="fas fa-at me-1 text-muted"></i> Usuario</label>
                                    <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($datos_usuario['username'] ?? '') ?>" disabled>
                                    <small class="text-muted">No se puede modificar</small>
                                </div>
                                <div class="col-12">
                                    <label for="naturaleza" class="form-label"><i class="fas fa-map-marker-alt me-1 text-muted"></i> Dirección / Notas</label>
                                    <textarea class="form-control" id="naturaleza" name="naturaleza" rows="2" placeholder="Dirección o notas"><?= htmlspecialchars($datos_usuario['naturaleza'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="config-card">
                        <div class="card-header">
                            <i class="fas fa-lock"></i> Cambiar contraseña
                            <small class="fw-normal text-muted ms-2">(Opcional)</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nueva_clave" class="form-label"><i class="fas fa-key me-1 text-muted"></i> Nueva contraseña</label>
                                    <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" minlength="6" placeholder="Mínimo 6 caracteres">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmar_clave" class="form-label"><i class="fas fa-check-double me-1 text-muted"></i> Confirmar contraseña</label>
                                    <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="6" placeholder="Repetir contraseña">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                        <a href="index.php?ctrl=empleado&action=dashboard" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-guardar">
                            <i class="fas fa-save me-2"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('configForm').addEventListener('submit', function(e) {
            var nueva = document.getElementById('nueva_clave').value;
            var conf = document.getElementById('confirmar_clave').value;
            if (nueva !== conf) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
        });
    </script>
</body>
</html>
