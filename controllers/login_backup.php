<?php
// controllers/login.php
require_once 'models/conexion.php';
require_once 'models/user.php';

class login {
    private $model;

    public function __construct() {
        $this->model = new User();
    }

    /**
     * Muestra la vista de login
     */
    public function index() {
        // Si ya existe una sesión activa, redirigir según el tipo de usuario
        if (isset($_SESSION['user'])) {
            $this->redirectByUserType($_SESSION['user']['tpusu_idtpusu']);
        }
        
        require_once 'views/login.php';
    }

    /**
     * Procesa el login del usuario
     */
    public function authenticate() {
        $error = "";
        
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST["username"] ?? '';
            $password = $_POST["password"] ?? '';

            $foundUser = $this->model->validateLogin($username, $password);

            if ($foundUser) {
                $_SESSION['user'] = $foundUser;
                $_SESSION['user_id'] = $foundUser['idusu'];
                $_SESSION['user_type'] = $foundUser['tpusu_idtpusu'];
                
                // Redirigir según el tipo de usuario
                $this->redirectByUserType($foundUser['tpusu_idtpusu']);
            } else {
                $error = "Usuario o contraseña incorrectos.";
                $_SESSION['login_error'] = $error;
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }
        }
    }

    /**
     * Redirige al usuario según su tipo
     */
    private function redirectByUserType($userType) {
        switch ($userType) {
            case 1: // Administrador
                header('Location: index.php?ctrl=dashboard&action=admin');
                break;
            case 2: // Vendedor
                header('Location: index.php?ctrl=dashboard&action=empleado');
                break;
            case 3: // Inventario
                header('Location: index.php?ctrl=dashboard&action=empleado');
                break;
            case 4: // Repartidor
                header('Location: index.php?ctrl=dashboard&action=empleado');
                break;
            case 5: // Cliente
                header('Location: index.php?ctrl=dashboard&action=cliente');
                break;
            default:
                header('Location: index.php?ctrl=dashboard&action=index');
                break;
        }
        exit();
    }

    /**
     * Muestra la vista de recuperación de contraseña
     */
    public function forgot() {
        // Verificar que el archivo existe antes de incluirlo
        $forgotPasswordFile = __DIR__ . "/../views/forgotpassword.php";
        if (file_exists($forgotPasswordFile)) {
            include($forgotPasswordFile);
        } else {
            // Buscar en rutas alternativas
            $alternativeFile = dirname(__DIR__) . "/views/forgotpassword.php";
            if (file_exists($alternativeFile)) {
                include($alternativeFile);
            } else {
                echo "<div class='alert alert-danger'>Error: No se pudo encontrar la vista de recuperación de contraseña.</div>";
                echo "<p>Buscando en: $forgotPasswordFile</p>";
                echo "<p>Alternativo: $alternativeFile</p>";
            }
        }
    }

    /**
     * Muestra la vista de reseteo de contraseña
     */
    public function reset() {
        $resetPasswordFile = __DIR__ . "/../views/resetpassword.php";
        if (file_exists($resetPasswordFile)) {
            include($resetPasswordFile);
        } else {
            echo "<div class='alert alert-danger'>Error: No se pudo encontrar la vista de reseteo de contraseña.</div>";
        }
    }

    /**
     * Procesa el envío de correo de recuperación
     */
    public function sendRecovery() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $correo = trim($_POST["email"] ?? '');

            if (empty($correo)) {
                $_SESSION['recovery_error'] = "Por favor ingresa tu correo electrónico.";
                header('Location: index.php?ctrl=login&action=forgot');
                exit();
            }

            // Validar formato de email
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['recovery_error'] = "Por favor ingresa un correo electrónico válido.";
                header('Location: index.php?ctrl=login&action=forgot');
                exit();
            }

            try {
                // Verificar directamente en la tabla usu si el correo existe y está activo
                $conexion = new conexion();
                $db = $conexion->get_conexion();
                
                $stmt = $db->prepare("SELECT idusu, email, nombre_completo, activo FROM usu WHERE email = ? AND activo = 1");
                $stmt->execute([$correo]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario) {
                    // El correo existe en la base de datos
                    
                    // Generar token de recuperación único
                    $token = bin2hex(random_bytes(32));
                    $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));
                    
                    // Guardar token en base de datos
                    $tokenGuardado = $this->saveRecoveryToken($usuario['idusu'], $token, $expiracion);
                    
                    if ($tokenGuardado) {
                        // Enviar correo de recuperación
                        $enviado = $this->sendRecoveryEmail($usuario['email'], $usuario['nombre_completo'], $token);
                        
                        if ($enviado) {
                            $_SESSION['recovery_success'] = "Te hemos enviado un correo con las instrucciones para recuperar tu contraseña. Revisa tu bandeja de entrada y carpeta de spam.";
                            
                            // Log exitoso para debugging
                            error_log("✅ Correo de recuperación enviado exitosamente a: " . $usuario['email'] . " (" . $usuario['nombre_completo'] . ")");
                        } else {
                            $_SESSION['recovery_error'] = "Error al enviar el correo. Verifica la configuración del servidor de correo o intenta más tarde.";
                            
                            // Log de error para debugging
                            error_log("❌ Error al enviar correo de recuperación a: " . $usuario['email']);
                        }
                    } else {
                        $_SESSION['recovery_error'] = "Error al generar el token de recuperación. Intenta más tarde.";
                        error_log("❌ Error al guardar token de recuperación para usuario: " . $usuario['email']);
                    }
                } else {
                    // El correo NO existe en la base de datos o el usuario está inactivo
                    // Por seguridad, mostramos un mensaje genérico para no revelar si el email existe
                    $_SESSION['recovery_success'] = "Si el correo existe en nuestro sistema, recibirás las instrucciones de recuperación en breve.";
                    
                    // Log para debugging (opcional en producción)
                    error_log("⚠️ Intento de recuperación con correo no registrado: " . $correo);
                }
                
            } catch (Exception $e) {
                $_SESSION['recovery_error'] = "Error del sistema. Intenta más tarde.";
                error_log("❌ Error en sistema de recuperación: " . $e->getMessage());
            }
        }
        
        header('Location: index.php?ctrl=login&action=forgot');
        exit();
    }

    /**
     * Guarda el token de recuperación en la base de datos
     */
    private function saveRecoveryToken($userId, $token, $expiracion) {
        try {
            $conexion = new conexion();
            $db = $conexion->get_conexion();
            
            // Primero verificar si la tabla existe, si no, crearla
            $this->createRecoveryTable($db);
            
            // Limpiar tokens expirados del usuario
            $stmt = $db->prepare("DELETE FROM tokens_recuperacion WHERE idusu = ? OR expiracion < NOW()");
            $stmt->execute([$userId]);
            
            // Insertar nuevo token
            $stmt = $db->prepare("INSERT INTO tokens_recuperacion (idusu, token, expiracion, usado) VALUES (?, ?, ?, 0)");
            $resultado = $stmt->execute([$userId, $token, $expiracion]);
            
            if ($resultado) {
                error_log("✅ Token de recuperación guardado para usuario ID: $userId");
                return true;
            } else {
                error_log("❌ Error al guardar token para usuario ID: $userId");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("❌ Excepción al guardar token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea la tabla de tokens de recuperación si no existe
     */
    private function createRecoveryTable($db) {
        $sql = "CREATE TABLE IF NOT EXISTS tokens_recuperacion (
            id INT(11) PRIMARY KEY AUTO_INCREMENT,
            idusu INT(11) NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            expiracion DATETIME NOT NULL,
            usado BOOLEAN DEFAULT FALSE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (idusu) REFERENCES usu(idusu) ON DELETE CASCADE
        )";
        $db->exec($sql);
    }

    /**
     * Envía el correo de recuperación usando el sistema seguro mejorado
     */
    private function sendRecoveryEmail($email, $nombre, $token) {
        // Incluir el sistema de correos seguro
        require_once __DIR__ . '/../views/mailer_seguro.php';
        
        // Usar el sistema mejorado de envío de correos
        $enviado = enviarCorreoRecuperacionSeguro($email, $token);
        
        // Log para debugging
        if ($enviado) {
            error_log("✅ Correo de recuperación enviado a: $email");
            
            // Mostrar enlace para debugging
            $baseUrl = "http://" . $_SERVER['HTTP_HOST'];
            $projectPath = "/ProyectoFloralTechhh";
            $recoveryLink = $baseUrl . $projectPath . "/index.php?ctrl=login&action=resetPassword&token=" . $token;
            error_log("🔗 Enlace de recuperación: $recoveryLink");
        } else {
            error_log("❌ Error al enviar correo a: $email");
        }
        
        return $enviado;
    }

    /**
     * Template HTML para el correo de recuperación
     */
    private function getEmailTemplate($nombre, $link) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                .container { max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; }
                .header { background: #2293c3; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .button { background: #2293c3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌸 FloralTech</h1>
                </div>
                <div class='content'>
                    <h2>Hola, $nombre</h2>
                    <p>Has solicitado recuperar tu contraseña en FloralTech.</p>
                    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                    <a href='$link' class='button'>Recuperar Contraseña</a>
                    <p><strong>Importante:</strong> Este enlace expira en 1 hora por seguridad.</p>
                    <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " FloralTech - Sistema de Gestión Floral</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Muestra el formulario para restablecer contraseña con token
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['login_error'] = "Token de recuperación inválido.";
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }
        
        // Verificar que el token sea válido
        $tokenData = $this->validateRecoveryToken($token);
        if (!$tokenData) {
            $_SESSION['login_error'] = "El enlace de recuperación ha expirado o es inválido.";
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }
        
        include(__DIR__ . "/../views/resetpassword.php");
    }

    /**
     * Procesa el cambio de contraseña
     */
    public function updatePassword() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $token = $_POST['token'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validaciones
            if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['reset_error'] = "Todos los campos son obligatorios.";
                header("Location: index.php?ctrl=login&action=resetPassword&token=$token");
                exit();
            }
            
            if ($newPassword !== $confirmPassword) {
                $_SESSION['reset_error'] = "Las contraseñas no coinciden.";
                header("Location: index.php?ctrl=login&action=resetPassword&token=$token");
                exit();
            }
            
            if (strlen($newPassword) < 6) {
                $_SESSION['reset_error'] = "La contraseña debe tener al menos 6 caracteres.";
                header("Location: index.php?ctrl=login&action=resetPassword&token=$token");
                exit();
            }
            
            // Verificar token
            $tokenData = $this->validateRecoveryToken($token);
            if (!$tokenData) {
                $_SESSION['login_error'] = "El enlace de recuperación ha expirado o es inválido.";
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }
            
            // Actualizar contraseña
            if ($this->updateUserPassword($tokenData['idusu'], $newPassword)) {
                // Marcar token como usado
                $this->markTokenAsUsed($token);
                
                $_SESSION['login_success'] = "Contraseña actualizada exitosamente. Puedes iniciar sesión.";
                header('Location: index.php?ctrl=login&action=index');
            } else {
                $_SESSION['reset_error'] = "Error al actualizar la contraseña.";
                header("Location: index.php?ctrl=login&action=resetPassword&token=$token");
            }
            exit();
        }
    }

    /**
     * Valida si un token de recuperación es válido
     */
    private function validateRecoveryToken($token) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("SELECT * FROM tokens_recuperacion WHERE token = ? AND expiracion > NOW() AND usado = 0");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza la contraseña del usuario
     */
    private function updateUserPassword($userId, $newPassword) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE usu SET clave = ? WHERE idusu = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Marca un token como usado
     */
    private function markTokenAsUsed($token) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("UPDATE tokens_recuperacion SET usado = 1 WHERE token = ?");
        return $stmt->execute([$token]);
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        session_destroy();
        header('Location: index.php?ctrl=login&action=index');
        exit();
    }
}
?>