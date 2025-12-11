<?php
/**
 * Vista Centro de Soporte
 * Sistema de tickets para que administradores contacten al soporte técnico
 */

require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Mailer.php';

$mensaje_exito = '';
$mensaje_error = '';
$conexion = (new conexion())->get_conexion();
$id_admin = $_SESSION['user']['idusu'] ?? ($_SESSION['user_id'] ?? null);

// Cargar datos del admin
$admin = [];
try {
    $stmt_admin = $conexion->prepare("SELECT idusu, nombre_completo, email FROM usu WHERE idusu = :id LIMIT 1");
    $stmt_admin->execute([':id' => $id_admin]);
    $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    $mensaje_error = 'Error al cargar datos de administrador';
}

// Cargar tickets
$tickets = [];
try {
    $stmt_tickets = $conexion->prepare("SELECT id, asunto, estado, fecha_creacion, respuesta FROM tickets_soporte WHERE admin_id = :id ORDER BY fecha_creacion DESC");
    $stmt_tickets->execute([':id' => $id_admin]);
    $tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tabla no existe aún
}

// Procesar eliminación de ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_ticket'])) {
    try {
        $id_ticket = (int)$_POST['id_ticket'];
        
        // Verificar que el ticket perteneza al usuario actual
        $stmt_verificar = $conexion->prepare("SELECT id FROM tickets_soporte WHERE id = :id AND admin_id = :admin_id");
        $stmt_verificar->execute([':id' => $id_ticket, ':admin_id' => $id_admin]);
        
        if ($stmt_verificar->fetch(PDO::FETCH_ASSOC)) {
            // Eliminar el ticket
            $stmt_eliminar = $conexion->prepare("DELETE FROM tickets_soporte WHERE id = :id AND admin_id = :admin_id");
            $stmt_eliminar->execute([':id' => $id_ticket, ':admin_id' => $id_admin]);
            
            $mensaje_exito = 'Ticket eliminado correctamente';
            
            // Recargar tickets
            try {
                $stmt_tickets = $conexion->prepare("SELECT id, asunto, estado, fecha_creacion, respuesta FROM tickets_soporte WHERE admin_id = :id ORDER BY fecha_creacion DESC");
                $stmt_tickets->execute([':id' => $id_admin]);
                $tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {}
        } else {
            $mensaje_error = 'No tienes permiso para eliminar este ticket';
        }
    } catch (Exception $e) {
        $mensaje_error = 'Error al eliminar el ticket: ' . $e->getMessage();
    }
}

// Procesar formulario de soporte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_soporte'])) {
    try {
        $asunto = trim($_POST['asunto_soporte'] ?? '');
        $descripcion = trim($_POST['descripcion_soporte'] ?? '');
        
        if (empty($asunto)) {
            throw new Exception('El asunto es requerido');
        }
        if (empty($descripcion)) {
            throw new Exception('La descripción es requerida');
        }
        
        // Procesar archivo si existe
        $archivo = null;
        if (isset($_FILES['archivo_soporte']) && $_FILES['archivo_soporte']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['archivo_soporte'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if ($file['size'] > $maxSize) {
                throw new Exception('El archivo no debe exceder 5MB');
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'text/plain'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Tipo de archivo no permitido');
            }
            
            $uploadDir = __DIR__ . '/../../uploads/tickets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $filename = uniqid('ticket_') . '_' . basename($file['name']);
            $filepath = $uploadDir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Error al subir el archivo');
            }
            
            $archivo = $filename;
        }
        
        // Guardar ticket en BD
        $stmt_ticket = $conexion->prepare("INSERT INTO tickets_soporte (admin_id, asunto, descripcion, archivo, estado) VALUES (:admin_id, :asunto, :descripcion, :archivo, 'abierto')");
        $stmt_ticket->execute([
            ':admin_id' => $id_admin,
            ':asunto' => $asunto,
            ':descripcion' => $descripcion,
            ':archivo' => $archivo
        ]);
        
        $id_ticket = $conexion->lastInsertId();
        
        // Enviar email al super admin
        try {
            $mailer = new Mailer();
            $mailer->sendSupportTicketEmail(
                'epymes270@gmail.com',
                $id_ticket,
                $admin,
                $asunto,
                $descripcion,
                $archivo
            );
            error_log("Email de ticket enviado exitosamente a epymes270@gmail.com para ticket #$id_ticket");
        } catch (Exception $e) {
            $error_msg = "Error enviando email: " . $e->getMessage();
            error_log($error_msg);
            // No mostrar error al usuario, pero registrar en logs
        }
        
        $mensaje_exito = 'Ticket enviado exitosamente. Te responderemos pronto.';
        
        // Recargar tickets
        try {
            $stmt_tickets = $conexion->prepare("SELECT id, asunto, estado, fecha_creacion, respuesta FROM tickets_soporte WHERE admin_id = :id ORDER BY fecha_creacion DESC");
            $stmt_tickets->execute([':id' => $id_admin]);
            $tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {}
        
    } catch (Exception $e) {
        $mensaje_error = $e->getMessage();
    }
}
?>

