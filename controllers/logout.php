<?php
session_start();

// Verificar si hay una sesión activa
if (isset($_SESSION)) {
    // Limpiar todas las variables de sesión
    session_unset();
    
    // Destruir la sesión
    session_destroy();
    
    // Eliminar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Redirigir al login
header("Location: ../index.php?ctrl=login&action=index");
exit();
?>