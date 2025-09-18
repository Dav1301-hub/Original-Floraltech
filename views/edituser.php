<?php
session_start();
require_once 'config/db.php';

// Verificar sesión activa
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();

// Obtener ID del usuario por GET
$idUsuario = $_GET['id'] ?? null;
if (!$idUsuario) {
    header("Location: manageusers.php");
    exit();
}

$mensaje = "";
$exito = false;

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $idTipoUsuario = $_POST['rol'] ?? '';

    if ($nombre && $apellido && $idTipoUsuario) {
        $stmt = $db->prepare("UPDATE usuarios SET Nombre = :nombre, Apellido = :apellido, idTipoUsuario = :rol WHERE idUsuario = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':rol', $idTipoUsuario);
        $stmt->bindParam(':id', $idUsuario);
        $stmt->execute();

        $exito = true;
        $mensaje = "Usuario actualizado correctamente.";
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// Obtener datos actuales del usuario
$stmt = $db->prepare("SELECT * FROM usuarios WHERE idUsuario = :id");
$stmt->bindParam(':id', $idUsuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: manageusers.php");
    exit();
}

// Obtener lista de roles
$roles = $db->query("SELECT * FROM tipousuario")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f8f9fa;">
<div class="container py-5">
    <h2 class="fw-bold mb-4">Editar Usuario</h2>

    <?php if ($mensaje): ?>
        <div class="alert <?= $exito ? 'alert-success' : 'alert-danger' ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['Nombre']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['Apellido']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="">Selecciona un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['idTipoUsuario'] ?>" <?= $usuario['idTipoUsuario'] == $rol['idTipoUsuario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($rol['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="manageusers.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
</body>
</html>
