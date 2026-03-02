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
    <link rel="stylesheet" href="assets/css/dashboard-general.css">
    <link rel="stylesheet" href="assets/css/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #28a745;
            --secondary-green: #20c997;
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
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
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
            transition: transform 0.3s ease;
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
            border-color: var(--primary-green); 
        }

        .flower-card.selected { 
            border-color: var(--primary-green); 
            background-color: #f0fdf4; 
            box-shadow: 0 0 0 2px var(--primary-green);
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
            font-weight: 500;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
            transition: all 0.3s ease;
        }

        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
        }

        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: border-color 0.3s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
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

        <div class="main-content">
            <div class="container-fluid px-0">
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
                                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($flor['nombre']) ?></h6>
                                                    <p class="text-muted mb-0 small"><i class="fas fa-tags me-1"></i><?= htmlspecialchars($flor['color'] ?? '') ?></p>
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
                                    <span class="text-muted">Subtotal</span>
                                </div>
                                <div class="col-6 text-end">
                                    <span id="subtotal" class="fw-bold">$0.00</span>
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
        <label for="cli_id_modal" class="form-label fw-bold">Cliente</label>
        <select id="cli_id_modal" name="cli_id" class="form-select" required>
            <option value="">Selecciona cliente...</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['idcli'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Flores (versión comprimida para modal) -->
    <div class="mb-3">
        <label class="form-label fw-bold">Flores</label>
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0" id="flowerSearchModal" onkeyup="filterFlowersModal()" placeholder="Filtrar flores...">
        </div>
        <div id="floresContainerModal" class="border rounded-3 p-2 bg-light" style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($flores as $flor): ?>
                <div class="p-2 mb-2 bg-white rounded-2 shadow-sm flower-item-modal" data-name="<?= strtolower($flor['nombre']) ?>">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="form-check mb-0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="flor_modal_<?= $flor['idtflor'] ?>"
                                name="flores[<?= $flor['idtflor'] ?>][selected]"
                                value="1"
                                <?= $flor['stock'] <= 0 ? 'disabled' : '' ?>
                                onchange="this.parentElement.parentElement.nextElementSibling.disabled = !this.checked; if(this.checked && this.parentElement.parentElement.nextElementSibling.value == 0) this.parentElement.parentElement.nextElementSibling.value = 1;"
                            >
                            <label class="form-check-label small" for="flor_modal_<?= $flor['idtflor'] ?>">
                                <strong><?= htmlspecialchars($flor['nombre']) ?></strong>
                            </label>
                        </div>
                        <span class="badge rounded-pill bg-light text-dark border">$<?= number_format($flor['precio'], 2) ?></span>
                    </div>
                    <input 
                        type="number" 
                        class="form-control form-control-sm mt-2"
                        name="flores[<?= $flor['idtflor'] ?>][cantidad]"
                        value="0"
                        min="0"
                        placeholder="Cant."
                        disabled
                    >
                    <?php if ($flor['stock'] <= 0): ?>
                        <div class="text-danger x-small mt-1"><i class="fas fa-times-circle me-1"></i>Sin stock</div>
                    <?php else: ?>
                        <div class="text-success x-small mt-1"><i class="fas fa-check-circle me-1"></i>Stock: <?= $flor['stock'] ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <label for="direccion_modal" class="form-label small fw-bold">Dirección</label>
            <input type="text" class="form-control form-control-sm" id="direccion_modal" name="direccion_entrega" placeholder="Ej: Calle 123...">
        </div>
        <div class="col-6">
            <label for="fecha_modal" class="form-label small fw-bold">Fecha Entrega</label>
            <input type="date" class="form-control form-control-sm" id="fecha_modal" name="fecha_entrega">
        </div>
    </div>

    <input type="hidden" id="monto_total_modal" name="monto_total" value="0">
    <button type="submit" class="btn btn-success w-100 py-2">
        <i class="fas fa-plus-circle me-1"></i> Crear Pedido
    </button>
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
