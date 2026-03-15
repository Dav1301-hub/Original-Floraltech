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

// Nequi QR y número desde configuración empresa
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
    $nequi_qr_url = $nequi_qr_base . '/' . $nequi_qr_url;
}

$navbar_volver_url = 'index.php?ctrl=cliente&action=historial';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="favicon.php">
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
        .realizar-pago-page .main-content { padding: 1rem 0.5rem; }
        .realizar-pago-page .pago-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1rem 0.5rem; max-width: 100%; width: 100%; box-sizing: border-box; }
        .realizar-pago-page .pago-titulo-full { grid-column: 1 / -1; }
        .realizar-pago-page .pago-col-izq { min-width: 0; }
        .realizar-pago-page .pago-col-der { min-width: 0; }
        @media (min-width: 992px) {
            .realizar-pago-page .main-content { padding: 1rem 1rem; max-width: 100%; }
            .realizar-pago-page .pago-layout { gap: 2rem; padding: 1rem 0.5rem; max-width: 100%; margin: 0; }
        }
        @media (min-width: 1200px) {
            .realizar-pago-page .main-content { padding: 1rem 1.25rem; }
            .realizar-pago-page .pago-layout { gap: 2rem; padding: 1rem 0.75rem; }
        }
        .realizar-pago-page .card-metodo-inner.con-nequi { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; align-items: start; }
        .realizar-pago-page .card-metodo-inner .metodos-opciones { min-width: 0; }
        /* Diseño interior: bloque Nequi (izquierda) */
        .realizar-pago-page .nequi-qr-lado {
            background: linear-gradient(180deg, #fafbff 0%, #f5f3ff 100%);
            border: 1px solid rgba(102, 126, 234, 0.15);
            border-radius: 14px;
            padding: 1.25rem;
        }
        .realizar-pago-page .nequi-qr-box {
            background: #fff;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.12);
            margin-bottom: 1rem;
        }
        .realizar-pago-page .nequi-qr-box h6 { font-size: 0.95rem; font-weight: 600; color: var(--rp-purple); margin-bottom: 0.75rem; }
        .realizar-pago-page .nequi-qr-box img { border-radius: 10px; display: block; margin: 0 auto 0.75rem; }
        .realizar-pago-page .nequi-numero-pill {
            display: inline-block;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            color: var(--rp-purple);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            margin-bottom: 0.75rem;
        }
        .realizar-pago-page .nequi-instrucciones { font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; }
        .realizar-pago-page .nequi-instrucciones strong { color: #475569; }
        .realizar-pago-page .nequi-instrucciones ol { margin: 0.25rem 0 0 1rem; padding-left: 0.25rem; line-height: 1.6; }
        .realizar-pago-page .nequi-upload-label { font-size: 0.85rem; color: #475569; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem; }
        .realizar-pago-page .nequi-upload-label i { color: var(--rp-purple); }
        .realizar-pago-page .nequi-qr-lado input[type="file"] {
            border: 1px dashed rgba(102, 126, 234, 0.35);
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            background: #fff;
        }
        .realizar-pago-page .nequi-qr-lado input[type="file"]:focus { border-color: var(--rp-purple); outline: none; box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2); }
        /* Diseño interior: opciones Efectivo / Nequi (derecha) */
        .realizar-pago-page .metodos-opciones .row { margin: 0 -0.35rem; }
        .realizar-pago-page .metodos-opciones .row > [class*="col-"] { padding: 0 0.35rem; }
        .realizar-pago-page .payment-method-option {
            border-radius: 14px;
            padding: 1.25rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
            background: #fff;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .realizar-pago-page .payment-method-option .metodo-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.65rem;
        }
        .realizar-pago-page .payment-method-option[data-method="efectivo"] .metodo-icon-wrap { background: rgba(34, 197, 94, 0.12); color: #16a34a; }
        .realizar-pago-page .payment-method-option[data-method="nequi"] .metodo-icon-wrap { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
        .realizar-pago-page .payment-method-option h6 { font-size: 1rem; font-weight: 700; margin-bottom: 0.2rem; color: #1e293b; }
        .realizar-pago-page .payment-method-option small { font-size: 0.75rem; color: #64748b; }
        .realizar-pago-page .payment-method-option:hover { border-color: rgba(102, 126, 234, 0.4); background: #fafbff; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(102, 126, 234, 0.12); }
        .realizar-pago-page .payment-method-option.selected { border-color: var(--rp-purple); background: linear-gradient(145deg, #f8f7ff 0%, #ede9fe 100%); box-shadow: 0 4px 16px rgba(102, 126, 234, 0.22); }
        .realizar-pago-page .payment-method-option.selected .metodo-icon-wrap { background: rgba(102, 126, 234, 0.2); color: var(--rp-purple); }
        @media (max-width: 991px) {
            .realizar-pago-page .pago-layout { grid-template-columns: 1fr; }
            .realizar-pago-page .card-metodo-inner.con-nequi { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="cliente-theme">
    <div class="dashboard-container realizar-pago-page">
        <?php include __DIR__ . '/partials/navbar_cliente.php'; ?>

        <div class="main-content">
            <div class="pago-layout">
                <div class="page-title-card pago-titulo-full">
                    <h1><i class="fas fa-credit-card me-2" style="color: var(--rp-purple);"></i>Realizar Pago</h1>
                    <p>Completa tu transacción para el pedido seleccionado</p>
                </div>

                <?php if (!empty($_SESSION['mensaje_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show pago-titulo-full" role="alert">
                        <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['mensaje_error']); ?>
                <?php endif; ?>

                <div class="pago-col-izq">
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
                </div>

                <div class="pago-col-der">
                    <form method="POST" action="index.php?ctrl=cliente&action=procesar_pago" enctype="multipart/form-data" id="formPago">
                        <input type="hidden" name="idpedido" value="<?= $idPedido ?>">
                        <input type="hidden" name="idpago" value="<?= htmlspecialchars($pedido['idpago'] ?? '') ?>">
                        <input type="hidden" name="metodo_pago" id="inputMetodoPago" value="efectivo">

                        <div class="resumen-card" id="cardMetodoPago">
                            <div class="card-header">
                                <i class="fas fa-wallet"></i> Método de pago
                            </div>
                            <div class="card-body">
                                <div class="card-metodo-inner" id="cardMetodoInner">
                                    <div id="bloqueEvidenciaNequi" class="nequi-qr-lado" style="display: none;">
                                        <div class="nequi-qr-box">
                                            <h6><i class="fas fa-qrcode me-1"></i> Escanea para pagar</h6>
                                            <?php if ($nequi_qr_url): ?>
                                                <img src="<?= htmlspecialchars($nequi_qr_url) ?>?v=<?= time() ?>" alt="QR Nequi" style="max-width: 160px; height: auto;">
                                            <?php else: ?>
                                                <p class="text-muted small mb-0">Configura el QR en Admin → Configuración.</p>
                                            <?php endif; ?>
                                            <?php if (isset($nequi_numero) && $nequi_numero !== ''): ?>
                                                <div class="nequi-numero-pill"><i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($nequi_numero) ?></div>
                                            <?php endif; ?>
                                            <div class="nequi-instrucciones text-start">
                                                <p class="mb-1"><strong>Instrucciones</strong></p>
                                                <ol class="mb-0">
                                                    <li>Abre tu app Nequi</li>
                                                    <li>Escanea el QR o envía al número</li>
                                                    <li>Confirma el monto indicado</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <label class="nequi-upload-label"><i class="fas fa-camera"></i> Sube comprobante</label>
                                        <input type="file" name="comprobante" id="inputComprobante" class="form-control form-control-sm w-100" accept="image/*">
                                    </div>
                                    <div class="metodos-opciones">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="payment-method-option" data-method="efectivo">
                                                    <div class="metodo-icon-wrap"><i class="fas fa-money-bill-wave fa-lg"></i></div>
                                                    <h6 class="mb-0">Efectivo</h6>
                                                    <small>Pago contra entrega</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="payment-method-option" data-method="nequi">
                                                    <div class="metodo-icon-wrap"><i class="fas fa-mobile-alt fa-lg"></i></div>
                                                    <h6 class="mb-0">Nequi</h6>
                                                    <small>Transferencia / QR</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-confirmar" id="btnConfirmar">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        var inputMetodo = document.getElementById('inputMetodoPago');
        var bloqueEvidencia = document.getElementById('bloqueEvidenciaNequi');
        var cardInner = document.getElementById('cardMetodoInner');
        var options = document.querySelectorAll('.payment-method-option');

        function selectMethod(method) {
            inputMetodo.value = method;
            options.forEach(function(el) {
                el.classList.toggle('selected', el.getAttribute('data-method') === method);
            });
            if (bloqueEvidencia) {
                bloqueEvidencia.style.display = method === 'nequi' ? 'block' : 'none';
                if (cardInner) cardInner.classList.toggle('con-nequi', method === 'nequi');
                var fileInput = document.getElementById('inputComprobante');
                if (fileInput) fileInput.required = (method === 'nequi');
            }
        }

        options.forEach(function(el) {
            el.addEventListener('click', function() { selectMethod(el.getAttribute('data-method')); });
        });
        selectMethod('efectivo');
    })();
    </script>
    <?php include __DIR__ . '/../partials/footer_empresa.php'; ?>
</body>
</html>
