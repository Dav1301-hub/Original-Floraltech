<?php
$user = $_SESSION['user'] ?? [];
$navbar_volver_url = 'index.php?ctrl=empleado&action=dashboard';
$navbar_volver_text = 'Volver al Dashboard';
$pagos_pendientes_recientes = array_slice($pagos_pendientes, 0, 5);
$pagos_completados_recientes = $pagos_verificados ?? [];
$filtro_completados = $filtro_completados ?? ['estado' => '', 'fecha_desde' => '', 'fecha_hasta' => ''];
$filtro_estado = $filtro_completados['estado'] ?? '';
$filtro_fecha_desde = $filtro_completados['fecha_desde'] ?? '';
$filtro_fecha_hasta = $filtro_completados['fecha_hasta'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pagos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
    <style>
        .pagos-page .dashboard-container { max-width: 100%; }
        .pagos-page .content { max-width: 100%; width: 100%; margin: 0; padding: 1.5rem 1.5rem; box-sizing: border-box; }
        .pagos-page .page-title { font-size: 1.35rem; font-weight: 700; color: var(--emp-text); margin-bottom: 0.25rem; }
        .pagos-page .page-subtitle { color: var(--emp-text-muted); font-size: 0.9rem; }
        .payments-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; width: 100%; max-width: 100%; }
        .payment-section { background: var(--emp-bg-card); border-radius: var(--emp-radius); box-shadow: var(--emp-shadow-card); border: 1px solid var(--emp-border); overflow: hidden; }
        .payment-section .section-header { padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
        .payment-section .section-header.pending { background: linear-gradient(135deg, #fef9c3 0%, #fde047 100%); color: #854d0e; }
        .payment-section .section-header.completed { background: linear-gradient(135deg, #a7f3d0 0%, #5eead4 100%); color: #065f46; }
        .payment-section .section-title { font-weight: 600; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .payment-section .section-badge { background: rgba(255,255,255,0.9); padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; }
        .payment-list { max-height: 420px; overflow-y: auto; }
        .payment-item { padding: 1.1rem 1.25rem; border-bottom: 1px solid var(--emp-border); transition: background 0.2s; }
        .payment-item:last-child { border-bottom: none; }
        .payment-item:hover { background: var(--emp-bg); }
        .payment-item .payment-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.6rem; }
        .payment-item .payment-id { font-weight: 600; color: var(--emp-primary); font-size: 0.9rem; }
        .payment-item .payment-amount { font-weight: 700; color: var(--emp-success); font-size: 1.15rem; }
        .payment-item .payment-details { display: flex; flex-direction: column; gap: 0.2rem; margin-bottom: 0.75rem; }
        .payment-item .payment-client { font-weight: 500; color: var(--emp-text); font-size: 0.9rem; }
        .payment-item .payment-method, .payment-item .payment-date { font-size: 0.8rem; color: var(--emp-text-muted); display: flex; align-items: center; gap: 0.4rem; }
        .payment-item .payment-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--emp-border); }
        .payment-item .btn-pago { padding: 0.45rem 0.9rem; border-radius: var(--emp-radius-sm); font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .payment-item .btn-pago.btn-approve { background: var(--emp-success); color: #fff; }
        .payment-item .btn-pago.btn-approve:hover { background: #047857; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5,150,105,0.35); }
        .payment-item .btn-pago.btn-reject { background: #fff; color: var(--emp-danger); border: 1px solid var(--emp-danger); }
        .payment-item .btn-pago.btn-reject:hover { background: var(--emp-danger); color: #fff; transform: translateY(-1px); }
        .payment-item .status-badge { padding: 0.3rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .payment-item .status-badge.pending { background: #fef3c7; color: #92400e; }
        .payment-item .status-badge.completed { background: #d1fae5; color: #065f46; }
        .payment-item .status-badge.rejected { background: #fee2e2; color: #b91c1c; }
        .pagos-page .filter-bar { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; padding: 0.75rem 1.25rem; background: var(--emp-bg); border-bottom: 1px solid var(--emp-border); }
        .pagos-page .filter-bar label { font-size: 0.8rem; font-weight: 500; color: var(--emp-text-muted); margin-bottom: 0.25rem; }
        .pagos-page .filter-bar .form-select, .pagos-page .filter-bar .form-control { font-size: 0.875rem; padding: 0.35rem 0.5rem; }
        .pagos-page .filter-bar .btn-filter { padding: 0.35rem 0.75rem; font-size: 0.875rem; }
        .payment-item .verificado-por { font-size: 0.75rem; color: var(--emp-text-muted); }
        .empty-state { padding: 2.5rem 1.5rem; text-align: center; color: var(--emp-text-muted); }
        .empty-state .empty-icon { font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.75rem; }
        .empty-state h3 { font-size: 1.1rem; margin-bottom: 0.25rem; color: var(--emp-text); }
        .pagos-footer-hint { text-align: center; padding: 0.75rem; font-size: 0.85rem; color: var(--emp-text-muted); background: var(--emp-bg); border-top: 1px solid var(--emp-border); }
        @media (max-width: 768px) {
            .payments-grid { grid-template-columns: 1fr; gap: 1rem; }
            .pagos-page .content { padding: 1rem 0.75rem; }
            .payment-item .payment-actions { flex-direction: column; align-items: stretch; }
            .payment-item .d-flex.gap-1 { flex-direction: column; }
        }
    </style>
</head>
<body class="empleado-theme pagos-page">
    <div class="dashboard-container">
        <?php include __DIR__ . '/partials/navbar_empleado.php'; ?>
        <main class="content">
            <div class="mb-3">
                <h1 class="page-title"><i class="fas fa-credit-card me-2"></i>Procesar pagos</h1>
                <p class="page-subtitle">Aprueba o rechaza los pagos pendientes de verificación.</p>
            </div>

            <?php if (!empty($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= $_SESSION['tipo_mensaje'] === 'success' ? 'success' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'warning' : 'info') ?> alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-<?= $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : 'info-circle' ?> me-2"></i>
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
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
                                        <?php if (!empty($pago['referencia'])): ?>
                                            <div class="payment-method mt-1" style="color: var(--emp-primary); font-weight: 600;">
                                                <i class="fas fa-fingerprint"></i>
                                                Ref: <?= htmlspecialchars($pago['referencia']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($pago['tiene_comprobante']) || !empty($pago['comprobante'])): ?>
                                            <div class="mt-2">
                                                <a href="ver_comprobante.php?idpago=<?= (int)$pago['idpago'] ?>" target="_blank" class="btn-pago btn-approve py-1 px-2" style="font-size: 0.75rem; background: var(--emp-info);">
                                                    <i class="fas fa-image"></i> Ver comprobante
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="payment-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                                        </div>
                                    </div>

                                    <div class="payment-actions">
                                        <span class="status-badge pending">
                                            <i class="fas fa-clock"></i> <?= htmlspecialchars($pago['estado_pag']) ?>
                                        </span>
                                        <?php if ($pago['estado_pag'] !== 'Completado' && $pago['estado_pag'] !== 'Rechazado'): ?>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <form method="POST" action="index.php?ctrl=empleado&action=verificar_pago" class="d-inline">
                                                    <input type="hidden" name="id_pago" value="<?= (int)$pago['idpago'] ?>">
                                                    <input type="hidden" name="accion" value="aprobar">
                                                    <input type="hidden" name="observaciones_empleado" value="">
                                                    <button type="submit" class="btn-pago btn-approve"><i class="fas fa-check"></i> Aprobar</button>
                                                </form>
                                                <form method="POST" action="index.php?ctrl=empleado&action=verificar_pago" class="d-inline" onsubmit="return confirm('¿Rechazar este pago? Se restaurará el stock del pedido.');">
                                                    <input type="hidden" name="id_pago" value="<?= (int)$pago['idpago'] ?>">
                                                    <input type="hidden" name="accion" value="rechazar">
                                                    <input type="hidden" name="observaciones_empleado" value="">
                                                    <button type="submit" class="btn-pago btn-reject"><i class="fas fa-times"></i> Rechazar</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($pagos_pendientes) > 5): ?>
                            <div class="pagos-footer-hint">
                                <i class="fas fa-info-circle"></i> Mostrando los 5 más recientes. Total: <?= count($pagos_pendientes) ?> pendientes.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagos Completados Recientes -->
                <div class="payment-section">
                    <div class="section-header completed">
                        <div class="section-title">
                            <i class="fas fa-check-circle"></i>
                            Pagos completados / rechazados
                        </div>
                        <div class="section-badge">
                            <?= count($pagos_completados_recientes) ?> resultado<?= count($pagos_completados_recientes) !== 1 ? 's' : '' ?>
                        </div>
                    </div>
                    <!-- Filtro -->
                    <form method="GET" action="index.php" class="filter-bar">
                        <input type="hidden" name="ctrl" value="empleado">
                        <input type="hidden" name="action" value="procesar_pagos">
                        <div>
                            <label class="d-block">Estado</label>
                            <select name="filtro_estado" class="form-select" style="min-width: 140px;">
                                <option value="">Todos</option>
                                <option value="Completado" <?= $filtro_estado === 'Completado' ? 'selected' : '' ?>>Completado</option>
                                <option value="Rechazado" <?= $filtro_estado === 'Rechazado' ? 'selected' : '' ?>>Rechazado</option>
                            </select>
                        </div>
                        <div>
                            <label class="d-block">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" style="min-width: 140px;" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                        </div>
                        <div>
                            <label class="d-block">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" style="min-width: 140px;" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-filter btn-primary"><i class="fas fa-filter me-1"></i> Filtrar</button>
                        </div>
                        <?php if ($filtro_estado !== '' || $filtro_fecha_desde !== '' || $filtro_fecha_hasta !== ''): ?>
                        <div>
                            <a href="index.php?ctrl=empleado&action=procesar_pagos" class="btn btn-filter btn-outline-secondary">Limpiar</a>
                        </div>
                        <?php endif; ?>
                    </form>

                    <?php if (empty($pagos_completados_recientes)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3><?= ($filtro_estado !== '' || $filtro_fecha_desde !== '' || $filtro_fecha_hasta !== '') ? 'No hay resultados con el filtro aplicado' : 'No hay pagos completados' ?></h3>
                            <p><?= ($filtro_estado !== '' || $filtro_fecha_desde !== '' || $filtro_fecha_hasta !== '') ? 'Prueba con otros criterios o limpia el filtro.' : 'No se han completado o rechazado pagos recientemente.' ?></p>
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
                                        <?php if (!empty($pago['referencia'])): ?>
                                            <div class="payment-method mt-1" style="color: var(--emp-primary);">
                                                <small>Ref: <?= htmlspecialchars($pago['referencia']) ?></small>
                                            </div>
                                        <?php endif; ?>
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
                                        <?php if (($pago['estado_pag'] ?? '') === 'Rechazado'): ?>
                                            <span class="status-badge rejected"><i class="fas fa-times"></i> Rechazado</span>
                                        <?php else: ?>
                                            <span class="status-badge completed"><i class="fas fa-check"></i> Completado</span>
                                        <?php endif; ?>
                                        <?php if (!empty($pago['verificado_por_nombre'])): ?>
                                            <span class="verificado-por">Por: <?= htmlspecialchars($pago['verificado_por_nombre']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($pagos_completados_recientes) >= 50): ?>
                            <div class="pagos-footer-hint">
                                <i class="fas fa-info-circle"></i> Mostrando hasta 50 resultados. Usa el filtro para acotar.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
