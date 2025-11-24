<?php
// Vista para procesar pagos - FloralTech
// Diseño optimizado para empleados con listas de 5 elementos más recientes

// Procesar pago si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pago_id'])) {
    require_once __DIR__ . '/../../controllers/CprocesarPagos.php';
    $controller = new CprocesarPagos();
    $pagoId = $_POST['pago_id'];
    $resultado = $controller->procesarPago($pagoId);
}

// Obtener solo los 5 más recientes para optimizar la pantalla
$pagos_pendientes_recientes = array_slice($pagos_pendientes, 0, 5);
$pagos_completados_recientes = array_slice($pagos_verificados, 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pagos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
            font-size: 0.875rem;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
        }

        /* Main Content */
        .content {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
            width: 100%;
        }

        /* Payment Sections */
        .payments-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .payment-section {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .section-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header.pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
        }

        .section-header.completed {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
        }

        .section-badge {
            background: rgba(255, 255, 255, 0.8);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Payment Items */
        .payment-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .payment-item {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s;
        }

        .payment-item:hover {
            background: #f8fafc;
        }

        .payment-item:last-child {
            border-bottom: none;
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .payment-id {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.875rem;
        }

        .payment-amount {
            font-weight: 700;
            color: var(--success-color);
            font-size: 1.125rem;
        }

        .payment-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-bottom: 0.75rem;
        }

        .payment-client {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .payment-date {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .payment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-process {
            background: var(--success-color);
            color: white;
        }

        .btn-process:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        .btn-view-all {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 0.75rem 1.5rem;
            margin: 1.5rem;
            width: calc(100% - 3rem);
        }

        .btn-view-all:hover {
            background: var(--primary-color);
            color: white;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .alert {
            border-radius: var(--radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .payments-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <div class="header-title">
                    <i class="fas fa-credit-card"></i>
                    Procesar Pagos - FloralTech
                </div>
                <div class="header-user">
                    <div class="user-info">
                        <div style="font-weight: 600;">
                            <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?>
                        </div>
                        <div style="opacity: 0.8;">Panel Empleado</div>
                    </div>
                    <a href="index.php?ctrl=empleado&action=dashboard" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content">
            <?php if (isset($resultado)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <?php echo $resultado ? 'Pago procesado correctamente.' : 'Error al procesar el pago.'; ?>
                </div>
            <?php endif; ?>

            <!-- Secciones de Pagos -->
            <div class="payments-grid">
                <!-- Pagos Pendientes -->
                <div class="payment-section">
                    <div class="section-header pending">
                        <div class="section-title">
                            <i class="fas fa-clock"></i>
                            Pagos Pendientes
                        </div>
                        <div class="section-badge">
                            <?= count($pagos_pendientes_recientes) ?> de <?= count($pagos_pendientes) ?>
                        </div>
                    </div>

                    <?php if (empty($pagos_pendientes_recientes)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3>No hay pagos pendientes</h3>
                            <p>Todos los pagos están procesados</p>
                        </div>
                    <?php else: ?>
                        <div class="payment-list">
                            <?php foreach ($pagos_pendientes_recientes as $pago): ?>
                                <div class="payment-item">
                                    <div class="payment-header">
                                        <div class="payment-id">
                                            PED-<?= $pago['ped_idped'] ?>
                                        </div>
                                        <div class="payment-amount">
                                            $<?= number_format($pago['monto'], 0) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-details">
                                        <div class="payment-client">
                                            <?= htmlspecialchars($pago['cliente_nombre']) ?>
                                        </div>
                                        <div class="payment-method">
                                            <i class="fas fa-credit-card"></i>
                                            <?= htmlspecialchars($pago['metodo_pago']) ?>
                                        </div>
                                        <div class="payment-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                                        </div>
                                    </div>

                                    <div class="payment-actions">
                                        <span class="status-badge" style="background: #fef3c7; color: #92400e;">
                                            <i class="fas fa-clock"></i>
                                            <?= $pago['estado_pag'] ?>
                                        </span>
                                        
                                        <?php if ($pago['estado_pag'] !== 'Completado'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="pago_id" value="<?= $pago['idpago'] ?>">
                                                <button type="submit" class="btn btn-process">
                                                    <i class="fas fa-check"></i>
                                                    Procesar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($pagos_pendientes) > 5): ?>
                            <button class="btn-view-all">
                                <i class="fas fa-eye"></i>
                                Ver todos los pagos pendientes (<?= count($pagos_pendientes) ?>)
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagos Completados Recientes -->
                <div class="payment-section">
                    <div class="section-header completed">
                        <div class="section-title">
                            <i class="fas fa-check-circle"></i>
                            Pagos Completados Recientes
                        </div>
                        <div class="section-badge">
                            <?= count($pagos_completados_recientes) ?> de <?= count($pagos_verificados) ?>
                        </div>
                    </div>

                    <?php if (empty($pagos_completados_recientes)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>No hay pagos completados</h3>
                            <p>No se han completado pagos recientemente</p>
                        </div>
                    <?php else: ?>
                        <div class="payment-list">
                            <?php foreach ($pagos_completados_recientes as $pago): ?>
                                <div class="payment-item">
                                    <div class="payment-header">
                                        <div class="payment-id">
                                            PED-<?= $pago['ped_idped'] ?>
                                        </div>
                                        <div class="payment-amount">
                                            $<?= number_format($pago['monto'], 0) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-details">
                                        <div class="payment-client">
                                            <?= htmlspecialchars($pago['cliente_nombre']) ?>
                                        </div>
                                        <div class="payment-method">
                                            <i class="fas fa-credit-card"></i>
                                            <?= htmlspecialchars($pago['metodo_pago']) ?>
                                        </div>
                                        <div class="payment-date">
                                            <i class="fas fa-calendar-check"></i>
                                            <?php if (isset($pago['fecha_verificacion']) && $pago['fecha_verificacion']): ?>
                                                <?= date('d/m/Y H:i', strtotime($pago['fecha_verificacion'])) ?>
                                            <?php else: ?>
                                                <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="payment-actions">
                                        <span class="status-badge status-completed">
                                            <i class="fas fa-check"></i>
                                            Completado
                                        </span>
                                        
                                        <?php if (isset($pago['verificado_por_nombre']) && $pago['verificado_por_nombre']): ?>
                                            <small style="color: var(--text-secondary); font-size: 0.75rem;">
                                                Por: <?= htmlspecialchars($pago['verificado_por_nombre']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($pagos_verificados) > 5): ?>
                            <button class="btn-view-all">
                                <i class="fas fa-history"></i>
                                Ver historial completo (<?= count($pagos_verificados) ?>)
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
