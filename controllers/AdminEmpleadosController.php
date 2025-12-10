<?php

require_once __DIR__ . '/../models/conexion.php';

class AdminEmpleadosController {
    private $db;

    public function __construct() {
        $conn = new conexion();
        $this->db = $conn->get_conexion();
    }

    public function obtenerContexto(): array {
        $usuarios    = $this->obtenerUsuarios();
        $clientesCli = $this->obtenerClientesCli($usuarios);
        // Total clientes = todos los registros en cli + usuarios con perfil cliente (tpusu=5)
        $clientesTotal = count($clientesCli) + $this->contarPorPerfil(5);

        return [
            'usuarios'           => $usuarios,
            'clientes_cli'       => $clientesCli,
            'empleados'          => $usuarios, // compatibilidad
            'usuarios_total'     => $this->contarUsuarios(),
            'usuarios_activos'   => $this->contarUsuarios(true),
            'clientes_total'     => $clientesTotal,
            'empleados_total'    => $this->contarPorPerfil([2,3,4]),
            'admins_total'       => $this->contarPorPerfil(1),
            'vacaciones'          => [],
            'vacaciones_activas'  => 0,
            'permisos'            => [],
            'permisos_pendientes' => 0,
            'turnos'              => [],
        ];
    }

    /**
     * Usuarios registrados en tabla usu.
     */
    private function obtenerUsuarios(): array {
        $stmt = $this->db->prepare("
            SELECT 
                u.idusu, u.nombre_completo, u.username, u.naturaleza, u.email, u.telefono, 
                u.tpusu_idtpusu, u.activo, u.fecha_registro, t.nombre AS rol, u.fecha_ultimo_acceso
            FROM usu u
            INNER JOIN tpusu t ON t.idtpusu = u.tpusu_idtpusu
            ORDER BY u.nombre_completo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Clientes en cli que no estén en usu (por email o teléfono).
     */
    private function obtenerClientesCli(array $usuarios): array {
        $stmtCli = $this->db->prepare("SELECT idcli, nombre, email, telefono, direccion, fecha_registro FROM cli ORDER BY nombre");
        $stmtCli->execute();
        return $stmtCli->fetchAll(PDO::FETCH_ASSOC);
    }

    private function contarUsuarios(bool $soloActivos = false): int {
        $sql = "SELECT COUNT(*) AS total FROM usu";
        if ($soloActivos) {
            $sql .= " WHERE activo = 1";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    private function contarPorPerfil($perfil): int {
        $inClause = '';
        $params = [];
        if (is_array($perfil)) {
            $inClause = '(' . implode(',', array_map('intval', $perfil)) . ')';
            $sql = "SELECT COUNT(*) AS total FROM usu WHERE tpusu_idtpusu IN $inClause";
            $stmt = $this->db->prepare($sql);
        } else {
            $sql = "SELECT COUNT(*) AS total FROM usu WHERE tpusu_idtpusu = ?";
            $stmt = $this->db->prepare($sql);
            $params[] = $perfil;
        }
        $stmt->execute($params);
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }
}
