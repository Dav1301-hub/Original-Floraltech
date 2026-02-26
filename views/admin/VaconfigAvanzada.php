<?php
/**
 * Vista de Configuración Avanzada del Sistema (Product Owner)
 * Configuración general de la empresa
 */

require_once __DIR__ . '/../../models/conexion.php';
$mensaje_exito = '';
$mensaje_error = '';

$conexion = (new conexion())->get_conexion();

// Cargar configuración de la empresa
try {
    $stmt_empresa = $conexion->prepare("SELECT * FROM empresa LIMIT 1");
    $stmt_empresa->execute();
    $config = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
    
    if (!$config) {
        // Si no existe configuración, usar valores por defecto
        $config = [
            'nombre' => 'FloralTech',
            'direccion' => '',
            'telefono' => '',
            'email' => '',
            'horario' => ''
        ];
    }
} catch (Exception $e) {
    $mensaje_error = 'Error al cargar configuración: ' . $e->getMessage();
    $config = [
        'nombre' => '',
        'direccion' => '',
        'telefono' => '',
        'email' => '',
        'horario' => ''
    ];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_empresa'])) {
    try {
        $nombre = trim($_POST['nombre_empresa'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email_contacto'] ?? '');
        $horario = trim($_POST['horario'] ?? '');
        
        if (empty($nombre)) {
            throw new Exception('El nombre de la empresa es requerido');
        }
        
        // Verificar si existe registro
        $stmt_check = $conexion->query("SELECT COUNT(*) FROM empresa");
        $existe = $stmt_check->fetchColumn() > 0;
        
        if ($existe) {
            // Actualizar
            $stmt_update = $conexion->prepare("UPDATE empresa SET nombre=:n, direccion=:d, telefono=:t, email=:e, horario=:h WHERE id=(SELECT MIN(id) FROM empresa)");
            $stmt_update->execute([
                ':n' => $nombre,
                ':d' => $direccion,
                ':t' => $telefono,
                ':e' => $email,
                ':h' => $horario
            ]);
        } else {
            // Insertar
            $stmt_insert = $conexion->prepare("INSERT INTO empresa (nombre, direccion, telefono, email, horario) VALUES (:n, :d, :t, :e, :h)");
            $stmt_insert->execute([
                ':n' => $nombre,
                ':d' => $direccion,
                ':t' => $telefono,
                ':e' => $email,
                ':h' => $horario
            ]);
        }
        
        $mensaje_exito = 'Configuración de la empresa actualizada correctamente';
        
        // Recargar configuración
        $stmt_empresa = $conexion->prepare("SELECT * FROM empresa LIMIT 1");
        $stmt_empresa->execute();
        $config = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $mensaje_error = $e->getMessage();
    }
}
?>

<div class="container-fluid py-4" style="background:#fff; min-height: 100vh;">
    
    <div class="p-4 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(120deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%); color: #fff;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <p class="mb-1 text-white-50 small" style="letter-spacing:1px;text-transform:uppercase;">
                    <i class="fas fa-building me-2"></i>Configuración de la empresa
                </p>
                <h2 class="fw-bold mb-0" style="color: #fff">Configuración Avanzada</h2>
            </div>
            <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25 fs-6">
                <i class="fas fa-crown me-2"></i>Product Owner
            </span>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mx-3" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mx-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Contenido -->
    <div class="px-3">
        <form method="POST">
            <div class="row g-4">
                <!-- Información de la Empresa -->
                <div class="col-12 col-lg-6 d-flex">
                    <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h5 class="mb-0">Información de la Empresa</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Nombre de la empresa</label>
                                <input type="text" class="form-control form-control-lg" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre'] ?? '') ?>" required>
                                <small class="text-muted">Nombre comercial o razón social</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Dirección</label>
                                <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($config['direccion'] ?? '') ?>">
                                <small class="text-muted">Dirección física de la empresa</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($config['telefono'] ?? '') ?>">
                                <small class="text-muted">Número de contacto principal</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="col-12 col-lg-6 d-flex">
                    <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h5 class="mb-0">Datos de Contacto</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Email de contacto</label>
                                <input type="email" class="form-control form-control-lg" name="email_contacto" value="<?= htmlspecialchars($config['email'] ?? '') ?>">
                                <small class="text-muted">Correo electrónico principal de la empresa</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Horario de atención</label>
                                <textarea class="form-control" name="horario" rows="4" placeholder="Ej: Lunes a Viernes 8:00 AM - 6:00 PM"><?= htmlspecialchars($config['horario'] ?? '') ?></textarea>
                                <small class="text-muted">Horario de atención al público</small>
                            </div>

                            <button type="submit" name="actualizar_empresa" class="btn btn-primary btn-lg w-100 mt-3">
                                <i class="fas fa-save me-2"></i>Guardar configuración
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Información adicional -->
        <div class="row g-4 mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5 class="mb-0">Información Importante</h5>
                        </div>
                        <div class="alert alert-light border-start border-warning border-3 mb-0">
                            <p class="mb-2"><strong><i class="fas fa-shield-alt me-2"></i>Nivel de acceso:</strong></p>
                            <p class="mb-0 small text-muted">Esta configuración solo está disponible para usuarios con rol de Product Owner o Administrador. Los cambios afectan a toda la organización.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar alertas después de 5 segundos
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

