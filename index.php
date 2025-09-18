<?php

// Configurar tiempo de expiración de sesión (15 minutos)
ini_set('session.gc_maxlifetime', 900); // 900 segundos = 15 minutos
session_set_cookie_params(900); // Cookie expira en 15 minutos

// 1. Configuración inicial
session_start();

require_once 'models/conexion.php';
require_once 'models/data.php';

// 2. Definir constantes para rutas
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
define('CONTROLLERS_DIR', __DIR__ . '/controllers/');
define('MODELS_DIR', __DIR__ . '/models/');
define('VIEWS_DIR', __DIR__ . '/views/');

// 3. Obtener parámetros de la URL
$ctrl = isset($_GET['ctrl']) ? $_GET['ctrl'] : 'login';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// 4. Definir acciones públicas que no requieren autenticación
$publicActions = [
    'login' => ['index', 'authenticate', 'forgot', 'sendRecovery', 'resetPassword', 'updatePassword', 'logout'],
    'register' => ['index', 'create']
];

// Verificar si el usuario está logueado (excepto para acciones públicas)
$isPublicAction = isset($publicActions[$ctrl]) && in_array($action, $publicActions[$ctrl]);

// Verificar tiempo de inactividad para sesiones activas
if (isset($_SESSION['user']) && !$isPublicAction) {
    $timeout = 900; // 15 minutos en segundos
    
    // Verificar inactividad
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
        // Sesión expirada por inactividad
        session_unset();
        session_destroy();
        
        // Redirigir al login con mensaje de timeout
        header('Location: index.php?ctrl=login&action=index&timeout=1');
        exit();
    }
    
    // Actualizar tiempo de última actividad
    $_SESSION['LAST_ACTIVITY'] = time();
}

if (!isset($_SESSION['user']) && !$isPublicAction) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}

// 5. Validar y cargar el controlador
$controllerFile = CONTROLLERS_DIR . $ctrl . '.php';
if (!file_exists($controllerFile)) {
    die("Error: El controlador '$ctrl' no existe.");
}

require_once $controllerFile;

// 6. Verificar que la clase del controlador existe
$controllerClass = $ctrl;
if (!class_exists($controllerClass)) {
    die("Error: La clase '$controllerClass' no está definida.");
}

// 7. Instanciar el controlador y llamar a la acción
try {
    $controller = new $controllerClass();
    
    // Verificar que el método existe
    if (!method_exists($controller, $action)) {
        die("Error: La acción '$action' no existe en el controlador '$ctrl'.");
    }
    
    // Llamar a la acción
    $controller->$action();
    
} catch (Exception $e) {
    // Manejo básico de errores
    die("Error: " . $e->getMessage());
}

?>