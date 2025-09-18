<?php
// Mostrar mensajes de éxito/error
if (isset($_SESSION['mensaje_exito'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            '.$_SESSION['mensaje_exito'].'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['mensaje_error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.$_SESSION['mensaje_error'].'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['mensaje_error']);
}

// Conectar a la base de datos
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

// Obtener ID del cliente
$usuario = $_SESSION['user'];
try {
    $stmt = $db->prepare("SELECT idcli FROM cli WHERE email = ?");
    $stmt->execute([$usuario['email']]);
    $cliente_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $cliente_id = $cliente_data['idcli'] ?? 0;

    // Obtener historial completo de pedidos
$stmt = $db->prepare("
    SELECT 
        p.*,
        DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y %H:%i') as fecha_formato,
        pg.estado_pag,
        pg.metodo_pago,
        pg.fecha_pago,
        pg.idpago,
        COUNT(dp.iddetped) as total_items,
        GROUP_CONCAT(DISTINCT CONCAT(tf.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ') as items_detalle
    FROM ped p 
    LEFT JOIN pagos pg ON p.idped = pg.ped_idped
    LEFT JOIN detped dp ON p.idped = dp.idped
    LEFT JOIN tflor tf ON dp.idtflor = tf.idtflor
    WHERE p.cli_idcli = ? 
    GROUP BY p.idped
    ORDER BY p.fecha_pedido DESC
");
    $stmt->execute([$cliente_id]);
    $historial_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $historial_pedidos = [];
    error_log("Error obteniendo historial: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Estilizado -->
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fas fa-seedling"></i>
                FloralTech
            </div>
            <div class="navbar-user">
                <div class="user-info">
                    <p class="user-name">Historial de Pedidos</p>
                    <p class="user-welcome">Todos tus pedidos anteriores</p>
                </div>
                <a href="index.php?ctrl=cliente&action=dashboard" class="logout-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </nav>

        <!-- Contenedor para alertas dinámicas -->
        <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000; width: 300px;"></div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-history"></i> Historial Completo de Pedidos
            </div>
            <div class="card-body">
                <?php if (!empty($historial_pedidos)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Pedido</th>
                                    <th>Fecha</th>
                                    <th>Items</th>
                                    <th>Monto</th>
                                    <th>Estado Pedido</th>
                                    <th>Estado Pago</th>
                                    <th>Método Pago</th>
                                    <th>Factura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_pedidos as $pedido): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?= htmlspecialchars($pedido['numped']) ?></strong>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($pedido['fecha_formato']) ?>
                                        </td>
                                        <td>
                                            <small><?= $pedido['total_items'] ?> items</small>
                                            <br>
                                            <span class="text-muted small"><?= htmlspecialchars($pedido['items_detalle'] ?? 'Sin detalles') ?></span>
                                        </td>
                                        <td>
                                            <strong>$<?= number_format($pedido['monto_total'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch (strtolower($pedido['estado'])) {
                                                case 'completado':
                                                    $badge_class = 'bg-success';
                                                    break;
                                                case 'pendiente':
                                                    $badge_class = 'bg-warning';
                                                    break;
                                                case 'procesando':
                                                    $badge_class = 'bg-info';
                                                    break;
                                                case 'cancelado':
                                                    $badge_class = 'bg-danger';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-info';
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>">
                                                <?= htmlspecialchars($pedido['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pedido['estado_pag']): ?>
                                                <?php
                                                $pago_badge = '';
                                                switch (strtolower($pedido['estado_pag'])) {
                                                    case 'completado':
                                                        $pago_badge = 'bg-success';
                                                        break;
                                                    case 'pendiente':
                                                        $pago_badge = 'bg-warning';
                                                        break;
                                                    default:
                                                        $pago_badge = 'bg-info';
                                                }
                                                ?>
                                                <span class="badge <?= $pago_badge ?>">
                                                    <?= htmlspecialchars($pedido['estado_pag']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Sin pago</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $pedido['metodo_pago'] ? htmlspecialchars($pedido['metodo_pago']) : '-' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Botón para generar factura - SIEMPRE visible y funcional -->
                                                <a href="index.php?ctrl=cliente&action=generar_factura&idpedido=<?= $pedido['idped'] ?>" 
                                                class="btn btn-outline-primary btn-sm"
                                                title="Descargar Factura"
                                                target="_blank">
                                                    <i class="fas fa-file-pdf"></i> Factura
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-icon success">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= count($historial_pedidos) ?></div>
                                        <div class="stat-label">Total de Pedidos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">
                $<?= 
                    number_format(array_reduce($historial_pedidos, function($total, $pedido) {
                        // Sumar solo si el pago está marcado como "Completado"
                        return ($pedido['estado_pag'] === 'Completado') 
                            ? $total + $pedido['monto_total'] 
                            : $total;
                    }, 0), 2) 
                ?>
            </div>
            <div class="stat-label">Total Gastado (Pagados)</div>
        </div>
    </div>
</div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No tienes pedidos aún</h4>
                        <p class="text-muted">¿Por qué no crear tu primer pedido?</p>
                        <a href="index.php?ctrl=cliente&action=realizar_pago" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Pedido
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3 text-center">
            <a href="index.php?ctrl=cliente&action=nuevo_pedido" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Pedido
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Manejar clic en botones de factura
document.querySelectorAll('.btn-generar-factura').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const idPedido = this.dataset.idpedido;
        const btn = this;
        const originalHtml = btn.innerHTML; // Guardar el contenido original
        
        // Mostrar indicador de carga
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        btn.disabled = true;
        
        // Establecer tiempo máximo de carga (5 segundos)
        const tiempoMaximo = 2000; // 5 segundos en milisegundos
        let timeoutId = setTimeout(() => {
            // Restaurar botón si la operación tarda demasiado
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            showAlert('warning', 'La operación está tardando más de lo esperado');
        }, tiempoMaximo);
        
        fetch(`index.php?ctrl=cliente&action=generar_factura&idpedido=${idPedido}`)
            .then(response => response.json())
            .then(data => {
                // Cancelar el timeout si la respuesta llega antes
                clearTimeout(timeoutId);
                
                if (data.success) {
                    showAlert('success', data.message);
                    window.open(data.pdf_url, '_blank');
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                showAlert('danger', 'Error al procesar la solicitud');
            })
            .finally(() => {
                // Restaurar botón después de 1 segundo (incluso si falla)
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 2000);
            });
    });
});

        // Manejar clic en botones de pago
document.querySelectorAll('.btn-pagar').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const idPedido = this.dataset.idpedido;
        const btn = this;
        const originalHtml = btn.innerHTML;
        
        // Mostrar indicador de carga
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        btn.disabled = true;
        
        // Tiempo máximo de 5 segundos
        const tiempoMaximo = 5000;
        let timeoutId = setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            showAlert('warning', 'El pago está tardando más de lo esperado');
        }, tiempoMaximo);
        
        // Simular procesamiento de pago
        setTimeout(() => {
            clearTimeout(timeoutId);
            showAlert('info', 'Generando la factura...');
            
            // Restaurar botón brevemente antes de redirigir
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                // URL CORREGIDA - Eliminar espacios en blanco
                window.location.href = `index.php?ctrl=cliente&action=generar_factura&idpedido=${idPedido}`;
            }, 2000);
        }, 2000);
    });
});

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertId = 'alert-' + Date.now();
            
            const alert = document.createElement('div');
            alert.id = alertId;
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.role = 'alert';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="document.getElementById('${alertId}').remove()"></button>
            `;
            
            alertContainer.appendChild(alert);
            
            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                if (document.getElementById(alertId)) {
                    document.getElementById(alertId).remove();
                }
            }, 3000);
        }
    });
    </script>
</body>
</html>