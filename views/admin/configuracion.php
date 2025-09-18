<?php
// Datos simulados del administrador y empresa
$admin = [
    'nombre_completo' => 'Jorge Luis Puentes Brochero',
    'email' => 'jorgepb2007@gmail.com',
    'usuario' => 'jorge',
    'telefono' => '3217837594',
    'direccion' => 'Carrera 5#20-65'
];
$empresa = [
    'nombre' => 'FloralTech Boutique',
    'direccion' => 'Calle Falsa 123, Ciudad',
    'telefono' => '555-1234',
    'email' => 'contacto@floraltech.com',
    'horario' => 'Lunes a Sábado 9:00-19:00'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Cuenta - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/dashboard-admin.css">
</head>
<body>
<div class="container-fluid py-4" style="background: #f7f7fb; min-height: 100vh;">
    <div class="mb-4">
        <h2 class="text-center fw-bold" style="color: #6a5af9;"><i class="fas fa-user-cog me-2"></i>Configuración de Cuenta</h2>
        <p class="text-center text-muted">Actualiza tu información personal y la de la empresa</p>
    </div>
    <form class="mx-auto" style="max-width: 1800px;">
    <div class="row g-4 justify-content-center align-items-stretch" style="width:100%;">
            <div class="col-lg-4 col-md-6 col-12 d-flex">
                <!-- Información Personal -->
                <div class="card h-100 flex-fill shadow-lg" style="min-width:350px; background: #fff; border-radius: 18px;">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-user-edit me-2"></i>Información Personal</h5>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nombre Completo</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($admin['nombre_completo']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
                            <small class="text-muted">El email no se puede modificar</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user-tag"></i> Nombre de Usuario</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($admin['usuario']) ?>" readonly>
                            <small class="text-muted">El nombre de usuario no se puede modificar</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($admin['telefono']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($admin['direccion']) ?>">
                        </div>
                        <button class="btn btn-primary w-100 mt-2"><i class="fas fa-save me-2"></i>Actualizar Información Personal</button>
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
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['nombre']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['direccion']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['telefono']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email de Contacto</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($empresa['email']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-clock"></i> Horarios de apertura</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['horario']) ?>">
                        </div>
                        <button class="btn btn-primary w-100 mt-2"><i class="fas fa-save me-2"></i>Actualizar Empresa</button>
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
                            <input type="password" class="form-control" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-key"></i> Confirmar Contraseña</label>
                            <input type="password" class="form-control" placeholder="Repetir nueva contraseña">
                        </div>
                        <button class="btn btn-primary w-100 mt-2"><i class="fas fa-save me-2"></i>Actualizar Contraseña</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</body>
</html>
