<?php
// controllers/register.php
require_once 'models/user.php';

class Register {
    private $model;
    private $recaptchaSecretKey;

    public function __construct() {
        $this->model = new User();
        
        // Cargar configuración de reCAPTCHA
        $recaptchaConfig = include 'config/recaptcha.php';
        $this->recaptchaSecretKey = $recaptchaConfig['secret_key'];
    }

    /**
     * Muestra la vista de registro
     */
    public function index() {
        // Si ya existe una sesión activa, redirigir al dashboard
        if (isset($_SESSION['user'])) {
            header('Location: index.php?ctrl=dashboard&action=index');
            exit();
        }
        
        require_once 'views/register.php';
    }

    /**
     * Verifica el token de reCAPTCHA
     */
    private function verifyRecaptcha($recaptchaResponse) {
        // Para desarrollo local, saltar verificación (opcional)
        if ($_SERVER['HTTP_HOST'] === 'localhost' || 
            $_SERVER['SERVER_NAME'] === 'localhost' ||
            $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
            error_log("Modo desarrollo: Saltando verificación reCAPTCHA");
            return true;
        }
        
        if (empty($recaptchaResponse)) {
            error_log("Error reCAPTCHA: Token vacío");
            return false;
        }
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->recaptchaSecretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        // Usar cURL para mejor control de errores
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);
        
        if ($response === false) {
            error_log("Error reCAPTCHA: No se pudo conectar con Google - " . $curlError);
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("Error reCAPTCHA: HTTP Error " . $httpCode);
            return false;
        }
        
        $result = json_decode($response, true);
        error_log("Respuesta reCAPTCHA: " . print_r($result, true));
        
        return $result['success'] === true;
    }

    /**
     * Procesa el registro del usuario
     */
    public function create() {
        $error = "";
        $success = "";
        
        error_log("=== INICIO PROCESO DE REGISTRO ===");
        error_log("REQUEST_METHOD: " . $_SERVER["REQUEST_METHOD"]);
        error_log("POST data: " . print_r($_POST, true));
        
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Verificar reCAPTCHA primero
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
            
            if (!$this->verifyRecaptcha($recaptchaResponse)) {
                $error = "Por favor, verifica que no eres un robot. Completa el reCAPTCHA.";
                error_log("Error reCAPTCHA: Validación fallida");
                $_SESSION['register_error'] = $error;
                header('Location: index.php?ctrl=register&action=index');
                exit();
            }
            
            error_log("reCAPTCHA validado correctamente");
            
            $username = trim($_POST["username"] ?? '');
            $nombre_completo = trim($_POST["nombre_completo"] ?? '');
            $telefono = trim($_POST["telefono"] ?? '');
            $email = trim($_POST["email"] ?? '');
            $password = $_POST["password"] ?? '';
            $password_confirm = $_POST["password_confirm"] ?? '';
            $tpusu_idtpusu = intval($_POST["tpusu_idtpusu"] ?? 5); // Por defecto cliente (ID = 5)
            $direccion = trim($_POST["direccion"] ?? '');

            error_log("Datos procesados: username=$username, email=$email, telefono=$telefono");

            // Validaciones
            if (empty($username) || empty($nombre_completo) || empty($telefono) || empty($email) || empty($password) || empty($direccion)) {
                $error = "Todos los campos son obligatorios.";
                error_log("Error: Campos vacíos detectados");
            } elseif ($password !== $password_confirm) {
                $error = "Las contraseñas no coinciden.";
                error_log("Error: Contraseñas no coinciden");
            } elseif (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
                error_log("Error: Contraseña muy corta");
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato del email no es válido.";
                error_log("Error: Email inválido");
            } elseif ($this->model->userExists($email, $username)) {
                $error = "El email o nombre de usuario ya están registrados.";
                error_log("Error: Usuario ya existe");
            } else {
                // Preparar datos para el registro
                $userData = [
                    'username' => $username,
                    'nombre_completo' => $nombre_completo,
                    'telefono' => $telefono,
                    'email' => $email,
                    'clave' => $password,
                    'tpusu_idtpusu' => $tpusu_idtpusu,
                    'direccion' => $direccion
                ];

                // Debug: Log de intento de registro
                error_log("=== LLAMANDO A registerUser ===");
                error_log("Intento de registro con datos: " . print_r($userData, true));

                $registerResult = $this->model->registerUser($userData);
                error_log("Resultado de registerUser: " . ($registerResult ? 'TRUE' : 'FALSE'));

                if ($registerResult) {
                    $success = "Usuario registrado exitosamente.";
                    $_SESSION['register_success'] = $success;
                    error_log("=== REGISTRO EXITOSO === para usuario: " . $username);
                    // Si el usuario es administrador, redirigir al dashboard de admin
                    if ($tpusu_idtpusu == 1) {
                        // Iniciar sesión automáticamente
                        $_SESSION['user'] = [
                            'username' => $username,
                            'nombre_completo' => $nombre_completo,
                            'telefono' => $telefono,
                            'email' => $email,
                            'tpusu_idtpusu' => $tpusu_idtpusu,
                            'direccion' => $direccion
                        ];
                        header('Location: index.php?ctrl=dashboard&action=admin');
                        exit();
                    } else {
                        header('Location: index.php?ctrl=login&action=index');
                        exit();
                    }
                } else {
                    $error = "Error al registrar el usuario. Inténtalo de nuevo.";
                    error_log("=== ERROR EN REGISTRO === para usuario: " . $username);
                }
            }
            
            if (!empty($error)) {
                $_SESSION['register_error'] = $error;
                error_log("Error final: " . $error);
            }
            
            header('Location: index.php?ctrl=register&action=index');
            exit();
        } else {
            error_log("Método no es POST");
        }
        
        error_log("=== FIN PROCESO DE REGISTRO ===");
    }
}
?>