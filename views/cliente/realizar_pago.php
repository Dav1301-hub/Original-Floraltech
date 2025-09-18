<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión y rol
if (!isset($_SESSION['user'])) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

// Obtener conexión a la base de datos
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

// Validar y obtener ID del pedido
$idPedido = isset($_GET['idpedido']) ? (int)$_GET['idpedido'] : 0;
if ($idPedido <= 0) {
    $_SESSION['mensaje_error'] = "ID de pedido inválido";
    header('Location: index.php?ctrl=cliente&action=historial');
    exit();
}

try {
    // 1. Obtener ID del cliente
    $stmt = $db->prepare("SELECT idcli FROM cli WHERE email = ? LIMIT 1");
    $stmt->execute([$_SESSION['user']['email']]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cliente) {
        $_SESSION['mensaje_error'] = "Cliente no encontrado";
        header('Location: index.php?ctrl=cliente&action=historial');
        exit();
    }

    // 2. Verificar que el pedido pertenece al cliente
    $stmt = $db->prepare("SELECT idped FROM ped WHERE idped = ? AND cli_idcli = ?");
    $stmt->execute([$idPedido, $cliente['idcli']]);
    $pedidoValido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedidoValido) {
        $_SESSION['mensaje_error'] = "No tienes permiso para acceder a este pedido";
        header('Location: index.php?ctrl=cliente&action=historial');
        exit();
    }

    // 3. Obtener información del pedido (CORREGIDO: usando fecha_entrega_solicitada en lugar de fecha_entrega)
    $stmt = $db->prepare("
        SELECT p.*, 
               DATE_FORMAT(p.fecha_entrega_solicitada, '%d/%m/%Y') as fecha_entrega_formato,
               pg.metodo_pago, 
               pg.estado_pag,
               pg.idpago
        FROM ped p
        LEFT JOIN pagos pg ON p.idped = pg.ped_idped
        WHERE p.idped = ?
    ");
    $stmt->execute([$idPedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. Obtener detalles del pedido
    $stmt = $db->prepare("
        SELECT dp.*, 
               tf.nombre, 
               tf.precio, 
               (dp.cantidad * tf.precio) as subtotal
        FROM detped dp
        JOIN tflor tf ON dp.idtflor = tf.idtflor
        WHERE dp.idped = ?
    ");
    $stmt->execute([$idPedido]);
    $flores_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total si es necesario
    if (empty($pedido['monto_total'])) {
        $pedido['monto_total'] = array_reduce($flores_pedido, function($total, $flor) {
            return $total + ($flor['cantidad'] * $flor['precio']);
        }, 0);
    }

} catch (PDOException $e) {
    error_log("Error en realizar_pago: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al cargar la información del pedido";
    header('Location: index.php?ctrl=cliente&action=historial');
    exit();
}

// Si no hay datos del pedido, redirigir
if (empty($pedido)) {
    $_SESSION['mensaje_error'] = "No se encontró información del pedido";
    header('Location: index.php?ctrl=cliente&action=historial');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .payment-method {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method:hover {
            border-color: #007bff;
        }
        .payment-method.selected {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        .qr-container {
            text-align: center;
            padding: 20px;
            border: 2px dashed #007bff;
            border-radius: 10px;
            margin: 20px 0;
        }
        .flower-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .readonly-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
        }
        .resumen-pago {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .total-pago {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
    </style>
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
                    <p class="user-name">Realizar Pago</p>
                    <p class="user-welcome">Completa tu transacción</p>
                </div>
                <a href="index.php?ctrl=cliente&action=historial" class="logout-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Historial
                </a>
            </div>
        </nav>

        <div class="row">
            <!-- Columna principal -->
           <!-- <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-credit-card"></i> Realizar Pago - Pedido #<?= htmlspecialchars($pedido['numped'] ?? 'N/A') ?>
                    </div>
                    <div class="card-body">
                    [Resto del contenido principal permanece igual] 
                    </div>
                </div>
            </div> -->

            <!-- Columna lateral - Resumen del pedido MEJORADO -->
<div class="col-md-4">
    <div class="total-summary">
        <h5><i class="fas fa-file-invoice-dollar"></i> Resumen de Pago</h5>
        
        <div class="resumen-pago">
            <h5 class="text-center mb-3">
                <i class="fas fa-shopping-bag"></i> Pedido #<?= htmlspecialchars($pedido['numped'] ?? 'N/A') ?>
            </h5>
            
            <div class="mb-3">
                <h6>Detalles del pedido:</h6>
                <div class="readonly-info mb-2">
                    <small>Número de pedido</small>
                    <div class="fw-bold">#<?= htmlspecialchars($pedido['numped'] ?? 'N/A') ?></div>
                </div>
                
                <?php if (!empty($flores_pedido)): ?>
                    <div id="selectedFlowers">
                        <?php foreach ($flores_pedido as $flor): ?>
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= htmlspecialchars($flor['nombre']) ?> x<?= $flor['cantidad'] ?></span>
                                <span>$<?= number_format($flor['subtotal'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No hay productos en este pedido</p>
                <?php endif; ?>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span id="subtotalAmount">$<?= number_format($pedido['monto_total'] ?? 0, 2) ?></span>
            </div>
            
            <div class="d-flex justify-content-between">
                <span>Envío:</span>
                <span>Gratis</span>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between align-items-center">
                <strong>Total a pagar:</strong>
                <strong id="totalAmount">$<?= number_format($pedido['monto_total'] ?? 0, 2) ?></strong>
            </div>
            
            <div class="alert alert-info small mt-3">
                <i class="fas fa-info-circle"></i> El pedido se procesará una vez confirmado el pago
            </div>
        </div>
        
        <form method="POST" action="index.php?ctrl=cliente&action=procesar_pago" class="mt-3">
            <input type="hidden" name="idpedido" value="<?= $idPedido ?>">
            <input type="hidden" name="idpago" value="<?= $pedido['idpago'] ?? '' ?>">
            
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Confirmar Pago
                </button>
                <a href="index.php?ctrl=cliente&action=historial" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPaymentMethod(method) {
            // Remover selección anterior
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Seleccionar nuevo método
            const radio = document.querySelector(`input[value="${method}"]`);
            radio.checked = true;
            radio.parentElement.classList.add('selected');
            
            // Mostrar/ocultar QR
            const qrContainer = document.getElementById('qrContainer');
            if (method === 'nequi') {
                qrContainer.style.display = 'block';
            } else {
                qrContainer.style.display = 'none';
            }
        }

        // Inicializar selección si ya hay un método seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            const selectedMethod = document.querySelector('input[name="metodo_pago"]:checked');
            if (selectedMethod) {
                selectPaymentMethod(selectedMethod.value);
            }
        });
    </script>
</body>
</html>