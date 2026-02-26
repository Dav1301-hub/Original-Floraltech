<?php
// Permitir renderizado como fragmento para el modal admin
$isFragment = isset($_GET['fragment']) && $_GET['fragment'] == '1';

// Verificar usuario solo si no es fragmento (cliente normal)
if (!$isFragment) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 5) {
        header('Location: index.php?ctrl=login&action=index');
        exit();
    }
}

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
    
    // Log para debugging
    error_log("Flores disponibles encontradas: " . count($flores_disponibles));
    if (empty($flores_disponibles)) {
        error_log("No se encontraron flores con stock disponible");
    }
    
} catch (PDOException $e) {
    $flores_disponibles = [];
    error_log("Error obteniendo flores: " . $e->getMessage());
}
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
    <style>
        .flower-card { border: 1px solid #e0e0e0; border-radius: 10px; padding: 15px; margin-bottom: 15px; transition: all 0.3s ease; position: relative; }
        .flower-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-color: #007bff; }
        .flower-card.selected { border-color: #28a745; background-color: #f8fff9; }
        .payment-method { border: 2px solid #e0e0e0; border-radius: 10px; padding: 20px; margin: 10px 0; cursor: pointer; transition: all 0.3s ease; }
        .payment-method:hover { border-color: #007bff; }
        .payment-method.selected { border-color: #28a745; background-color: #f8fff9; }
        .qr-container { text-align: center; padding: 20px; border: 2px dashed #007bff; border-radius: 10px; margin: 20px 0; }
        .total-summary { background-color: #f8f9fa; border-radius: 10px; padding: 20px; position: sticky; top: 20px; }
        .quantity-control { display: flex; align-items: center; gap: 10px; }
        .quantity-control input { width: 60px; text-align: center; }
        .no-stock { opacity: 0.6; background-color: #f8f9fa; border-color: #e0e0e0 !important; }
        .no-stock:hover { box-shadow: none !important; cursor: not-allowed; }
        #flowerSearch { border-radius: 20px; padding: 10px 15px; border: 1px solid #ced4da; transition: all 0.3s ease; }
        #flowerSearch:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fas fa-seedling"></i>
                FloralTech
            </div>
            <div class="navbar-user">
                <div class="user-info">
                    <p class="user-name">Nuevo Pedido</p>
                    <p class="user-welcome">Selecciona tus flores favoritas</p>
                </div>
                <a href="index.php?ctrl=cliente&action=dashboard" class="logout-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
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
        <div class="row">
            <!-- Columna principal - Selección de flores -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-leaf"></i> Seleccionar Flores
                        </div>
                        <div class="w-50">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="flowerSearch" 
                                placeholder="Buscar flores por nombre, color o descripción..."
                                onkeyup="filterFlowers()"
                            >
                        </div>
                    </div>
                    <div class="card-body">
<?php endif; ?>
                        <form id="pedidoForm" method="POST" action="index.php?ctrl=cliente&action=procesar_pedido">
                            <?php if (!empty($flores_disponibles)): ?>
                                <div id="floresContainer">
                                    <?php foreach ($flores_disponibles as $flor): ?>
                                        <div class="flower-card <?= $flor['stock'] <= 0 ? 'no-stock' : '' ?>" 
                                             data-id="<?= $flor['idtflor'] ?>" 
                                             data-price="<?= $flor['precio'] ?>"
                                             data-name="<?= htmlspecialchars(strtolower($flor['nombre'])) ?>"
                                             data-color="<?= htmlspecialchars(strtolower($flor['color'] ?? '')) ?>"
                                             data-desc="<?= htmlspecialchars(strtolower($flor['descripcion'] ?? '')) ?>">
                                            
                                            <?php if ($flor['stock'] <= 0): ?>
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-danger">Sin stock</span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="row align-items-center">
                                                <div class="col-md-1">
                                                    <div class="form-check">
                                                        <input 
                                                            class="form-check-input flower-checkbox" 
                                                            type="checkbox" 
                                                            name="flor_<?= $flor['idtflor'] ?>"
                                                            id="flor_<?= $flor['idtflor'] ?>"
                                                            onchange="toggleFlowerSelection(<?= $flor['idtflor'] ?>)"
                                                            <?= $flor['stock'] <= 0 ? 'disabled' : '' ?>
                                                        >
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1"><?= htmlspecialchars($flor['nombre']) ?></h6>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-palette"></i> 
                                                        <?= htmlspecialchars($flor['color'] ?? 'Sin color específico') ?>
                                                    </p>
                                                    <small class="text-muted"><?= htmlspecialchars($flor['descripcion'] ?? 'Sin descripción') ?></small>
                                                </div>
                                                <div class="col-md-2">
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-boxes"></i> 
                                                        <?= $flor['stock'] ?> disponibles
                                                    </span>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="text-center">
                                                        <strong class="text-success">$<?= number_format($flor['precio'], 2) ?></strong>
                                                        <br><small class="text-muted">por unidad</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="quantity-control">
                                                        <input 
                                                            type="number" 
                                                            name="cantidad_<?= $flor['idtflor'] ?>"
                                                            id="cantidad_<?= $flor['idtflor'] ?>"
                                                            class="form-control quantity-input"
                                                            min="0" 
                                                            max="<?= $flor['stock'] ?>" 
                                                            value="0"
                                                            disabled
                                                            onchange="updateTotal()"
                                                            style="margin-left: -20px; margin-top: 0px;" 
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Información de entrega -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <i class="fas fa-truck"></i> Información de Entrega
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="direccion_entrega" class="form-label">Dirección de Entrega</label>
                                                <textarea 
                                                    class="form-control" 
                                                    id="direccion_entrega" 
                                                    name="direccion_entrega" 
                                                    rows="3" 
                                                    placeholder="Ingrese la dirección completa de entrega"
                                                    required
                                                ></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="fecha_entrega" class="form-label">Fecha de Entrega Deseada</label>
                                                <?php
                                                    $fechaPreseleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : '';
                                                    $minFecha = date('Y-m-d', strtotime('+1 day'));
                                                    if ($fechaPreseleccionada && $fechaPreseleccionada > date('Y-m-d')) {
                                                        $minFecha = $fechaPreseleccionada;
                                                    }
                                                ?>
                                                <input 
                                                    type="date" 
                                                    class="form-control" 
                                                    id="fecha_entrega" 
                                                    name="fecha_entrega"
                                                    min="<?= $minFecha ?>"
                                                    value="<?= htmlspecialchars($fechaPreseleccionada) ?>"
                                                    required
                                                >
                                                <small class="text-muted">Mínimo 24 horas de anticipación</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Método de pago -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <i class="fas fa-credit-card"></i> Método de Pago
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="payment-method" onclick="selectPaymentMethod('efectivo')">
                                                    <input type="radio" name="metodo_pago" value="efectivo" id="efectivo" hidden>
                                                    <div class="text-center">
                                                        <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                        <h6>Pago en Efectivo</h6>
                                                        <p class="text-muted small">Pago contra entrega</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="payment-method" onclick="selectPaymentMethod('nequi')">
                                                    <input type="radio" name="metodo_pago" value="nequi" id="nequi" hidden>
                                                    <div class="text-center">
                                                        <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                                        <h6>Transferencia Nequi</h6>
                                                        <p class="text-muted small">Pago por código QR</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- QR Code Container (inicialmente oculto) -->
                                        <div id="qrContainer" class="qr-container" style="display: none;">
                                            <h5><i class="fas fa-qrcode"></i> Código QR para Transferencia Nequi</h5>
                                            <img src="assets/images/qr/qr_transferencia.png" alt="QR Nequi" class="img-fluid" style="max-width: 200px;">
                                            <p class="mt-3">
                                                <strong>Instrucciones:</strong><br>
                                                1. Abre tu app Nequi<br>
                                                2. Escanea este código QR<br>
                                                3. Confirma el pago por el monto mostrado<br>
                                                4. Envía el comprobante si es necesario
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                                    <h5>No hay flores disponibles</h5>
                                    <p class="text-muted">Actualmente no tenemos flores en stock. Por favor, vuelve más tarde.</p>
                                    <a href="index.php?ctrl=cliente&action=dashboard" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Columna lateral - Resumen del pedido -->
            <div class="col-md-4">
                <div class="total-summary">
                    <h5><i class="fas fa-receipt"></i> Resumen del Pedido</h5>
                    
                    <div id="selectedFlowers">
                        <p class="text-muted">No has seleccionado flores aún</p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span id="subtotalAmount">$0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span>Entrega:</span>
                        <span>Gratis</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong id="totalAmount">$0.00</strong>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" form="pedidoForm" class="btn btn-success w-100" id="submitBtn" disabled>
                            <i class="fas fa-shopping-cart"></i> Realizar Pedido
                        </button>
                    </div>
                    
                    <div class="mt-2">
                        <a href="index.php?ctrl=cliente&action=dashboard" class="btn btn-outline w-100">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFlowers = {};
        let totalAmount = 0;

        function filterFlowers() {
            const searchTerm = document.getElementById('flowerSearch').value.toLowerCase();
            const flowerCards = document.querySelectorAll('.flower-card');
            
            flowerCards.forEach(card => {
                const name = card.dataset.name;
                const color = card.dataset.color;
                const desc = card.dataset.desc;
                
                if (name.includes(searchTerm) || 
                    color.includes(searchTerm) || 
                    desc.includes(searchTerm) ||
                    searchTerm === '') {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function toggleFlowerSelection(flowerId) {
            const checkbox = document.getElementById(`flor_${flowerId}`);
            const quantityInput = document.getElementById(`cantidad_${flowerId}`);
            const flowerCard = document.querySelector(`[data-id="${flowerId}"]`);
            const maxStock = parseInt(quantityInput.max);
            
            if (checkbox.checked && maxStock <= 0) {
                checkbox.checked = false;
                alert('Esta flor no tiene stock disponible');
                return;
            }
            
            if (checkbox.checked) {
                quantityInput.disabled = false;
                quantityInput.value = 1;
                quantityInput.focus();
                flowerCard.classList.add('selected');
            } else {
                quantityInput.disabled = true;
                quantityInput.value = 0;
                flowerCard.classList.remove('selected');
            }
            
            updateTotal();
        }

        function updateTotal() {
            selectedFlowers = {};
            totalAmount = 0;
            let hasInvalidQuantities = false;
            
            document.querySelectorAll('.flower-checkbox:checked').forEach(checkbox => {
                const flowerId = checkbox.name.replace('flor_', '');
                const quantityInput = document.getElementById(`cantidad_${flowerId}`);
                const flowerCard = document.querySelector(`[data-id="${flowerId}"]`);
                const price = parseFloat(flowerCard.dataset.price);
                const maxStock = parseInt(quantityInput.max);
                let quantity = parseInt(quantityInput.value) || 0;
                
                // Validar que no exceda el stock disponible
                if (quantity > maxStock) {
                    quantity = maxStock;
                    quantityInput.value = maxStock;
                    hasInvalidQuantities = true;
                }
                
                if (quantity > 0) {
                    const flowerName = flowerCard.querySelector('h6').textContent;
                    selectedFlowers[flowerId] = {
                        name: flowerName,
                        price: price,
                        quantity: quantity,
                        total: price * quantity
                    };
                    totalAmount += price * quantity;
                }
            });
            
            if (hasInvalidQuantities) {
                alert('Algunas cantidades se ajustaron al stock disponible');
            }
            
            updateSummary();
        }

        function updateSummary() {
            const selectedFlowersDiv = document.getElementById('selectedFlowers');
            const subtotalElement = document.getElementById('subtotalAmount');
            const totalElement = document.getElementById('totalAmount');
            const submitBtn = document.getElementById('submitBtn');
            
            if (Object.keys(selectedFlowers).length === 0) {
                selectedFlowersDiv.innerHTML = '<p class="text-muted">No has seleccionado flores aún</p>';
                submitBtn.disabled = true;
            } else {
                let html = '';
                Object.entries(selectedFlowers).forEach(([id, flower]) => {
                    html += `
                        <div class="d-flex justify-content-between mb-1">
                            <span>${flower.name} x${flower.quantity}</span>
                            <span>$${flower.total.toFixed(2)}</span>
                        </div>
                    `;
                });
                selectedFlowersDiv.innerHTML = html;
                submitBtn.disabled = false;
            }
            
            subtotalElement.textContent = `$${totalAmount.toFixed(2)}`;
            totalElement.textContent = `$${totalAmount.toFixed(2)}`;
        }

        function selectPaymentMethod(method) {
            // Remover selección anterior
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Seleccionar nuevo método
            document.querySelector(`input[value="${method}"]`).checked = true;
            document.querySelector(`input[value="${method}"]`).parentElement.classList.add('selected');
            
            // Mostrar/ocultar QR
            const qrContainer = document.getElementById('qrContainer');
            if (method === 'nequi') {
                qrContainer.style.display = 'block';
            } else {
                qrContainer.style.display = 'none';
            }
        }

        // Validación del formulario
        document.getElementById('pedidoForm').addEventListener('submit', function(e) {
            const selectedCount = Object.keys(selectedFlowers).length;
            const paymentMethod = document.querySelector('input[name="metodo_pago"]:checked');
            const direccion = document.getElementById('direccion_entrega').value.trim();
            const fecha = document.getElementById('fecha_entrega').value;
            
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos una flor');
                return;
            }
            
            if (!paymentMethod) {
                e.preventDefault();
                alert('Debe seleccionar un método de pago');
                return;
            }
            
            if (!direccion) {
                e.preventDefault();
                alert('Debe ingresar una dirección de entrega');
                return;
            }
            
            if (!fecha) {
                e.preventDefault();
                alert('Debe seleccionar una fecha de entrega');
                return;
            }
            
            // Confirmación final
            const confirmMessage = `¿Confirma el pedido por un total de $${totalAmount.toFixed(2)}?`;
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });

        // Establecer fecha mínima (mañana)
        document.addEventListener('DOMContentLoaded', function() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('fecha_entrega').min = tomorrow.toISOString().split('T')[0];
        });
    </script>
</body>
</html>