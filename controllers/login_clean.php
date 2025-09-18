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
        if (isset($_SESSION['user'])) {
            $this->redirectByUserType($_SESSION['user']['tpusu_idtpusu']);
        }
        
        require_once 'views/login.php';
    }

    /**
     * Procesa el login del usuario
     */
    public function authenticate() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST["username"] ?? '';
            $password = $_POST["password"] ?? '';

            $foundUser = $this->model->validateLogin($username, $password);

            if ($foundUser) {
                $_SESSION['user'] = $foundUser;
                $_SESSION['user_id'] = $foundUser['idusu'];
                $_SESSION['user_type'] = $foundUser['tpusu_idtpusu'];
                
                $this->redirectByUserType($foundUser['tpusu_idtpusu']);
            } else {
                $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
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
            case 1:
                header('Location: index.php?ctrl=dashboard&action=admin');
                break;
            case 2:
            case 3:
            case 4:
                header('Location: index.php?ctrl=dashboard&action=empleado');
                break;
            case 5:
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
        include(__DIR__ . "/../views/forgotpassword.php");
    }

    /**
     * Procesa el envío de solicitud de recuperación de contraseña
     */
    public function sendRecovery() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST['email'] ?? '';
            
            if (empty($email)) {
                $_SESSION['forgot_error'] = "Por favor ingresa tu correo electrónico.";
                header('Location: index.php?ctrl=login&action=forgot');
                exit();
            }
            
            $user = $this->getUserByEmail($email);
            
            if ($user) {
                $token = $this->generateRecoveryToken($user['idusu']);
                
                if ($token) {
                    require_once __DIR__ . '/../views/mailer_seguro.php';
                    $enviado = enviarCorreoRecuperacionSeguro($email, $token);
                    
                    if ($enviado) {
                        $_SESSION['forgot_success'] = "Te hemos enviado un correo con el enlace de recuperación.";
                    } else {
                        $_SESSION['forgot_error'] = "Error al enviar el correo. Intenta más tarde.";
                    }
                } else {
                    $_SESSION['forgot_error'] = "Error interno. Intenta más tarde.";
                }
            } else {
                $_SESSION['forgot_success'] = "Si el correo existe en nuestro sistema, recibirás un enlace de recuperación.";
            }
            
            header('Location: index.php?ctrl=login&action=forgot');
            exit();
        }
    }

    /**
     * Muestra el formulario para restablecer contraseña
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['login_error'] = "Token de recuperación inválido.";
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }
        
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
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
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
            
            $tokenData = $this->validateRecoveryToken($token);
            if (!$tokenData) {
                $_SESSION['login_error'] = "El enlace de recuperación ha expirado o es inválido.";
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }
            
            if ($this->updateUserPassword($tokenData['idUsuario'], $newPassword)) {
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

    // Métodos privados auxiliares
    private function getUserByEmail($email) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("SELECT idusu, username, nombre_completo, email FROM usu WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function generateRecoveryToken($userId) {
        try {
            $conexion = new conexion();
            $db = $conexion->get_conexion();
            
            $stmt = $db->prepare("DELETE FROM tokens_recuperacion WHERE idUsuario = ? OR expiracion < NOW()");
            $stmt->execute([$userId]);
            
            $token = bin2hex(random_bytes(32));
            $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));
            
            $stmt = $db->prepare("INSERT INTO tokens_recuperacion (idUsuario, token, expiracion) VALUES (?, ?, ?)");
            $result = $stmt->execute([$userId, $token, $expiracion]);
            
            return $result ? $token : false;
            
        } catch (Exception $e) {
            error_log("Error al generar token: " . $e->getMessage());
            return false;
        }
    }

    private function validateRecoveryToken($token) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("SELECT * FROM tokens_recuperacion WHERE token = ? AND expiracion > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function updateUserPassword($userId, $newPassword) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE usu SET clave = ? WHERE idusu = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    private function markTokenAsUsed($token) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("DELETE FROM tokens_recuperacion WHERE token = ?");
        return $stmt->execute([$token]);
    }
}
?>
