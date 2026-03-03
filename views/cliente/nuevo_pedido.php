<?php
// views/cliente/nuevo_pedido.php
// Formulario para crear pedido desde panel de cliente - Estructura unificada

// Conectar a la base de datos para obtener flores
require_once __DIR__ . '/../../models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

// Obtener flores disponibles
try {
    $stmt = $db->prepare("
        SELECT 
            tf.idtflor,
            tf.nombre,
            tf.naturaleza as color,
            tf.descripcion,
            tf.precio,
            COALESCE(i.cantidad_disponible, 0) as stock
        FROM tflor tf
        LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
        WHERE COALESCE(i.cantidad_disponible, 0) > 0
        ORDER BY tf.nombre
    ");
    $stmt->execute();
    $flores_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $flores_disponibles = [];
    error_log("Error obteniendo flores: " . $e->getMessage());
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
    <link rel="stylesheet" href="assets/css/dashboard-general.css">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #7b61ff;
            --secondary-purple: #6366f1;
            --accent-blue: #3b82f6;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
            --border-radius: 12px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .dashboard-container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            padding: 0.75rem 1.5rem;
            box-shadow: var(--shadow-md);
            border: none;
        }

        .main-content {
            padding: 1.5rem;
            width: 100%;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            background: white;
        }

        .flower-card { 
            border: 1px solid #f1f5f9; 
            border-radius: var(--border-radius); 
            padding: 1rem; 
            margin-bottom: 1rem; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            position: relative;
        }

        .flower-card:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.08); 
            border-color: var(--primary-purple); 
        }

        .flower-card.selected { 
            border-color: var(--primary-purple); 
            background-color: #f5f3ff; 
            box-shadow: 0 0 0 2px var(--primary-purple);
        }

        .flower-card.no-stock { 
            opacity: 0.7; 
            background-color: #f8fafc; 
            border-color: #e2e8f0 !important; 
            cursor: not-allowed;
        }

        .quantity-control input { 
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        .total-summary { 
            background: white; 
            border-radius: var(--border-radius); 
            padding: 1.5rem; 
            position: sticky; 
            top: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid #f1f5f9;
        }

        .badge-stock { 
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            box-shadow: var(--shadow-sm);
        }

        .text-purple {
            color: var(--primary-purple) !important;
        }

        .btn-purple {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(123, 97, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-purple:hover:not(:disabled) {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(123, 97, 255, 0.3);
        }

        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: border-color 0.3s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 3px rgba(123, 97, 255, 0.1);
        }
        
        .payment-method { 
            border: 2px solid #f1f5f9; 
            border-radius: var(--border-radius); 
            padding: 1rem; 
            cursor: pointer; 
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .payment-method:hover { border-color: var(--primary-purple); }
        .payment-method.selected { 
            border-color: var(--primary-purple); 
            background-color: #f5f3ff;
        }
        
        .qr-container { 
            text-align: center; 
            padding: 1.5rem; 
            border: 2px dashed var(--primary-purple); 
            border-radius: var(--border-radius); 
            margin-top: 1rem; 
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem 0.5rem;
            }

            .total-summary {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                top: auto;
                z-index: 1000;
                border-radius: 20px 20px 0 0;
                padding: 1rem;
                box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
            }

            .total-summary h5, #selectedFlowersSummary, hr, .d-flex.justify-content-between.mb-2 {
                display: none !important;
            }

            .total-summary .bg-light {
                margin-bottom: 0.5rem !important;
                padding: 0.5rem !important;
            }

            .total-summary h4 {
                font-size: 1.25rem;
            }

            .total-summary .btn-purple {
                padding: 0.6rem;
            }

            .col-md-8 {
                margin-bottom: 120px; /* Space for sticky summary */
            }

            .flower-card .row > div {
                margin-bottom: 0.5rem;
            }

            .flower-card .text-end {
                text-align: left !important;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <nav class="navbar">
            <div class="navbar-brand" style="color: white; font-weight: 600;">
                <i class="fas fa-seedling"></i> FloralTech - Nuevo Pedido
            </div>
            <div class="navbar-user">
                <a href="index.php?ctrl=cliente&action=dashboard" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </nav>

        <div class="main-content">
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

                <div class="row">
                    <!-- Columna Principal - Flores -->
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-leaf text-purple me-2"></i> Seleccionar Flores</h5>
                            </div>
                            <div class="card-body">
                                <form id="pedidoForm" method="POST" action="index.php?ctrl=cliente&action=procesar_pedido" enctype="multipart/form-data">
                                    <!-- Búsqueda de Flores -->
                                    <div class="mb-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                            <input 
                                                type="text" 
                                                class="form-control border-start-0" 
                                                id="flowerSearch" 
                                                placeholder="Buscar por nombre, color o descripción..."
                                                onkeyup="filterFlowers()"
                                            >
                                        </div>
                                    </div>

                                    <!-- Listado de Flores -->
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
                                                    
                                                    <div class="position-absolute top-0 end-0 mt-2 me-3">
                                                        <?php if ($flor['stock'] <= 0): ?>
                                                            <span class="badge bg-danger badge-stock"><i class="fas fa-times-circle me-1"></i> Agotado</span>
                                                        <?php elseif ($flor['stock'] <= 15): ?>
                                                            <span class="badge bg-warning text-dark badge-stock"><i class="fas fa-exclamation-triangle me-1"></i> ¡Últimas <?= $flor['stock'] ?> unidades!</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success badge-stock"><i class="fas fa-check-circle me-1"></i> Disponible</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div class="row align-items-center">
                                                        <div class="col-md-1">
                                                            <input 
                                                                type="checkbox" 
                                                                class="form-check-input flower-checkbox"
                                                                name="flor_<?= $flor['idtflor'] ?>"
                                                                id="chk_<?= $flor['idtflor'] ?>"
                                                                onchange="toggleFlowerSelection(<?= $flor['idtflor'] ?>)"
                                                                <?= $flor['stock'] <= 0 ? 'disabled' : '' ?>
                                                            >
                                                        </div>
                                                        <div class="col-md-7">
                                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($flor['nombre']) ?></h6>
                                                            <p class="text-muted mb-1 small"><i class="fas fa-palette me-1"></i><?= htmlspecialchars($flor['color'] ?? 'N/A') ?></p>
                                                            <p class="text-muted mb-0 x-small"><?= htmlspecialchars($flor['descripcion'] ?? '') ?></p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input 
                                                                type="number" 
                                                                class="form-control form-control-sm quantity-input"
                                                                name="cantidad_<?= $flor['idtflor'] ?>"
                                                                id="cantidad_<?= $flor['idtflor'] ?>"
                                                                value="0" 
                                                                min="0"
                                                                max="<?= $flor['stock'] ?>"
                                                                onchange="updateTotal()"
                                                                disabled
                                                            >
                                                        </div>
                                                        <div class="col-md-2 text-end">
                                                            <strong>$<?= number_format($flor['precio'], 2) ?></strong>
                                                            <br><small class="text-muted">unidad</small>
                                                        </div>
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

                                    <!-- Información de Entrega -->
                                    <div class="mt-5 pt-4 border-top">
                                        <h5 class="fw-bold mb-4"><i class="fas fa-truck text-purple me-2"></i> Información de Entrega</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="direccion_entrega" class="form-label small fw-bold text-muted uppercase">Dirección de Entrega</label>
                                                <textarea class="form-control" id="direccion_entrega" name="direccion_entrega" rows="2" placeholder="Ej: Calle 123 #45-67..." required></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="fecha_entrega" class="form-label small fw-bold text-muted uppercase">Fecha de Entrega</label>
                                                <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                                                <small class="text-muted x-small">Mínimo 24 horas de anticipación</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Método de Pago -->
                                    <div class="mt-5 pt-4 border-top">
                                        <h5 class="fw-bold mb-4"><i class="fas fa-credit-card text-purple me-2"></i> Método de Pago</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="payment-method active" id="method_efectivo" onclick="selectPaymentMethod('efectivo')">
                                                    <input type="radio" name="metodo_pago" value="efectivo" id="pay_efectivo" hidden required>
                                                    <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                    <h6>Efectivo</h6>
                                                    <p class="x-small text-muted mb-0">Pago contra entrega</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="payment-method" id="method_nequi" onclick="selectPaymentMethod('nequi')">
                                                    <input type="radio" name="metodo_pago" value="nequi" id="pay_nequi" hidden>
                                                    <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                                    <h6>Nequi</h6>
                                                    <p class="x-small text-muted mb-0">Transferencia electrónica</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="qrContainer" class="qr-container" style="display: none;">
                                            <h6 class="fw-bold"><i class="fas fa-qrcode me-2"></i> Escanea para pagar</h6>
                                            <img src="assets/images/qr/qr_transferencia.png" alt="Nequi QR" class="img-fluid rounded mb-2" style="max-height: 180px;">
                                            <p class="x-small text-muted mb-0">Envía el comprobante por WhatsApp al finalizar.</p>
                                        </div>

                                        <div id="referenceContainer" class="mt-4 border-top pt-3" style="display: none;">
                                            <h6 class="fw-bold mb-3"><i class="fas fa-file-invoice-dollar me-2 text-purple"></i> Detalles del Pago</h6>
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

                    <!-- Columna Lateral - Resumen -->
                    <div class="col-md-4">
                        <div class="total-summary shadow-md">
                            <h5 class="mb-4 fw-bold"><i class="fas fa-shopping-cart text-purple me-2"></i> Resumen del Pedido</h5>
                            
                            <div id="selectedFlowersSummary" class="mb-4">
                                <p class="text-muted small italic">No has seleccionado flores aún...</p>
                            </div>

                            <hr class="my-3 opacity-10">
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span id="summarySubtotal" class="fw-bold">$0.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted small">Envío</span>
                                <span class="text-success small fw-bold">Gratis</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-4">
                                <span class="fw-bold">Total a Pagar</span>
                                <h4 id="summaryTotal" class="mb-0 fw-bold text-purple">$0.00</h4>
                            </div>

                            <button type="submit" form="pedidoForm" class="btn btn-purple w-100 py-3" id="submitBtn" disabled>
                                Realizar Pedido <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            
                            <a href="index.php?ctrl=cliente&action=dashboard" class="btn btn-link w-100 mt-2 text-muted text-decoration-none small">
                                <i class="fas fa-times me-1"></i> Cancelar
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

            if (!confirm('¿Estás seguro de realizar este pedido?')) {
                e.preventDefault();
            }
        });

        // Configurar fecha mínima
        document.addEventListener('DOMContentLoaded', function() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('fecha_entrega').min = tomorrow.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
<?php endif; ?>