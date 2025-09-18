
<?php
// Simulación de datos de pedidos
$pedidos = [
    [
        'id' => 1001,
        'fecha' => '2025-08-28 14:23',
        'estado' => 'Pendiente',
        'metodo_pago' => 'Tarjeta',
        'total' => 120.50,
        'notas' => 'Entregar antes de las 18:00',
        'cliente' => [
            'nombre' => 'Juan Pérez',
            'email' => 'juanperez@email.com',
            'telefono' => '555-1234',
            'direccion_envio' => 'Calle Falsa 123',
            'direccion_facturacion' => 'Calle Falsa 123',
        ],
        'productos' => [
            ['nombre' => 'Rosa Roja', 'cantidad' => 2, 'precio' => 30, 'sku' => 'RR001'],
            ['nombre' => 'Tulipán Amarillo', 'cantidad' => 1, 'precio' => 60, 'sku' => 'TA002'],
        ],
        'envio' => [
            'metodo' => 'Express',
            'empresa' => 'DHL',
            'tracking' => 'DHL123456',
            'estado_envio' => 'En camino',
            'fecha_estimada' => '2025-08-29',
        ]
    ],
    [
        'id' => 1002,
        'fecha' => '2025-08-27 10:05',
        'estado' => 'Entregado',
        'metodo_pago' => 'Efectivo',
        'total' => 75.00,
        'notas' => '',
        'cliente' => [
            'nombre' => 'Ana López',
            'email' => 'ana.lopez@email.com',
            'telefono' => '555-5678',
            'direccion_envio' => 'Av. Principal 456',
            'direccion_facturacion' => 'Av. Principal 456',
        ],
        'productos' => [
            ['nombre' => 'Lirio Blanco', 'cantidad' => 3, 'precio' => 25, 'sku' => 'LB003'],
        ],
        'envio' => [
            'metodo' => 'Normal',
            'empresa' => 'FedEx',
            'tracking' => 'FDX789012',
            'estado_envio' => 'Entregado',
            'fecha_estimada' => '2025-08-27',
        ]
    ]
];

// Resumen
$totalPedidos = count($pedidos);
$totalPendientes = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Pendiente'));
$totalEntregados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Entregado'));
$ventasTotales = array_sum(array_column($pedidos, 'total'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">Gestión de Pedidos</h1>

    <!-- Resumen general -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Pedidos Totales</h6>
                    <div class="fs-2 fw-bold text-primary"><?= $totalPedidos ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Pendientes</h6>
                    <div class="fs-2 fw-bold text-warning"><?= $totalPendientes ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Entregados</h6>
                    <div class="fs-2 fw-bold text-success"><?= $totalEntregados ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Ventas Totales</h6>
                    <div class="fs-2 fw-bold text-info">$<?= number_format($ventasTotales, 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <form class="row mb-4">
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Buscar por ID o cliente" name="busqueda">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="estado">
                <option value="">Todos los estados</option>
                <option>Pendiente</option>
                <option>Entregado</option>
                <option>Cancelado</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" name="fecha">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Buscar / Filtrar</button>
        </div>
    </form>

    <!-- Listado de pedidos -->
    <div class="card mb-4">
        <div class="card-header">Pedidos recientes</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?= $pedido['id'] ?></td>
                            <td><?= $pedido['fecha'] ?></td>
                            <td><?= htmlspecialchars($pedido['cliente']['nombre']) ?></td>
                            <td><span class="badge bg-<?= $pedido['estado'] === 'Pendiente' ? 'warning' : ($pedido['estado'] === 'Entregado' ? 'success' : 'secondary') ?>"><?= $pedido['estado'] ?></span></td>
                            <td>$<?= number_format($pedido['total'], 2) ?></td>
                            <td><?= $pedido['metodo_pago'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detalleModal<?= $pedido['id'] ?>">Ver Detalle</button>
                                <button class="btn btn-sm btn-secondary">Editar</button>
                                <button class="btn btn-sm btn-danger">Cancelar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales de detalle de pedido -->
    <?php foreach ($pedidos as $pedido): ?>
    <div class="modal fade" id="detalleModal<?= $pedido['id'] ?>" tabindex="-1" aria-labelledby="detalleModalLabel<?= $pedido['id'] ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detalleModalLabel<?= $pedido['id'] ?>">Detalle del Pedido #<?= $pedido['id'] ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h6>Información del Cliente</h6>
            <ul>
                <li><strong>Nombre:</strong> <?= htmlspecialchars($pedido['cliente']['nombre']) ?></li>
                <li><strong>Email:</strong> <?= htmlspecialchars($pedido['cliente']['email']) ?></li>
                <li><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['cliente']['telefono']) ?></li>
                <li><strong>Dirección de envío:</strong> <?= htmlspecialchars($pedido['cliente']['direccion_envio']) ?></li>
                <li><strong>Dirección de facturación:</strong> <?= htmlspecialchars($pedido['cliente']['direccion_facturacion']) ?></li>
            </ul>
            <h6>Productos</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                        <th>SKU</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedido['productos'] as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['nombre']) ?></td>
                        <td><?= $prod['cantidad'] ?></td>
                        <td>$<?= number_format($prod['precio'], 2) ?></td>
                        <td>$<?= number_format($prod['cantidad'] * $prod['precio'], 2) ?></td>
                        <td><?= htmlspecialchars($prod['sku']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h6>Información de Envío</h6>
            <ul>
                <li><strong>Método:</strong> <?= htmlspecialchars($pedido['envio']['metodo']) ?></li>
                <li><strong>Empresa:</strong> <?= htmlspecialchars($pedido['envio']['empresa']) ?></li>
                <li><strong>Tracking:</strong> <?= htmlspecialchars($pedido['envio']['tracking']) ?></li>
                <li><strong>Estado envío:</strong> <?= htmlspecialchars($pedido['envio']['estado_envio']) ?></li>
                <li><strong>Fecha estimada:</strong> <?= htmlspecialchars($pedido['envio']['fecha_estimada']) ?></li>
            </ul>
            <h6>Notas del cliente</h6>
            <p><?= htmlspecialchars($pedido['notas']) ?: '<em>Sin notas</em>' ?></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary">Imprimir</button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
