<?php
// controllers/login.php
require_once __DIR__ . '/../models/conexion.php';
require_once __DIR__ . '/../models/user.php';
// Incluir helper de sesión
require_once __DIR__ . '/../helpers/session_helper.php';

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
        
        // Verificar si la sesión expiró por inactividad
        if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
            $_SESSION['login_error'] = "Sesión expirada por inactividad. Por favor ingresa nuevamente.";
        }
        
        // Mostrar mensajes de error o éxito
        $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
        $success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : '';
        $forgot_success = isset($_SESSION['forgot_success']) ? $_SESSION['forgot_success'] : '';
        
        // Obtener información de intentos para la vista
        $attempts_remaining = isset($_SESSION['attempts_remaining']) ? $_SESSION['attempts_remaining'] : null;
        $max_attempts = isset($_SESSION['max_attempts']) ? $_SESSION['max_attempts'] : 3;
        $account_locked = isset($_SESSION['account_locked']) ? $_SESSION['account_locked'] : false;
        
        // Limpiar mensajes después de mostrarlos
        unset($_SESSION['login_error']);
        unset($_SESSION['login_success']);
        unset($_SESSION['forgot_success']);
        
        require_once 'views/login.php';
    }

    /**
     * Procesa el login del usuario con sistema de bloqueo
     */
    public function authenticate() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? '');
            $password = $_POST["password"] ?? '';

            error_log("=== INTENTO DE LOGIN ===");
            error_log("Usuario: " . $username);

            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = "Usuario y contraseña son obligatorios.";
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }

            // Validar entrada contra inyección SQL
            require_once __DIR__ . '/../helpers/security_helper.php';
            
            $username_limpio = sanitizarCampoBusqueda($username, 'login_username');
            
            if ($username_limpio === false) {
                $_SESSION['login_error'] = "Entrada inválida detectada. Por seguridad, tu solicitud fue bloqueada.";
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }
            
            // Usar el username limpio para el resto del proceso
            $username = $username_limpio;

            // Usar el método validateLogin existente que ahora incluye el sistema de bloqueo
            $foundUser = $this->model->validateLogin($username, $password);

            if ($foundUser) {
                // Login exitoso
                $_SESSION['user'] = $foundUser;
                $_SESSION['user_id'] = $foundUser['idusu'];
                $_SESSION['usuario_id'] = $foundUser['idusu']; // Para compatibilidad AJAX
                $_SESSION['user_type'] = $foundUser['tpusu_idtpusu'];
                
                // Limpiar variables de intentos fallidos de la sesión
                unset($_SESSION['attempts_remaining']);
                unset($_SESSION['max_attempts']);
                unset($_SESSION['account_locked']);
                unset($_SESSION['lockout_time']);
                
                error_log("Login exitoso: " . $username);
                $this->redirectByUserType($foundUser['tpusu_idtpusu']);
                
            } else {
                // Login fallido - obtener información del usuario para mostrar mensaje adecuado
                $userInfo = $this->getUserInfoForMessage($username);
                
                if ($userInfo && $userInfo['activo'] == 0) {
                    // Cuenta bloqueada
                    $_SESSION['login_error'] = "Cuenta bloqueada permanentemente por múltiples intentos fallidos. Contacta al administrador.";
                    $_SESSION['account_locked'] = true;
                    error_log("Intento de login en cuenta bloqueada: " . $username);
                } else {
                    // Credenciales incorrectas
                    $remainingAttempts = $userInfo ? $this->model->getRemainingAttempts($userInfo['idusu']) : 3;
                    
                    // Guardar intentos restantes en sesión para la vista
                    $_SESSION['attempts_remaining'] = $remainingAttempts;
                    $_SESSION['max_attempts'] = 3;
                    
                    if ($remainingAttempts <= 0) {
                        $_SESSION['login_error'] = "Cuenta bloqueada permanentemente por múltiples intentos fallidos. Contacta al administrador.";
                        $_SESSION['account_locked'] = true;
                    } else {
                        $_SESSION['login_error'] = "Usuario o contraseña incorrectos. Te quedan " . $remainingAttempts . " intentos.";
                        $_SESSION['account_locked'] = false;
                    }
                    
                    error_log("Login fallido para usuario: " . $username . ", intentos restantes: " . $remainingAttempts);
                }
                
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }
        }
    }

    /**
     * Obtiene información del usuario para mostrar mensajes personalizados
     */
    private function getUserInfoForMessage($username) {
        // Método auxiliar para obtener información del usuario sin validar contraseña
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $query = "SELECT idusu, activo, intentos_fallidos FROM usu WHERE email = :username OR username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Limpiar específicamente las variables de intentos
        unset($_SESSION['attempts_remaining']);
        unset($_SESSION['max_attempts']);
        unset($_SESSION['account_locked']);
        unset($_SESSION['lockout_time']);
        
        // Si se desea destruir la cookie de sesión también
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
        
        // Redirigir al login
        header('Location: index.php?ctrl=login&action=index');
        exit();
    }

    /**
     * Muestra la vista de recuperación de contraseña
     */
    public function forgot() {
        // Verificar si hay cuentas bloqueadas antes de permitir recuperación
        $error = isset($_SESSION['forgot_error']) ? $_SESSION['forgot_error'] : '';
        $success = isset($_SESSION['forgot_success']) ? $_SESSION['forgot_success'] : '';
        
        unset($_SESSION['forgot_error']);
        unset($_SESSION['forgot_success']);
        
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
                // Verificar si la cuenta está bloqueada
                if ($this->model->isAccountLocked($user['idusu'])) {
                    $_SESSION['forgot_error'] = "Tu cuenta está bloqueada permanentemente. Contacta al administrador.";
                    header('Location: index.php?ctrl=login&action=forgot');
                    exit();
                }
                
                $token = $this->generateRecoveryToken($user['idusu']);
                
                if ($token) {
                    require_once __DIR__ . '/../views/mailer.php';
                    $enviado = enviarCorreoRecuperacion($email, $token);
                    
                    if ($enviado) {
                        $_SESSION['forgot_success'] = "Te hemos enviado un correo con el enlace de recuperación.";
                    } else {
                        $_SESSION['forgot_error'] = "Error al enviar el correo. Intenta más tarde.";
                    }
                } else {
                    $_SESSION['forgot_error'] = "Error interno. Intenta más tarde.";
                }
            } else {
                // Por seguridad, no revelar si el email existe o no
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
        
        // Verificar si la cuenta está bloqueada
        if ($this->model->isAccountLocked($tokenData['idUsuario'])) {
            $_SESSION['login_error'] = "Tu cuenta está bloqueada permanentemente. Contacta al administrador.";
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
            
            // Verificar si la cuenta está bloqueada
            if ($this->model->isAccountLocked($tokenData['idUsuario'])) {
                $_SESSION['login_error'] = "Tu cuenta está bloqueada permanentemente. Contacta al administrador.";
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

    // Métodos privados auxiliares (se mantienen igual)
    private function getUserByEmail($email) {
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("SELECT idusu, username, nombre_completo, email FROM usu WHERE email = ?");
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