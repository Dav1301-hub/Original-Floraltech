<?php
/**
 * Sirve el logo de la empresa como favicon (icono de la pestaña del navegador).
 * Si no hay logo configurado, no devuelve nada (404).
 */
header('Cache-Control: public, max-age=86400'); // cache 1 día

$logo_path = null;
try {
    require_once __DIR__ . '/models/conexion.php';
    $conexion = (new conexion())->get_conexion();
    $stmt = $conexion->prepare("SELECT logo FROM empresa LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['logo'])) {
        $logo_path = $row['logo'];
    }
} catch (Exception $e) {
    $logo_path = null;
}

$full_path = $logo_path ? (__DIR__ . '/' . $logo_path) : null;
if (!$full_path || !is_file($full_path)) {
    http_response_code(404);
    exit();
}

$ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
$mimes = [
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'ico'  => 'image/x-icon',
];
$content_type = $mimes[$ext] ?? 'image/png';
header('Content-Type: ' . $content_type);
readfile($full_path);
exit();
