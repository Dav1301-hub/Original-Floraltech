<?php
// views/empleado/nuevo_pedido.php
// Formulario para crear pedido desde panel de empleado

$isModal = isset($_GET['modal']) && $_GET['modal'] == '1';
?>
<?php if (!$isModal): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido - FloralTech Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/dashboard-general.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .navbar {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        .flower-card { 
            border: 1px solid #e0e0e0; 
            border-radius: 10px; 
            padding: 15px; 
            margin-bottom: 15px; 
            transition: all 0.3s ease;
        }
        .flower-card:hover { 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            border-color: #28a745; 
        }
        .flower-card.selected { 
            border-color: #28a745; 
            background-color: #f8fff9; 
        }
        .flower-card.no-stock { 
            opacity: 0.6; 
            background-color: #f8f9fa; 
            border-color: #e0e0e0 !important; 
        }
        .quantity-control { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .quantity-control input { 
            width: 70px; 
            text-align: center; 
        }
        .total-summary { 
            background-color: #f8f9fa; 
            border-radius: 10px; 
            padding: 20px; 
            position: sticky; 
            top: 20px;
        }
        .badge-stock { 
            position: absolute; 
            top: 10px; 
            right: 10px; 
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
                <a href="index.php?ctrl=empleado&action=gestion_pedidos" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </nav>

        <div class="container-fluid mt-4 mb-4">
            <div class="row">
                <!-- Columna Principal - Flores -->
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-leaf"></i> Seleccionar Flores</h5>
                        </div>
                        <div class="card-body">
                            <form id="pedidoForm" method="POST" action="index.php?ctrl=empleado&action=crearPedidoEmpleado">
                                <!-- Cliente Selector -->
                                <div class="mb-4">
                                    <label for="cli_id" class="form-label"><strong>Cliente</strong></label>
                                    <select id="cli_id" name="cli_id" class="form-select" required>
                                        <option value="">-- Selecciona un cliente --</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?= $cliente['idcli'] ?>">
                                                <?= htmlspecialchars($cliente['nombre']) ?> (<?= $cliente['email'] ?? '' ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Búsqueda de Flores -->
                                <div class="mb-3">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="flowerSearch" 
                                        placeholder="🔍 Buscar flores..."
                                        onkeyup="filterFlowers()"
                                    >
                                </div>

                                <!-- Listado de Flores -->
                                <div id="floresContainer">
                                    <?php foreach ($flores as $flor): ?>
                                        <div class="flower-card position-relative <?= $flor['stock'] <= 0 ? 'no-stock' : '' ?>" 
                                             data-id="<?= $flor['idtflor'] ?>" 
                                             data-price="<?= $flor['precio'] ?>"
                                             data-name="<?= htmlspecialchars(strtolower($flor['nombre'])) ?>"
                                             data-stock="<?= $flor['stock'] ?>">
                                            
                                            <?php if ($flor['stock'] <= 0): ?>
                                                <span class="badge bg-danger badge-stock">Sin stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-info badge-stock">Stock: <?= $flor['stock'] ?></span>
                                            <?php endif; ?>
                                            
                                            <div class="row align-items-center">
                                                <div class="col-md-1">
                                                    <input 
                                                        type="checkbox" 
                                                        class="form-check-input flower-checkbox"
                                                        name="flores[<?= $flor['idtflor'] ?>][selected]"
                                                        value="1"
                                                        onchange="toggleFlowerSelection(<?= $flor['idtflor'] ?>)"
                                                        <?= $flor['stock'] <= 0 ? 'disabled' : '' ?>
                                                    >
                                                </div>
                                                <div class="col-md-7">
                                                    <h6 class="mb-1"><?= htmlspecialchars($flor['nombre']) ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($flor['color'] ?? '') ?></small>
                                                </div>
                                                <div class="col-md-2">
                                                    <input 
                                                        type="number" 
                                                        class="form-control form-control-sm quantity-input"
                                                        name="flores[<?= $flor['idtflor'] ?>][cantidad]"
                                                        value="0" 
                                                        min="0"
                                                        onchange="updateTotal()"
                                                        disabled
                                                    >
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <strong>$<?= number_format($flor['precio'], 2) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna Lateral - Resumen y Detalles -->
                <div class="col-md-4">
                    <div class="total-summary shadow">
                        <h5 class="mb-3"><i class="fas fa-shopping-cart"></i> Resumen</h5>
                        
                        <div class="mb-3">
                            <label for="direccion_entrega" class="form-label"><small><strong>Dirección de Entrega</strong></small></label>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                id="direccion_entrega"
                                name="direccion_entrega"
                                placeholder="Dirección..."
                                form="pedidoForm"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="fecha_entrega" class="form-label"><small><strong>Fecha de Entrega</strong></small></label>
                            <input 
                                type="date" 
                                class="form-control form-control-sm" 
                                id="fecha_entrega"
                                name="fecha_entrega"
                                form="pedidoForm"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="notas" class="form-label"><small><strong>Notas Adicionales</strong></small></label>
                            <textarea 
                                class="form-control form-control-sm" 
                                id="notas"
                                name="notas"
                                rows="3"
                                placeholder="Notas..."
                                form="pedidoForm"
                            ></textarea>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Subtotal:</small>
                                </div>
                                <div class="col-6 text-end">
                                    <strong id="subtotal">$0.00</strong>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small class="text-muted">Total:</small>
                                </div>
                                <div class="col-6 text-end">
                                    <h5 id="total">$0.00</h5>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="monto_total" name="monto_total" form="pedidoForm">

                        <button type="submit" form="pedidoForm" class="btn btn-success w-100" id="submitBtn" disabled>
                            <i class="fas fa-check"></i> Crear Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterFlowers() {
            const searchTerm = document.getElementById('flowerSearch').value.toLowerCase();
            const flowers = document.querySelectorAll('.flower-card');
            
            flowers.forEach(flower => {
                const name = flower.getAttribute('data-name');
                if (name.includes(searchTerm)) {
                    flower.style.display = 'block';
                } else {
                    flower.style.display = 'none';
                }
            });
        }

        function toggleFlowerSelection(florid) {
            const checkbox = document.querySelector(`input[name="flores[${florid}][selected]"]`);
            const quantityInput = document.querySelector(`input[name="flores[${florid}][cantidad]"]`);
            const card = document.querySelector(`[data-id="${florid}"]`);
            
            if (checkbox.checked) {
                quantityInput.disabled = false;
                quantityInput.value = 1;
                card.classList.add('selected');
            } else {
                quantityInput.disabled = true;
                quantityInput.value = 0;
                card.classList.remove('selected');
            }
            
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            const quantityInputs = document.querySelectorAll('.quantity-input:not(:disabled)');
            
            quantityInputs.forEach(input => {
                const quantity = parseInt(input.value) || 0;
                const card = input.closest('.flower-card');
                const price = parseFloat(card.getAttribute('data-price'));
                total += quantity * price;
            });
            
            document.getElementById('subtotal').textContent = '$' + total.toFixed(2);
            document.getElementById('total').textContent = '$' + total.toFixed(2);
            document.getElementById('monto_total').value = total;
            
            // Habilitar/deshabilitar botón submit
            const submitBtn = document.getElementById('submitBtn');
            const clientSelect = document.getElementById('cli_id');
            submitBtn.disabled = total === 0 || clientSelect.value === '';
        }

        // Validar cliente
        document.getElementById('cli_id').addEventListener('change', updateTotal);
    </script>
</body>
</html>
<?php else: ?>
<!-- Modo Modal: Solo el formulario -->
<form id="pedidoFormModal" method="POST" action="index.php?ctrl=empleado&action=crearPedidoEmpleado">
    <!-- Cliente -->
    <div class="mb-3">
        <label for="cli_id_modal" class="form-label">Cliente</label>
        <select id="cli_id_modal" name="cli_id" class="form-select form-select-sm" required>
            <option value="">Selecciona cliente...</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['idcli'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Flores (versión comprimida para modal) -->
    <div class="mb-3">
        <label class="form-label">Flores</label>
        <input type="text" class="form-control form-control-sm mb-2" id="flowerSearchModal" onkeyup="filterFlowersModal()" placeholder="Buscar...">
        <div id="floresContainerModal" style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($flores as $flor): ?>
                <div class="form-check flower-item-modal" data-name="<?= strtolower($flor['nombre']) ?>">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="flor_modal_<?= $flor['idtflor'] ?>"
                        name="flores[<?= $flor['idtflor'] ?>][selected]"
                        value="1"
                        <?= $flor['stock'] <= 0 ? 'disabled' : '' ?>
                    >
                    <label class="form-check-label small" for="flor_modal_<?= $flor['idtflor'] ?>">
                        <?= htmlspecialchars($flor['nombre']) ?> 
                        <span class="badge badge-sm bg-info ms-2">$<?= number_format($flor['precio'], 2) ?></span>
                    </label>
                    <input 
                        type="number" 
                        class="form-control form-control-sm mt-1"
                        name="flores[<?= $flor['idtflor'] ?>][cantidad]"
                        value="0"
                        min="0"
                        disabled
                    >
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="direccion_modal" class="form-label form-label-sm">Dirección</label>
        <input type="text" class="form-control form-control-sm" id="direccion_modal" name="direccion_entrega">
    </div>

    <div class="mb-3">
        <label for="fecha_modal" class="form-label form-label-sm">Fecha Entrega</label>
        <input type="date" class="form-control form-control-sm" id="fecha_modal" name="fecha_entrega">
    </div>

    <input type="hidden" id="monto_total_modal" name="monto_total" value="0">
    <button type="submit" class="btn btn-success btn-sm w-100">Crear Pedido</button>
</form>

<script>
function filterFlowersModal() {
    const term = document.getElementById('flowerSearchModal').value.toLowerCase();
    document.querySelectorAll('.flower-item-modal').forEach(item => {
        item.style.display = item.getAttribute('data-name').includes(term) ? 'block' : 'none';
    });
}
</script>
<?php endif; ?>
