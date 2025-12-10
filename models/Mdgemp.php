        /**
         * Crear un turno para un empleado, validando solapamiento de fechas
         * @param int $empleado_id
         * @param string $fecha_inicio (Y-m-d)
         * @param string $fecha_fin (Y-m-d)
         * @param string $horario
         * @param string $tipo_temporada
         * @param string $turno
         * @param string $observaciones
         * @throws Exception Si hay solapamiento o error de datos
         * @return int ID del turno creado
         */
        public function crearTurno($empleado_id, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones) {
            // Validar fechas
            if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
                throw new Exception('Fechas inválidas.');
            }
            // Validar solapamiento
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM turnos WHERE idempleado = ? AND (
                (fecha_inicio <= ? AND fecha_fin >= ?) OR
                (fecha_inicio <= ? AND fecha_fin >= ?) OR
                (fecha_inicio >= ? AND fecha_fin <= ?)
            )");
            $stmt->execute([
                $empleado_id,
                $fecha_inicio, $fecha_inicio,
                $fecha_fin, $fecha_fin,
                $fecha_inicio, $fecha_fin
            ]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Ya existe un turno que se solapa con las fechas seleccionadas.');
            }
            // Insertar turno
            $stmt = $this->conn->prepare("INSERT INTO turnos (idempleado, fecha_inicio, fecha_fin, horario, tipo_temporada, turno, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$empleado_id, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones]);
            return $this->conn->lastInsertId();
        }
    /**
     * Crear una vacación para un empleado, validando solapamiento de fechas
     * @param int $empleado_id
     * @param string $fecha_inicio (Y-m-d)
     * @param string $fecha_fin (Y-m-d)
     * @param string $motivo
     * @param string $estado
     * @throws Exception Si hay solapamiento o error de datos
     * @return int ID de la vacación creada
     */
    public function crearVacacion($empleado_id, $fecha_inicio, $fecha_fin, $motivo, $estado) {
        // Validar fechas
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        // Validar solapamiento
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM vacaciones WHERE id_empleado = ? AND (
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio >= ? AND fecha_fin <= ?)
        )");
        $stmt->execute([
            $empleado_id,
            $fecha_inicio, $fecha_inicio,
            $fecha_fin, $fecha_fin,
            $fecha_inicio, $fecha_fin
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Ya existe una vacación que se solapa con las fechas seleccionadas.');
        }
        // Insertar vacación
        $stmt = $this->conn->prepare("INSERT INTO vacaciones (id_empleado, fecha_inicio, fecha_fin, motivo, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$empleado_id, $fecha_inicio, $fecha_fin, $motivo, $estado]);
        return $this->conn->lastInsertId();
    }
<?php
require_once __DIR__ . '/conexion.php';

class Mdgemp {

    /**
     * Obtener empleados paginados y filtrados con orden dinámico
     * @param int $page Página actual
     * @param int $perPage Cantidad por página
     * @param string $estado Estado ('activo', 'inactivo', '')
     * @param int $tipo Tipo de usuario
     * @param string $nombre Búsqueda por nombre, username o email
     * @param string $orderBy Columna para ordenar
     * @param string $orderDir Dirección ('ASC' o 'DESC')
     * @return array
     */
    public function getEmpleadosPaginados($page = 1, $perPage = 10, $estado = '', $tipo = 0, $nombre = '', $orderBy = 'nombre_completo', $orderDir = 'ASC') {
        $offset = max(0, ($page - 1) * $perPage);
        $allowedOrder = ['idusu','nombre_completo','username','email','telefono','tpusu_idtpusu','activo','naturaleza','fecha_registro'];
        $orderBy = in_array($orderBy, $allowedOrder) ? $orderBy : 'nombre_completo';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
        $where = [];
        $params = [];
        if ($estado !== '') {
            $where[] = 'activo = ?';
            $params[] = ($estado === 'activo') ? 1 : 0;
        }
        if ($tipo > 0) {
            $where[] = 'tpusu_idtpusu = ?';
            $params[] = $tipo;
        }
        if ($nombre !== '') {
            $where[] = '(nombre_completo LIKE ? OR username LIKE ? OR email LIKE ?)';
            $params[] = "%$nombre%";
            $params[] = "%$nombre%";
            $params[] = "%$nombre%";
        }
        $whereSQL = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS idusu, nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro FROM usu $whereSQL ORDER BY $orderBy $orderDir LIMIT $offset, $perPage";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->conn->query('SELECT FOUND_ROWS()')->fetchColumn();
        return [
            'empleados' => $empleados,
            'total' => intval($total),
            'page' => $page,
            'perPage' => $perPage,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ];
    }
    private $conn;

    public function __construct() {
        $conexion = new conexion();
        $this->conn = $conexion->get_conexion();
    }

    /**
     * Registrar auditoría de acciones sobre empleados
     * @param int $usuario_id ID del usuario que realiza la acción
     * @param int $empleado_id ID del empleado afectado
     * @param string $accion Acción realizada
     * @param array|null $datos_anteriores Datos anteriores (opcional)
     * @param array|null $datos_nuevos Datos nuevos (opcional)
     * @return int ID de la auditoría registrada
     */
    public function registrarAuditoriaEmpleado($usuario_id, $empleado_id, $accion, $datos_anteriores = null, $datos_nuevos = null) {
        $stmt = $this->conn->prepare("INSERT INTO auditoria_empleados (usuario_id, empleado_id, accion, datos_anteriores, datos_nuevos, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $usuario_id,
            $empleado_id,
            $accion,
            $datos_anteriores ? json_encode($datos_anteriores) : null,
            $datos_nuevos ? json_encode($datos_nuevos) : null
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * Crear empleado y registrar auditoría
     * @param array $datos Datos del empleado
     * @return int ID del empleado creado
     * @throws Exception Si falla la validación o inserción
     */
    public function crearEmpleado($datos) {
        $usuario_id = $_SESSION['user']['idusu'] ?? null;
        // Validar campos requeridos
        $requeridos = ['nombre_completo', 'username', 'email', 'telefono', 'tpusu_idtpusu', 'naturaleza', 'password'];
        foreach ($requeridos as $campo) {
            if (empty($datos[$campo])) {
                throw new Exception("El campo '$campo' es obligatorio.");
            }
        }

        // Validar formato de email
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato de email es inválido.");
        }

        // Validar formato de teléfono (solo dígitos, mínimo 7)
        if (!preg_match('/^\d{7,}$/', $datos['telefono'])) {
            throw new Exception("El teléfono debe contener al menos 7 dígitos.");
        }

        // Validar duplicados de username/email
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM usu WHERE username = ? OR email = ?");
        $stmt->execute([$datos['username'], $datos['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("El username o email ya existe.");
        }

        // Insertar empleado
        $stmt = $this->conn->prepare("INSERT INTO usu (nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro, clave) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([
            $datos['nombre_completo'],
            $datos['username'],
            $datos['email'],
            $datos['telefono'],
            $datos['tpusu_idtpusu'],
            1,
            $datos['naturaleza'],
            password_hash($datos['password'], PASSWORD_DEFAULT)
        ]);
        $empleado_id = $this->conn->lastInsertId();
        $this->registrarAuditoriaEmpleado($usuario_id, $empleado_id, 'crear', null, $datos);
        return $empleado_id;
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
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total_usuarios, SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as usuarios_bloqueados, SUM(intentos_fallidos) as total_intentos_fallidos FROM usu");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    // ========================================
    // MÉTODOS PARA GESTIÓN DE EMPLEADOS
    // ========================================

    /**
     * Obtener todos los empleados con información completa
     */
    public function getAllEmpleados() {
        $stmt = $this->conn->prepare("SELECT idusu, nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro FROM usu");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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


    /**
     * Obtener permisos de empleados
     */
    public function getPermisosEmpleados() {
        $query = "SELECT p.idpermiso, u.nombre_completo as empleado, p.tipo, p.fecha_inicio, p.fecha_fin, p.estado FROM permisos p LEFT JOIN usu u ON p.idempleado = u.idusu";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener turnos de empleados
     */
    public function getTurnosEmpleados() {
        $query = "SELECT t.idturno, u.nombre_completo as empleado, t.fecha_inicio, t.fecha_fin, t.horario FROM turnos t LEFT JOIN usu u ON t.idempleado = u.idusu";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener vacaciones de empleados
     */
    public function getVacacionesEmpleados() {
        $query = "SELECT v.id, u.nombre_completo as empleado, v.fecha_inicio, v.fecha_fin, v.estado, v.motivo FROM vacaciones v LEFT JOIN usu u ON v.id_empleado = u.idusu";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Crear una vacación para un empleado, validando solapamiento de fechas
     * @param int $empleado_id
     * @param string $fecha_inicio (Y-m-d)
     * @param string $fecha_fin (Y-m-d)
     * @param string $motivo
     * @param string $estado
     * @throws Exception Si hay solapamiento o error de datos
     * @return int ID de la vacación creada
     */
    public function crearVacacion($empleado_id, $fecha_inicio, $fecha_fin, $motivo, $estado) {
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM vacaciones WHERE id_empleado = ? AND (
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio >= ? AND fecha_fin <= ?)
        )");
        $stmt->execute([
            $empleado_id,
            $fecha_inicio, $fecha_inicio,
            $fecha_fin, $fecha_fin,
            $fecha_inicio, $fecha_fin
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Ya existe una vacación que se solapa con las fechas seleccionadas.');
        }
        $stmt = $this->conn->prepare("INSERT INTO vacaciones (id_empleado, fecha_inicio, fecha_fin, motivo, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$empleado_id, $fecha_inicio, $fecha_fin, $motivo, $estado]);
        return $this->conn->lastInsertId();
    }

    /**
     * Crear un turno para un empleado, validando solapamiento de fechas
     * @param int $empleado_id
     * @param string $fecha_inicio (Y-m-d)
     * @param string $fecha_fin (Y-m-d)
     * @param string $horario
     * @param string $tipo_temporada
     * @param string $turno
     * @param string $observaciones
     * @throws Exception Si hay solapamiento o error de datos
     * @return int ID del turno creado
     */
    public function crearTurno($empleado_id, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones) {
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM turnos WHERE idempleado = ? AND (
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio >= ? AND fecha_fin <= ?)
        )");
        $stmt->execute([
            $empleado_id,
            $fecha_inicio, $fecha_inicio,
            $fecha_fin, $fecha_fin,
            $fecha_inicio, $fecha_fin
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Ya existe un turno que se solapa con las fechas seleccionadas.');
        }
        $stmt = $this->conn->prepare("INSERT INTO turnos (idempleado, fecha_inicio, fecha_fin, horario, tipo_temporada, turno, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$empleado_id, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones]);
        return $this->conn->lastInsertId();
    }
}