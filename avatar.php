<?php
/**
 * Sirve la imagen de perfil desde la base de datos.
 * Uso: avatar.php?tipo=cli&id=20  o  avatar.php?tipo=usu&id=70
 */
session_start();
require_once __DIR__ . '/models/conexion.php';

$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!in_array($tipo, ['cli', 'usu'], true) || $id <= 0) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

try {
    $conn = new conexion();
    $db = $conn->get_conexion();
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    exit;
}

if ($tipo === 'cli') {
    $stmt = $db->prepare("SELECT avatar_data, avatar_tipo, avatar FROM cli WHERE idcli = ? LIMIT 1");
} else {
    $stmt = $db->prepare("SELECT avatar_data, avatar_tipo, avatar FROM usu WHERE idusu = ? LIMIT 1");
}
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Preferir imagen en DB
if (!empty($row['avatar_data']) && !empty($row['avatar_tipo'])) {
    header('Content-Type: ' . $row['avatar_tipo']);
    header('Cache-Control: private, max-age=3600');
    echo $row['avatar_data'];
    exit;
}

// Compatibilidad: avatar como ruta de archivo (antes se guardaba en disco)
if (!empty($row['avatar']) && is_string($row['avatar'])) {
    $path = __DIR__ . '/' . $row['avatar'];
    if (file_exists($path) && is_file($path)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);
        if ($mime && strpos($mime, 'image/') === 0) {
            header('Content-Type: ' . $mime);
            header('Cache-Control: private, max-age=3600');
            readfile($path);
            exit;
        }
    }
}

header('HTTP/1.0 404 Not Found');
exit;
