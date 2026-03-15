<?php
/**
 * Sirve la imagen del comprobante de pago desde la base de datos (o archivo legacy).
 * Accesible por cliente (solo sus pedidos) y empleado/admin (cualquier pago).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Acceso no autorizado');
}

$idpago = isset($_GET['idpago']) ? (int)$_GET['idpago'] : 0;
if ($idpago <= 0) {
    header('HTTP/1.0 400 Bad Request');
    exit('ID de pago inválido');
}

require_once __DIR__ . '/models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$rol = (int)($_SESSION['user']['tpusu_idtpusu'] ?? 0);

try {
    $stmt = $db->prepare("
        SELECT pg.idpago, pg.comprobante_imagen, pg.comprobante_tipo, pg.comprobante_transferencia, p.cli_idcli
        FROM pagos pg
        INNER JOIN ped p ON pg.ped_idped = p.idped
        WHERE pg.idpago = ?
    ");
    $stmt->execute([$idpago]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header('HTTP/1.0 404 Not Found');
        exit('Pago no encontrado');
    }

    // Cliente (rol 5) solo puede ver comprobantes de sus propios pedidos
    if ($rol === 5) {
        $stmtCli = $db->prepare("SELECT idcli FROM cli WHERE email = ? LIMIT 1");
        $stmtCli->execute([$_SESSION['user']['email'] ?? '']);
        $cli = $stmtCli->fetch(PDO::FETCH_ASSOC);
        $cliente_id = (int)($cli['idcli'] ?? 0);
        if ((int)$row['cli_idcli'] !== $cliente_id) {
            header('HTTP/1.0 403 Forbidden');
            exit('No tiene permiso para ver este comprobante');
        }
    } elseif (!in_array($rol, [1, 2, 3, 4], true)) {
        header('HTTP/1.0 403 Forbidden');
        exit('Acceso no autorizado');
    }

    // 1) Imagen guardada en BD
    if (!empty($row['comprobante_imagen'])) {
        $tipo = !empty($row['comprobante_tipo']) ? $row['comprobante_tipo'] : 'image/jpeg';
        header('Content-Type: ' . $tipo);
        header('Cache-Control: private, max-age=3600');
        echo $row['comprobante_imagen'];
        exit;
    }

    // 2) Legacy: archivo en carpeta assets/comprobantes
    $ruta = !empty(trim($row['comprobante_transferencia'] ?? '')) ? trim($row['comprobante_transferencia']) : null;
    if ($ruta) {
        $path = __DIR__ . '/assets/comprobantes/' . $ruta;
        if (file_exists($path) && is_readable($path)) {
            $tipo = mime_content_type($path) ?: 'image/jpeg';
            header('Content-Type: ' . $tipo);
            header('Cache-Control: private, max-age=3600');
            readfile($path);
            exit;
        }
    }

    header('HTTP/1.0 404 Not Found');
    exit('No hay comprobante para este pago');
} catch (Exception $e) {
    error_log('ver_comprobante: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    exit('Error al cargar el comprobante');
}
