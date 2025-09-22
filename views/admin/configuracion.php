
<?php
// Este archivo es incluido por dashboard.php, no es una página independiente

// Variables para mensajes
$mensaje_exito = '';
$mensaje_error = '';

// Obtener ID del usuario actual de la sesión
$id_admin = $_SESSION['user_id'] ?? null;

if (!$id_admin) {
    // Fallback: buscar en la estructura anidada $_SESSION['user']
    if (isset($_SESSION['user']) && isset($_SESSION['user']['idusu'])) {
        $id_admin = $_SESSION['user']['idusu'];
    } else {
        $id_admin = 43; // Forzar el ID correcto de maria como último recurso
    }
}

// Incluir la clase de conexión PDO
require_once($_SERVER['DOCUMENT_ROOT'] . '/Original-Floraltech/views/config/database.php');

try {
    $db = new Database();
    $conexion = $db->getConnection();
    
    if (!$conexion) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    // Verificación rápida de conexión
    $test_query = "SELECT 1 as test";
    $test_stmt = $conexion->prepare($test_query);
    $test_stmt->execute();
    $test_result = $test_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$test_result) {
        throw new Exception('La conexión a la base de datos no responde correctamente');
    }
    
} catch (Exception $e) {
    $mensaje_error = 'Error de conexión: ' . $e->getMessage();
    die($mensaje_error);
}

// Obtener datos del administrador
try {
    $sql_admin = "SELECT idusu, nombre_completo, email, username, telefono, direccion FROM usu WHERE idusu = :id_admin LIMIT 1";
    $stmt_admin = $conexion->prepare($sql_admin);
    $stmt_admin->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
    $stmt_admin->execute();
    $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Usuario encontrado correctamente
    } else {
        // Intentar obtener el primer usuario administrador disponible
        $sql_fallback = "SELECT idusu, nombre_completo, email, username, telefono, direccion FROM usu WHERE tpusu_idtpusu = 1 LIMIT 1";
        $stmt_fallback = $conexion->prepare($sql_fallback);
        $stmt_fallback->execute();
        $admin = $stmt_fallback->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            $id_admin = $admin['idusu'];
            $mensaje_error = 'Advertencia: Usuario de sesión no encontrado. Mostrando datos del primer administrador disponible.';
        } else {
            $admin = [
                'nombre_completo' => '',
                'email' => '',
                'username' => '',
                'telefono' => '',
                'direccion' => ''
            ];
            $mensaje_error = 'Error: No se encontraron usuarios administradores en el sistema.';
        }
    }
    
} catch (PDOException $e) {
    $mensaje_error = 'Error al obtener datos del usuario: ' . $e->getMessage();
    $admin = [
        'nombre_completo' => '',
        'email' => '',
        'username' => '',
        'telefono' => '',
        'direccion' => ''
    ];
}

