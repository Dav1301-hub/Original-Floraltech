<?php
require_once '../../models/PagoModel.php';
require_once '../../config/db.php';
$db = (new Database())->connect();
$model = new PagoModel($db);

$pagos = $model->obtenerTodosLosPagos();

$pedido = $_GET['pedido'] ?? '';
$fecha = $_GET['fecha'] ?? '';

if ($pedido) {
    $pagos = array_filter($pagos, function($p) use ($pedido) {
        return stripos($p['numped'], $pedido) !== false;
    });
}
if ($fecha) {
    $pagos = array_filter($pagos, function($p) use ($fecha) {
        $fechaPago = date('Y-m-d', strtotime($p['fecha_pago']));
        return $fechaPago === $fecha;
    });
}
usort($pagos, function($a, $b) {
    return strtotime($b['fecha_pago']) - strtotime($a['fecha_pago']);
});

// Renderizar la tabla
?>
<div class="table-responsive">
    <table class="table pagos-admin-table">
        <thead>
            <tr style="background:#212529; color:#fff;">
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Pedido</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagos as $pago): ?>
            <tr style="background:#f8f9fa; color:#212529;">
                <td><?= htmlspecialchars($pago['idpago']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                <td><?= htmlspecialchars($pago['cliente']) ?></td>
                <td><?= htmlspecialchars($pago['numped']) ?></td>
                <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                <td>$<?= number_format($pago['monto'], 2) ?></td>
                <td>
                    <span class="badge bg-<?= 
                        $pago['estado_pag'] === 'Completado' ? 'success' : 
                        ($pago['estado_pag'] === 'Pendiente' ? 'warning' : 'danger')
                    ?>">
                        <?= htmlspecialchars($pago['estado_pag']) ?>
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info btn-detalle-pago" data-idpago="<?= $pago['idpago'] ?>">
                        <i class="bi bi-eye-fill"></i> Detalle
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
