<?php
// Mostrar mensajes de éxito/error
if (isset($_SESSION['mensaje_exito'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            '.$_SESSION['mensaje_exito'].'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['mensaje_error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.$_SESSION['mensaje_error'].'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['mensaje_error']);
}

// Conectar a la base de datos
require_once __DIR__ . '/../../models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

// Datos Nequi (QR y número) desde configuración empresa
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

// Obtener ID del cliente
$usuario = $_SESSION['user'];
try {
    $stmt = $db->prepare("SELECT idcli FROM cli WHERE email = ?");
    $stmt->execute([$usuario['email']]);
    $cliente_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $cliente_id = $cliente_data['idcli'] ?? 0;

    // Obtener historial completo de pedidos
$stmt = $db->prepare("
    SELECT 
        p.*,
        DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y %H:%i') as fecha_formato,
        DATE_FORMAT(p.fecha_pedido, '%d %b') as fecha_corta,
        pg.estado_pag,
        pg.metodo_pago,
        pg.fecha_pago,
        pg.idpago,
        pg.transaccion_id,
        pg.comprobante_transferencia,
        (pg.comprobante_imagen IS NOT NULL) AS tiene_comprobante_bd,
        COUNT(dp.iddetped) as total_items,
        GROUP_CONCAT(DISTINCT CONCAT(tf.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ') as items_detalle
    FROM ped p 
    LEFT JOIN pagos pg ON p.idped = pg.ped_idped
    LEFT JOIN detped dp ON p.idped = dp.idped
    LEFT JOIN tflor tf ON dp.idtflor = tf.idtflor
    WHERE p.cli_idcli = ? 
    GROUP BY p.idped
    ORDER BY p.fecha_pedido DESC
");
    $stmt->execute([$cliente_id]);
    $historial_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $historial_pedidos = [];
    error_log("Error obteniendo historial: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="cliente-theme">
    <div class="dashboard-container">
        <?php $navbar_volver_url = 'index.php?ctrl=cliente&action=dashboard'; $usuario = $_SESSION['user']; include __DIR__ . '/partials/navbar_cliente.php'; ?>

        <div class="main-content">
        <!-- Contenedor para alertas dinámicas -->
        <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000; width: 300px;"></div>

        <div class="content-card card">
            <div class="card-header">
                <i class="fas fa-history"></i> Historial Completo de Pedidos
            </div>
            <div class="card-body">
                <?php if (!empty($historial_pedidos)): ?>
                    <div class="table-responsive pedidos-table-wrap">
                        <table class="table table-hover align-middle pedidos-dashboard-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-receipt me-1 opacity-75"></i> Pedido</th>
                                    <th class="d-none d-md-table-cell"><i class="fas fa-calendar-alt me-1 opacity-75"></i> Fecha</th>
                                    <th class="d-none d-lg-table-cell"><i class="fas fa-list me-1 opacity-75"></i> Items</th>
                                    <th><i class="fas fa-tag me-1 opacity-75"></i> Monto</th>
                                    <th><i class="fas fa-box me-1 opacity-75"></i> Estado</th>
                                    <th class="d-none d-sm-table-cell"><i class="fas fa-credit-card me-1 opacity-75"></i> Pago</th>
                                    <th class="d-none d-xl-table-cell"><i class="fas fa-wallet me-1 opacity-75"></i> Método</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_pedidos as $pedido): ?>
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
                                    $badge_estado = $estados_pedido_clases[$estado_pedido] ?? 'badge-estado-secondary';
                                    $estado_pago = $pedido['estado_pag'] ?? 'Sin pago';
                                    $pago_badge_class = (strtolower($estado_pago) === 'completado') ? 'badge-estado-success' : ((strtolower($estado_pago) === 'pendiente') ? 'badge-estado-warning' : 'badge-estado-danger');
                                    $fecha_detalle = $pedido['fecha_formato'] ?? '';
                                    $dir_entrega = !empty(trim($pedido['direccion_entrega'] ?? '')) ? $pedido['direccion_entrega'] : null;
                                    $notas_pedido = !empty(trim($pedido['notas'] ?? '')) ? $pedido['notas'] : null;
                                    $fecha_ent = !empty($pedido['fecha_entrega_solicitada']) ? date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) : null;
                                    $productos = !empty(trim($pedido['items_detalle'] ?? '')) ? $pedido['items_detalle'] : null;
                                    $ref_pago = !empty(trim($pedido['transaccion_id'] ?? '')) ? trim($pedido['transaccion_id']) : null;
                                    $tiene_comprobante_bd = !empty($pedido['tiene_comprobante_bd']);
                                    $comprobante_legacy = !empty(trim($pedido['comprobante_transferencia'] ?? '')) ? trim($pedido['comprobante_transferencia']) : null;
                                    $comprobante_legacy_existe = $comprobante_legacy && file_exists(__DIR__ . '/../../assets/comprobantes/' . $comprobante_legacy);
                                    $tiene_evidencia = $tiene_comprobante_bd || $comprobante_legacy_existe;
                                    $url_comprobante = 'ver_comprobante.php?idpago=' . (int)$pedido['idpago'];
                                    $fecha_pago_fmt = !empty($pedido['fecha_pago']) ? date('d/m/Y H:i', strtotime($pedido['fecha_pago'])) : null;
                                    ?>
                                    <tr class="pedido-main-row" role="button" tabindex="0">
                                        <td>
                                            <span class="pedido-num">#<?= htmlspecialchars($pedido['numped']) ?></span>
                                            <div class="d-md-none small text-muted mt-1"><?= htmlspecialchars($pedido['fecha_corta'] ?? $pedido['fecha_formato']) ?></div>
                                        </td>
                                        <td class="d-none d-md-table-cell text-muted"><?= htmlspecialchars($pedido['fecha_corta'] ?? $pedido['fecha_formato']) ?></td>
                                        <td class="d-none d-lg-table-cell">
                                            <small><?= (int)$pedido['total_items'] ?> items</small>
                                            <div class="text-muted small text-truncate" style="max-width:120px" title="<?= htmlspecialchars($pedido['items_detalle'] ?? '') ?>"><?= htmlspecialchars($pedido['items_detalle'] ?? '-') ?></div>
                                        </td>
                                        <td><span class="monto-pedido">$<?= number_format($pedido['monto_total'], 2) ?></span></td>
                                        <td>
                                            <span class="badge-pedido <?= $badge_estado ?>"><?= htmlspecialchars($pedido['estado']) ?></span>
                                            <div class="d-sm-none mt-1">
                                                <span class="badge-pedido <?= $pago_badge_class ?> badge-pedido-sm"><?= htmlspecialchars($estado_pago) ?></span>
                                            </div>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge-pedido <?= $pago_badge_class ?>"><?= htmlspecialchars($estado_pago) ?></span>
                                        </td>
                                        <td class="d-none d-xl-table-cell text-muted small"><?= $pedido['metodo_pago'] ? htmlspecialchars($pedido['metodo_pago']) : '-' ?></td>
                                        <td class="text-end pedido-row-actions">
                                            <div class="d-flex flex-wrap gap-1 justify-content-end">
                                                <a href="index.php?ctrl=cliente&action=generar_factura&idpedido=<?= $pedido['idped'] ?>" class="btn btn-action btn-pdf" title="Descargar Factura" target="_blank">
                                                    <i class="fas fa-file-pdf"></i><span class="d-none d-md-inline ms-1">PDF</span>
                                                </a>
                                                <?php if (strtolower($pedido['estado_pag'] ?? 'sin pago') === 'pendiente' || strtolower($pedido['estado_pag'] ?? '') === 'sin pago'): ?>
                                                    <button type="button" class="btn btn-action btn-pagar" title="Pagar Pedido" data-bs-toggle="modal" data-bs-target="#modalPago"
                                                        onclick="prepararModalPago('<?= htmlspecialchars($pedido['numped']) ?>', '<?= number_format($pedido['monto_total'], 2, '.', '') ?>', <?= (int)$pedido['idped'] ?>)">
                                                        <i class="fas fa-credit-card"></i><span class="d-none d-md-inline ms-1">Pagar</span>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-action btn-enviar-factura" title="Enviar Factura por Email" data-idpedido="<?= $pedido['idped'] ?>" data-email="<?= htmlspecialchars($usuario['email']) ?>">
                                                    <i class="fas fa-envelope"></i><span class="d-none d-md-inline ms-1">Enviar</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="pedido-detail-row">
                                        <td colspan="9" class="pedido-detail-cell">
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
                                                <hr class="my-2">
                                                <h6 class="mb-2 mt-2"><i class="fas fa-file-invoice-dollar me-1 text-primary"></i> Detalles del pago</h6>
                                                <div class="pedido-detail-item"><strong>Estado:</strong> <span class="badge-pedido <?= $pago_badge_class ?>"><?= htmlspecialchars($estado_pago) ?></span></div>
                                                <div class="pedido-detail-item"><strong>Método:</strong> <?= $pedido['metodo_pago'] ? htmlspecialchars($pedido['metodo_pago']) : '<span class="text-muted">—</span>' ?></div>
                                                <?php if ($fecha_pago_fmt): ?>
                                                    <div class="pedido-detail-item"><strong>Fecha pago:</strong> <?= htmlspecialchars($fecha_pago_fmt) ?></div>
                                                <?php endif; ?>
                                                <?php if ($ref_pago): ?>
                                                    <div class="pedido-detail-item"><strong>Referencia:</strong> <?= htmlspecialchars($ref_pago) ?></div>
                                                <?php endif; ?>
                                                <?php if ($tiene_evidencia): ?>
                                                    <div class="pedido-detail-item mt-2">
                                                        <strong>Evidencia de pago:</strong>
                                                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                            <a href="<?= htmlspecialchars($url_comprobante) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-external-link-alt me-1"></i> Ver comprobante
                                                            </a>
                                                            <a href="<?= htmlspecialchars($url_comprobante) ?>" target="_blank" rel="noopener" class="d-inline-block">
                                                                <img src="<?= htmlspecialchars($url_comprobante) ?>" alt="Comprobante" class="rounded border" style="max-width:80px;max-height:80px;object-fit:contain;background:#f8f9fa;">
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-icon success">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= count($historial_pedidos) ?></div>
                                        <div class="stat-label">Total de Pedidos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">
                $<?= 
                    number_format(array_reduce($historial_pedidos, function($total, $pedido) {
                        // Sumar solo si el pago está marcado como "Completado"
                        return (strtolower($pedido['estado_pag'] ?? '') === 'completado') 
                            ? $total + $pedido['monto_total'] 
                            : $total;
                    }, 0), 2) 
                ?>
            </div>
            <div class="stat-label">Total Gastado (Pagados)</div>
        </div>
    </div>
</div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No tienes pedidos aún</h4>
                        <p class="text-muted">¿Por qué no crear tu primer pedido?</p>
                        <a href="index.php?ctrl=cliente&action=nuevo_pedido" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Pedido
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal de Pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header modal-header-cliente text-white">
                    <h5 class="modal-title" id="modalPagoLabel">
                        <i class="fas fa-credit-card me-2"></i>Realizar Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h4 class="mb-2">Pedido <span id="modalNumPed" class="text-primary"></span></h4>
                        <div class="display-6 fw-bold text-success mb-2">
                            $<span id="modalMontoTotal">0.00</span>
                        </div>
                        <p class="text-muted">Total a pagar</p>
                    </div>

                    <h6 class="mb-3 text-secondary">Seleccione un método de pago:</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="payment-method text-center p-3 border rounded h-100" onclick="selectPaymentMethod('efectivo')" id="method-efectivo" style="cursor: pointer; transition: all 0.2s;">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h6>Efectivo</h6>
                                <small class="text-muted d-block">Pago contra entrega</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="payment-method text-center p-3 border rounded h-100" onclick="selectPaymentMethod('nequi')" id="method-nequi" style="cursor: pointer; transition: all 0.2s;">
                                <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                <h6>Nequi</h6>
                                <small class="text-muted d-block">Código QR</small>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor QR Nequi (Oculto por defecto) -->
                    <div id="qrContainer" class="text-center border rounded p-4 bg-light" style="display: none;">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-qrcode me-2"></i>Código QR Nequi
                        </h6>
                        <?php if ($nequi_qr_url): ?>
                            <img src="<?= htmlspecialchars($nequi_qr_url) ?>?v=<?= time() ?>" alt="QR Nequi" class="nequi-qr-img bg-white p-2 rounded shadow-sm mb-2">
                        <?php else: ?>
                            <p class="text-muted small mb-0">Configura el QR Nequi en Admin → Configuración.</p>
                        <?php endif; ?>
                        <?php if (isset($nequi_numero) && $nequi_numero !== ''): ?>
                            <p class="mb-2 small fw-semibold text-dark">Número Nequi: <span class="text-primary"><?= htmlspecialchars($nequi_numero) ?></span></p>
                        <?php endif; ?>
                        <div class="text-start small text-muted">
                            <p class="mb-1"><strong>Instrucciones:</strong></p>
                            <ol class="ps-3 mb-0">
                                <li>Abre tu app Nequi</li>
                                <li>Escanea este código QR</li>
                                <li>Confirma el pago por el monto indicado</li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- Contenedor Efectivo (Oculto por defecto) -->
                    <div id="efectivoContainer" class="text-center border rounded p-4 bg-light shadow-sm" style="display: none;">
                        <i class="fas fa-hand-holding-usd fa-3x text-success mb-3"></i>
                        <h6>Pago Contra Entrega</h6>
                        <p class="text-muted small mb-0">Por favor, ten el dinero exacto al momento de recibir tu pedido para facilitar el cambio al domiciliario.</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="#" id="btnIrAPagar" class="btn btn-success"><i class="fas fa-external-link-alt me-1"></i> Ir a pagar este pedido</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos adicionales para el modal -->
    <style>
        .payment-method:hover {
            border-color: #0d6efd !important;
            background-color: #f8f9fa;
        }
        .payment-method.selected {
            border-color: #0d6efd !important;
            background-color: #e9ecef;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        }
        .nequi-qr-img { width: 200px; height: 200px; object-fit: contain; display: block; margin-left: auto; margin-right: auto; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let currentPedidoId = '';
    function prepararModalPago(numPed, monto, idped) {
        document.getElementById('modalNumPed').textContent = numPed;
        const formatMonto = parseFloat(monto).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('modalMontoTotal').textContent = formatMonto;
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
        document.getElementById('qrContainer').style.display = 'none';
        document.getElementById('efectivoContainer').style.display = 'none';
        currentPedidoId = idped || '';
        var link = document.getElementById('btnIrAPagar');
        link.href = currentPedidoId ? ('index.php?ctrl=cliente&action=realizar_pago&idpedido=' + currentPedidoId) : '#';
    }

    // Función para seleccionar método de pago en el modal
    function selectPaymentMethod(method) {
        // Remover clase selected de todos
        document.querySelectorAll('.payment-method').forEach(el => {
            el.classList.remove('selected');
        });
        
        // Añadir clase selected al método elegido
        document.getElementById('method-' + method).classList.add('selected');
        
        // Mostrar contenido correspondiente
        if (method === 'nequi') {
            document.getElementById('qrContainer').style.display = 'block';
            document.getElementById('efectivoContainer').style.display = 'none';
        } else if (method === 'efectivo') {
            document.getElementById('efectivoContainer').style.display = 'block';
            document.getElementById('qrContainer').style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
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

        // Función para mostrar alertas dinámicas
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertId = 'alert-' + Date.now();
            
            const alert = document.createElement('div');
            alert.id = alertId;
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.role = 'alert';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="document.getElementById('${alertId}').remove()"></button>
            `;
            
            alertContainer.appendChild(alert);
            
            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                if (document.getElementById(alertId)) {
                    document.getElementById(alertId).remove();
                }
            }, 5000);
        }

        // Manejar clic en botones de enviar factura por email
        document.querySelectorAll('.btn-enviar-factura').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const idPedido = this.dataset.idpedido;
                const email = this.dataset.email;
                const btn = this;
                const originalHtml = btn.innerHTML;
                
                // Confirmar antes de enviar
                if (!confirm(`¿Desea enviar la factura del pedido #${idPedido} al correo ${email}?`)) {
                    return;
                }
                
                // Mostrar indicador de carga
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                // Tiempo máximo de 30 segundos (más tiempo para el proceso)
                const tiempoMaximo = 30000;
                let timeoutId = setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    showAlert('warning', 'El envío está tardando más de lo esperado. Por favor intente nuevamente.');
                }, tiempoMaximo);
                
                // Enviar solicitud AJAX
                const formData = new FormData();
                formData.append('idpedido', idPedido);
                formData.append('email', email);
                
                fetch('index.php?ctrl=cliente&action=enviar_factura_email', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Verificar si la respuesta es JSON
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        // Si no es JSON, leer como texto
                        return response.text().then(text => {
                            throw new Error('Respuesta no válida del servidor: ' + text.substring(0, 100));
                        });
                    }
                })
                .then(data => {
                    clearTimeout(timeoutId);
                    
                    if (data.success) {
                        showAlert('success', data.message || '✅ Factura enviada exitosamente');
                        // Restaurar el ícono de email con un check temporal
                        btn.innerHTML = '<i class="fas fa-check"></i>';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        showAlert('danger', data.message || '❌ Error al enviar la factura');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('Error en fetch:', error);
                    showAlert('danger', 'Error en la conexión: ' + error.message);
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
            });
        });

        // Manejar clic en enlaces de descarga directa de factura
        document.querySelectorAll('a[href*="action=generar_factura"]').forEach(link => {
            link.addEventListener('click', function(e) {
                // No es necesario hacer nada especial aquí, 
                // ya que es un enlace normal que abre el PDF
                // Pero podemos añadir un pequeño feedback visual
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
                
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                }, 2000);
            });
        });
        
        // Función para formatear números con separadores de miles
        function formatNumber(number) {
            return number.toLocaleString('es-ES', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    });
    </script>
    <?php include __DIR__ . '/../partials/footer_empresa.php'; ?>
</body>
</html>