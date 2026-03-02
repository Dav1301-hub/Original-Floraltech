<?php

class SupportController {
    private $db;
    private $id_admin;

    public function __construct() {
        require_once __DIR__ . '/../models/conexion.php';
        require_once __DIR__ . '/../models/Mailer.php';
        $conexion = new conexion();
        $this->db = $conexion->get_conexion();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->id_admin = $_SESSION['user']['idusu'] ?? ($_SESSION['user_id'] ?? null);
    }

    public function index() {
        // Redirigir al dashboard unificado
        header('Location: index.php?ctrl=dashboard&action=admin&page=soporte');
        exit;
    }

    /**
     * Obtiene el contexto necesario para la vista de soporte dentro del dashboard
     */
    public function obtenerContexto() {
        return [
            'tickets' => $this->getTickets(),
            'admin' => $this->getAdminData(),
            'mensaje_exito' => $_GET['success'] ?? '',
            'mensaje_error' => $_GET['error'] ?? ''
        ];
    }

    public function getTickets() {
        try {
            $stmt = $this->db->prepare("SELECT id, asunto, estado, fecha_creacion, respuesta, descripcion FROM tickets_soporte WHERE admin_id = :id ORDER BY fecha_creacion DESC");
            $stmt->execute([':id' => $this->id_admin]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAdminData() {
        try {
            $stmt = $this->db->prepare("SELECT idusu, nombre_completo, email FROM usu WHERE idusu = :id LIMIT 1");
            $stmt->execute([':id' => $this->id_admin]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function enviarTicket() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?ctrl=dashboard&action=admin&page=soporte');
            exit;
        }

        try {
            $asunto = trim($_POST['asunto_soporte'] ?? '');
            $descripcion = trim($_POST['descripcion_soporte'] ?? '');
            
            if (empty($asunto) || empty($descripcion)) {
                throw new Exception('El asunto y la descripción son requeridos');
            }

            $archivo = $this->procesarArchivo();

            // Guardar ticket en BD
            $stmt = $this->db->prepare("INSERT INTO tickets_soporte (admin_id, asunto, descripcion, archivo, estado) VALUES (:admin_id, :asunto, :descripcion, :archivo, 'abierto')");
            $stmt->execute([
                ':admin_id' => $this->id_admin,
                ':asunto' => $asunto,
                ':descripcion' => $descripcion,
                ':archivo' => $archivo
            ]);
            
            $id_ticket = $this->db->lastInsertId();
            $admin_data = $this->getAdminData();

            // Enviar email al super admin
            try {
                $mailer = new Mailer();
                $mailer->sendSupportTicketEmail(
                    'epymes270@gmail.com',
                    $id_ticket,
                    $admin_data,
                    $asunto,
                    $descripcion,
                    $archivo
                );
            } catch (Exception $e) {
                error_log("Error enviando email de soporte: " . $e->getMessage());
            }

            header('Location: index.php?ctrl=dashboard&action=admin&page=soporte&success=' . urlencode('Ticket enviado exitosamente.'));
            exit;

        } catch (Exception $e) {
            header('Location: index.php?ctrl=dashboard&action=admin&page=soporte&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function eliminarTicket() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_ticket'])) {
            header('Location: index.php?ctrl=dashboard&action=admin&page=soporte');
            exit;
        }

        try {
            $id_ticket = (int)$_POST['id_ticket'];
            $stmt = $this->db->prepare("DELETE FROM tickets_soporte WHERE id = :id AND admin_id = :admin_id");
            $stmt->execute([':id' => $id_ticket, ':admin_id' => $this->id_admin]);
            
            header('Location: index.php?ctrl=dashboard&action=admin&page=soporte&success=' . urlencode('Ticket eliminado correctamente.'));
            exit;
        } catch (Exception $e) {
            header('Location: index.php?ctrl=dashboard&action=admin&page=soporte&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    private function procesarArchivo() {
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
            
            $uploadDir = __DIR__ . '/../uploads/tickets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $filename = uniqid('ticket_') . '_' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                return $filename;
            }
        }
        return null;
    }
}
