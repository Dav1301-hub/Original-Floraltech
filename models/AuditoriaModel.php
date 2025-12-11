<?php

class AuditoriaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Verifica si existe una tabla en la base de datos actual.
     */
    private function tableExists(string $table): bool {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t");
            $stmt->bindValue(':t', $table, PDO::PARAM_STR);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Auditoria tableExists {$table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si existe una columna en una tabla dada.
     */
    private function columnExists(string $table, string $column): bool {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c");
            $stmt->bindValue(':t', $table, PDO::PARAM_STR);
            $stmt->bindValue(':c', $column, PDO::PARAM_STR);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Auditoria columnExists {$table}.{$column}: " . $e->getMessage());
            return false;
        }
    }

    public function resumenAuditoriaPagos() {
        try {
            $query = "SELECT 
                            COUNT(*) as acciones,
                            COUNT(DISTINCT ped.empleado_id) as usuarios,
                            MAX(p.fecha_pago) as ultima,
                            SUM(CASE WHEN p.estado_pag IN ('Rechazado','Reembolsado','Cancelado') THEN 1 ELSE 0 END) as incidencias
                       FROM pagos p
                       LEFT JOIN ped ON ped.idped = p.ped_idped";
            return $this->db->query($query)->fetch(PDO::FETCH_ASSOC) ?: [
                'acciones' => 0,
                'usuarios' => 0,
                'incidencias' => 0,
                'ultima' => null
            ];
        } catch (Exception $e) {
            error_log('Auditoria resumenAuditoriaPagos: ' . $e->getMessage());
            return [
                'acciones' => 0,
                'usuarios' => 0,
                'incidencias' => 0,
                'ultima' => null
            ];
        }
    }

    public function accionesPorEstado() {
        $stmt = $this->db->prepare("
            SELECT estado_pag AS tipo, COUNT(*) as cantidad
            FROM pagos
            GROUP BY estado_pag
            ORDER BY cantidad DESC
        ");
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Auditoria accionesPorEstado: ' . $e->getMessage());
            return [];
        }
    }

    public function actividadSemanal() {
        $stmt = $this->db->prepare("
            SELECT DATE(fecha_pago) as dia, COUNT(*) as cantidad
            FROM pagos
            WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(fecha_pago)
            ORDER BY DATE(fecha_pago)
        ");
        try {
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Auditoria actividadSemanal: ' . $e->getMessage());
            $rows = [];
        }
        $dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dias[$d] = 0;
        }
        foreach ($rows as $row) {
            $dias[$row['dia']] = (int)$row['cantidad'];
        }
        return $dias;
    }

    public function usuariosActivosResumen() {
        if (!$this->tableExists('usu')) {
            return [0, 0, 0];
        }
        try {
            $totales = (int)$this->db->query("SELECT COUNT(*) FROM usu")->fetchColumn();
            $activos = (int)$this->db->query("SELECT COUNT(*) FROM usu WHERE activo = 1")->fetchColumn();
            // Algunas bases no tienen fecha_ultimo_acceso, en ese caso devolvemos 0
            if ($this->columnExists('usu', 'fecha_ultimo_acceso')) {
                $activosHoy = (int)$this->db->query("SELECT COUNT(*) FROM usu WHERE DATE(fecha_ultimo_acceso) = CURDATE()")->fetchColumn();
            } else {
                $activosHoy = 0;
            }
            return [$totales, $activos, $activosHoy];
        } catch (Exception $e) {
            error_log('Auditoria usuariosActivosResumen: ' . $e->getMessage());
            return [0, 0, 0];
        }
    }

    public function usuariosRecientes($limit = 8) {
        if (!$this->tableExists('usu')) {
            return [];
        }
        try {
            $colFecha = $this->columnExists('usu', 'fecha_ultimo_acceso') ? 'u.fecha_ultimo_acceso' : 'u.fecha_registro';
            $stmt = $this->db->prepare("
                SELECT u.idusu, u.nombre_completo, u.username, u.email, {$colFecha} AS fecha_ultimo_acceso, tp.nombre AS rol
                FROM usu u
                LEFT JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu
                WHERE {$colFecha} IS NOT NULL
                ORDER BY {$colFecha} DESC
                LIMIT :lim
            ");
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Auditoria usuariosRecientes: ' . $e->getMessage());
            return [];
        }
    }

    public function productosActivosResumen() {
        // Coincidir con inventario: activo si COALESCE(cantidad_disponible, stock) > 0
        $count = 0;
        try {
            $count = (int)$this->db->query("SELECT COUNT(*) FROM inv WHERE stock > 0")->fetchColumn();
        } catch (Exception $e) {
            error_log('Auditoria productosActivosResumen count: ' . $e->getMessage());
        }
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(t.nombre, CONCAT('Producto #', i.idinv)) AS nombre,
                    i.stock,
                    t.naturaleza,
                    i.precio,
                    t.nombre AS tipo
                FROM inv i
                LEFT JOIN tflor t ON t.idtflor = i.tflor_idtflor
                WHERE i.stock > 0
                ORDER BY i.stock DESC, t.nombre ASC
                LIMIT 8
            ");
            $stmt->execute();
            $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Auditoria productosActivosResumen: ' . $e->getMessage());
            $detalle = [];
        }
        return [$count, $detalle];
    }

    public function pagosMes() {
        try {
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(monto),0) as monto, COUNT(*) as conteo
                FROM pagos
                WHERE estado_pag = 'Completado'
                  AND YEAR(fecha_pago) = YEAR(CURDATE())
                  AND MONTH(fecha_pago) = MONTH(CURDATE())
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['monto' => 0, 'conteo' => 0];
        } catch (Exception $e) {
            error_log('Auditoria pagosMes: ' . $e->getMessage());
            return ['monto' => 0, 'conteo' => 0];
        }
    }

    public function proyeccionActiva() {
        if (!$this->tableExists('proyecciones_pagos')) {
            return [
                'titulo' => 'Sin proyeccion',
                'monto_objetivo' => 0,
                'fecha_inicio' => date('Y-m-01'),
                'fecha_fin' => date('Y-m-t'),
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'notas' => ''
            ];
        }
        try {
            $stmt = $this->db->prepare("
                SELECT *
                FROM proyecciones_pagos
                WHERE fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()
                ORDER BY fecha_creacion DESC
                LIMIT 1
            ");
            $stmt->execute();
            $proy = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$proy) {
                $stmt = $this->db->prepare("SELECT * FROM proyecciones_pagos ORDER BY fecha_creacion DESC LIMIT 1");
                $stmt->execute();
                $proy = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log('Auditoria proyeccionActiva: ' . $e->getMessage());
            $proy = null;
        }
        if (!$proy) {
            $proy = [
                'titulo' => 'Sin proyeccion',
                'monto_objetivo' => 0,
                'fecha_inicio' => date('Y-m-01'),
                'fecha_fin' => date('Y-m-t'),
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'notas' => ''
            ];
        }
        return $proy;
    }

    public function guardarProyeccion($data) {
        if (!$this->tableExists('proyecciones_pagos')) {
            return false;
        }
        $stmt = $this->db->prepare("
            INSERT INTO proyecciones_pagos (titulo, monto_objetivo, fecha_inicio, fecha_fin, creado_por, estado, notas, fecha_creacion)
            VALUES (:titulo, :monto, :inicio, :fin, :creado_por, 'Activa', :notas, NOW())
        ");
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':monto' => $data['monto'],
            ':inicio' => $data['inicio'],
            ':fin' => $data['fin'],
            ':creado_por' => $data['creado_por'],
            ':notas' => $data['notas']
        ]);
    }

    public function avanceProyeccion($inicio, $fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(monto),0) as monto, COUNT(*) as conteo
                FROM pagos
                WHERE estado_pag = 'Completado'
                  AND fecha_pago BETWEEN :inicio AND DATE_ADD(:fin, INTERVAL 1 DAY)
            ");
            $stmt->bindParam(':inicio', $inicio);
            $stmt->bindParam(':fin', $fin);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['monto' => 0, 'conteo' => 0];
        } catch (Exception $e) {
            error_log('Auditoria avanceProyeccion: ' . $e->getMessage());
            return ['monto' => 0, 'conteo' => 0];
        }
    }

    public function pagosPorFecha($fecha) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.idpago, p.monto, p.estado_pag, p.metodo_pago, p.fecha_pago, pe.numped, c.nombre as cliente
                FROM pagos p
                JOIN ped pe ON p.ped_idped = pe.idped
                JOIN cli c ON pe.cli_idcli = c.idcli
                WHERE DATE(p.fecha_pago) = :f
                ORDER BY p.fecha_pago DESC
            ");
            $stmt->bindParam(':f', $fecha);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Auditoria pagosPorFecha: ' . $e->getMessage());
            return [];
        }
    }

    public function listadoAuditoria($limit = 200) {
        $query = "
            SELECT
                p.idpago as id,
                COALESCE(u.nombre_completo, c.nombre, 'Sistema') as usuario,
                p.estado_pag as tipo,
                p.fecha_pago as fecha,
                p.metodo_pago as metodo,
                pe.numped,
                c.nombre as cliente,
                p.monto
            FROM pagos p
            LEFT JOIN ped pe ON p.ped_idped = pe.idped
            LEFT JOIN cli c ON pe.cli_idcli = c.idcli
            LEFT JOIN usu u ON pe.empleado_id = u.idusu
            ORDER BY p.fecha_pago DESC
            LIMIT :lim
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Auditoria listadoAuditoria: ' . $e->getMessage());
            return [];
        }
    }
}