<div class="container-fluid py-4" style="background:#fff; min-height: 100vh;">
    
    <div class="p-4 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color: #fff;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <p class="mb-1 text-white-50 small" style="letter-spacing:1px;text-transform:uppercase;">
                    <i class="fas fa-life-ring me-2" style="color: #ffff"></i>Soporte técnico
                </p>
                <h2 class="fw-bold mb-0"style="color: #ffff">Centro de Soporte</h2>
            </div>
            <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25 fs-6">
                <i class="fas fa-ticket me-2"></i><?= count($tickets) ?> Tickets
            </span>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mx-3" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mx-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Contenido -->
    <div class="px-3">
        <div class="row g-4">
            <!-- Formulario de soporte -->
            <div class="col-12 col-lg-5 d-flex">
                <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                <i class="fas fa-pen-fancy"></i>
                            </div>
                            <h5 class="mb-0">Reportar Problema</h5>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-600">Asunto</label>
                                <input type="text" class="form-control form-control-lg" name="asunto_soporte" placeholder="Ej: Error al generar reportes" required>
                                <small class="text-muted">Describe brevemente tu problema</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Tu Mensaje</label>
                                <textarea class="form-control" name="descripcion_soporte" rows="5" placeholder="Cuéntanos qué está pasando, qué pasos tomaste, etc." required style="resize: vertical;"></textarea>
                                <small class="text-muted">Sé lo más específico posible para una respuesta más rápida</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Archivo adjunto (opcional)</label>
                                <input type="file" class="form-control" name="archivo_soporte" accept=".pdf,.jpg,.jpeg,.png,.txt,.zip">
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Máx. 5MB | Formatos: PDF, JPG, PNG, TXT, ZIP
                                </small>
                            </div>

                            <button type="submit" name="enviar_soporte" class="btn btn-info btn-lg w-100 text-white fw-600 shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Ticket de Soporte
                            </button>
                        </form>

                        <div class="alert alert-light border-start border-info border-3 mt-4 mb-0">
                            <p class="mb-2"><strong><i class="fas fa-clock me-2"></i>Tiempo de respuesta:</strong></p>
                            <p class="mb-0 small text-muted">Respondemos dentro de 24-48 horas en días hábiles</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de tickets -->
            <div class="col-12 col-lg-7 d-flex">
                <div class="card h-100 flex-fill border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                <i class="fas fa-history"></i>
                            </div>
                            <h5 class="mb-0">Mis Tickets</h5>
                        </div>

                        <?php if (!empty($tickets)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($tickets as $index => $ticket): ?>
                                    <div class="list-group-item px-0 py-3 border-bottom" style="<?= $index === count($tickets) - 1 ? 'border-bottom: none !important;' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <small class="text-muted bg-light px-2 py-1 rounded">#<?= htmlspecialchars($ticket['id']) ?></small>
                                                    <span class="badge bg-<?php
                                                        switch($ticket['estado']) {
                                                            case 'abierto': echo 'danger'; break;
                                                            case 'en_proceso': echo 'warning'; break;
                                                            case 'respondido': echo 'success'; break;
                                                            case 'cerrado': echo 'secondary'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <i class="fas fa-<?php
                                                            switch($ticket['estado']) {
                                                                case 'abierto': echo 'clock'; break;
                                                                case 'en_proceso': echo 'spinner'; break;
                                                                case 'respondido': echo 'check'; break;
                                                                case 'cerrado': echo 'check-double'; break;
                                                            }
                                                        ?> me-1"></i><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $ticket['estado']))) ?>
                                                    </span>
                                                </div>
                                                <h6 class="mb-1 fw-600"><?= htmlspecialchars(substr($ticket['asunto'], 0, 50)) ?><?= strlen($ticket['asunto']) > 50 ? '...' : '' ?></h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i><?= date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])) ?>
                                                </small>
                                                <?php if (!empty($ticket['respuesta'])): ?>
                                                    <div class="mt-2 p-2 bg-success bg-opacity-10 border-start border-success border-3 rounded">
                                                        <small class="text-success fw-600"><i class="fas fa-reply me-1"></i>Respondido</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detalleTicket<?= $ticket['id'] ?>" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este ticket? Esta acción no se puede deshacer.');">
                                                    <input type="hidden" name="id_ticket" value="<?= $ticket['id'] ?>">
                                                    <button type="submit" name="eliminar_ticket" class="btn btn-sm btn-outline-danger" title="Eliminar ticket">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de detalles -->
                                    <div class="modal fade" id="detalleTicket<?= $ticket['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-light border-0">
                                                    <div>
                                                        <h5 class="modal-title">Ticket #<?= $ticket['id'] ?> - <?= htmlspecialchars(substr($ticket['asunto'], 0, 40)) ?></h5>
                                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])) ?></small>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h6 class="fw-600 text-muted small text-uppercase">Asunto</h6>
                                                        <p class="mb-0"><?= htmlspecialchars($ticket['asunto']) ?></p>
                                                    </div>

                                                    <hr>

                                                    <div class="mb-3">
                                                        <h6 class="fw-600 text-muted small text-uppercase">Tu mensaje</h6>
                                                        <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['descripcion'] ?? '') ?></p>
                                                    </div>

                                                    <?php if (!empty($ticket['respuesta'])): ?>
                                                        <hr>
                                                        <div class="alert alert-success mb-0">
                                                            <h6 class="fw-600 text-success small text-uppercase mb-2">
                                                                <i class="fas fa-check-circle me-1"></i>Respuesta del equipo de soporte
                                                            </h6>
                                                            <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['respuesta']) ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: #d0d0d0;"></i>
                                <p class="text-muted mt-3 mb-0">No tienes tickets aún</p>
                                <p class="text-muted small">Cuando reportes un problema, aparecerá aquí</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info adicional -->
        <div class="row g-4 mt-2">
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-600 mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>¿Necesitas ayuda?</h6>
                        <ul class="mb-0 small text-muted">
                            <li>Describe el problema con el máximo detalle posible</li>
                            <li>Incluye los pasos que realizaste antes del error</li>
                            <li>Adjunta capturas de pantalla si es relevante</li>
                            <li>Especifica la hora aproximada cuando ocurrió</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-600 mb-3"><i class="fas fa-headset text-info me-2"></i>Contacto directo</h6>
                        <p class="mb-2 small"><strong>Email:</strong> epymes270@gmail.com</p>
                        <p class="mb-0 small"><strong>Horario:</strong> Lunes a Viernes, 9:00 - 18:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

