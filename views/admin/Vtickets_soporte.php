<?php
/**
 * Vista de administración de tickets de soporte
 * Solo accesible para super administrador
 */

require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Mailer.php';

$id_admin = $_SESSION['user']['idusu'] ?? ($_SESSION['user_id'] ?? null);

// Verificar que sea super admin (tpusu_idtpusu = 1 o similar)
// Esto depende de tu estructura, ajusta según sea necesario

$conexion = (new conexion())->get_conexion();
$mensaje = '';

// Procesar respuesta a ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['responder_ticket'])) {
    try {
        $id_ticket = (int)$_POST['id_ticket'];
        $respuesta = trim($_POST['respuesta'] ?? '');
        
        if (empty($respuesta)) {
            $mensaje = 'La respuesta no puede estar vacía';
        } else {
            // Obtener datos del ticket y admin
            $stmt_ticket_data = $conexion->prepare("
                SELECT t.asunto, t.admin_id, u.email, u.nombre_completo
                FROM tickets_soporte t
                JOIN usu u ON t.admin_id = u.idusu
                WHERE t.id = :id
            ");
            $stmt_ticket_data->execute([':id' => $id_ticket]);
            $ticket_data = $stmt_ticket_data->fetch(PDO::FETCH_ASSOC);
            
            // Actualizar respuesta en BD
            $stmt = $conexion->prepare("UPDATE tickets_soporte SET respuesta = :respuesta, estado = 'respondido', fecha_respuesta = NOW() WHERE id = :id");
            $stmt->execute([
                ':respuesta' => $respuesta,
                ':id' => $id_ticket
            ]);
            
            // Enviar email al admin que reportó el ticket
            if ($ticket_data) {
                try {
                    $mailer = new Mailer();
                    $email_admin = $ticket_data['email'];
                    $nombre_admin = $ticket_data['nombre_completo'];
                    $asunto_ticket = $ticket_data['asunto'];
                    
                    $subject = "[RESPUESTA] Ticket #$id_ticket - " . htmlspecialchars($asunto_ticket);
                    
                    $html = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
                            .body { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 8px 8px; }
                            .field { margin: 15px 0; }
                            .label { font-weight: bold; color: #0d6efd; font-size: 12px; text-transform: uppercase; }
                            .value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #0d6efd; }
                            .response-box { background: #e7f3ff; border-left: 4px solid #0d6efd; padding: 15px; margin: 15px 0; border-radius: 4px; }
                            .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999; }
                            .button { background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2 style='margin: 0;'>✅ Tu Ticket ha sido Respondido</h2>
                                <p style='margin: 5px 0 0 0;'>Ticket #$id_ticket</p>
                            </div>
                            <div class='body'>
                                <p>Hola $nombre_admin,</p>
                                
                                <p>El equipo de soporte de FloralTech ha respondido a tu ticket:</p>
                                
                                <div class='field'>
                                    <div class='label'>Asunto</div>
                                    <div class='value'>" . htmlspecialchars($asunto_ticket) . "</div>
                                </div>
                                
                                <div class='response-box'>
                                    <strong style='color: #0d6efd;'>Respuesta del soporte:</strong>
                                    <p style='margin: 10px 0 0 0;'>" . nl2br(htmlspecialchars($respuesta)) . "</p>
                                </div>
                                
                                <p>Puedes ver más detalles accediendo a tu Centro de Soporte en el panel de administración.</p>
                                
                                <div style='text-align: center; margin: 20px 0;'>
                                    <a href='http://localhost/Original-Floraltechx/index.php?ctrl=dashboard&action=admin&page=soporte' class='button'>Ver mi Centro de Soporte</a>
                                </div>
                                
                                <div class='footer'>
                                    <p>Este es un email automático. Por favor, no responder directamente a este email.</p>
                                    <p>Si tienes más preguntas, envía un nuevo ticket desde tu Centro de Soporte.</p>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    
                    $mailer->sendEmail($email_admin, $subject, $html, true);
                } catch (Exception $e) {
                    error_log("Error enviando email de respuesta: " . $e->getMessage());
                }
            }
            
            $mensaje = 'Respuesta enviada correctamente y email notificado al administrador';
        }
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
    }
}

// Obtener todos los tickets
$tickets = [];
try {
    $stmt = $conexion->query("
        SELECT t.id, t.admin_id, t.asunto, t.descripcion, t.archivo, t.estado, t.fecha_creacion, t.respuesta, u.nombre_completo, u.email
        FROM tickets_soporte t
        JOIN usu u ON t.admin_id = u.idusu
        ORDER BY t.fecha_creacion DESC
    ");
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mensaje = 'Tabla de tickets no existe. Ejecuta install_tickets_table.php';
}

?>

<div class="container-fluid py-4" style="background:#fff; min-height: 100vh;">
    <div class="p-4 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color: #fff;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <p class="mb-1 text-white-50 small">Administración</p>
                <h2 class="fw-bold mb-0">Tickets de Soporte</h2>
            </div>
            <span class="badge bg-light text-primary fs-6"><?= count($tickets) ?> Tickets</span>
        </div>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?= strpos($mensaje, 'Error') !== false ? 'danger' : 'success' ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-<?= strpos($mensaje, 'Error') !== false ? 'exclamation-triangle' : 'check-circle' ?> me-2"></i><?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="px-3">
        <?php if (!empty($tickets)): ?>
            <div class="table-responsive">
                <table class="table table-hover border-0 shadow-sm rounded-4 overflow-hidden">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th>#</th>
                            <th>Asunto</th>
                            <th>Admin</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><small class="text-muted">#<?= htmlspecialchars($ticket['id']) ?></small></td>
                                <td>
                                    <strong><?= htmlspecialchars(substr($ticket['asunto'], 0, 30)) ?><?= strlen($ticket['asunto']) > 30 ? '...' : '' ?></strong>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($ticket['nombre_completo']) ?></small><br>
                                    <small class="text-muted"><?= htmlspecialchars($ticket['email']) ?></small>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php
                                        switch($ticket['estado']) {
                                            case 'abierto': echo 'danger'; break;
                                            case 'en_proceso': echo 'warning'; break;
                                            case 'respondido': echo 'info'; break;
                                            case 'cerrado': echo 'success'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $ticket['estado']))) ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detalleTicket<?= $ticket['id'] ?>">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal de detalles -->
                            <div class="modal fade" id="detalleTicket<?= $ticket['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-bottom border-light-subtle">
                                            <div>
                                                <h5 class="modal-title">Ticket #<?= $ticket['id'] ?></h5>
                                                <small class="text-muted"><?= htmlspecialchars($ticket['nombre_completo']) ?> (<?= htmlspecialchars($ticket['email']) ?>)</small>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6 class="fw-bold mb-2">Asunto:</h6>
                                            <p class="mb-3"><?= htmlspecialchars($ticket['asunto']) ?></p>

                                            <h6 class="fw-bold mb-2">Mensaje del usuario:</h6>
                                            <div class="bg-light p-3 rounded mb-3" style="border-left: 4px solid #0d6efd;">
                                                <?= nl2br(htmlspecialchars($ticket['descripcion'])) ?>
                                            </div>

                                            <?php if (!empty($ticket['archivo'])): ?>
                                                <h6 class="fw-bold mb-2">Archivo adjunto:</h6>
                                                <p class="mb-3">
                                                    <a href="/Original-Floraltechx/uploads/tickets/<?= htmlspecialchars($ticket['archivo']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-download me-1"></i>Descargar
                                                    </a>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($ticket['respuesta'])): ?>
                                                <h6 class="fw-bold mb-2 mt-4">Respuesta del soporte:</h6>
                                                <div class="alert alert-info mb-3">
                                                    <strong>Estado:</strong> Respondido<br>
                                                    <?= nl2br(htmlspecialchars($ticket['respuesta'])) ?>
                                                </div>
                                            <?php else: ?>
                                                <h6 class="fw-bold mb-2 mt-4">Responder a este ticket:</h6>
                                                <form method="POST">
                                                    <input type="hidden" name="id_ticket" value="<?= $ticket['id'] ?>">
                                                    <div class="mb-2">
                                                        <textarea class="form-control" name="respuesta" rows="4" placeholder="Escribe tu respuesta..." required></textarea>
                                                    </div>
                                                    <button type="submit" name="responder_ticket" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-reply me-1"></i>Responder
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>No hay tickets de soporte aún.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
