<?php include '../../partials/header.php'; ?>

<div class="container">
    <?php
    // Inicializar variable $pago con valores por defecto si no está definida
    $pago = $pago ?? [
        'idpago' => 'N/A',
        'fecha_pago' => date('Y-m-d H:i:s'),
        'numped' => 'N/A',
        'cliente' => 'Cliente no especificado',
        'metodo_pago' => 'No especificado',
        'monto' => 0,
        'estado_pag' => 'Desconocido',
        'transaccion_id' => null
    ];
    ?>
    
    <h2 class="my-4">Detalle del Pago #<?= htmlspecialchars($pago['idpago']) ?></h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Información del Pago
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></p>
                    <p><strong>Pedido:</strong> <?= htmlspecialchars($pago['numped']) ?></p>
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($pago['cliente']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Método de Pago:</strong> <?= htmlspecialchars($pago['metodo_pago']) ?></p>
                    <p><strong>Monto:</strong> $<?= number_format($pago['monto'], 2) ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="badge bg-<?= 
                            $pago['estado_pag'] === 'Completado' ? 'success' : 
                            ($pago['estado_pag'] === 'Pendiente' ? 'warning' : 'danger')
                        ?>">
                            <?= htmlspecialchars($pago['estado_pag']) ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <hr>
            
            <h5>Información de Transacción</h5>
            <p><strong>ID de Transacción:</strong> <?= htmlspecialchars($pago['transaccion_id'] ?? 'N/A') ?></p>
            
            <?php if (($pago['metodo_pago'] ?? '') === 'QR'): ?>
            <div class="mt-3">
                <p><strong>Comprobante QR:</strong></p>
                <img src="/assets/img/qr-placeholder.jpg" alt="Comprobante QR" class="img-thumbnail" style="max-width: 200px;">
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-secondary text-white">
            Acciones
        </div>
        <div class="card-body">
            <form method="POST" action="/admin/pagos/actualizar-estado">
                <input type="hidden" name="id_pago" value="<?= $pago['idpago'] ?>">
                <div class="mb-3">
                    <label for="nuevo_estado" class="form-label">Cambiar Estado</label>
                    <select class="form-select" id="nuevo_estado" name="nuevo_estado" required>
                        <option value="Completado" <?= ($pago['estado_pag'] ?? '') === 'Completado' ? 'selected' : '' ?>>Completado</option>
                        <option value="Pendiente" <?= ($pago['estado_pag'] ?? '') === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Cancelado" <?= ($pago['estado_pag'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Actualizar Estado
                </button>
                <a href="/floraltech/views/admin/Vareportes.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </form>
        </div>
    </div>
</div>

<?php include '../../partials/footer.php'; ?>