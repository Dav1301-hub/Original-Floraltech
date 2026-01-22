<?php
// Vista de configuración (incluida en el dashboard admin)

require_once __DIR__ . '/../../models/conexion.php';
$mensaje_exito = '';
$mensaje_error = '';

$conexion = (new conexion())->get_conexion();
$id_admin = $_SESSION['user']['idusu'] ?? ($_SESSION['user_id'] ?? null);

// Cargar usuario admin
try {
    $stmt_admin = $conexion->prepare("SELECT idusu, nombre_completo, email, username, telefono, avatar, notificaciones_email FROM usu WHERE idusu = :id LIMIT 1");
    $stmt_admin->execute([':id' => $id_admin]);
    $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
    if (!$admin) {
        $stmt_admin = $conexion->query("SELECT idusu, nombre_completo, email, username, telefono, avatar, notificaciones_email FROM usu WHERE tpusu_idtpusu = 1 LIMIT 1");
        $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC) ?: [
            'nombre_completo' => '',
            'email' => '',
            'username' => '',
            'telefono' => '',
            'avatar' => null,
            'notificaciones_email' => 1
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
        'telefono' => '',
        'avatar' => null,
        'notificaciones_email' => 1
    ];
}

// Cargar configuración de la empresa
try {
    $stmt_empresa = $conexion->prepare("SELECT * FROM empresa LIMIT 1");
    $stmt_empresa->execute();
    $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        $empresa = [
            'nombre' => 'FloralTech',
            'direccion' => '',
            'telefono' => '',
            'email' => '',
            'horario' => '',
            'logo' => null,
            'facebook' => '',
            'instagram' => '',
            'whatsapp' => '',
            'moneda' => 'CRC',
            'iva_porcentaje' => 13.00,
            'zona_horaria' => 'America/Costa_Rica',
            'formato_fecha' => 'd/m/Y'
        ];
    }
} catch (Exception $e) {
    $empresa = [
        'nombre' => 'FloralTech',
        'direccion' => '',
        'telefono' => '',
        'email' => '',
        'horario' => '',
        'logo' => null,
        'facebook' => '',
        'instagram' => '',
        'whatsapp' => '',
        'moneda' => 'CRC',
        'iva_porcentaje' => 13.00,
        'zona_horaria' => 'America/Costa_Rica',
        'formato_fecha' => 'd/m/Y'
    ];
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['actualizar_personal'])) {
            $nombre = trim($_POST['nombre_completo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $notif_email = isset($_POST['notificaciones_email']) ? 1 : 0;
            
            if ($nombre === '') {
                throw new Exception('El nombre completo es requerido');
            }
            
            // Procesar avatar si se subió
            $avatar_path = $admin['avatar'];
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if ($file['size'] > $maxSize) {
                    throw new Exception('El avatar no debe exceder 2MB');
                }
                
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedTypes)) {
                    throw new Exception('Solo se permiten imágenes JPG o PNG');
                }
                
                $uploadDir = __DIR__ . '/../../uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $id_admin . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Eliminar avatar anterior si existe
                    if ($admin['avatar'] && file_exists(__DIR__ . '/../../' . $admin['avatar'])) {
                        unlink(__DIR__ . '/../../' . $admin['avatar']);
                    }
                    $avatar_path = 'uploads/avatars/' . $filename;
                }
            }
            
            $stmt_update = $conexion->prepare("UPDATE usu SET nombre_completo=:n, telefono=:t, avatar=:a, notificaciones_email=:ne WHERE idusu=:id");
            $stmt_update->execute([
                ':n' => $nombre,
                ':t' => $telefono,
                ':a' => $avatar_path,
                ':ne' => $notif_email,
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

        if (isset($_POST['actualizar_empresa'])) {
            $nombre_emp = trim($_POST['nombre_empresa'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono_emp = trim($_POST['telefono_empresa'] ?? '');
            $email_emp = trim($_POST['email_contacto'] ?? '');
            $horario = trim($_POST['horario'] ?? '');
            $facebook = trim($_POST['facebook'] ?? '');
            $instagram = trim($_POST['instagram'] ?? '');
            $whatsapp = trim($_POST['whatsapp'] ?? '');
            $moneda = trim($_POST['moneda'] ?? 'CRC');
            $iva = floatval($_POST['iva_porcentaje'] ?? 13.00);
            $zona_horaria = trim($_POST['zona_horaria'] ?? 'America/Costa_Rica');
            $formato_fecha = trim($_POST['formato_fecha'] ?? 'd/m/Y');
            
            if (empty($nombre_emp)) {
                throw new Exception('El nombre de la empresa es requerido');
            }
            
            // Procesar logo si se subió
            $logo_path = $empresa['logo'];
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['logo'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                if ($file['size'] > $maxSize) {
                    throw new Exception('El logo no debe exceder 5MB');
                }
                
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedTypes)) {
                    throw new Exception('Solo se permiten imágenes JPG o PNG para el logo');
                }
                
                $uploadDir = __DIR__ . '/../../uploads/logos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Eliminar logo anterior si existe
                    if ($empresa['logo'] && file_exists(__DIR__ . '/../../' . $empresa['logo'])) {
                        unlink(__DIR__ . '/../../' . $empresa['logo']);
                    }
                    $logo_path = 'uploads/logos/' . $filename;
                }
            }
            
            // Verificar si existe registro
            $stmt_check = $conexion->query("SELECT COUNT(*) FROM empresa");
            $existe = $stmt_check->fetchColumn() > 0;
            
            if ($existe) {
                $stmt_update = $conexion->prepare("UPDATE empresa SET nombre=:n, direccion=:d, telefono=:t, email=:e, horario=:h, logo=:l, facebook=:f, instagram=:i, whatsapp=:w, moneda=:m, iva_porcentaje=:iva, zona_horaria=:zh, formato_fecha=:ff WHERE id=(SELECT MIN(id) FROM empresa)");
                $stmt_update->execute([
                    ':n' => $nombre_emp,
                    ':d' => $direccion,
                    ':t' => $telefono_emp,
                    ':e' => $email_emp,
                    ':h' => $horario,
                    ':l' => $logo_path,
                    ':f' => $facebook,
                    ':i' => $instagram,
                    ':w' => $whatsapp,
                    ':m' => $moneda,
                    ':iva' => $iva,
                    ':zh' => $zona_horaria,
                    ':ff' => $formato_fecha
                ]);
            } else {
                $stmt_insert = $conexion->prepare("INSERT INTO empresa (nombre, direccion, telefono, email, horario, logo, facebook, instagram, whatsapp, moneda, iva_porcentaje, zona_horaria, formato_fecha) VALUES (:n, :d, :t, :e, :h, :l, :f, :i, :w, :m, :iva, :zh, :ff)");
                $stmt_insert->execute([
                    ':n' => $nombre_emp,
                    ':d' => $direccion,
                    ':t' => $telefono_emp,
                    ':e' => $email_emp,
                    ':h' => $horario,
                    ':l' => $logo_path,
                    ':f' => $facebook,
                    ':i' => $instagram,
                    ':w' => $whatsapp,
                    ':m' => $moneda,
                    ':iva' => $iva,
                    ':zh' => $zona_horaria,
                    ':ff' => $formato_fecha
                ]);
            }
            
            $mensaje_exito = 'Configuración de la empresa actualizada';
            
            // Recargar configuración de empresa
            $stmt_empresa = $conexion->prepare("SELECT * FROM empresa LIMIT 1");
            $stmt_empresa->execute();
            $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
        }

    } catch (Exception $e) {
        $mensaje_error = $e->getMessage();
    }

    if ($mensaje_exito && !isset($_POST['actualizar_empresa'])) {
        try {
            $stmt_admin = $conexion->prepare("SELECT nombre_completo, email, username, telefono, avatar, notificaciones_email FROM usu WHERE idusu = :id LIMIT 1");
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

    <!-- Configuración Personal -->
    <div class="mb-4">
        <h5 class="mb-3 text-muted"><i class="fas fa-user-circle me-2"></i>Configuración Personal</h5>
        <form class="mx-auto px-3 w-100" method="POST" enctype="multipart/form-data">
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
                            
                            <!-- Avatar -->
                            <div class="mb-3 text-center">
                                <label class="form-label fw-600">Foto de perfil</label>
                                <div class="mb-2">
                                    <?php if (!empty($admin['avatar']) && file_exists(__DIR__ . '/../../' . $admin['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($admin['avatar']) ?>?v=<?= time() ?>" alt="Avatar" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #0d6efd;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                            <i class="fas fa-user fa-3x text-primary"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control" name="avatar" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">JPG o PNG, máx. 2MB</small>
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
                            
                            <!-- Notificaciones -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="notificaciones_email" id="notifEmail" <?= !empty($admin['notificaciones_email']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="notifEmail">
                                        <i class="fas fa-bell me-1"></i>Recibir notificaciones por email
                                    </label>
                                </div>
                                <small class="text-muted">Recibe alertas de pedidos, pagos y actividad del sistema</small>
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

    <!-- Configuración de la Empresa -->
    <div class="mb-4">
        <h5 class="mb-3 text-muted"><i class="fas fa-building me-2"></i>Configuración de la Empresa</h5>
        <form class="mx-auto px-3 w-100" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- Información General -->
                <div class="col-12 col-lg-6 d-flex">
                    <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h5 class="mb-0">Información General</h5>
                            </div>

                            <!-- Logo -->
                            <div class="mb-3 text-center">
                                <label class="form-label fw-600">Logo de la empresa</label>
                                <div class="mb-2">
                                    <?php if (!empty($empresa['logo']) && file_exists(__DIR__ . '/../../' . $empresa['logo'])): ?>
                                        <img src="<?= htmlspecialchars($empresa['logo']) ?>?v=<?= time() ?>" alt="Logo" class="img-fluid rounded" style="max-width: 200px; max-height: 100px; object-fit: contain; border: 2px solid #198754;">
                                    <?php else: ?>
                                        <div class="bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded" style="width: 200px; height: 100px;">
                                            <i class="fas fa-image fa-3x text-success"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">JPG o PNG, máx. 5MB - Recomendado: 400x200px</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Nombre de la empresa</label>
                                <input type="text" class="form-control form-control-lg" name="nombre_empresa" value="<?= htmlspecialchars($empresa['nombre'] ?? '') ?>" required>
                                <small class="text-muted">Nombre comercial o razón social</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Dirección</label>
                                <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($empresa['direccion'] ?? '') ?>">
                                <small class="text-muted">Dirección física de la empresa</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Teléfono</label>
                                <input type="text" class="form-control" name="telefono_empresa" value="<?= htmlspecialchars($empresa['telefono'] ?? '') ?>">
                                <small class="text-muted">Número de contacto principal</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de Contacto y Redes -->
                <div class="col-12 col-lg-6 d-flex">
                    <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h5 class="mb-0">Contacto y Redes</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Email de contacto</label>
                                <input type="email" class="form-control form-control-lg" name="email_contacto" value="<?= htmlspecialchars($empresa['email'] ?? '') ?>">
                                <small class="text-muted">Correo electrónico principal</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Horario de atención</label>
                                <textarea class="form-control" name="horario" rows="3" placeholder="Ej: Lunes a Viernes 8:00 AM - 6:00 PM"><?= htmlspecialchars($empresa['horario'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600"><i class="fab fa-facebook text-primary me-1"></i>Facebook</label>
                                <input type="url" class="form-control" name="facebook" value="<?= htmlspecialchars($empresa['facebook'] ?? '') ?>" placeholder="https://facebook.com/tuempresa">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600"><i class="fab fa-instagram text-danger me-1"></i>Instagram</label>
                                <input type="url" class="form-control" name="instagram" value="<?= htmlspecialchars($empresa['instagram'] ?? '') ?>" placeholder="https://instagram.com/tuempresa">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600"><i class="fab fa-whatsapp text-success me-1"></i>WhatsApp</label>
                                <input type="text" class="form-control" name="whatsapp" value="<?= htmlspecialchars($empresa['whatsapp'] ?? '') ?>" placeholder="+506 1234-5678">
                                <small class="text-muted">Formato: +506 1234-5678</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración Regional y Fiscal -->
                <div class="col-12 d-flex">
                    <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <h5 class="mb-0">Configuración Regional y Fiscal</h5>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Moneda</label>
                                    <select class="form-select" name="moneda">
                                        <option value="CRC" <?= ($empresa['moneda'] ?? 'CRC') === 'CRC' ? 'selected' : '' ?>>₡ Colón (CRC)</option>
                                        <option value="USD" <?= ($empresa['moneda'] ?? '') === 'USD' ? 'selected' : '' ?>>$ Dólar (USD)</option>
                                        <option value="EUR" <?= ($empresa['moneda'] ?? '') === 'EUR' ? 'selected' : '' ?>>€ Euro (EUR)</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">IVA (%)</label>
                                    <input type="number" class="form-control" name="iva_porcentaje" step="0.01" min="0" max="100" value="<?= htmlspecialchars($empresa['iva_porcentaje'] ?? 13.00) ?>">
                                    <small class="text-muted">Porcentaje de impuesto</small>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Zona Horaria</label>
                                    <select class="form-select" name="zona_horaria">
                                        <option value="America/Costa_Rica" <?= ($empresa['zona_horaria'] ?? 'America/Costa_Rica') === 'America/Costa_Rica' ? 'selected' : '' ?>>Costa Rica (GMT-6)</option>
                                        <option value="America/New_York" <?= ($empresa['zona_horaria'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>New York (GMT-5)</option>
                                        <option value="America/Los_Angeles" <?= ($empresa['zona_horaria'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Los Angeles (GMT-8)</option>
                                        <option value="Europe/Madrid" <?= ($empresa['zona_horaria'] ?? '') === 'Europe/Madrid' ? 'selected' : '' ?>>Madrid (GMT+1)</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Formato Fecha</label>
                                    <select class="form-select" name="formato_fecha">
                                        <option value="d/m/Y" <?= ($empresa['formato_fecha'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                        <option value="m/d/Y" <?= ($empresa['formato_fecha'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                        <option value="Y-m-d" <?= ($empresa['formato_fecha'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" name="actualizar_empresa" class="btn btn-success btn-lg w-100 mt-3">
                                <i class="fas fa-save me-2"></i>Guardar configuración de empresa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
