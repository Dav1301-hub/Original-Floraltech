<?php
$isModal = isset($_GET['modal']) && $_GET['modal'] == '1';
$navbar_volver_url = 'index.php?ctrl=empleado&action=gestion_pedidos';
$navbar_volver_text = 'Volver a Pedidos';
$user = $user ?? $_SESSION['user'] ?? [];
$clientes = $clientes ?? [];
$flores = $flores ?? [];
if (!$isModal):
    $tipo_empleado = '';
    if (!empty($user['tpusu_idtpusu'])) {
        switch ((int)$user['tpusu_idtpusu']) {
            case 2: $tipo_empleado = 'Panel Vendedor'; break;
            case 3: $tipo_empleado = 'Panel Inventario'; break;
            case 4: $tipo_empleado = 'Panel Repartidor'; break;
            default: $tipo_empleado = 'Panel Empleado'; break;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido - FloralTech Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nuevo-pedido-page .content-card { margin-bottom: 1rem; }
        .flower-card {
            border: 1px solid var(--emp-border);
            border-radius: var(--emp-radius-sm);
            padding: 0.85rem 1rem;
            margin-bottom: 0.75rem;
            background: var(--emp-bg-card);
            transition: var(--emp-transition);
        }
        .flower-card:hover { border-color: var(--emp-primary); box-shadow: 0 2px 8px rgba(13,148,136,0.08); }
        .flower-card.selected { border-color: var(--emp-primary); background: var(--emp-primary-light); box-shadow: 0 0 0 2px var(--emp-primary); }
        .flower-card.no-stock { opacity: 0.7; cursor: not-allowed; }
        .total-summary-card {
            background: var(--emp-bg-card);
            border: 1px solid var(--emp-border);
            border-radius: var(--emp-radius-sm);
            padding: 1.25rem;
            position: sticky;
            top: 1rem;
            box-shadow: var(--emp-shadow);
        }
        .badge-stock { font-size: 0.75rem; padding: 0.35rem 0.6rem; }
    </style>
</head>
<body class="empleado-theme">
    <div class="dashboard-container">
        <?php include __DIR__ . '/partials/navbar_empleado.php'; ?>
        <div class="main-content">
            <div class="content-wrapper nuevo-pedido-page">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--emp-primary) 0%, var(--emp-primary-dark) 100%); color: #fff;">
                                <h5 class="mb-0"><i class="fas fa-leaf me-2"></i>Seleccionar productos</h5>
                            </div>
                            <div class="card-body">
                                <form id="pedidoForm" method="POST" action="index.php?ctrl=empleado&action=crearPedidoEmpleado">
                                    <div class="mb-4">
                                        <label for="cli_id" class="form-label fw-bold">Cliente</label>
                                        <select id="cli_id" name="cli_id" class="form-select" required>
                                            <option value="">— Selecciona un cliente —</option>
                                            <?php foreach ($clientes as $c): ?>
                                                <option value="<?= (int)$c['idcli'] ?>"><?= htmlspecialchars($c['nombre']) ?> <?= !empty($c['email']) ? '(' . htmlspecialchars($c['email']) . ')' : '' ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="flowerSearch" placeholder="Buscar por nombre..." onkeyup="filterFlowers()">
                                    </div>
                                    <div id="floresContainer">
                                        <?php foreach ($flores as $flor): ?>
                                            <div class="flower-card <?= ($flor['stock'] ?? 0) <= 0 ? 'no-stock' : '' ?>"
                                                 data-id="<?= (int)$flor['idtflor'] ?>"
                                                 data-price="<?= number_format((float)($flor['precio'] ?? 0), 2, '.', '') ?>"
                                                 data-name="<?= htmlspecialchars(mb_strtolower($flor['nombre'] ?? '')) ?>"
                                                 data-stock="<?= (int)($flor['stock'] ?? 0) ?>">
                                                <?php if (($flor['stock'] ?? 0) <= 0): ?>
                                                    <span class="badge bg-danger badge-stock">Sin stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info badge-stock">Stock: <?= (int)$flor['stock'] ?></span>
                                                <?php endif; ?>
                                                <input type="hidden" name="flores[<?= (int)$flor['idtflor'] ?>][precio]" value="<?= number_format((float)($flor['precio'] ?? 0), 2, '.', '') ?>">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-auto">
                                                        <input type="checkbox" class="form-check-input flower-checkbox"
                                                            name="flores[<?= (int)$flor['idtflor'] ?>][selected]" value="1"
                                                            onchange="toggleFlowerSelection(<?= (int)$flor['idtflor'] ?>)"
                                                            <?= ($flor['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                                                    </div>
                                                    <div class="col">
                                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($flor['nombre'] ?? '') ?></h6>
                                                        <?php if (!empty($flor['color'])): ?>
                                                            <small class="text-muted"><?= htmlspecialchars($flor['color']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-auto">
                                                        <input type="number" class="form-control form-control-sm quantity-input" style="width: 80px;"
                                                            name="flores[<?= (int)$flor['idtflor'] ?>][cantidad]" value="0" min="0" max="<?= max(0, (int)($flor['stock'] ?? 0)) ?>"
                                                            onchange="updateTotal()" disabled>
                                                    </div>
                                                    <div class="col-auto text-end">
                                                        <strong class="text-success">$<?= number_format((float)($flor['precio'] ?? 0), 2) ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="total-summary-card">
                            <h5 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Resumen</h5>
                            <div class="mb-3">
                                <label for="direccion_entrega" class="form-label small fw-bold">Dirección de entrega</label>
                                <input type="text" class="form-control form-control-sm" id="direccion_entrega" name="direccion_entrega" placeholder="Dirección..." form="pedidoForm">
                            </div>
                            <div class="mb-3">
                                <label for="fecha_entrega" class="form-label small fw-bold">Fecha de entrega</label>
                                <input type="date" class="form-control form-control-sm" id="fecha_entrega" name="fecha_entrega" form="pedidoForm">
                            </div>
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label small fw-bold">Método de pago</label>
                                <select class="form-select form-select-sm" id="metodo_pago" name="metodo_pago" form="pedidoForm">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado_pago" class="form-label small fw-bold">Estado del pago</label>
                                <select class="form-select form-select-sm" id="estado_pago" name="estado_pago" form="pedidoForm">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Completado">Completado</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notas" class="form-label small fw-bold">Notas</label>
                                <textarea class="form-control form-control-sm" id="notas" name="notas" rows="2" placeholder="Notas..." form="pedidoForm"></textarea>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Subtotal</span>
                                    <span id="subtotal" class="fw-bold">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-muted">Total</span>
                                    <h5 id="total" class="mb-0 text-success">$0.00</h5>
                                </div>
                            </div>
                            <input type="hidden" id="monto_total" name="monto_total" form="pedidoForm" value="0">
                            <button type="submit" form="pedidoForm" class="btn btn-success w-100" id="submitBtn" disabled>
                                <i class="fas fa-check me-2"></i>Crear pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterFlowers() {
            var term = document.getElementById('flowerSearch').value.toLowerCase();
            document.querySelectorAll('.flower-card').forEach(function(el) {
                el.style.display = el.getAttribute('data-name').indexOf(term) !== -1 ? 'block' : 'none';
            });
        }
        function toggleFlowerSelection(florid) {
            var q = document.querySelector('input[name="flores[' + florid + '][cantidad]"]');
            var card = document.querySelector('.flower-card[data-id="' + florid + '"]');
            var chk = document.querySelector('input[name="flores[' + florid + '][selected]"]');
            if (chk && chk.checked) {
                if (q) { q.disabled = false; q.value = 1; }
                if (card) card.classList.add('selected');
            } else {
                if (q) { q.disabled = true; q.value = 0; }
                if (card) card.classList.remove('selected');
            }
            updateTotal();
        }
        function updateTotal() {
            var total = 0;
            document.querySelectorAll('.quantity-input:not(:disabled)').forEach(function(input) {
                var q = parseInt(input.value, 10) || 0;
                var card = input.closest('.flower-card');
                if (card) total += q * parseFloat(card.getAttribute('data-price'));
            });
            document.getElementById('subtotal').textContent = '$' + total.toFixed(2);
            document.getElementById('total').textContent = '$' + total.toFixed(2);
            document.getElementById('monto_total').value = total.toFixed(2);
            var submitBtn = document.getElementById('submitBtn');
            var cli = document.getElementById('cli_id');
            submitBtn.disabled = total === 0 || !cli || cli.value === '';
        }
        document.getElementById('cli_id').addEventListener('change', updateTotal);

        document.getElementById('pedidoForm').addEventListener('submit', function(e) {
            var invalid = false;
            var msg = '';
            document.querySelectorAll('.flower-card:not(.no-stock)').forEach(function(card) {
                var id = card.getAttribute('data-id');
                var stock = parseInt(card.getAttribute('data-stock'), 10) || 0;
                var input = document.querySelector('input[name="flores[' + id + '][cantidad]"]');
                if (!input || input.disabled) return;
                var qty = parseInt(input.value, 10) || 0;
                if (qty <= 0) {
                    var nombre = card.querySelector('h6');
                    msg = 'La cantidad de "' + (nombre ? nombre.textContent : 'producto') + '" debe ser mayor a 0.';
                    invalid = true;
                } else if (qty > stock) {
                    var nombre = card.querySelector('h6');
                    msg = 'La cantidad de "' + (nombre ? nombre.textContent : 'producto') + '" no puede ser mayor al stock disponible (' + stock + ').';
                    invalid = true;
                }
            });
            if (invalid) {
                e.preventDefault();
                alert(msg);
            }
        });
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
