<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión y rol
if (!isset($_SESSION['user'])) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

$usuario = $_SESSION['user'];

// Obtener conexión a la base de datos
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

// Validar y obtener ID del pedido
$idPedido = isset($_GET['idpedido']) ? (int)$_GET['idpedido'] : 0;
if ($idPedido <= 0) {
    $_SESSION['mensaje_error'] = "ID de pedido inválido";
    header('Location: index.php?ctrl=cliente&action=historial');
    exit();
}

try {
    $stmt = $db->prepare("SELECT idcli FROM cli WHERE email = ? LIMIT 1");
    $stmt->execute([$_SESSION['user']['email']]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        $_SESSION['mensaje_error'] = "Cliente no encontrado";
        header('Location: index.php?ctrl=cliente&action=historial');
        exit();
    }

    $stmt = $db->prepare("SELECT idped FROM ped WHERE idped = ? AND cli_idcli = ?");
    $stmt->execute([$idPedido, $cliente['idcli']]);
    $pedidoValido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedidoValido) {
        $_SESSION['mensaje_error'] = "No tienes permiso para acceder a este pedido";
        header('Location: index.php?ctrl=cliente&action=historial');
        exit();
    }

    $stmt = $db->prepare("
        SELECT p.*,
               DATE_FORMAT(p.fecha_entrega_solicitada, '%d/%m/%Y') as fecha_entrega_formato,
               pg.metodo_pago,
               pg.estado_pag,
               pg.idpago
        FROM ped p
        LEFT JOIN pagos pg ON p.idped = pg.ped_idped
        WHERE p.idped = ?
    ");
    $stmt->execute([$idPedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        SELECT dp.*,
               tf.nombre,
               tf.precio,
               (dp.cantidad * tf.precio) as subtotal
        FROM detped dp
        JOIN tflor tf ON dp.idtflor = tf.idtflor
        WHERE dp.idped = ?
    ");
    $stmt->execute([$idPedido]);
    $flores_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($pedido['monto_total'])) {
        $pedido['monto_total'] = array_reduce($flores_pedido, function($total, $flor) {
            return $total + ($flor['cantidad'] * $flor['precio']);
        }, 0);
    }
} catch (PDOException $e) {
    error_log("Error en realizar_pago: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al cargar la información del pedido";
    header('Location: index.php?ctrl=cliente&action=historial');
    exit();
}

if (empty($pedido)) {
    $_SESSION['mensaje_error'] = "No se encontró información del pedido";
    header('Location: index.php?ctrl=cliente&action=historial');
    exit();
}

$navbar_volver_url = 'index.php?ctrl=cliente&action=historial';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .realizar-pago-page { --rp-purple: #667eea; --rp-purple-dark: #5a67d8; --rp-radius: 16px; --rp-shadow: 0 4px 20px rgba(102, 126, 234, 0.08); }
        .realizar-pago-page body { font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; }
        .realizar-pago-page .page-title-card {
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
            border: 1px solid var(--cli-border);
            border-radius: var(--rp-radius);
            box-shadow: var(--rp-shadow);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--rp-purple);
        }
        .realizar-pago-page .page-title-card h1 { font-size: 1.35rem; font-weight: 700; color: var(--cli-text); margin: 0 0 0.25rem 0; }
        .realizar-pago-page .page-title-card p { margin: 0; color: var(--cli-text-muted); font-size: 0.9rem; }
        .realizar-pago-page .resumen-card {
            background: #fff;
            border: 1px solid var(--cli-border);
            border-radius: var(--rp-radius);
            box-shadow: var(--rp-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .realizar-pago-page .resumen-card .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid var(--cli-border);
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: var(--cli-text);
            font-size: 1rem;
        }
        .realizar-pago-page .resumen-card .card-header i { color: var(--rp-purple); margin-right: 0.5rem; }
        .realizar-pago-page .resumen-card .card-body { padding: 1.25rem 1.5rem; }
        .realizar-pago-page .pedido-num { font-weight: 700; color: var(--rp-purple); font-size: 1.05rem; }
        .realizar-pago-page .detalle-item { padding: 0.5rem 0; border-bottom: 1px solid var(--cli-border); display: flex; justify-content: space-between; align-items: center; }
        .realizar-pago-page .detalle-item:last-of-type { border-bottom: none; }
        .realizar-pago-page .total-row {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
            border: 1px solid rgba(102, 126, 234, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .realizar-pago-page .total-row .total-label { font-weight: 700; color: var(--cli-text); font-size: 1rem; }
        .realizar-pago-page .total-row .total-value { font-size: 1.5rem; font-weight: 800; color: var(--rp-purple); }
        .realizar-pago-page .info-alert {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid rgba(2, 132, 199, 0.25);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            color: #0c4a6e;
            font-size: 0.9rem;
        }
        .realizar-pago-page .info-alert i { color: var(--cli-info); margin-right: 0.5rem; }
        .realizar-pago-page .btn-confirmar {
            background: linear-gradient(135deg, var(--rp-purple) 0%, #764ba2 100%);
            color: #fff;
            border: none;
            padding: 0.9rem 1.5rem;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.35);
            transition: all 0.2s ease;
            width: 100%;
        }
        .realizar-pago-page .btn-confirmar:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
        .realizar-pago-page .btn-cancelar { color: var(--cli-text-muted); text-decoration: none; font-size: 0.9rem; margin-top: 0.75rem; display: inline-block; }
        .realizar-pago-page .btn-cancelar:hover { color: var(--rp-purple); }
        .realizar-pago-page .pago-center { max-width: 520px; margin: 0 auto; }
    </style>
</head>
<body class="cliente-theme">
    <div class="dashboard-container realizar-pago-page">
        <?php include __DIR__ . '/partials/navbar_cliente.php'; ?>

        <div class="main-content">
            <div class="pago-center">
                <div class="page-title-card">
                    <h1><i class="fas fa-credit-card me-2" style="color: var(--rp-purple);"></i>Realizar Pago</h1>
                    <p>Completa tu transacción para el pedido seleccionado</p>
                </div>

                <?php if (!empty($_SESSION['mensaje_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['mensaje_error']); ?>
                <?php endif; ?>

                <div class="resumen-card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice-dollar"></i> Resumen de Pago
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <i class="fas fa-shopping-bag text-muted me-1"></i>
                            <span class="pedido-num">Pedido #<?= htmlspecialchars($pedido['numped'] ?? 'N/A') ?></span>
                        </p>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Detalles del pedido</small>
                            <?php if (!empty($flores_pedido)): ?>
                                <?php foreach ($flores_pedido as $flor): ?>
                                    <div class="detalle-item">
                                        <span><?= htmlspecialchars($flor['nombre']) ?> x<?= (int)$flor['cantidad'] ?></span>
                                        <span class="fw-semibold">$<?= number_format($flor['subtotal'], 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small mb-0">No hay productos en este pedido</p>
                            <?php endif; ?>
                        </div>

                        <div class="detalle-item">
                            <span>Subtotal</span>
                            <span>$<?= number_format($pedido['monto_total'] ?? 0, 2) ?></span>
                        </div>
                        <div class="detalle-item">
                            <span>Envío</span>
                            <span class="text-success fw-semibold">Gratis</span>
                        </div>

                        <div class="total-row">
                            <span class="total-label">Total a pagar</span>
                            <span class="total-value">$<?= number_format($pedido['monto_total'] ?? 0, 2) ?></span>
                        </div>
                    </div>
                </div>

                <div class="info-alert">
                    <i class="fas fa-info-circle"></i> El pedido se procesará una vez confirmado el pago.
                </div>

                <form method="POST" action="index.php?ctrl=cliente&action=procesar_pago">
                    <input type="hidden" name="idpedido" value="<?= $idPedido ?>">
                    <input type="hidden" name="idpago" value="<?= htmlspecialchars($pedido['idpago'] ?? '') ?>">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-confirmar">
                            <i class="fas fa-check-circle me-2"></i> Confirmar Pago
                        </button>
                        <a href="<?= htmlspecialchars($navbar_volver_url) ?>" class="btn-cancelar text-center">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/../partials/footer_empresa.php'; ?>
</body>
</html>