// Obtener datos de la empresa
try {
    $sql_empresa = "SELECT nombre, direccion, telefono, email, horario FROM empresa LIMIT 1";
    $stmt_empresa = $conexion->prepare($sql_empresa);
    $stmt_empresa->execute();
    $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC) ?: [
        'nombre' => '',
        'direccion' => '',
        'telefono' => '',
        'email' => '',
        'horario' => ''
    ];
} catch (PDOException $e) {
    $mensaje_error = 'Error al obtener datos de la empresa: ' . $e->getMessage();
    $empresa = [
        'nombre' => '',
        'direccion' => '',
        'telefono' => '',
        'email' => '',
        'horario' => ''
    ];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Actualizar información personal
        if (isset($_POST['actualizar_personal'])) {
            // Validar datos
            $nombre = trim($_POST['nombre_completo']);
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            
            if (empty($nombre)) {
                throw new Exception('El nombre completo es requerido');
            }
            
            $sql_update = "UPDATE usu SET nombre_completo=:nombre, telefono=:telefono, direccion=:direccion WHERE idusu=:id_admin";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindParam(':nombre', $nombre);
            $stmt_update->bindParam(':telefono', $telefono);
            $stmt_update->bindParam(':direccion', $direccion);
            $stmt_update->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                $mensaje_exito = 'Información de administrador actualizada correctamente';
            } else {
                throw new Exception('Error al actualizar la información de administrador');
            }
        }
        
        // Actualizar empresa
        if (isset($_POST['actualizar_empresa'])) {
            // Validar datos
            $nombre = trim($_POST['nombre']);
            $direccion = trim($_POST['direccion_empresa']);
            $telefono = trim($_POST['telefono_empresa']);
            $email = trim($_POST['email_empresa']);
            $horario = trim($_POST['horario']);
            
            if (empty($nombre)) {
                throw new Exception('El nombre de la empresa es requerido');
            }
            
            // Verificar si existe un registro de empresa
            $sql_check = "SELECT COUNT(*) FROM empresa";
            $stmt_check = $conexion->prepare($sql_check);
            $stmt_check->execute();
            $count = $stmt_check->fetchColumn();
            
            if ($count > 0) {
                // Actualizar registro existente
                $sql_update = "UPDATE empresa SET nombre=:nombre, direccion=:direccion, telefono=:telefono, email=:email, horario=:horario";
                $stmt_update = $conexion->prepare($sql_update);
            } else {
                // Insertar nuevo registro
                $sql_update = "INSERT INTO empresa (nombre, direccion, telefono, email, horario) VALUES (:nombre, :direccion, :telefono, :email, :horario)";
                $stmt_update = $conexion->prepare($sql_update);
            }
            
            $stmt_update->bindParam(':nombre', $nombre);
            $stmt_update->bindParam(':direccion', $direccion);
            $stmt_update->bindParam(':telefono', $telefono);
            $stmt_update->bindParam(':email', $email);
            $stmt_update->bindParam(':horario', $horario);
            
            if ($stmt_update->execute()) {
                $mensaje_exito = 'Información de la empresa actualizada correctamente';
            } else {
                throw new Exception('Error al actualizar la información de la empresa');
            }
        }
        
        // Cambiar contraseña
        if (isset($_POST['actualizar_contrasena'])) {
            $pass1 = $_POST['nueva_contrasena'];
            $pass2 = $_POST['confirmar_contrasena'];
            
            if (empty($pass1) || empty($pass2)) {
                throw new Exception('Ambos campos de contraseña son requeridos');
            }
            
            if ($pass1 !== $pass2) {
                throw new Exception('Las contraseñas no coinciden');
            }
            
            if (strlen($pass1) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            $hash = hash('sha256', $pass1);
            $sql_update = "UPDATE usu SET clave=:hash WHERE idusu=:id_admin";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindParam(':hash', $hash);
            $stmt_update->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                $mensaje_exito = 'Contraseña actualizada correctamente';
            } else {
                throw new Exception('Error al actualizar la contraseña');
            }
        }
        
        // Si llegamos aquí y hay mensaje de éxito, no hacer redirección
        // Los datos se recargarán automáticamente en las siguientes consultas
        
    } catch (Exception $e) {
        $mensaje_error = $e->getMessage();
    }
    
    // Recargar datos después de cualquier actualización exitosa
    if (!empty($mensaje_exito)) {
        // Recargar datos del administrador
        try {
            $sql_admin = "SELECT nombre_completo, email, username, telefono, direccion FROM usu WHERE idusu = :id_admin LIMIT 1";
            $stmt_admin = $conexion->prepare($sql_admin);
            $stmt_admin->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
            $stmt_admin->execute();
            $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC) ?: [
                'nombre_completo' => '',
                'email' => '',
                'username' => '',
                'telefono' => '',
                'direccion' => ''
            ];
        } catch (PDOException $e) {
            // No cambiar el mensaje de éxito si hay error al recargar
        }
        
        // Recargar datos de la empresa
        try {
            $sql_empresa = "SELECT nombre, direccion, telefono, email, horario FROM empresa LIMIT 1";
            $stmt_empresa = $conexion->prepare($sql_empresa);
            $stmt_empresa->execute();
            $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC) ?: [
                'nombre' => '',
                'direccion' => '',
                'telefono' => '',
                'email' => '',
                'horario' => ''
            ];
        } catch (PDOException $e) {
            // No cambiar el mensaje de éxito si hay error al recargar
        }
    }
}
?>

