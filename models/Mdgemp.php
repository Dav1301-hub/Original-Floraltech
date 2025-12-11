<?php
require_once __DIR__ . '/conexion.php';

class Mdgemp {
    private $conn;

    public function __construct() {
        $conexion = new conexion();
        $this->conn = $conexion->get_conexion();
    }

    // ========================================
    // MÉTODOS DE AUTENTICACIÓN Y USUARIOS
    // ========================================

    /**
     * Valida las credenciales de inicio de sesión con sistema de bloqueo
     */
    public function validateLogin($username, $password) {
        $user = $this->getUserByUsernameOrEmail($username);
        
        if (!$user) {
            return false;
        }

        if ($user['activo'] == 0) {
            return false;
        }

        if (password_verify($password, $user['clave'])) {
            $this->resetFailedAttempts($user['idusu']);
            return $user;
        } else {
            $this->incrementFailedAttempts($user['idusu']);
            $updatedUser = $this->getLockInfo($user['idusu']);
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
                  LEFT JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
                  WHERE u.username = :username OR u.email = :username";
        
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
     * Bloquear cuenta permanentemente
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
     * Reiniciar intentos fallidos
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
                                    LEFT JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
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
                  LEFT JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
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
                         u.fecha_registro
                  FROM usu u 
                  LEFT JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu 
                  WHERE u.activo = 1
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
     * Crear empleado y registrar auditoría
     */
    public function crearEmpleado($datos) {
        $usuario_id = $_SESSION['user']['idusu'] ?? null;
        $requeridos = ['nombre_completo', 'username', 'email', 'telefono', 'tpusu_idtpusu', 'naturaleza', 'password'];
        foreach ($requeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                throw new Exception("El campo '$campo' es requerido.");
            }
        }

        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El formato del email no es válido.');
        }

        if (!preg_match('/^\d{7,}$/', $datos['telefono'])) {
            throw new Exception('El teléfono debe contener al menos 7 dígitos.');
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM usu WHERE username = ? OR email = ?");
        $stmt->execute([$datos['username'], $datos['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('El username o email ya existe.');
        }

        $stmt = $this->conn->prepare("INSERT INTO usu (nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro, clave) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([
            $datos['nombre_completo'],
            $datos['username'],
            $datos['email'],
            $datos['telefono'],
            $datos['tpusu_idtpusu'],
            isset($datos['activo']) ? $datos['activo'] : 1,
            $datos['naturaleza'],
            password_hash($datos['password'], PASSWORD_DEFAULT)
        ]);
        $empleado_id = $this->conn->lastInsertId();
        $this->registrarAuditoriaEmpleado($usuario_id, $empleado_id, 'crear', null, $datos);
        return $empleado_id;
    }

    /**
     * Registrar auditoría de acciones sobre empleados
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
            3 => 'Cliente',
            4 => 'Inventario',
            5 => 'Empleado'
        ];
    }

    /**
     * Obtener empleado por ID
     */
    public function getEmpleadoById($id) {
        $stmt = $this->conn->prepare("
            SELECT idusu, nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro 
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
            SET nombre_completo = ?, username = ?, email = ?, telefono = ?, tpusu_idtpusu = ?, naturaleza = ?, activo = ? 
            WHERE idusu = ?
        ");
        
        return $stmt->execute([
            $datos['nombre_completo'],
            $datos['username'],
            $datos['email'],
            $datos['telefono'],
            $datos['tpusu_idtpusu'],
            $datos['naturaleza'],
            $datos['activo'] ?? 1,
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
     * Obtener empleados paginados y filtrados
     */
    public function getEmpleadosPaginados($page = 1, $perPage = 10, $estado = '', $tipo = 0, $nombre = '', $orderBy = 'nombre_completo', $orderDir = 'ASC') {
        $offset = max(0, ($page - 1) * $perPage);
        $allowedOrder = ['idusu','nombre_completo','username','email','telefono','tpusu_idtpusu','activo','naturaleza','fecha_registro'];
        $orderBy = in_array($orderBy, $allowedOrder) ? $orderBy : 'nombre_completo';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
        $where = [];
        $params = [];
        if ($estado !== '') {
            $where[] = "activo = ?";
            $params[] = $estado === 'activo' ? 1 : 0;
        }
        if ($tipo > 0) {
            $where[] = "tpusu_idtpusu = ?";
            $params[] = $tipo;
        }
        if ($nombre !== '') {
            $where[] = "(nombre_completo LIKE ? OR username LIKE ? OR email LIKE ?)";
            $search = "%$nombre%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        $whereSQL = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT SQL_CALC_FOUND_ROWS idusu, nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro FROM usu $whereSQL ORDER BY $orderBy $orderDir LIMIT $offset, $perPage";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->conn->query('SELECT FOUND_ROWS()')->fetchColumn();
        return [
            'data' => $empleados,
            'total' => intval($total),
            'page' => $page,
            'perPage' => $perPage,
            'pages' => ceil($total / $perPage)
        ];
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

    // ========================================
    // MÉTODOS PARA GESTIÓN DE PERMISOS
    // ========================================

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
     * Crear un permiso para un empleado
     */
    public function crearPermiso($idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado = 'Pendiente') {
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        $stmt = $this->conn->prepare("INSERT INTO permisos (idempleado, tipo, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado]);
        return $this->conn->lastInsertId();
    }

    /**
     * Obtener un permiso por ID
     */
    public function getPermisoById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM permisos WHERE idpermiso = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar un permiso
     */
    public function actualizarPermiso($id, $idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado) {
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        $stmt = $this->conn->prepare("UPDATE permisos SET idempleado = ?, tipo = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE idpermiso = ?");
        return $stmt->execute([$idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado, $id]);
    }

    /**
     * Eliminar un permiso
     */
    public function eliminarPermiso($id) {
        $stmt = $this->conn->prepare("DELETE FROM permisos WHERE idpermiso = ?");
        return $stmt->execute([$id]);
    }

    // ========================================
    // MÉTODOS PARA GESTIÓN DE TURNOS
    // ========================================

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
     * Crear un turno para un empleado
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

    /**
     * Obtener un turno por ID
     */
    public function getTurnoById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM turnos WHERE idturno = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar un turno
     */
    public function actualizarTurno($id, $idempleado, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada = '', $turno = '', $observaciones = '') {
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM turnos WHERE idempleado = ? AND idturno != ? AND (
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio >= ? AND fecha_fin <= ?)
        )");
        $stmt->execute([
            $idempleado, $id,
            $fecha_inicio, $fecha_inicio,
            $fecha_fin, $fecha_fin,
            $fecha_inicio, $fecha_fin
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Ya existe un turno que se solapa con las fechas seleccionadas.');
        }
        
        $stmt = $this->conn->prepare("UPDATE turnos SET idempleado = ?, fecha_inicio = ?, fecha_fin = ?, horario = ?, tipo_temporada = ?, turno = ?, observaciones = ? WHERE idturno = ?");
        return $stmt->execute([$idempleado, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones, $id]);
    }

    /**
     * Eliminar un turno
     */
    public function eliminarTurno($id) {
        $stmt = $this->conn->prepare("DELETE FROM turnos WHERE idturno = ?");
        return $stmt->execute([$id]);
    }

    // ========================================
    // MÉTODOS PARA GESTIÓN DE VACACIONES
    // ========================================

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
     * Crear una vacación para un empleado
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
     * Obtener una vacación por ID
     */
    public function getVacacionById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM vacaciones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar una vacación
     */
    public function actualizarVacacionCompleta($id, $id_empleado, $fecha_inicio, $fecha_fin, $motivo, $estado) {
        if (!$fecha_inicio || !$fecha_fin || strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas.');
        }
        
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM vacaciones WHERE id_empleado = ? AND id != ? AND (
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio <= ? AND fecha_fin >= ?) OR
            (fecha_inicio >= ? AND fecha_fin <= ?)
        )");
        $stmt->execute([
            $id_empleado, $id,
            $fecha_inicio, $fecha_inicio,
            $fecha_fin, $fecha_fin,
            $fecha_inicio, $fecha_fin
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Ya existe una vacación que se solapa con las fechas seleccionadas.');
        }
        
        $stmt = $this->conn->prepare("UPDATE vacaciones SET id_empleado = ?, fecha_inicio = ?, fecha_fin = ?, motivo = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$id_empleado, $fecha_inicio, $fecha_fin, $motivo, $estado, $id]);
    }

    /**
     * Eliminar una vacación
     */
    public function eliminarVacacion($id) {
        $stmt = $this->conn->prepare("DELETE FROM vacaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
