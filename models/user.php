<?php
require_once __DIR__ . '/conexion.php';

class User {
    private $conn;

    public function __construct() {
        $conexion = new conexion();
        $this->conn = $conexion->get_conexion();
    }

    /**
     * Retorna el total de usuarios en la base de datos
     */
    public function getTotalUsuarios() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM usu");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['total']) : 0;
    }

    /**
     * Valida las credenciales de inicio de sesión con sistema de bloqueo
     * @param string $username Correo electrónico o username del usuario
     * @param string $password Contraseña sin encriptar del usuario
     * @return array|false Retorna los datos del usuario si las credenciales son válidas, o false si no lo son
     */
    public function validateLogin($username, $password) {
        // Primero obtener el usuario para verificar estado de bloqueo
        $user = $this->getUserByUsernameOrEmail($username);
        
        if (!$user) {
            return false; // Usuario no existe
        }

        // Verificar si la cuenta está bloqueada permanentemente
        if ($user['activo'] == 0) {
            return false; // Cuenta bloqueada permanentemente
        }

        // Validar que el usuario exista y que la contraseña sea correcta
        if (password_verify($password, $user['clave'])) {
            // Contraseña correcta - reiniciar intentos fallidos si existen
            if ($user['intentos_fallidos'] > 0) {
                $this->resetFailedAttempts($user['idusu']);
            }
            // Marcar último acceso exitoso
            $this->touchLastAccess($user['idusu']);
            return $user;
        } else {
            // Contraseña incorrecta - incrementar intentos fallidos
            $this->incrementFailedAttempts($user['idusu']);
            
            // Verificar si supera el límite de intentos (3)
            $updatedUser = $this->getUserByUsernameOrEmail($username);
            if ($updatedUser['intentos_fallidos'] >= 3) {
                $this->lockAccountPermanently($user['idusu'], "Múltiples intentos fallidos de inicio de sesión");
            }
            
            return false;
        }
    }

    /**
     * Obtiene usuario por username o email (sin verificar activo)
     */
    private function getUserByUsernameOrEmail($username) {
        $query = "SELECT u.*, tp.nombre as tipo_usuario_nombre 
                  FROM usu u 
                  JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
                  WHERE u.email = :username OR u.username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Incrementar intentos fallidos
     */
    private function incrementFailedAttempts($userId) {
        $stmt = $this->conn->prepare("UPDATE usu SET intentos_fallidos = intentos_fallidos + 1 WHERE idusu = :id");
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    /**
     * Bloquear cuenta permanentemente (sin opción de desbloqueo automático)
     */
    private function lockAccountPermanently($userId, $motivo = "Múltiples intentos fallidos de inicio de sesión") {
        $fechaBloqueo = date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("UPDATE usu SET activo = 0, fecha_bloqueo = :fecha, motivo_bloqueo = :motivo WHERE idusu = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':fecha', $fechaBloqueo);
        $stmt->bindParam(':motivo', $motivo);
        return $stmt->execute();
    }

    /**
     * Reiniciar intentos fallidos (al iniciar sesión exitosamente)
     */
    private function resetFailedAttempts($userId) {
        $stmt = $this->conn->prepare("UPDATE usu SET intentos_fallidos = 0, fecha_bloqueo = NULL, motivo_bloqueo = NULL WHERE idusu = :id");
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    /**
     * Verificar si la cuenta está bloqueada
     */
    public function isAccountLocked($userId) {
        $stmt = $this->conn->prepare("SELECT activo FROM usu WHERE idusu = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['activo'] == 0;
    }

    /**
     * Actualiza la marca de último acceso exitoso
     */
    private function touchLastAccess($userId) {
        try {
            $stmt = $this->conn->prepare("UPDATE usu SET fecha_ultimo_acceso = NOW() WHERE idusu = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("No se pudo actualizar fecha_ultimo_acceso: " . $e->getMessage());
        }
    }

    /**
     * Obtener información de bloqueo
     */
    public function getLockInfo($userId) {
        $stmt = $this->conn->prepare("SELECT intentos_fallidos, fecha_bloqueo, activo, motivo_bloqueo FROM usu WHERE idusu = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener intentos fallidos restantes
     */
    public function getRemainingAttempts($userId) {
        $user = $this->getUserById($userId);
        $attempts = $user['intentos_fallidos'] ?? 0;
        return max(0, 3 - $attempts);
    }

    /**
     * Obtener todos los usuarios bloqueados
     */
    public function getLockedUsers() {
        $stmt = $this->conn->prepare("SELECT u.*, tp.nombre as tipo_usuario_nombre 
                                    FROM usu u 
                                    JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
                                    WHERE u.activo = 0 
                                    ORDER BY u.fecha_bloqueo DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estadísticas de bloqueo
     */
    public function getLockStats() {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_usuarios,
                SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as usuarios_bloqueados,
                SUM(intentos_fallidos) as total_intentos_fallidos
            FROM usu
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registra un nuevo usuario en la base de datos
     */
    public function registerUser($data) {
        $query = "INSERT INTO usu (username, nombre_completo, telefono, naturaleza, email, clave, tpusu_idtpusu, activo, intentos_fallidos)
              VALUES (:username, :nombre_completo, :telefono, :naturaleza, :email, :clave, :tipo, 1, 0)";
        
        $required = ['username', 'nombre_completo', 'telefono', 'email', 'clave', 'tpusu_idtpusu', 'direccion'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                error_log("Campo requerido faltante o vacío: $field");
                return false;
            }
        }

        if (!$this->conn) {
            error_log("Error: No hay conexión a la base de datos");
            return false;
        }

        $hashedPassword = password_hash($data['clave'], PASSWORD_BCRYPT);

        try {
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                error_log("Error al preparar la consulta: " . print_r($this->conn->errorInfo(), true));
                return false;
            }
            
            $params = [
                ':username' => $data['username'],
                ':nombre_completo' => $data['nombre_completo'],
                ':telefono' => $data['telefono'],
                ':naturaleza' => $data['direccion'],
                ':email' => $data['email'],
                ':clave' => $hashedPassword,
                ':tipo' => $data['tpusu_idtpusu']
            ];
            
            error_log("Ejecutando query: " . $query);
            error_log("Con parámetros: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $lastId = $this->conn->lastInsertId();
                error_log("Usuario registrado exitosamente en BD con ID: " . $lastId);
                return true;
            } else {
                error_log("Error en execute del statement");
                $errorInfo = $stmt->errorInfo();
                error_log("Error info: " . print_r($errorInfo, true));
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error PDO en registro de usuario: " . $e->getMessage());
            error_log("Query: " . $query);
            error_log("Datos: " . print_r($data, true));
            return false;
        }
    }

    /**
     * Obtiene un usuario por su ID
     */
    public function getUserById($id) {
        $query = "SELECT u.*, tp.nombre as tipo_usuario_nombre 
                  FROM usu u 
                  JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
                  WHERE u.idusu = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si un email o username ya existe
     */
    public function userExists($email, $username) {
        $query = "SELECT COUNT(*) FROM usu WHERE email = :email OR username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene todos los usuarios activos
     */
    public function getAllUsers() {
        $query = "SELECT u.idusu, u.nombre_completo, u.username, u.email, tp.nombre as rol, u.activo,
                         u.intentos_fallidos, u.fecha_bloqueo, u.motivo_bloqueo
                  FROM usu u
                  JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu
                  ORDER BY u.nombre_completo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los días de vacaciones de un usuario
     */
    public function updateVacacion($id, $dias) {
        $query = "UPDATE usu SET vacaciones = :dias WHERE idusu = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

    public function getAllEmpleados() {
        $stmt = $this->conn->prepare("
            SELECT idusu, nombre_completo, username, email, telefono, 
                   tpusu_idtpusu, activo, naturaleza, fecha_registro 
            FROM usu
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function crearEmpleado($datos) {
        $nombre_completo = trim($datos['nombre']) . ' ' . trim($datos['apellido']);
        $username = trim($datos['documento']);
        $cargo = trim($datos['cargo']);
        $fecha_ingreso = $datos['fecha_ingreso'] ?? date('Y-m-d');
        $password = trim($datos['password'] ?? '123456');
        $email = $username . '@floraltech.local';
        $telefono = '';
        $tpusu_idtpusu = 2; // Tipo usuario empleado por defecto
        $activo = ($datos['estado'] ?? 'activo') === 'activo' ? 1 : 0;
        $clave = password_hash($password, PASSWORD_DEFAULT);

        // Verificar si el usuario ya existe
        if ($this->existeUsername($username)) {
            throw new Exception('El documento/usuario ya existe.');
        }

        $stmt = $this->conn->prepare("
            INSERT INTO usu (username, nombre_completo, naturaleza, telefono, email, clave, tpusu_idtpusu, fecha_registro, activo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $username, $nombre_completo, $cargo, $telefono, $email, 
            $clave, $tpusu_idtpusu, $fecha_ingreso, $activo
        ]);
    }

    /**
     * Verificar si existe un username
     */
    public function existeUsername($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM usu WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener empleados activos
     */
    public function getEmpleadosActivos() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM usu WHERE activo = 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['total']) : 0;
    }

    /**
     * Obtener mapeo de tipos de usuario
     */
    public function getTiposUsuario() {
        return [
            1 => 'Administrador',
            2 => 'Vendedor', 
            3 => 'Inventario',
            4 => 'Repartidor',
            5 => 'Cliente'
        ];
    }

    /**
     * Obtener empleado por ID
     */
    public function getEmpleadoById($id) {
        $stmt = $this->conn->prepare("
            SELECT idusu, nombre_completo, username, email, telefono, 
                   tpusu_idtpusu, activo, naturaleza, fecha_registro 
            FROM usu WHERE idusu = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar empleado
     */
    public function actualizarEmpleado($id, $datos) {
        $stmt = $this->conn->prepare("
            UPDATE usu 
            SET nombre_completo = ?, email = ?, telefono = ?, 
                naturaleza = ?, tpusu_idtpusu = ?, activo = ?
            WHERE idusu = ?
        ");
        
        return $stmt->execute([
            $datos['nombre_completo'],
            $datos['email'],
            $datos['telefono'],
            $datos['naturaleza'],
            $datos['tpusu_idtpusu'],
            $datos['activo'],
            $id
        ]);
    }

    /**
     * Eliminar empleado (marcar como inactivo)
     */
    public function eliminarEmpleado($id) {
        $stmt = $this->conn->prepare("UPDATE usu SET activo = 0 WHERE idusu = ?");
        return $stmt->execute([$id]);
    }
}
