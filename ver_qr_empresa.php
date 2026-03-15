<?php
/**
 * Sirve la imagen del QR Nequi de la empresa desde la base de datos (o archivo legacy).
 * Requiere sesión activa (cliente, empleado o admin).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Acceso no autorizado');
}

require_once __DIR__ . '/models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$row = null;
try {
    $stmt = @$db->query("SELECT nequi_qr_imagen, nequi_qr_tipo, nequi_qr FROM empresa LIMIT 1");
    if ($stmt) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Columnas nequi_qr_imagen pueden no existir aún
}
if (!$row) {
    try {
        $stmt = $db->query("SELECT nequi_qr FROM empresa LIMIT 1");
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {}
}

$defaultPath = __DIR__ . '/assets/images/qr/qr_transferencia.png';

if (!$row) {
    if (file_exists($defaultPath)) {
        header('Content-Type: image/png');
        header('Cache-Control: private, max-age=3600');
        readfile($defaultPath);
        exit;
    }
    header('HTTP/1.0 404 Not Found');
    exit('QR no configurado');
}

// 1) Imagen en BD
if (!empty($row['nequi_qr_imagen'])) {
    $tipo = !empty($row['nequi_qr_tipo']) ? $row['nequi_qr_tipo'] : 'image/png';
    header('Content-Type: ' . $tipo);
    header('Cache-Control: private, max-age=3600');
    echo $row['nequi_qr_imagen'];
    exit;
}

// 2) Legacy: archivo en uploads/nequi
$ruta = !empty(trim($row['nequi_qr'] ?? '')) ? trim($row['nequi_qr']) : null;
if ($ruta) {
    $path = __DIR__ . '/' . $ruta;
    if (file_exists($path) && is_readable($path)) {
        $tipo = @mime_content_type($path) ?: 'image/png';
        header('Content-Type: ' . $tipo);
        header('Cache-Control: private, max-age=3600');
        readfile($path);
        exit;
    }
}

// 3) Imagen por defecto
if (file_exists($defaultPath)) {
    header('Content-Type: image/png');
    header('Cache-Control: private, max-age=3600');
    readfile($defaultPath);
    exit;
}

header('HTTP/1.0 404 Not Found');
exit('QR no configurado');
