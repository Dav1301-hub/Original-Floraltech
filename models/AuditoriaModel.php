<?php

class AuditoriaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function resumenAuditoriaPagos() {
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
    }

    public function accionesPorEstado() {
        $stmt = $this->db->prepare("
            SELECT estado_pag AS tipo, COUNT(*) as cantidad
            FROM pagos
            GROUP BY estado_pag
            ORDER BY cantidad DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actividadSemanal() {
        $stmt = $this->db->prepare("
            SELECT DATE(fecha_pago) as dia, COUNT(*) as cantidad
            FROM pagos
            WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(fecha_pago)
            ORDER BY DATE(fecha_pago)
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $totales = (int)$this->db->query("SELECT COUNT(*) FROM usu")->fetchColumn();
        $activos = (int)$this->db->query("SELECT COUNT(*) FROM usu WHERE activo = 1")->fetchColumn();
        $activosHoy = (int)$this->db->query("SELECT COUNT(*) FROM usu WHERE DATE(fecha_ultimo_acceso) = CURDATE()")->fetchColumn();
        return [$totales, $activos, $activosHoy];
    }

    public function usuariosRecientes($limit = 8) {
        $stmt = $this->db->prepare("
            SELECT u.idusu, u.nombre_completo, u.username, u.email, u.fecha_ultimo_acceso, tp.nombre AS rol
            FROM usu u
            LEFT JOIN tpusu tp ON u.tpusu_idtpusu = tp.idtpusu
            WHERE u.fecha_ultimo_acceso IS NOT NULL
            ORDER BY u.fecha_ultimo_acceso DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function productosActivosResumen() {
        $count = (int)$this->db->query("SELECT COUNT(*) FROM inv WHERE COALESCE(cantidad_disponible, 0) > 0")->fetchColumn();
        $stmt = $this->db->prepare("
            SELECT t.nombre, COALESCE(i.cantidad_disponible, 0) as stock, t.naturaleza, t.precio
            FROM tflor t
            LEFT JOIN inv i ON t.idtflor = i.tflor_idtflor
            WHERE COALESCE(i.cantidad_disponible, 0) > 0
            ORDER BY i.cantidad_disponible DESC, t.nombre ASC
            LIMIT 8
        ");
        $stmt->execute();
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [$count, $detalle];
    }

    public function pagosMes() {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(monto),0) as monto, COUNT(*) as conteo
            FROM pagos
            WHERE estado_pag = 'Completado'
              AND YEAR(fecha_pago) = YEAR(CURDATE())
              AND MONTH(fecha_pago) = MONTH(CURDATE())
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['monto' => 0, 'conteo' => 0];
    }

    public function proyeccionActiva() {
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
        if (!$proy) {
            $proy = [
                'titulo' => 'Sin proyección',
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
    }

    public function pagosPorFecha($fecha) {
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
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
