<?php
/**
 * Vista Centro de Soporte
 * Sistema de tickets para que administradores contacten al soporte técnico
 */

// Las variables $admin, $tickets, $mensaje_exito y $mensaje_error vienen del SupportController via $ctx
$tickets = $tickets ?? [];
$admin = $admin ?? [];
?>

<div class="support-container animate-fade-in">
    
    <!-- Hero Section -->
    <div class="premium-hero mb-4">
        <div class="hero-content">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hero-icon">
                    <i class="fas fa-life-ring"></i>
                </div>
                <div>
                    <h2 class="hero-title">Centro de Soporte</h2>
                    <p class="hero-subtitle">Estamos aquí para ayudarte a resolver cualquier inconveniente</p>
                </div>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-value"><?= count($tickets) ?></span>
                    <span class="stat-label">Tickets generados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-4 border-success mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-600">¡Éxito!</h6>
                    <p class="mb-0 small"><?= htmlspecialchars($mensaje_exito) ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-4 border-danger mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-600">Error</h6>
                    <p class="mb-0 small"><?= htmlspecialchars($mensaje_error) ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulario de soporte -->
        <div class="col-12 col-xl-5">
            <div class="card premium-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info-soft text-info me-3">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h5 class="card-title mb-0 fw-bold">Nuevo Ticket</h5>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <form method="POST" action="index.php?ctrl=SupportController&action=enviarTicket" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Asunto del problema</label>
                            <input type="text" class="form-control form-control-lg premium-input" name="asunto_soporte" placeholder="Ej: Error al generar reportes" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción detallada</label>
                            <textarea class="form-control premium-input" name="descripcion_soporte" rows="6" placeholder="Cuéntanos con detalle qué sucede..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Archivo adjunto <span class="text-lowercase fw-normal">(opcional)</span></label>
                            <div class="upload-zone p-3 rounded-3 text-center border-dashed">
                                <input type="file" class="form-control d-none" id="archivo_soporte" name="archivo_soporte" accept=".pdf,.jpg,.jpeg,.png,.txt,.zip">
                                <label for="archivo_soporte" class="m-0 cursor-pointer w-100">
                                    <i class="fas fa-cloud-upload-alt fs-3 text-muted mb-2"></i>
                                    <p class="mb-0 small text-muted">Haz clic para subir o arrastra un archivo</p>
                                    <small class="text-muted-xs">PDF, JPG, PNG (Máx 5MB)</small>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 premium-btn shadow-sm py-3">
                            <i class="fas fa-paper-plane me-2"></i>Enviar a Soporte Técnico
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Historial de tickets -->
        <div class="col-12 col-xl-7">
            <div class="card premium-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success-soft text-success me-3">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h5 class="card-title mb-0 fw-bold">Historial de Tickets</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($tickets)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-faint">
                                    <tr>
                                        <th class="ps-4 py-3 small text-muted text-uppercase">Ticket</th>
                                        <th class="py-3 small text-muted text-uppercase">Estado</th>
                                        <th class="py-3 small text-muted text-uppercase">Fecha</th>
                                        <th class="pe-4 py-3 text-end small text-muted text-uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-600 text-dark"><?= htmlspecialchars(substr($ticket['asunto'], 0, 40)) ?><?= strlen($ticket['asunto']) > 40 ? '...' : '' ?></span>
                                                    <small class="text-muted">ID: #<?= $ticket['id'] ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill badge-<?= $ticket['estado'] ?> px-3">
                                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $ticket['estado']))) ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted">
                                                <?= date('d/m/Y', strtotime($ticket['fecha_creacion'])) ?>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                    <button class="btn btn-white btn-sm px-3" data-bs-toggle="modal" data-bs-target="#detalleTicket<?= $ticket['id'] ?>" title="Ver">
                                                        <i class="fas fa-eye text-primary"></i>
                                                    </button>
                                                    <form method="POST" action="index.php?ctrl=SupportController&action=eliminarTicket" onsubmit="return confirm('¿Eliminar este ticket?');" class="m-0">
                                                        <input type="hidden" name="id_ticket" value="<?= $ticket['id'] ?>">
                                                        <button type="submit" class="btn btn-white btn-sm px-3 border-start" title="Eliminar">
                                                            <i class="fas fa-trash-alt text-danger"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal de detalles modernizado -->
                                        <div class="modal fade" id="detalleTicket<?= $ticket['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <div class="modal-header bg-faint border-0 py-3 px-4">
                                                        <h5 class="modal-title fw-bold">Ticket #<?= $ticket['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="row g-4">
                                                            <div class="col-12">
                                                                <label class="small text-muted text-uppercase fw-bold d-block mb-1">Asunto</label>
                                                                <h5 class="fw-bold"><?= htmlspecialchars($ticket['asunto']) ?></h5>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="p-3 bg-light rounded-3">
                                                                    <label class="small text-muted text-uppercase fw-bold d-block mb-2">Mensaje original</label>
                                                                    <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['descripcion'] ?? '') ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($ticket['respuesta'])): ?>
                                                                <div class="col-12">
                                                                    <div class="p-3 bg-success-soft rounded-3 border-start border-4 border-success">
                                                                        <label class="small text-success text-uppercase fw-bold d-block mb-2"><i class="fas fa-reply me-1"></i>Respuesta soporte</label>
                                                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['respuesta']) ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="col-12 text-center py-3">
                                                                    <span class="text-muted small"><i class="fas fa-hourglass-half me-2"></i>Esperando respuesta del equipo de soporte...</span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox mb-3 text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted fw-bold">Sin tickets</h6>
                            <p class="text-muted small px-4">Cuando reportes un problema con el sistema, aparecerá en este listado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos premium específicos para el módulo de soporte */
.support-container {
    animation: fadeIn 0.5s ease;
}

.premium-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 1.25rem;
    padding: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2);
}

.premium-hero::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.hero-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    backdrop-filter: blur(5px);
}

.hero-title { font-weight: 800; letter-spacing: -0.5px; }
.hero-subtitle { opacity: 0.8; font-size: 1rem; margin-bottom: 0; }

.hero-stats {
    margin-top: 1.5rem;
    display: flex;
    gap: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1; }
.stat-label { font-size: 0.75rem; text-transform: uppercase; opacity: 0.7; font-weight: 600; margin-top: 4px; }

.icon-box {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-faint { background-color: #f8fafc; }

.premium-input {
    border: 1.5px solid #e2e8f0;
    font-size: 0.95rem;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    transition: all 0.2s;
}

.premium-input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.upload-zone {
    border: 2px dashed #e2e8f0;
    transition: all 0.2s;
}

.upload-zone:hover {
    border-color: #4f46e5;
    background: #f8fafc;
}

.cursor-pointer { cursor: pointer; }
.text-muted-xs { font-size: 0.7rem; color: #94a3b8; }

.premium-btn {
    border-radius: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.2px;
    transition: all 0.3s;
}

.premium-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px -3px rgba(79, 70, 229, 0.3);
}

.badge-abierto { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
.badge-en_proceso { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
.badge-respondido { background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
.badge-cerrado { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

.btn-white {
    background: white;
    border: none;
    transition: background 0.2s;
}

.btn-white:hover {
    background: #f1f5f9;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

