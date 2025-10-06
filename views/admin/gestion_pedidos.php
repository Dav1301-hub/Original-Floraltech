
<?php
// Este archivo es incluido por dashboard.php, no es una página independiente

// Conectar a la base de datos
require_once($_SERVER['DOCUMENT_ROOT'] . '/Original-Floraltech/views/config/database.php');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Variables para mensajes
$mensaje_exito = '';
$mensaje_error = '';

// Procesar acciones POST (cambio de estado, cancelación, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'cambiar_estado':
                if (isset($_POST['pedido_id']) && isset($_POST['nuevo_estado'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE ped SET estado = ? WHERE idped = ?");
                        $stmt->execute([$_POST['nuevo_estado'], $_POST['pedido_id']]);
                        $mensaje_exito = "Estado del pedido actualizado correctamente.";
                    } catch (PDOException $e) {
                        $mensaje_error = "Error al actualizar el estado: " . $e->getMessage();
                    }
                }
                break;
                
            case 'actualizar_entrega':
                if (isset($_POST['pedido_id']) && isset($_POST['fecha_entrega'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE ped SET fecha_entrega_solicitada = ? WHERE idped = ?");
                        $stmt->execute([$_POST['fecha_entrega'], $_POST['pedido_id']]);
                        $mensaje_exito = "Fecha de entrega actualizada correctamente.";
                    } catch (PDOException $e) {
                        $mensaje_error = "Error al actualizar la fecha de entrega: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Filtros de búsqueda
$where_conditions = ["1=1"];
$params = [];

if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $where_conditions[] = "p.estado = ?";
    $params[] = $_GET['estado'];
}

if (isset($_GET['cliente']) && !empty($_GET['cliente'])) {
    $where_conditions[] = "c.nombre LIKE ?";
    $params[] = '%' . $_GET['cliente'] . '%';
}

if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) {
    $where_conditions[] = "DATE(p.fecha_pedido) >= ?";
    $params[] = $_GET['fecha_desde'];
}

if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) {
    $where_conditions[] = "DATE(p.fecha_pedido) <= ?";
    $params[] = $_GET['fecha_hasta'];
}

$where_clause = implode(' AND ', $where_conditions);

// Obtener pedidos de la base de datos
try {
    $sql = "
        SELECT 
            p.idped,
            p.numped,
            p.fecha_pedido,
            p.monto_total,
            p.estado,
            p.direccion_entrega,
            p.fecha_entrega_solicitada,
            c.nombre as cliente_nombre,
            c.email as cliente_email,
            c.telefono as cliente_telefono,
            c.direccion as cliente_direccion,
            pg.metodo_pago,
            pg.estado_pag as estado_pago,
            pg.fecha_pago,
            COUNT(dp.idtflor) as total_productos
        FROM ped p
        INNER JOIN cli c ON p.cli_idcli = c.idcli
        LEFT JOIN pagos pg ON p.idped = pg.ped_idped
        LEFT JOIN detped dp ON p.idped = dp.idped
        WHERE $where_clause
        GROUP BY p.idped, pg.idpago
        ORDER BY p.fecha_pedido DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $mensaje_error = "Error al obtener pedidos: " . $e->getMessage();
    $pedidos = [];
}

// Calcular estadísticas
$totalPedidos = count($pedidos);
$totalPendientes = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Pendiente'));
$totalEnProceso = count(array_filter($pedidos, fn($p) => $p['estado'] === 'En proceso'));
$totalCompletados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Completado'));
$ventasTotales = array_sum(array_column($pedidos, 'monto_total'));

// Función para obtener detalles de productos de un pedido
function obtenerDetallesPedido($conn, $idPedido) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                dp.cantidad,
                dp.precio_unitario,
                tf.nombre as producto_nombre,
                tf.naturaleza as producto_color,
                tf.descripcion as producto_descripcion
            FROM detped dp
            INNER JOIN tflor tf ON dp.idtflor = tf.idtflor
            WHERE dp.idped = ?
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
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
                    <h6 class="card-title">Completados</h6>
                    <div class="fs-2 fw-bold text-success"><?= $totalCompletados ?></div>
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

    <!-- Filtros y búsqueda FUNCIONALES -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="ctrl" value="dashboard">
                <input type="hidden" name="action" value="admin">
                <input type="hidden" name="page" value="pedidos">
                
                <div class="col-md-3">
                    <label class="form-label">Cliente</label>
                    <input type="text" class="form-control" placeholder="Nombre del cliente" name="cliente" value="<?= $_GET['cliente'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" <?= ($_GET['estado'] ?? '') === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En proceso" <?= ($_GET['estado'] ?? '') === 'En proceso' ? 'selected' : '' ?>>En proceso</option>
                        <option value="Completado" <?= ($_GET['estado'] ?? '') === 'Completado' ? 'selected' : '' ?>>Completado</option>
                        <option value="Cancelado" <?= ($_GET['estado'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" name="fecha_desde" value="<?= $_GET['fecha_desde'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" name="fecha_hasta" value="<?= $_GET['fecha_hasta'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de pedidos CON DATOS REALES -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Pedidos</h5>
            <span class="badge bg-primary"><?= count($pedidos) ?> pedidos</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($pedidos)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado Pedido</th>
                            <th>Estado Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong>
                                <br><small class="text-muted">ID: <?= $pedido['idped'] ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($pedido['cliente_nombre']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($pedido['cliente_email']) ?></small>
                            </td>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= $pedido['total_productos'] ?> items</span>
                            </td>
                            <td>
                                <strong class="text-success">$<?= number_format($pedido['monto_total'], 2) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $pedido['estado'] === 'Pendiente' ? 'warning' : 
                                    ($pedido['estado'] === 'En proceso' ? 'info' : 
                                    ($pedido['estado'] === 'Completado' ? 'success' : 'secondary')) 
                                ?>">
                                    <?= htmlspecialchars($pedido['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($pedido['estado_pago']): ?>
                                    <span class="badge bg-<?= 
                                        $pedido['estado_pago'] === 'Pendiente' ? 'warning' : 
                                        ($pedido['estado_pago'] === 'Completado' ? 'success' : 'danger') 
                                    ?>">
                                        <?= htmlspecialchars($pedido['estado_pago']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin pago</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- Botón de ver detalle simplificado -->
                                    <button type="button" class="btn btn-sm btn-info" onclick="verDetallePedido(<?= $pedido['idped'] ?>)" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- Botones de cambio de estado directo -->
                                    <?php if ($pedido['estado'] === 'Pendiente'): ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'En proceso')" title="Procesar pedido">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Completado')" title="Marcar completado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Cancelado')" title="Cancelar pedido">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif ($pedido['estado'] === 'En proceso'): ?>
                                        <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Completado')" title="Marcar completado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Cancelado')" title="Cancelar pedido">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Finalizado</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4>No hay pedidos</h4>
                    <p class="text-muted">No se encontraron pedidos con los filtros aplicados</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
                        <i class="fas fa-plus me-2"></i>Crear primer pedido
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript funcional para acciones de pedidos -->
<script>
// Función para cambiar estado de pedido
function cambiarEstado(idPedido, nuevoEstado) {
    const iconos = {
        'En proceso': '⚙️',
        'Completado': '✅', 
        'Cancelado': '❌'
    };
    
    const colores = {
        'En proceso': 'info',
        'Completado': 'success',
        'Cancelado': 'danger'
    };
    
    if (confirm(`${iconos[nuevoEstado]} ¿Estás seguro de cambiar el estado del pedido #${idPedido} a "${nuevoEstado}"?`)) {
        // Mostrar loading en el botón
        const botones = document.querySelectorAll(`button[onclick*="cambiarEstado(${idPedido}"]`);
        botones.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
        
        // Crear formulario dinámico para envío POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Agregar datos del formulario
        const inputIdPedido = document.createElement('input');
        inputIdPedido.name = 'id_pedido';
        inputIdPedido.value = idPedido;
        form.appendChild(inputIdPedido);
        
        const inputNuevoEstado = document.createElement('input');
        inputNuevoEstado.name = 'nuevo_estado';
        inputNuevoEstado.value = nuevoEstado;
        form.appendChild(inputNuevoEstado);
        
        const inputAccion = document.createElement('input');
        inputAccion.name = 'accion';
        inputAccion.value = 'cambiar_estado';
        form.appendChild(inputAccion);
        
        // Agregar formulario al documento y enviarlo
        document.body.appendChild(form);
        form.submit();
    }
}

// Función para ver detalles de pedido
function verDetallePedido(idPedido) {
    // Por ahora mostrar un alert con la información básica
    // En el futuro se puede implementar un modal con detalles completos
    
    // Buscar la fila del pedido en la tabla
    const filas = document.querySelectorAll('tr');
    let infoPedido = null;
    
    filas.forEach(fila => {
        if (fila.innerHTML.includes(`ID: ${idPedido}`)) {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length > 0) {
                infoPedido = {
                    numero: celdas[0].textContent.trim(),
                    cliente: celdas[1].textContent.trim(),
                    fecha: celdas[2].textContent.trim(),
                    productos: celdas[3].textContent.trim(),
                    total: celdas[4].textContent.trim(),
                    estado: celdas[5].textContent.trim(),
                    pago: celdas[6].textContent.trim()
                };
            }
        }
    });
    
    if (infoPedido) {
        alert(`📋 DETALLES DEL PEDIDO\n\n` +
              `🏷️ Número: ${infoPedido.numero}\n` +
              `👤 Cliente: ${infoPedido.cliente}\n` +
              `📅 Fecha: ${infoPedido.fecha}\n` +
              `📦 Productos: ${infoPedido.productos}\n` +
              `💰 Total: ${infoPedido.total}\n` +
              `📊 Estado: ${infoPedido.estado}\n` +
              `💳 Pago: ${infoPedido.pago}`);
    } else {
        alert('❌ No se pudo encontrar la información del pedido');
    }
}

// Auto-dismiss alerts después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
    
    // Agregar tooltips a los botones
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
