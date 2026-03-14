<?php
// views/cliente/nuevo_pedido.php
// Formulario para crear pedido desde panel de cliente - Estructura unificada
// $flores_disponibles lo envía el controlador (todos los productos con su stock).
// Si se incluye como fragmento sin controlador, cargar aquí todas las flores sin filtrar por stock.

if (!isset($flores_disponibles)) {
    require_once __DIR__ . '/../../models/conexion.php';
    $conn = new conexion();
    $db = $conn->get_conexion();
    try {
        $stmt = $db->prepare("
            SELECT 
                tf.idtflor,
                tf.nombre,
                tf.naturaleza as color,
                tf.descripcion,
                tf.precio,
                COALESCE(i.stock, i.cantidad_disponible, 0) as stock
            FROM tflor tf
            LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
            ORDER BY tf.nombre
        ");
        $stmt->execute();
        $flores_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $flores_disponibles = [];
        error_log("Error obteniendo flores: " . $e->getMessage());
    }
}

$isFragment = isset($_GET['fragment']) && $_GET['fragment'] == '1';
?>

<?php if (!$isFragment): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .nuevo-pedido-page { --np-purple: #667eea; --np-purple-dark: #5a67d8; --np-radius: 14px; --np-shadow: 0 4px 20px rgba(102, 126, 234, 0.08); }
        .nuevo-pedido-page .page-title-card { background: linear-gradient(135deg, #fff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; border-radius: var(--np-radius); box-shadow: var(--np-shadow); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid var(--np-purple); }
        .nuevo-pedido-page .page-title-card h1 { font-size: 1.35rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0; }
        .nuevo-pedido-page .page-title-card p { margin: 0; color: #64748b; font-size: 0.9rem; }
        .nuevo-pedido-page .card { border: 1px solid #e2e8f0; border-radius: var(--np-radius); box-shadow: var(--np-shadow); overflow: hidden; background: #fff; }
        .nuevo-pedido-page .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; font-weight: 600; color: #0f172a; font-size: 1rem; }
        .nuevo-pedido-page .card-header i { color: var(--np-purple); }
        .nuevo-pedido-page .search-wrap { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.5rem 1rem; margin-bottom: 1.25rem; }
        .nuevo-pedido-page .search-wrap .form-control { border: none; background: transparent; font-size: 0.95rem; }
        .nuevo-pedido-page .search-wrap .form-control:focus { box-shadow: none; }
        .nuevo-pedido-page .search-wrap i { color: #94a3b8; }
        .nuevo-pedido-page .flower-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 0.75rem; transition: all 0.2s ease; background: #fff; position: relative; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .nuevo-pedido-page .flower-card:hover { border-color: var(--np-purple); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.12); }
        .nuevo-pedido-page .flower-card.selected { border-color: var(--np-purple); background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2); }
        .nuevo-pedido-page .flower-card:not(.no-stock) { cursor: pointer; }
.nuevo-pedido-page .flower-card.no-stock { opacity: 0.75; background: #f8fafc; border-color: #e2e8f0 !important; cursor: not-allowed; }
        .nuevo-pedido-page .flower-card .flower-icon { width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: var(--np-purple); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .nuevo-pedido-page .flower-card.no-stock .flower-icon { background: #f1f5f9; color: #94a3b8; }
        .nuevo-pedido-page .flower-card .flower-info { flex: 1; min-width: 0; }
        .nuevo-pedido-page .flower-card .flower-info h6 { margin: 0 0 0.2rem 0; font-size: 1rem; font-weight: 600; color: #0f172a; }
        .nuevo-pedido-page .flower-card .flower-info .flower-meta { font-size: 0.8rem; color: #64748b; margin-bottom: 0.15rem; }
        .nuevo-pedido-page .flower-card .flower-price { font-weight: 700; color: #0f172a; font-size: 1.05rem; white-space: nowrap; }
        .nuevo-pedido-page .flower-card .flower-price small { font-weight: 400; color: #64748b; font-size: 0.75rem; }
        .nuevo-pedido-page .flower-card .qty-wrap { display: flex; align-items: center; gap: 0.5rem; }
        .nuevo-pedido-page .flower-card .form-control.qty-input { width: 70px; text-align: center; border-radius: 10px; font-weight: 600; border: 1px solid #e2e8f0; }
        .nuevo-pedido-page .badge-stock { padding: 0.35rem 0.65rem; border-radius: 8px; font-weight: 600; font-size: 0.7rem; }
        .nuevo-pedido-page .section-block { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; }
        .nuevo-pedido-page .section-block h5 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }
        .nuevo-pedido-page .section-block h5 i { color: var(--np-purple); margin-right: 0.5rem; }
        .nuevo-pedido-page .payment-method { border: 2px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; cursor: pointer; transition: all 0.2s ease; text-align: center; background: #fff; }
        .nuevo-pedido-page .payment-method:hover { border-color: var(--np-purple); background: #f8fafc; }
        .nuevo-pedido-page .payment-method.active, .nuevo-pedido-page .payment-method.selected { border-color: var(--np-purple); background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); }
        .nuevo-pedido-page .qr-container { text-align: center; padding: 1.5rem; border: 2px dashed var(--np-purple); border-radius: 12px; margin-top: 1rem; background: #faf5ff; }
        .nuevo-pedido-page .total-summary { background: #fff; border: 1px solid #e2e8f0; border-radius: var(--np-radius); padding: 1.5rem; position: sticky; top: 1.25rem; box-shadow: var(--np-shadow); }
        .nuevo-pedido-page .total-summary h5 { font-size: 1.05rem; margin-bottom: 1rem; color: #0f172a; }
        .nuevo-pedido-page .total-summary .total-row { background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-radius: 10px; padding: 1rem 1.25rem; margin: 1rem 0; border: 1px solid rgba(102, 126, 234, 0.2); }
        .nuevo-pedido-page .total-summary .total-row .total-label { font-weight: 600; color: #0f172a; }
        .nuevo-pedido-page .total-summary .total-row .total-value { font-size: 1.5rem; font-weight: 800; color: var(--np-purple); }
        .nuevo-pedido-page .btn-purple { background: linear-gradient(135deg, var(--np-purple) 0%, #764ba2 100%); color: #fff; border: none; padding: 0.9rem 1.25rem; font-weight: 600; border-radius: 12px; box-shadow: 0 4px 14px rgba(102, 126, 234, 0.35); transition: all 0.2s ease; width: 100%; }
        .nuevo-pedido-page .btn-purple:hover:not(:disabled) { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
        .nuevo-pedido-page .btn-purple:disabled { opacity: 0.6; cursor: not-allowed; }
        .nuevo-pedido-page .btn-cancel-link { color: #64748b; text-decoration: none; font-size: 0.9rem; margin-top: 0.75rem; display: inline-block; }
        .nuevo-pedido-page .btn-cancel-link:hover { color: var(--np-purple); }
        @media (max-width: 768px) {
            .nuevo-pedido-page .total-summary { position: fixed; bottom: 0; left: 0; right: 0; top: auto; z-index: 1000; border-radius: 16px 16px 0 0; padding: 1rem; box-shadow: 0 -4px 20px rgba(0,0,0,0.1); }
            .nuevo-pedido-page .total-summary h5, .nuevo-pedido-page #selectedFlowersSummary, .nuevo-pedido-page .total-summary hr, .nuevo-pedido-page .total-summary .d-flex.justify-content-between.mb-2 { display: none !important; }
            .nuevo-pedido-page .total-summary .total-row { margin: 0.5rem 0; padding: 0.75rem; }
            /* Margen inferior para que el contenido baje y no quede tapado por la barra fija (flores + entrega + método de pago + Nequi) */
            .nuevo-pedido-page .col-lg-8 { margin-bottom: 200px; padding-bottom: 1rem; }
            .nuevo-pedido-page .flower-card { flex-direction: column; align-items: stretch; }
            .nuevo-pedido-page .flower-card .flower-icon { align-self: flex-start; }
        }
    </style>
</head>
<body class="cliente-theme">
    <div class="dashboard-container nuevo-pedido-page">
        <?php $navbar_volver_url = 'index.php?ctrl=cliente&action=dashboard'; $usuario = $_SESSION['user'] ?? []; include __DIR__ . '/partials/navbar_cliente.php'; ?>

        <div class="main-content">
            <div class="page-title-card">
                <h1><i class="fas fa-plus-circle me-2" style="color: #667eea;"></i> Nuevo Pedido</h1>
                <p>Elige las flores, indica la dirección y el método de pago. Envío gratis.</p>
            </div>
            <div class="container-fluid px-0">
                <!-- Mensajes del sistema -->
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?? 'info' ?> alert-dismissible fade show mb-4 shadow-sm" style="border-radius: 12px;">
                        <i class="fas fa-info-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['mensaje']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-leaf me-2"></i> Seleccionar Flores
                            </div>
                            <div class="card-body">
                                <form id="pedidoForm" method="POST" action="index.php?ctrl=cliente&action=procesar_pedido" enctype="multipart/form-data">
                                    <div class="search-wrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-search"></i>
                                            <input type="text" class="form-control" id="flowerSearch" placeholder="Buscar por nombre, color o descripción..." onkeyup="filterFlowers()">
                                        </div>
                                    </div>

                                    <div id="floresContainer">
                                        <?php if (!empty($flores_disponibles)): ?>
                                            <?php foreach ($flores_disponibles as $flor): ?>
                                                <div class="flower-card position-relative <?= $flor['stock'] <= 0 ? 'no-stock' : '' ?>" 
                                                     data-id="<?= $flor['idtflor'] ?>" 
                                                     data-price="<?= $flor['precio'] ?>"
                                                     data-name="<?= htmlspecialchars(strtolower($flor['nombre'])) ?>"
                                                     data-color="<?= htmlspecialchars(strtolower($flor['color'] ?? '')) ?>"
                                                     data-desc="<?= htmlspecialchars(strtolower($flor['descripcion'] ?? '')) ?>"
                                                     data-stock="<?= $flor['stock'] ?>">
                                                    <div class="flower-icon">
                                                        <i class="fas fa-seedling"></i>
                                                    </div>
                                                    <div class="form-check mb-0 align-self-center">
                                                        <input type="checkbox" class="form-check-input flower-checkbox" name="flor_<?= $flor['idtflor'] ?>" id="chk_<?= $flor['idtflor'] ?>" onchange="toggleFlowerSelection(<?= $flor['idtflor'] ?>)" <?= $flor['stock'] <= 0 ? 'disabled' : '' ?>
                                                        >
                                                    </div>
                                                    <div class="flower-info">
                                                        <h6><?= htmlspecialchars($flor['nombre']) ?></h6>
                                                        <div class="flower-meta"><i class="fas fa-palette me-1"></i><?= htmlspecialchars($flor['color'] ?? 'N/A') ?></div>
                                                        <?php if (!empty($flor['descripcion'])): ?>
                                                            <div class="flower-meta text-truncate"><?= htmlspecialchars($flor['descripcion']) ?></div>
                                                        <?php endif; ?>
                                                        <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                                                            <?php if ($flor['stock'] <= 0): ?>
                                                                <span class="badge bg-danger badge-stock"><i class="fas fa-times-circle me-1"></i> Agotado</span>
                                                            <?php elseif ($flor['stock'] <= 15): ?>
                                                                <span class="badge bg-warning text-dark badge-stock"><i class="fas fa-exclamation-triangle me-1"></i> ¡Últimas <?= $flor['stock'] ?>!</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-success badge-stock"><i class="fas fa-check-circle me-1"></i> Disponible</span>
                                                            <?php endif; ?>
                                                            <span class="small text-muted"><?= (int)$flor['stock'] ?> en stock</span>
                                                        </div>
                                                    </div>
                                                    <div class="qty-wrap">
                                                        <input type="number" class="form-control qty-input quantity-input" name="cantidad_<?= $flor['idtflor'] ?>" id="cantidad_<?= $flor['idtflor'] ?>" value="0" min="0" max="<?= $flor['stock'] ?>" onchange="updateTotal()" disabled>
                                                    </div>
                                                    <div class="flower-price text-end">
                                                        $<?= number_format($flor['precio'], 2) ?>
                                                        <br><small>unidad</small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="fas fa-seedling fa-3x text-muted mb-3 opacity-50"></i>
                                                <h5 class="text-muted">No hay flores disponibles en este momento</h5>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="section-block">
                                        <h5><i class="fas fa-truck"></i> Información de Entrega</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="direccion_entrega" class="form-label small fw-bold text-muted">Dirección de Entrega</label>
                                                <textarea class="form-control" id="direccion_entrega" name="direccion_entrega" rows="2" placeholder="Calle, número, barrio..." required></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="fecha_entrega" class="form-label small fw-bold text-muted">Fecha de Entrega</label>
                                                <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                                                <small class="text-muted">Puedes elegir desde hoy en adelante</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-block">
                                        <h5><i class="fas fa-credit-card"></i> Método de Pago</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="payment-method active" id="method_efectivo" onclick="selectPaymentMethod('efectivo')">
                                                    <input type="radio" name="metodo_pago" value="efectivo" id="pay_efectivo" checked required>
                                                    <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                    <h6 class="mb-1">Efectivo</h6>
                                                    <p class="small text-muted mb-0">Pago contra entrega</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="payment-method" id="method_nequi" onclick="selectPaymentMethod('nequi')">
                                                    <input type="radio" name="metodo_pago" value="nequi" id="pay_nequi">
                                                    <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                                    <h6 class="mb-1">Nequi</h6>
                                                    <p class="small text-muted mb-0">Transferencia electrónica</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="qrContainer" class="qr-container" style="display: none;">
                                            <h6 class="fw-bold"><i class="fas fa-qrcode me-2"></i> Escanea para pagar</h6>
                                            <img src="assets/images/qr/qr_transferencia.png" alt="Nequi QR" class="img-fluid rounded mb-2" style="max-height: 180px;">
                                            <p class="x-small text-muted mb-0">Envía el comprobante por WhatsApp al finalizar.</p>
                                        </div>

                                        <div id="referenceContainer" class="mt-4 border-top pt-3" style="display: none;">
                                            <h6 class="fw-bold mb-3"><i class="fas fa-file-invoice-dollar me-2" style="color: #667eea;"></i> Detalles del Pago</h6>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Número de Referencia / Teléfono Origen</label>
                                                <input type="text" name="referencia_pago" class="form-control form-control-sm" placeholder="Ej: 12345678 o 312...">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold">Captura del Comprobante (Opcional)</label>
                                                <input type="file" name="comprobante" class="form-control form-control-sm" accept="image/*">
                                                <p class="x-small text-muted mt-1 mb-0">Sube una captura de pantalla de tu transferencia para verificarla más rápido.</p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="total-summary">
                            <h5><i class="fas fa-shopping-cart me-2"></i> Resumen del Pedido</h5>
                            <div id="selectedFlowersSummary" class="mb-3">
                                <p class="text-muted small mb-0">No has seleccionado flores aún...</p>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Subtotal</span>
                                <span id="summarySubtotal" class="fw-semibold">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Envío</span>
                                <span class="text-success fw-semibold">Gratis</span>
                            </div>
                            <div class="total-row d-flex justify-content-between align-items-center">
                                <span class="total-label">Total a Pagar</span>
                                <span id="summaryTotal" class="total-value">$0.00</span>
                            </div>
                            <button type="submit" form="pedidoForm" class="btn btn-purple" id="submitBtn" disabled>
                                Realizar Pedido <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <a href="index.php?ctrl=cliente&action=dashboard" class="btn-cancel-link">
                                <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFlowers = {};

        function filterFlowers() {
            const term = document.getElementById('flowerSearch').value.toLowerCase();
            document.querySelectorAll('.flower-card').forEach(card => {
                const searchData = (card.dataset.name + card.dataset.color + card.dataset.desc).toLowerCase();
                card.style.display = searchData.includes(term) ? 'block' : 'none';
            });
        }

        function toggleFlowerSelection(id) {
            const chk = document.getElementById(`chk_${id}`);
            const input = document.getElementById(`cantidad_${id}`);
            const card = document.querySelector(`[data-id="${id}"]`);
            
            if (chk.checked) {
                input.disabled = false;
                if (input.value == 0) input.value = 1;
                card.classList.add('selected');
            } else {
                input.disabled = true;
                input.value = 0;
                card.classList.remove('selected');
            }
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            let summaryHtml = '';
            let count = 0;

            document.querySelectorAll('.flower-checkbox:checked').forEach(chk => {
                const id = chk.id.split('_')[1];
                const input = document.getElementById(`cantidad_${id}`);
                const card = input.closest('.flower-card');
                const price = parseFloat(card.dataset.price);
                const name = card.querySelector('h6').textContent;
                const qty = parseInt(input.value) || 0;
                
                if (qty > 0) {
                    const sub = qty * price;
                    total += sub;
                    summaryHtml += `
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>${name} x${qty}</span>
                            <span>$${sub.toLocaleString('es-CO', {minimumFractionDigits: 2})}</span>
                        </div>
                    `;
                    count++;
                }
            });

            document.getElementById('selectedFlowersSummary').innerHTML = summaryHtml || '<p class="text-muted small italic">No has seleccionado flores aún...</p>';
            document.getElementById('summarySubtotal').textContent = `$${total.toLocaleString('es-CO', {minimumFractionDigits: 2})}`;
            document.getElementById('summaryTotal').textContent = `$${total.toLocaleString('es-CO', {minimumFractionDigits: 2})}`;
            
            document.getElementById('submitBtn').disabled = total === 0;
        }

        function selectPaymentMethod(method) {
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
            document.getElementById(`pay_${method}`).checked = true;
            document.getElementById(`method_${method}`).classList.add('active');
            
            document.getElementById('qrContainer').style.display = (method === 'nequi') ? 'block' : 'none';
            document.getElementById('referenceContainer').style.display = (method === 'nequi') ? 'block' : 'none';
        }

        document.getElementById('pedidoForm').addEventListener('submit', function(e) {
            const fecha = document.getElementById('fecha_entrega').value;
            const payMethod = document.querySelector('input[name="metodo_pago"]:checked');
            
            if (!fecha || !payMethod) {
                e.preventDefault();
                alert('Por favor completa todos los campos requeridos (Fecha y Método de Pago).');
                return;
            }

            // Validar cantidades: debe ser > 0 y no mayor al stock disponible
            let errorCantidad = '';
            document.querySelectorAll('.flower-checkbox:checked').forEach(function(chk) {
                const id = chk.id.split('_')[1];
                const input = document.getElementById('cantidad_' + id);
                const card = document.querySelector('.flower-card[data-id="' + id + '"]');
                const qty = parseInt(input.value, 10) || 0;
                const stock = parseInt(card ? card.dataset.stock : 0, 10) || 0;
                const nombre = card ? (card.querySelector('h6') && card.querySelector('h6').textContent) : '';
                if (qty <= 0) {
                    errorCantidad = 'La cantidad de "' + (nombre || 'producto') + '" debe ser mayor a 0.';
                    return;
                }
                if (qty > stock) {
                    errorCantidad = 'La cantidad de "' + (nombre || 'producto') + '" no puede ser mayor al stock disponible (' + stock + ').';
                    return;
                }
            });
            if (errorCantidad) {
                e.preventDefault();
                alert(errorCantidad);
                return;
            }

            if (!confirm('¿Estás seguro de realizar este pedido?')) {
                e.preventDefault();
            }
        });

        // Clic en toda la tarjeta selecciona/deselecciona el producto (excepto en el checkbox y en el input de cantidad)
        document.getElementById('floresContainer').addEventListener('click', function(e) {
            const card = e.target.closest('.flower-card');
            if (!card || card.classList.contains('no-stock')) return;
            if (e.target.closest('.flower-checkbox') || e.target.closest('.quantity-input')) return;
            e.preventDefault();
            const id = card.dataset.id;
            const chk = document.getElementById('chk_' + id);
            chk.checked = !chk.checked;
            toggleFlowerSelection(id);
        });

        // Configurar fecha mínima: desde hoy en adelante
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            document.getElementById('fecha_entrega').min = today.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
<?php endif; ?>