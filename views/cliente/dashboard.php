<?php
// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 5) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

// Obtener datos del usuario actual
$usuario = $_SESSION['user'];

// Conectar a la base de datos
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/MDashboardGeneral.php';
$conn = new conexion();
$db = $conn->get_conexion();

$modeloGeneral = new MDashboardGeneral($db);

// Nequi (QR y número) desde empresa
$nequi_qr_url = '';
$nequi_numero = '';
try {
    $emp = null;
    $st = @$db->query("SELECT nequi_qr, nequi_numero, (nequi_qr_imagen IS NOT NULL) as nequi_qr_en_bd FROM empresa LIMIT 1");
    if ($st) {
        $emp = $st->fetch(PDO::FETCH_ASSOC);
    }
    if (!$emp) {
        $st = $db->query("SELECT nequi_qr, nequi_numero FROM empresa LIMIT 1");
        if ($st) {
            $emp = $st->fetch(PDO::FETCH_ASSOC);
        }
    }
    if ($emp) {
        if (!empty($emp['nequi_qr_en_bd'])) {
            $nequi_qr_url = 'ver_qr_empresa.php';
        } elseif (!empty($emp['nequi_qr']) && file_exists(__DIR__ . '/../../' . $emp['nequi_qr'])) {
            $nequi_qr_url = $emp['nequi_qr'];
        }
        if (!empty(trim($emp['nequi_numero'] ?? ''))) {
            $nequi_numero = trim($emp['nequi_numero']);
        }
    }
} catch (Exception $e) {
    try {
        $st = $db->query("SELECT nequi_qr, nequi_numero FROM empresa LIMIT 1");
        if ($st && ($emp = $st->fetch(PDO::FETCH_ASSOC))) {
            if (!empty($emp['nequi_qr']) && file_exists(__DIR__ . '/../../' . $emp['nequi_qr'])) {
                $nequi_qr_url = $emp['nequi_qr'];
            }
            if (!empty(trim($emp['nequi_numero'] ?? ''))) {
                $nequi_numero = trim($emp['nequi_numero']);
            }
        }
    } catch (Exception $e2) {}
}
if ($nequi_qr_url === '' && file_exists(__DIR__ . '/../../assets/images/qr/qr_transferencia.png')) {
    $nequi_qr_url = 'assets/images/qr/qr_transferencia.png';
}
$nequi_qr_base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
if ($nequi_qr_base !== '' && isset($nequi_qr_url) && strpos($nequi_qr_url, 'ver_qr_empresa.php') !== false) {
    $nequi_qr_url = $nequi_qr_base . '/ver_qr_empresa.php';
}

// Buscar el cliente asociado al usuario actual por email
try {
    $stmt = $db->prepare("SELECT idcli FROM cli WHERE email = ?");
    $stmt->execute([$usuario['email']]);
    $cliente_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente_data) {
        // Si no existe el cliente, crearlo automáticamente
        $stmt = $db->prepare("INSERT INTO cli (nombre, direccion, telefono, email, fecha_registro) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt->execute([
            $usuario['nombre_completo'],
            $usuario['direccion'] ?? 'Sin dirección', // Cambiado de 'naturaleza' a 'direccion'
            $usuario['telefono'] ?? 'Sin teléfono',
            $usuario['email']
        ]);
        $cliente_id = $db->lastInsertId();
    } else {
        $cliente_id = $cliente_data['idcli'];
    }


    // Obtener estadísticas del cliente
    $params_stats = [$cliente_id];

    // Obtener estadísticas del cliente con una sola consulta optimizada
