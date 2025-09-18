<?php
// Verificar que el usuario esté logueado y sea administrador
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 1) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

// Incluir archivos de configuración
require_once '../../models/conexion.php';
require_once '../../controllers/cconfig.php';

$usuario = $_SESSION['user'];
$mensaje = '';
$tipo_mensaje = '';

// Conectar a la base de datos
$conn = new conexion();
$db = $conn->get_conexion();

// Procesar actualización de configuración general (adaptar según tu modelo)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre_empresa = $_POST['nombre_empresa'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $email_contacto = $_POST['email_contacto'] ?? '';
        // Validaciones
        if (empty($nombre_empresa)) {
            throw new Exception('El nombre de la empresa es requerido');
        }
        // Actualizar datos de configuración general (adaptar a tu modelo y tabla)
        $stmt = $db->prepare("UPDATE config SET nombre_empresa = ?, telefono = ?, direccion = ?, email_contacto = ? WHERE idconfig = 1");
        $stmt->execute([$nombre_empresa, $telefono, $direccion, $email_contacto]);
        $mensaje = 'Configuración actualizada correctamente';
        $tipo_mensaje = 'success';
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}
// Obtener configuración actual
$stmt = $db->prepare("SELECT * FROM config WHERE idconfig = 1");
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración General - Administrador</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <div class="container">
        <h2>Configuración General del Sistema</h2>
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>"> <?= htmlspecialchars($mensaje) ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_empresa" class="form-label">Nombre de la empresa</label>
                <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre_empresa'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($config['telefono'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($config['direccion'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="email_contacto" class="form-label">Email de contacto</label>
                <input type="email" class="form-control" id="email_contacto" name="email_contacto" value="<?= htmlspecialchars($config['email_contacto'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
