<?php
/**
 * Security Helper - Funciones de seguridad para prevenir inyecciones SQL
 * Archivo: helpers/security_helper.php
 */

/**
 * Valida entrada contra patrones de inyección SQL
 * @param string $input - Entrada del usuario a validar
 * @return bool - true si es segura, false si es sospechosa
 */
function validarContraInyeccionSQL($input) {
    // Patrones de inyección SQL comunes
    $patrones_peligrosos = [
        '/(\bOR\b\s*[\'"]?\w+[\'"]?\s*=\s*[\'"]?\w+[\'"]?)/i',  // OR '1'='1' o OR 1=1
        '/(\bAND\b\s*[\'"]?\w+[\'"]?\s*=\s*[\'"]?\w+[\'"]?)/i', // AND '1'='1' o AND 1=1
        '/(\bUNION\b.*\bSELECT\b)/i',                            // UNION SELECT
        '/(\bDROP\b.*\bTABLE\b)/i',                              // DROP TABLE
        '/(\bDROP\b.*\bDATABASE\b)/i',                           // DROP DATABASE
        '/(\bINSERT\b.*\bINTO\b)/i',                             // INSERT INTO
        '/(\bUPDATE\b.*\bSET\b)/i',                              // UPDATE SET
        '/(\bDELETE\b.*\bFROM\b)/i',                             // DELETE FROM
        '/(--|#|\/\*|\*\/)/i',                                   // Comentarios SQL
        '/(\bEXEC\b|\bEXECUTE\b)/i',                             // EXEC/EXECUTE
        '/(\bSELECT\b.*\bFROM\b)/i',                             // SELECT FROM
        '/(\bSHOW\b.*\bTABLES\b)/i',                             // SHOW TABLES
        '/(\bDESCRIBE\b|\bDESC\b)/i',                            // DESCRIBE
        '/(\bTRUNCATE\b)/i',                                     // TRUNCATE
        '/(\bALTER\b.*\bTABLE\b)/i',                             // ALTER TABLE
        '/(\bCREATE\b.*\bTABLE\b)/i',                            // CREATE TABLE
        '/(\bGRANT\b|\bREVOKE\b)/i',                             // GRANT/REVOKE
        '/(;|\||&)/i',                                           // Separadores de comandos
        '/(\bINTO\b.*\bOUTFILE\b)/i',                            // INTO OUTFILE
        '/(\bLOAD_FILE\b)/i',                                    // LOAD_FILE
        '/(\bBENCHMARK\b)/i',                                    // BENCHMARK
        '/(\bSLEEP\b\s*\()/i',                                   // SLEEP()
        '/(0x[0-9a-f]+)/i',                                      // Hexadecimal
        '/(\bCHAR\b\s*\()/i',                                    // CHAR()
        '/(\bCONCAT\b\s*\()/i',                                  // CONCAT()
    ];
    
    foreach ($patrones_peligrosos as $patron) {
        if (preg_match($patron, $input)) {
            return false; // Entrada sospechosa detectada
        }
    }
    
    return true; // Entrada parece segura
}

/**
 * Registra intentos de inyección SQL en archivo de logs
 * @param string $input - Entrada sospechosa del usuario
 * @param string $campo - Nombre del campo donde se intentó la inyección
 */
function registrarIntentoInyeccion($input, $campo = 'unknown') {
    $log_dir = __DIR__ . '/../logs';
    
    // Crear directorio de logs si no existe
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/security_attempts.log';
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $usuario = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'GUEST';
    
    $request_uri = $_SERVER['REQUEST_URI'] ?? 'CLI';
    
    $mensaje = "[{$fecha}] === SQL INJECTION ATTEMPT DETECTED ===\n";
    $mensaje .= "Usuario: {$usuario}\n";
    $mensaje .= "IP: {$ip}\n";
    $mensaje .= "Campo: {$campo}\n";
    $mensaje .= "User Agent: {$user_agent}\n";
    $mensaje .= "Input Malicioso: {$input}\n";
    $mensaje .= "URI: {$request_uri}\n";
    $mensaje .= str_repeat('=', 80) . "\n\n";
    
    // Escribir en log con manejo de errores
    @file_put_contents($log_file, $mensaje, FILE_APPEND | LOCK_EX);
    
    // También registrar en error_log de PHP
    error_log("SECURITY ALERT: SQL Injection attempt detected from IP {$ip} - User: {$usuario} - Campo: {$campo}");
}

/**
 * Sanitiza entrada para campos de búsqueda
 * Combina limpieza de HTML y validación contra inyección SQL
 * @param string $input - Entrada del usuario
 * @param string $campo - Nombre del campo (para logging)
 * @return string|false - Entrada sanitizada o false si es peligrosa
 */
function sanitizarCampoBusqueda($input, $campo = 'busqueda') {
    // Remover espacios en blanco al inicio y final
    $input = trim($input);
    
    // Si está vacío, retornar vacío
    if (empty($input)) {
        return '';
    }
    
    // Remover tags HTML
    $input = strip_tags($input);
    
    // Validar contra patrones de inyección SQL
    if (!validarContraInyeccionSQL($input)) {
        registrarIntentoInyeccion($input, $campo);
        return false; // Entrada peligrosa detectada
    }
    
    // Escapar caracteres especiales HTML
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    // Limitar longitud para evitar ataques de buffer overflow
    $input = substr($input, 0, 200);
    
    return $input;
}

/**
 * Valida y sanitiza parámetros GET/POST de forma segura
 * @param array $params - Array con nombres de parámetros a validar
 * @param string $method - 'GET' o 'POST'
 * @return array - Array con parámetros sanitizados o false si hay alguno peligroso
 */