$stmt = $db->prepare("
    SELECT 
        COUNT(DISTINCT p.idped) as total_pedidos,
        COALESCE(SUM(
            CASE 
                WHEN LOWER(pg.estado_pag) = 'completado'
                AND p.estado != 'Cancelado' 
                THEN p.monto_total 
                ELSE 0 
            END
        ), 0) as total_gastado,
        COALESCE(SUM(
            CASE 
                WHEN (LOWER(pg.estado_pag) = 'sin pago' OR pg.estado_pag IS NULL) 
                AND p.estado != 'Cancelado' 
                THEN p.monto_total 
                ELSE 0 
            END
        ), 0) as total_pendiente_pago,
        SUM(CASE WHEN p.estado = 'Pendiente' AND p.estado != 'Cancelado' THEN 1 ELSE 0 END) as pedidos_pendientes,
        SUM(CASE WHEN p.estado = 'Completado' THEN 1 ELSE 0 END) as pedidos_completados,
        SUM(CASE WHEN (LOWER(pg.estado_pag) = 'pendiente' AND p.estado != 'Cancelado') THEN 1 ELSE 0 END) as pagos_pendientes,
        SUM(CASE WHEN LOWER(pg.estado_pag) = 'completado' THEN 1 ELSE 0 END) as pagos_completados,
        SUM(CASE WHEN (LOWER(pg.estado_pag) = 'sin pago' OR pg.estado_pag IS NULL) THEN 1 ELSE 0 END) as pagos_sin_pago,
        COALESCE(AVG(
            CASE 
                WHEN LOWER(pg.estado_pag) = 'completado'
                AND p.estado != 'Cancelado' 
                THEN p.monto_total 
                ELSE NULL 
            END
        ), 0) as promedio_pedido,
        MAX(p.fecha_pedido) as ultimo_pedido
    FROM ped p 
    LEFT JOIN pagos pg ON p.idped = pg.ped_idped 
    WHERE p.cli_idcli = ? 
");
    $stmt->execute([$cliente_id]);
    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
        
    // Extraer estadísticas
    $total_pedidos = $estadisticas['total_pedidos'] ?? 0;
    $pedidos_pendientes = $estadisticas['pedidos_pendientes'] ?? 0;
    $pedidos_completados = $estadisticas['pedidos_completados'] ?? 0;
    $pagos_pendientes = $estadisticas['pagos_pendientes'] ?? 0;
    $pagos_completados = $estadisticas['pagos_completados'] ?? 0;
    $promedio_pedido = $estadisticas['promedio_pedido'] ?? 0;
    $ultimo_pedido = $estadisticas['ultimo_pedido'];
    
    // Calcular tendencias y métricas adicionales
    $porcentaje_completados = $total_pedidos > 0 ? round(($pedidos_completados / $total_pedidos) * 100, 1) : 0;
    $dias_ultimo_pedido = $ultimo_pedido ? floor((time() - strtotime($ultimo_pedido)) / (60 * 60 * 24)) : null;

    $stmt = $db->prepare("
        SELECT 
            p.*,
            DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y %H:%i') as fecha_formato,
            DATE_FORMAT(p.fecha_pedido, '%d %b') as fecha_corta,
            pg.estado_pag as estado_pago,
            pg.metodo_pago,
            COUNT(dp.iddetped) as cantidad_items,
            GROUP_CONCAT(DISTINCT tf.nombre SEPARATOR ', ') as flores_nombres
        FROM ped p 
        LEFT JOIN pagos pg ON p.idped = pg.ped_idped 
        LEFT JOIN detped dp ON p.idped = dp.idped
        LEFT JOIN tflor tf ON dp.idtflor = tf.idtflor
        WHERE p.cli_idcli = ? 
        GROUP BY p.idped
        ORDER BY p.fecha_pedido DESC 
    ");
    $stmt->execute([$cliente_id]);
    $pedidos_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Actividad reciente mejorada con más detalles
    $stmt = $db->prepare("
        SELECT 
            'pago' as tipo, 
            pg.fecha_pago as fecha, 
            CONCAT('Pago de $', FORMAT(pg.monto, 2), ' - ', pg.metodo_pago) as descripcion, 
            pg.estado_pag as estado,
            p.numped as referencia
        FROM pagos pg 
        INNER JOIN ped p ON pg.ped_idped = p.idped 
        WHERE p.cli_idcli = ?
        
        UNION ALL
        
        SELECT 
            'pedido' as tipo, 
            p.fecha_pedido as fecha, 
            CONCAT('Pedido #', p.numped, ' - $', FORMAT(p.monto_total, 2)) as descripcion, 
            p.estado as estado,
            p.numped as referencia
        FROM ped p 
        WHERE p.cli_idcli = ?
        
        UNION ALL
        
        SELECT 
            'entrega' as tipo,
            e.fecha_ent as fecha,
            CONCAT('Entrega pedido #', p.numped, ' - ', e.direccion) as descripcion,
            e.estado_ent as estado,
            p.numped as referencia
        FROM ent e
        INNER JOIN ped p ON e.ped_idped = p.idped
        WHERE p.cli_idcli = ?
        
        ORDER BY fecha DESC 
        LIMIT 10
    ");
    $stmt->execute([$cliente_id, $cliente_id, $cliente_id]);
    $actividad_reciente = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener flores más compradas por el cliente
    $stmt = $db->prepare("
        SELECT 
            tf.nombre,
            tf.color,
            SUM(dp.cantidad) as total_comprado,
            COUNT(DISTINCT dp.idped) as veces_comprado,
            AVG(dp.precio_unitario) as precio_promedio
        FROM detped dp
        INNER JOIN ped p ON dp.idped = p.idped
        INNER JOIN tflor tf ON dp.idtflor = tf.idtflor
        WHERE p.cli_idcli = ?
        GROUP BY tf.idtflor
        ORDER BY total_comprado DESC
        LIMIT 3
    ");
    $stmt->execute([$cliente_id]);
    $flores_favoritas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener datos del cliente para personalización
    $stmt = $db->prepare("
        SELECT 
            c.*,
            DATEDIFF(CURDATE(), c.fecha_registro) as dias_cliente,
            u.username,
            u.fecha_registro as fecha_registro_usuario
        FROM cli c 
        INNER JOIN usu u ON c.email = u.email
        WHERE c.idcli = ?
    ");
    $stmt->execute([$cliente_id]);
    $datos_cliente = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En caso de error, usar valores por defecto
    $total_pedidos = 0;
    $pedidos_pendientes = 0;
    $pedidos_completados = 0;
    $pagos_pendientes = 0;
    $pagos_completados = 0;
    $promedio_pedido = 0;
    $porcentaje_completados = 0;
    $dias_ultimo_pedido = null;
    $pedidos_recientes = [];
    $actividad_reciente = [];
    $flores_favoritas = [];
    $datos_cliente = null;
    $cliente_id = 0;
    
    // Log del error para debug
    error_log("Error en dashboard cliente: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="favicon.php">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard - Cliente' ?> - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="cliente-theme">
    <div class="dashboard-container">
        <?php $navbar_volver_url = ''; include __DIR__ . '/partials/navbar_cliente.php'; ?>

        <div class="main-content">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i><?= htmlspecialchars($_SESSION['mensaje']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['info_transferencia'])): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-university me-2"></i><strong>Información sobre transferencia:</strong><br>
                    <?= htmlspecialchars($_SESSION['info_transferencia']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['info_transferencia']); ?>
            <?php endif; ?>

            <div class="welcome-section">
                <div class="welcome-card card">
                    <div class="card-body">
                        <div class="welcome-header">
                            <h2><i class="fas fa-chart-line me-2"></i>¡Hola, <?= explode(' ', $usuario['nombre_completo'])[0] ?>!</h2>
                            <p class="mb-0 text-muted">Bienvenido a FloralTech. Aquí tienes un resumen de toda tu actividad.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <h3 class="stats-section-title">Resumen</h3>
                <p class="stats-section-desc">Tus pedidos y gastos en un vistazo</p>
                <div class="stats-grid">
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($total_pedidos) ?></h3>
                            <p>Total Pedidos</p>
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($pedidos_pendientes) ?></h3>
                            <p>Pendientes de Envío</p>
                        </div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($pagos_pendientes) ?></h3>
                            <p>Por Pagar</p>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($pedidos_completados) ?></h3>
                            <p>Completados</p>
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                        <div class="stat-info">
                            <h3>$<?= number_format($estadisticas['total_gastado'], 2) ?></h3>
                            <p>Total Gastado</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="quick-actions-bar">
                <a href="index.php?ctrl=cliente&action=nuevo_pedido" class="quick-action-link quick-action-pedidos">
                    <i class="fas fa-plus"></i><span>Nuevo Pedido</span>
                </a>
                <a href="index.php?ctrl=cliente&action=historial" class="quick-action-link quick-action-pagos">
                    <i class="fas fa-history"></i><span>Historial</span>
                </a>
                <a href="index.php?ctrl=cliente&action=configuracion" class="quick-action-link quick-action-inventario">
                    <i class="fas fa-cog"></i><span>Configuración</span>
                </a>
            </div>

            <div class="content-grid">
                <div class="main-column">
                <div class="content-card card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i> Todos tus Pedidos</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pedidos_recientes)): ?>
                            <div class="table-responsive pedidos-table-wrap">
                                <table class="table table-hover align-middle pedidos-dashboard-table">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-receipt me-1 opacity-75"></i> Pedido</th>
                                            <th class="d-none d-md-table-cell"><i class="fas fa-calendar-alt me-1 opacity-75"></i> Fecha</th>
                                            <th><i class="fas fa-tag me-1 opacity-75"></i> Monto</th>
                                            <th><i class="fas fa-box me-1 opacity-75"></i> Estado</th>
                                            <th class="d-none d-sm-table-cell"><i class="fas fa-credit-card me-1 opacity-75"></i> Pago</th>
                                            <th class="text-end">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pedidos_recientes as $pedido): ?>
                                            <?php 
                                            $estado_pago = $pedido['estado_pago'] ?? 'Sin pago';
                                            $fecha_detalle = $pedido['fecha_formato'] ?? '';
                                            $dir_entrega = !empty(trim($pedido['direccion_entrega'] ?? '')) ? $pedido['direccion_entrega'] : null;
                                            $notas_pedido = !empty(trim($pedido['notas'] ?? '')) ? $pedido['notas'] : null;
                                            $fecha_ent = !empty($pedido['fecha_entrega_solicitada']) ? date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) : null;
                                            $productos = !empty(trim($pedido['flores_nombres'] ?? '')) ? $pedido['flores_nombres'] : null;
                                            ?>
                                            <tr class="pedido-main-row" role="button" tabindex="0">
                                                <td>
                                                    <span class="pedido-num">#<?= htmlspecialchars($pedido['numped']) ?></span>
                                                    <div class="d-md-none small text-muted mt-1"><?= htmlspecialchars($pedido['fecha_corta']) ?></div>
                                                </td>
                                                <td class="d-none d-md-table-cell text-muted"><?= htmlspecialchars($pedido['fecha_corta']) ?></td>
                                                <td><span class="monto-pedido">$<?= number_format($pedido['monto_total'], 2) ?></span></td>
                                                <td>
                                                    <?php
                                                    $estado_pedido = strtolower($pedido['estado']);
                                                    $estados_pedido_clases = [
                                                        'completado' => 'badge-estado-success',
                                                        'pendiente' => 'badge-estado-warning',
                                                        'procesando' => 'badge-estado-info',
                                                        'cancelado' => 'badge-estado-danger',
                                                        'enviado' => 'badge-estado-primary',
                                                        'entregado' => 'badge-estado-success'
                                                    ];
                                                    $badge_class = $estados_pedido_clases[$estado_pedido] ?? 'badge-estado-secondary';
                                                    $pago_bg = (strtolower($estado_pago) === 'completado') ? 'badge-estado-success' : ((strtolower($estado_pago) === 'pendiente') ? 'badge-estado-warning' : 'badge-estado-danger');
                                                    $mismo_estado = (strtolower($pedido['estado']) === strtolower($estado_pago));
                                                    ?>
                                                    <span class="badge-pedido <?= $badge_class ?>"><?= htmlspecialchars($pedido['estado']) ?></span>
                                                    <div class="d-sm-none mt-1">
                                                        <?php if ($mismo_estado): ?>
                                                            <small class="text-muted">Pedido y pago</small>
                                                        <?php else: ?>
                                                            <span class="badge-pedido <?= $pago_bg ?> badge-pedido-sm">Pago: <?= htmlspecialchars($estado_pago) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    <?php
                                                    $pago_badge_class = '';
                                                    switch (strtolower($estado_pago)) {
                                                        case 'completado': $pago_badge_class = 'badge-estado-success'; break;
                                                        case 'pendiente': $pago_badge_class = 'badge-estado-warning'; break;
                                                        case 'rechazado':
                                                        case 'sin pago': $pago_badge_class = 'badge-estado-danger'; break;
                                                        default: $pago_badge_class = 'badge-estado-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge-pedido <?= $pago_badge_class ?>"><?= htmlspecialchars($estado_pago) ?></span>
                                                </td>
                                                <td class="text-end pedido-row-actions">
                                                    <?php if (strtolower($estado_pago) === 'pendiente' || strtolower($estado_pago) === 'sin pago'): ?>
                                                        <a href="index.php?ctrl=cliente&action=realizar_pago&idpedido=<?= (int)$pedido['idped'] ?>" class="btn btn-action btn-pagar">
                                                            <i class="fas fa-credit-card"></i><span class="d-none d-md-inline ms-1">Pagar</span>
                                                        </a>
                                                    <?php elseif (strtolower($estado_pago) === 'completado'): ?>
                                                        <a href="index.php?ctrl=cliente&action=generar_factura&idpedido=<?= $pedido['idped'] ?>" class="btn btn-action btn-pdf" target="_blank">
                                                            <i class="fas fa-file-pdf"></i><span class="d-none d-md-inline ms-1">PDF</span>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr class="pedido-detail-row">
                                                <td colspan="6" class="pedido-detail-cell">
                                                    <div class="pedido-detail-inner">
                                                        <div class="pedido-detail-item"><strong>Producto:</strong> <?= $productos ? htmlspecialchars($productos) : '<span class="text-muted">—</span>' ?></div>
                                                        <div class="pedido-detail-item"><strong>Fecha:</strong> <?= htmlspecialchars($fecha_detalle) ?></div>
                                                        <?php if ($fecha_ent): ?>
                                                            <div class="pedido-detail-item"><strong>Entrega solicitada:</strong> <?= htmlspecialchars($fecha_ent) ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($dir_entrega): ?>
                                                            <div class="pedido-detail-item"><strong>Dirección de entrega:</strong> <?= htmlspecialchars($dir_entrega) ?></div>
                                                        <?php endif; ?>
                                                        <div class="pedido-detail-item"><strong>Comentario:</strong> <?= $notas_pedido ? nl2br(htmlspecialchars($notas_pedido)) : '<span class="text-muted">Ninguno</span>' ?></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No tienes pedidos recientes</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                </div>

                <div class="sidebar-column">
                    <?php if (!empty($flores_favoritas)): ?>
                    <div class="content-card card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-heart me-2"></i> Favoritas</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach (array_slice($flores_favoritas, 0, 5) as $flor): ?>
                                <div class="activity-item">
                                    <div class="activity-icon success"><i class="fas fa-seedling"></i></div>
                                    <div class="activity-content">
                                        <h6><?= htmlspecialchars($flor['nombre']) ?></h6>
                                        <p><?= $flor['total_comprado'] ?> compradas</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Filas expandibles: clic en la fila muestra/oculta el detalle (excepto en botones/enlaces)
    document.querySelectorAll('.pedido-main-row').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.pedido-row-actions a, .pedido-row-actions button')) return;
            var next = row.nextElementSibling;
            if (next && next.classList.contains('pedido-detail-row')) {
                next.classList.toggle('visible');
            }
        });
    });
    </script>
    <script src="assets/js/dashboard-cliente.js"></script>
    <?php include __DIR__ . '/../partials/footer_empresa.php'; ?>
</body>
</html>
