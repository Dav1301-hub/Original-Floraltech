<?php
// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 5) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

// Obtener datos del usuario actual
$usuario = $_SESSION['user'];

// Conectar a la base de datos
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

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

    // Obtener estadísticas del cliente con una sola consulta optimizada
$stmt = $db->prepare("
    SELECT 
        COUNT(DISTINCT p.idped) as total_pedidos,
        COALESCE(SUM(
            CASE 
                WHEN (pg.estado_pag = 'Completado' OR pg.estado_pag = 'COMPLETADO') 
                AND p.estado != 'Cancelado' 
                THEN p.monto_total 
                ELSE 0 
            END
        ), 0) as total_gastado,
        COALESCE(SUM(
            CASE 
                WHEN (pg.estado_pag = 'Sin pago' OR pg.estado_pag IS NULL) 
                AND p.estado != 'Cancelado' 
                THEN p.monto_total 
                ELSE 0 
            END
        ), 0) as total_pendiente_pago,
        SUM(CASE WHEN p.estado = 'Pendiente' AND p.estado != 'Cancelado' THEN 1 ELSE 0 END) as pedidos_pendientes,
        SUM(CASE WHEN p.estado = 'Completado' THEN 1 ELSE 0 END) as pedidos_completados,
        SUM(CASE WHEN (pg.estado_pag = 'Pendiente' AND p.estado != 'Cancelado') THEN 1 ELSE 0 END) as pagos_pendientes,
        SUM(CASE WHEN (pg.estado_pag = 'Completado' OR pg.estado_pag = 'COMPLETADO') THEN 1 ELSE 0 END) as pagos_completados,
        SUM(CASE WHEN (pg.estado_pag = 'Sin pago' OR pg.estado_pag IS NULL) THEN 1 ELSE 0 END) as pagos_sin_pago,
        COALESCE(AVG(
            CASE 
                WHEN (pg.estado_pag = 'Completado' OR pg.estado_pag = 'COMPLETADO') 
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

    // Pedidos recientes con más detalles
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
        LIMIT 5
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard - Cliente' ?> - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Estilizado -->
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fas fa-seedling"></i>
                FloralTech
            </div>
            <div class="navbar-user">
                <div class="user-info">
                    <p class="user-name">Bienvenido, <?= htmlspecialchars($usuario['nombre_completo']) ?></p>
                    <p class="user-welcome">Panel de Cliente</p>
                </div>
                <a href="index.php?ctrl=login&action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </nav>

        <!-- Mensajes del sistema -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?? 'info' ?> alert-dismissible fade show">
                <i class="fas fa-info-circle"></i>
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['info_transferencia'])): ?>
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="fas fa-university"></i>
                <strong>Información importante sobre transferencia:</strong><br>
                <?= htmlspecialchars($_SESSION['info_transferencia']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['info_transferencia']); ?>
        <?php endif; ?>

        <!-- Saludo Principal Estilizado -->
        <div class="welcome-card card">
            <div class="card-body">
                <div class="welcome-header">
                    <h2>¡Hola, <?= explode(' ', $usuario['nombre_completo'])[0] ?>!</h2>
                    <p>
                            Bienvenido a FloralTech
                    </p>
                </div>
            </div>
        </div>

            <!-- Statistics minimalistas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-number"><?= number_format($total_pedidos) ?></div>
                    <div class="stat-label">Pedidos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?= number_format($pedidos_pendientes) ?></div>
                    <div class="stat-label">Pedidos pendientes</div>
                    <?php if ($pedidos_pendientes > 0): ?>
                        <div class="stat-change negative">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stat-number"><?= number_format($pagos_pendientes) ?></div>
                    <div class="stat-label">Por Pagar</div>
                    <?php if ($pagos_pendientes > 0): ?>
                        
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content Grid minimalista -->
            <div class="content-grid">
                <!-- Recent Orders simplificado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list-alt"></i> Pedidos Recientes
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pedidos_recientes)): ?>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Pedido</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Estado del pedido</th>
                                            <th>Estado del pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($pedidos_recientes, 0, 5) as $pedido): ?>
                                            <tr>
                                                <td>
                                                    <strong>#<?= htmlspecialchars($pedido['numped']) ?></strong>
                                                    <br><small class="text-muted"><?= $pedido['cantidad_items'] ?> items</small>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($pedido['fecha_corta']) ?>
                                                </td>
                                                <td>
                                                    <strong>$<?= number_format($pedido['monto_total'], 2) ?></strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Mapeo de estados del PEDIDO (nuevo código)
                                                    $estado_pedido = strtolower($pedido['estado']);
                                                    $estados_pedido_clases = [
                                                        'completado' => 'badge-success',
                                                        'pendiente' => 'badge-warning',
                                                        'procesando' => 'badge-info',
                                                        'cancelado' => 'badge-danger',
                                                        'enviado' => 'badge-primary',
                                                        'entregado' => 'badge-success'
                                                    ];
                                                    $badge_class = $estados_pedido_clases[$estado_pedido] ?? 'badge-secondary';
                                                    ?>
                                                    <span class="badge <?= $badge_class ?>">
                                                        <?= htmlspecialchars($pedido['estado']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Mapeo de estados del PAGO (código existente)
                                                    $pago_badge_class = '';
                                                    $estado_pago = $pedido['estado_pago'] ?? 'Sin pago';
                                                    
                                                    switch (strtolower($estado_pago)) {
                                                        case 'completado':
                                                            $pago_badge_class = 'badge-success';
                                                            break;
                                                        case 'pendiente':
                                                            $pago_badge_class = 'badge-warning';
                                                            break;
                                                        case 'rechazado':
                                                        case 'sin pago':
                                                            $pago_badge_class = 'badge-danger';
                                                            break;
                                                        default:
                                                            $pago_badge_class = 'badge-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $pago_badge_class ?>">
                                                        <?= htmlspecialchars($estado_pago) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes pedidos recientes</p>
                                <a href="index.php?ctrl=cliente&action=realizar_pago" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Realizar Pedido
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar minimalista -->
                <div>
                    <!-- Quick Actions -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-bolt"></i> Acciones
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="index.php?ctrl=cliente&action=nuevo_pedido" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nuevo Pedido
                                </a>
                                <a href="index.php?ctrl=cliente&action=historial" class="btn btn-outline">
                                    <i class="fas fa-history"></i> Historial
                                </a>

                                <a href="index.php?ctrl=cliente&action=configuracion" class="btn btn-outline">
                                    <i class="fas fa-cog"></i> Configuración
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Flores Favoritas minimalista -->
                    <?php if (!empty($flores_favoritas)): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-heart"></i> Favoritas
                        </div>
                        <div class="card-body">
                            <?php foreach (array_slice($flores_favoritas, 0, 2) as $flor): ?>
                                <div class="activity-item">
                                    <div class="activity-icon success">
                                        <i class="fas fa-seedling"></i>
                                    </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dashboard-cliente.js"></script>
</body>
</html>