function validarParametrosSeguro($params, $method = 'GET') {
    $source = $method === 'POST' ? $_POST : $_GET;
    $resultado = [];
    
    foreach ($params as $param) {
        if (isset($source[$param])) {
            $valor_limpio = sanitizarCampoBusqueda($source[$param], $param);
            
            if ($valor_limpio === false) {
                return false; // Entrada peligrosa detectada
            }
            
            $resultado[$param] = $valor_limpio;
        } else {
            $resultado[$param] = '';
        }
    }
    
    return $resultado;
}

/**
 * Valida entrada numérica (IDs, montos, etc)
 * @param mixed $input - Valor a validar
 * @param string $tipo - 'int' o 'float'
 * @return int|float|false - Valor sanitizado o false si no es válido
 */
function sanitizarNumero($input, $tipo = 'int') {
    if ($tipo === 'int') {
        $filtro = FILTER_VALIDATE_INT;
    } else {
        $filtro = FILTER_VALIDATE_FLOAT;
    }
    
    $resultado = filter_var($input, $filtro);
    
    return $resultado !== false ? $resultado : false;
}

/**
 * Valida formato de email
 * @param string $email - Email a validar
 * @return string|false - Email sanitizado o false si no es válido
 */
function sanitizarEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    
    return false;
}

/**
 * Valida formato de fecha
 * @param string $fecha - Fecha en formato Y-m-d
 * @return string|false - Fecha validada o false si no es válida
 */
function validarFecha($fecha) {
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return ($d && $d->format('Y-m-d') === $fecha) ? $fecha : false;
}

/**
 * Genera token CSRF para formularios
 * @return string - Token CSRF generado
 */
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF de formulario
 * @param string $token - Token recibido del formulario
 * @return bool - true si es válido, false si no
 */
function validarTokenCSRF($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Muestra mensaje de error de seguridad y redirige
 * @param string $ruta - Ruta de redirección
 * @param string $mensaje - Mensaje personalizado (opcional)
 */
function mostrarErrorSeguridad($ruta, $mensaje = null) {
    $mensaje_default = "Entrada inválida detectada. Por seguridad, tu solicitud fue bloqueada.";
    $_SESSION['error_seguridad'] = $mensaje ?? $mensaje_default;
    header("Location: {$ruta}");
    exit();
}

/**
 * Registra intentos de manipulación del DOM (PSE2)
 * @param array $datos - Datos de la manipulación detectada
 * @param string $tipo - Tipo de manipulación
 */
function registrarIntentoManipulacion($datos, $tipo) {
    $log_dir = __DIR__ . '/../logs';
    
    // Crear directorio de logs si no existe
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/dom_manipulation.log';
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $usuario = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'GUEST';
    $request_uri = $_SERVER['REQUEST_URI'] ?? 'CLI';
    
    $mensaje = "[{$fecha}] === DOM MANIPULATION DETECTED ===\n";
    $mensaje .= "Usuario: {$usuario}\n";
    $mensaje .= "IP: {$ip}\n";
    $mensaje .= "Tipo: {$tipo}\n";
    $mensaje .= "User Agent: {$user_agent}\n";
    $mensaje .= "URI: {$request_uri}\n";
    $mensaje .= "Datos: " . json_encode($datos, JSON_PRETTY_PRINT) . "\n";
    $mensaje .= str_repeat('=', 80) . "\n\n";
    
    // Escribir en log con manejo de errores
    @file_put_contents($log_file, $mensaje, FILE_APPEND | LOCK_EX);
    
    // También registrar en error_log de PHP
    error_log("SECURITY ALERT: DOM Manipulation detected from IP {$ip} - User: {$usuario} - Tipo: {$tipo}");
}

/**
 * Valida que un campo oculto no haya sido manipulado
 * @param mixed $valor_recibido - Valor recibido del formulario
 * @param mixed $valor_esperado - Valor esperado por el servidor
 * @param string $nombre_campo - Nombre del campo
 * @return bool - true si es válido, false si fue manipulado
 */
function validarCampoOculto($valor_recibido, $valor_esperado, $nombre_campo) {
    if ($valor_recibido != $valor_esperado) {
        registrarIntentoManipulacion([
            'campo' => $nombre_campo,
            'valor_recibido' => $valor_recibido,
            'valor_esperado' => $valor_esperado
        ], 'hidden_field_manipulation');
        return false;
    }
    return true;
}

/**
 * Valida que el usuario tenga permisos para realizar una acción
 * @param int $usuario_id - ID del usuario
 * @param string $accion - Acción a realizar
 * @param mixed $recurso_id - ID del recurso (opcional)
 * @return bool - true si tiene permisos, false si no
 */
function validarPermisosAccion($usuario_id, $accion, $recurso_id = null) {
    // Obtener rol del usuario desde sesión (NO desde formulario)
    $rol_usuario = $_SESSION['user']['tpusu_idtpusu'] ?? null;
    
    if (!$rol_usuario) {
        return false;
    }
    
    // Definir matriz de permisos
    $permisos = [
        1 => ['*'], // Admin tiene todos los permisos
        2 => ['read', 'write', 'process_payment', 'change_status'], // Empleado
        3 => ['read', 'process_payment'], // Cajero
        4 => ['read', 'write'], // Vendedor
        5 => ['read'] // Cliente
    ];
    
    $permisos_usuario = $permisos[$rol_usuario] ?? [];
    
    // Admin tiene acceso a todo
    if (in_array('*', $permisos_usuario)) {
        return true;
    }
    
    // Verificar permiso específico
    if (!in_array($accion, $permisos_usuario)) {
        registrarIntentoManipulacion([
            'usuario_id' => $usuario_id,
            'rol' => $rol_usuario,
            'accion_solicitada' => $accion,
            'recurso_id' => $recurso_id
        ], 'privilege_escalation');
        return false;
    }
    
    return true;
}
?>
