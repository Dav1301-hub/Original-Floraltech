<?php
// Inicio del documento HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Configuración - E-Pymes Floral Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (puedes ocultarlo en móviles o hacerlo colapsable si quieres) -->
            <?php include __DIR__ . '/../partials/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Editar Configuración del Sistema</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php?ctrl=cconfig&action=index" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <!-- Formulario de edición -->
                <form action="index.php?ctrl=cconfig&action=update" method="POST">
                    <input type="hidden" name="id_cfg" value="<?= htmlspecialchars($config['id_cfg']) ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Configuración General</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="moneda" class="form-label">Moneda</label>
                                        <select class="form-select" id="moneda" name="moneda" required>
                                            <option value="COP" <?= $config['moneda'] == 'COP' ? 'selected' : '' ?>>Peso Colombiano (COP)</option>
                                            <option value="USD" <?= $config['moneda'] == 'USD' ? 'selected' : '' ?>>Dólar Estadounidense (USD)</option>
                                            <option value="EUR" <?= $config['moneda'] == 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="idioma" class="form-label">Idioma</label>
                                        <select class="form-select" id="idioma" name="idioma" required>
                                            <option value="Español" <?= $config['idioma'] == 'Español' ? 'selected' : '' ?>>Español</option>
                                            <option value="Inglés" <?= $config['idioma'] == 'Inglés' ? 'selected' : '' ?>>Inglés</option>
                                            <option value="Portugués" <?= $config['idioma'] == 'Portugués' ? 'selected' : '' ?>>Portugués</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="zona_hor" class="form-label">Zona Horaria</label>
                                        <select class="form-select" id="zona_hor" name="zona_hor" required>
                                            <option value="America/Bogota" <?= $config['zona_hor'] == 'America/Bogota' ? 'selected' : '' ?>>Colombia (Bogotá)</option>
                                            <option value="America/Mexico_City" <?= $config['zona_hor'] == 'America/Mexico_City' ? 'selected' : '' ?>>México (Ciudad de México)</option>
                                            <option value="America/New_York" <?= $config['zona_hor'] == 'America/New_York' ? 'selected' : '' ?>>EST (Nueva York)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="fmt_fecha" class="form-label">Formato de Fecha</label>
                                        <select class="form-select" id="fmt_fecha" name="fmt_fecha" required>
                                            <option value="dd/mm/yyyy" <?= $config['fmt_fecha'] == 'dd/mm/yyyy' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                            <option value="mm/dd/yyyy" <?= $config['fmt_fecha'] == 'mm/dd/yyyy' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                            <option value="yyyy-mm-dd" <?= $config['fmt_fecha'] == 'yyyy-mm-dd' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="estilo_ui" class="form-label">Estilo de Interfaz</label>
                                        <select class="form-select" id="estilo_ui" name="estilo_ui" required>
                                            <option value="Claro" <?= $config['estilo_ui'] == 'Claro' ? 'selected' : '' ?>>Claro</option>
                                            <option value="Oscuro" <?= $config['estilo_ui'] == 'Oscuro' ? 'selected' : '' ?>>Oscuro</option>
                                            <option value="Contraste" <?= $config['estilo_ui'] == 'Contraste' ? 'selected' : '' ?>>Alto Contraste</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Configuración Avanzada</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="act_auto" name="act_auto" <?= $config['act_auto'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="act_auto">Actualización automática en segundo plano</label>
                                    </div>

                                    <div class="mb-3 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notif_act" name="notif_act" <?= $config['notif_act'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="notif_act">Notificar al usuario sobre actualizaciones disponibles</label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="act_prog" class="form-label">Actualizaciones programadas</label>
                                        <input type="text" class="form-control" id="act_prog" name="act_prog" value="<?= htmlspecialchars($config['act_prog']) ?>">
                                        <div class="form-text">Ejemplo: "Diario a las 2:00 AM"</div>
                                    </div>

                                    <div class="mb-3 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="auth_2fa" name="auth_2fa" <?= $config['auth_2fa'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="auth_2fa">Autenticación en dos factores (2FA)</label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="intentos_max" class="form-label">Intentos máximos de login</label>
                                        <input type="number" class="form-control" id="intentos_max" name="intentos_max" min="1" max="10" value="<?= htmlspecialchars($config['intentos_max']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="bloqueo_min" class="form-label">Minutos de bloqueo tras intentos fallidos</label>
                                        <input type="number" class="form-control" id="bloqueo_min" name="bloqueo_min" min="1" max="1440" value="<?= htmlspecialchars($config['bloqueo_min']) ?>" required>
                                    </div>

                                    <div class="mb-3 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="log_cambios" name="log_cambios" <?= $config['log_cambios'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="log_cambios">Registrar cambios en el sistema</label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="retencion_log" class="form-label">Días de retención de logs</label>
                                        <input type="number" class="form-control" id="retencion_log" name="retencion_log" min="30" max="1095" value="<?= htmlspecialchars($config['retencion_log']) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">Restablecer</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
    
</body>
</html>