<!-- CONTENIDO DE CONFIGURACIÓN -->
<div class="container-fluid py-4" style="background: #f7f7fb; min-height: 100vh;"
>>
    <!-- Mensajes de éxito y error -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="mb-4">
        <h2 class="text-center fw-bold" style="color: #6a5af9;"><i class="fas fa-user-cog me-2"></i>Configuración de Cuenta</h2>
        <p class="text-center text-muted">Actualiza tu información personal y la de la empresa</p>
    </div>
    <form class="mx-auto" style="max-width: 1800px;" method="POST">
    <div class="row g-4 justify-content-center align-items-stretch" style="width:100%;">
            <div class="col-lg-4 col-md-6 col-12 d-flex">
                <!-- Información Personal -->
                <div class="card h-100 flex-fill shadow-lg" style="min-width:350px; background: #fff; border-radius: 18px;">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-user-edit me-2"></i>Información Personal</h5>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nombre Completo</label>
                            <input type="text" class="form-control" name="nombre_completo" value="<?= htmlspecialchars($admin['nombre_completo']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
                            <small class="text-muted">El email no se puede modificar</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user-tag"></i> Nombre de Usuario</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" readonly>
                            <small class="text-muted">El nombre de usuario no se puede modificar</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($admin['telefono']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                            <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($admin['direccion']) ?>">
                        </div>
                        <button class="btn btn-primary w-100 mt-2" name="actualizar_personal"><i class="fas fa-save me-2"></i>Actualizar Información Personal</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 d-flex">
                <!-- Información de la Empresa -->
                <div class="card h-100 flex-fill shadow-lg" style="min-width:350px; background: #fff; border-radius: 18px;">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-building me-2"></i>Información de la Empresa</h5>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-store"></i> Nombre de la Empresa</label>
                            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($empresa['nombre']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                            <input type="text" class="form-control" name="direccion_empresa" value="<?= htmlspecialchars($empresa['direccion']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="text" class="form-control" name="telefono_empresa" value="<?= htmlspecialchars($empresa['telefono']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email de Contacto</label>
                            <input type="email" class="form-control" name="email_empresa" value="<?= htmlspecialchars($empresa['email']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-clock"></i> Horarios de apertura</label>
                            <input type="text" class="form-control" name="horario" value="<?= htmlspecialchars($empresa['horario']) ?>">
                        </div>
                        <button class="btn btn-primary w-100 mt-2" name="actualizar_empresa"><i class="fas fa-save me-2"></i>Actualizar Empresa</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 d-flex">
                <!-- Cambiar Contraseña -->
                <div class="card h-100 flex-fill shadow-lg" style="min-width:350px; background: #fff; border-radius: 18px;">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-lock"></i> Cambiar Contraseña <span class="text-muted" style="font-size: 0.9em;">(Opcional - Dejar en blanco para mantener la actual)</span></h5>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-key"></i> Nueva Contraseña</label>
                            <input type="password" class="form-control" name="nueva_contrasena" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-key"></i> Confirmar Contraseña</label>
                            <input type="password" class="form-control" name="confirmar_contrasena" placeholder="Repetir nueva contraseña">
                        </div>
                        <button class="btn btn-primary w-100 mt-2" name="actualizar_contrasena"><i class="fas fa-save me-2"></i>Actualizar Contraseña</button>
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
    // Validación de contraseñas
    const passwordForm = document.querySelector('button[name="actualizar_contrasena"]').closest('form');
    const nuevaContrasena = document.querySelector('input[name="nueva_contrasena"]');
    const confirmarContrasena = document.querySelector('input[name="confirmar_contrasena"]');
    
    // Validar contraseñas en tiempo real
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
    
    // Validar antes de enviar formulario de contraseña
    document.querySelector('button[name="actualizar_contrasena"]').addEventListener('click', function(e) {
        if (nuevaContrasena.value || confirmarContrasena.value) {
            if (!nuevaContrasena.value || !confirmarContrasena.value) {
                e.preventDefault();
                alert('Debe completar ambos campos de contraseña');
                return false;
            }
            if (nuevaContrasena.value !== confirmarContrasena.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            if (nuevaContrasena.value.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
        }
    });
    
    // Validar campos requeridos para información personal
    document.querySelector('button[name="actualizar_personal"]').addEventListener('click', function(e) {
        const nombreCompleto = document.querySelector('input[name="nombre_completo"]').value.trim();
        if (!nombreCompleto) {
            e.preventDefault();
            alert('El nombre completo es requerido');
            return false;
        }
    });
    
    // Validar campos requeridos para empresa
    document.querySelector('button[name="actualizar_empresa"]').addEventListener('click', function(e) {
        const nombreEmpresa = document.querySelector('input[name="nombre"]').value.trim();
        if (!nombreEmpresa) {
            e.preventDefault();
            alert('El nombre de la empresa es requerido');
            return false;
        }
    });
    
    // Auto-dismiss solo para alertas de mensajes (éxito/error) después de 5 segundos
    setTimeout(function() {
        const messageAlerts = document.querySelectorAll('.alert-success, .alert-danger');
        messageAlerts.forEach(function(alert) {
            if (alert.classList.contains('alert-dismissible')) {
                const alertInstance = new bootstrap.Alert(alert);
                alertInstance.close();
            }
        });
    }, 5000);
});
</script>