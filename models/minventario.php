<?php
require_once __DIR__ . '/conexion.php';

/**
 * Modelo simplificado de inventario (CRUD) sin dependencia de precio_compra.
 * Trabaja con las tablas existentes: inv (stock/precio) y tflor (datos del producto).
 */
class Minventario {
                    /**
                     * Obtener flores para select (id y nombre)
                     * @return array
                     */
                    public function getFloresParaSelect() {
                        $sql = "SELECT i.idinv AS id, COALESCE(t.nombre, CONCAT('Producto #', i.idinv)) AS nombre FROM inv i LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor ORDER BY t.nombre ASC";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute();
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                /**
                 * Obtener todas las flores (productos) del inventario
                 * @return array
                 */
                public function getTodasLasFlores() {
                    $sql = "SELECT 
                                i.idinv AS id,
                                i.tflor_idtflor,
                                COALESCE(t.nombre, CONCAT('Producto #', i.idinv)) AS nombre,
                                COALESCE(t.nombre, 'Sin tipo') AS tipo,
                                COALESCE(i.naturaleza, 'Sin categorizar') AS naturaleza,
                                COALESCE(i.color, 'Sin color') AS color,
                                COALESCE(i.descripcion, '') AS descripcion,
                                i.stock AS stock_disp,
                                i.precio,
                                COALESCE(i.alimentacion, '') AS alimentacion,
                                i.disponible,
                                i.fecha_actualizacion
                            FROM inv i
                            LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                            ORDER BY t.nombre ASC";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            /**
             * Total de elementos según filtros (para paginación)
             * @param array $filtros
             * @return int
             */
            public function getTotalElementos($filtros = []) {
                $where = [];
                $params = [];
                if (!empty($filtros['nombre'])) {
                    $where[] = 't.nombre LIKE :nombre';
                    $params[':nombre'] = '%' . $filtros['nombre'] . '%';
                }
                if (!empty($filtros['tipo'])) {
                    $where[] = 't.nombre LIKE :tipo';
                    $params[':tipo'] = '%' . $filtros['tipo'] . '%';
                }
                if (!empty($filtros['naturaleza'])) {
                    $where[] = 'i.naturaleza LIKE :naturaleza';
                    $params[':naturaleza'] = '%' . $filtros['naturaleza'] . '%';
                }
                if (!empty($filtros['color'])) {
                    $where[] = 'i.color LIKE :color';
                    $params[':color'] = '%' . $filtros['color'] . '%';
                }
                if (isset($filtros['disponible']) && $filtros['disponible'] !== '') {
                    $where[] = 'i.disponible = :disponible';
                    $params[':disponible'] = (int)$filtros['disponible'];
                }
                $sql = "SELECT COUNT(*) AS total FROM inv i LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor";
                if ($where) {
                    $sql .= ' WHERE ' . implode(' AND ', $where);
                }
                $stmt = $this->db->prepare($sql);
                foreach ($params as $k => $v) {
                    $stmt->bindValue($k, $v);
                }
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return (int)($row['total'] ?? 0);
            }
        /**
         * Inventario paginado y filtrado
         * @param int $limit
         * @param int $offset
         * @param array $filtros (nombre, tipo, naturaleza, color, disponible)
         * @return array
         */
        public function getInventarioPaginado($limit = 10, $offset = 0, $filtros = []) {
            $where = [];
            $params = [];
                if (!empty($filtros['nombre'])) {
                    $where[] = 't.nombre LIKE :nombre';
                    $params[':nombre'] = '%' . $filtros['nombre'] . '%';
            }
            if (!empty($filtros['tipo'])) {
                $where[] = 't.nombre LIKE :tipo';
                $params[':tipo'] = '%' . $filtros['tipo'] . '%';
            }
            if (!empty($filtros['naturaleza'])) {
                $where[] = 'i.naturaleza LIKE :naturaleza';
                $params[':naturaleza'] = '%' . $filtros['naturaleza'] . '%';
            }
            if (!empty($filtros['color'])) {
                $where[] = 'i.color LIKE :color';
                $params[':color'] = '%' . $filtros['color'] . '%';
            }
            if (isset($filtros['disponible']) && $filtros['disponible'] !== '') {
                $where[] = 'i.disponible = :disponible';
                $params[':disponible'] = (int)$filtros['disponible'];
            }
            $sql = "SELECT 
                        i.idinv AS id,
                        i.tflor_idtflor,
                                COALESCE(t.nombre, CONCAT('Producto #', i.idinv)) AS nombre,
                        COALESCE(t.nombre, 'Sin tipo') AS tipo,
                        COALESCE(i.naturaleza, 'Sin categorizar') AS naturaleza,
                        COALESCE(i.color, 'Sin color') AS color,
                        COALESCE(i.descripcion, '') AS descripcion,
                        i.stock AS stock_disp,
                        i.precio,
                        COALESCE(i.alimentacion, '') AS alimentacion,
                        i.disponible,
                        i.fecha_actualizacion
                    FROM inv i
                    LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor";
            if ($where) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }
            $sql .= ' ORDER BY i.nombre ASC LIMIT :limit OFFSET :offset';
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    private $db;

    public function __construct() {
        $conexion = new conexion();
        $this->db = $conexion->get_conexion();
    }

    /**
    * Tarjetas de resumen.
    */
    public function obtenerEstadisticas() {
        $estadisticas = [
            'total_registrados' => 0,
            'total_activos'     => 0,
            'stock_bajo'        => 0,
            'sin_stock'         => 0,
            'valor_total'       => 0
        ];

        // Totales y valor (base inventario, solo stock)
        $sql = "SELECT 
                    COUNT(*) AS total_registrados,
                    SUM(CASE WHEN i.stock > 0 THEN 1 ELSE 0 END) AS total_activos,
                    SUM(CASE WHEN i.stock > 0 AND i.stock < 20 THEN 1 ELSE 0 END) AS stock_bajo,
                    SUM(CASE WHEN i.stock = 0 THEN 1 ELSE 0 END) AS sin_stock,
                    COALESCE(SUM(i.stock * i.precio),0) AS valor_total
                FROM inv i";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $estadisticas = array_merge($estadisticas, $row);
        }
        return $estadisticas;
    }

    /**
    * Lista de productos con datos de tflor.
    */
    public function listarProductos() {
        $sql = "SELECT 
                    i.idinv AS id,
                    i.tflor_idtflor,
                    COALESCE(i.nombre, CONCAT('Producto #', i.idinv)) AS nombre,
                    COALESCE(t.nombre, 'Sin tipo') AS tipo,
                    COALESCE(i.naturaleza, 'Sin categorizar') AS naturaleza,
                    COALESCE(i.color, 'Sin color') AS color,
                    COALESCE(i.descripcion, '') AS descripcion,
                    i.stock AS stock_disp,
                    i.precio,
                    COALESCE(i.alimentacion, '') AS alimentacion,
                    i.disponible,
                    i.fecha_actualizacion
                FROM inv i
                LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    * Producto por id.
    */
    public function obtenerProducto($id) {
        $sql = "SELECT 
                    i.idinv AS id,
                    i.tflor_idtflor,
                    COALESCE(i.nombre, '') AS nombre,
                    COALESCE(t.nombre, '') AS tipo,
                    COALESCE(i.naturaleza, '') AS naturaleza,
                    COALESCE(i.color, '') AS color,
                    COALESCE(i.descripcion, '') AS descripcion,
                    i.stock,
                    i.precio,
                    COALESCE(i.alimentacion, '') AS alimentacion,
                    i.disponible
                FROM inv i
                LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                WHERE i.idinv = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
    * Crear producto: inserta en tflor y luego en inv.
    */
    public function crearProducto($data, $usuarioId = null) {
        $this->db->beginTransaction();
        try {
            if (empty($data['tflor_idtflor'])) {
                throw new Exception('Tipo de flor requerido');
            }

            $sqlInv = "INSERT INTO inv (tflor_idtflor, nombre, color, naturaleza, descripcion, stock, precio, alimentacion, fecha_actualizacion, empleado_id, motivo, disponible)
                       VALUES (:tflor_id, :nombre, :color, :naturaleza, :descripcion, :stock, :precio, :alimentacion, NOW(), :empleado_id, :motivo, :disp)";
            $stmtInv = $this->db->prepare($sqlInv);
            $stmtInv->execute([
                ':tflor_id'    => $data['tflor_idtflor'],
                ':nombre'      => $data['nombre'],
                ':color'       => $data['color'] ?? '',
                ':naturaleza'  => $data['naturaleza'] ?? '',
                ':descripcion' => $data['descripcion'] ?? '',
                ':stock'       => $data['stock'],
                ':precio'      => $data['precio'],
                ':alimentacion'=> $data['alimentacion'] ?? '',
                ':empleado_id' => $usuarioId,
                ':motivo'      => $data['motivo'] ?? null,
                ':disp'        => isset($data['disponible']) ? 1 : 0
            ]);

            $this->db->commit();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception('Error al crear producto: ' . $e->getMessage());
        }
    }

    /**
    * Actualizar datos de producto e inventario.
    */
    public function actualizarProducto($id, $data, $usuarioId = null) {
        $this->db->beginTransaction();
        try {
            // Obtener tflor asociado
            $stmtGet = $this->db->prepare("SELECT tflor_idtflor FROM inv WHERE idinv = ?");
            $stmtGet->execute([$id]);
            $row = $stmtGet->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                throw new Exception('Producto no encontrado');
            }
            $tflorId = $row['tflor_idtflor'];

            // Actualizar inventario
            $sqlInv = "UPDATE inv SET 
                        tflor_idtflor = :tflor_id,
                        nombre = :nombre,
                        color = :color,
                        naturaleza = :naturaleza,
                        descripcion = :descripcion,
                        stock = :stock,
                        precio = :precio,
                        alimentacion = :alimentacion,
                        disponible = :disp,
                        empleado_id = :empleado_id,
                        motivo = :motivo,
                        fecha_actualizacion = NOW()
                       WHERE idinv = :id";
            $stmtInv = $this->db->prepare($sqlInv);
            $stmtInv->execute([
                ':tflor_id'    => $data['tflor_idtflor'] ?? $tflorId,
                ':nombre'      => $data['nombre'],
                ':color'       => $data['color'] ?? '',
                ':naturaleza'  => $data['naturaleza'] ?? '',
                ':descripcion' => $data['descripcion'] ?? '',
                ':stock'        => $data['stock'],
                ':precio'       => $data['precio'],
                ':alimentacion' => $data['alimentacion'] ?? '',
                ':disp'         => isset($data['disponible']) ? 1 : 0,
                ':empleado_id'  => $usuarioId,
                ':motivo'       => $data['motivo'] ?? null,
                ':id'           => $id
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception('Error al actualizar producto: ' . $e->getMessage());
        }
    }

    /**
    * Eliminar inventario (mantiene tflor intacta para no romper pedidos previos).
    */
    public function eliminarProducto($id) {
        try {
            $sql = "DELETE FROM inv WHERE idinv = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception('Error al eliminar producto: ' . $e->getMessage());
        }
    }

    /**
     * Listar tipos (tflor) para select.
     */
    public function listarTipos() {
        $sql = "SELECT 
                    t.idtflor,
                    t.nombre,
                    t.descripcion,
                    t.disponible,
                    COALESCE(SUM(i.stock),0) AS total_stock,
                    COUNT(i.idinv) AS total_productos
                FROM tflor t
                LEFT JOIN inv i ON i.tflor_idtflor = t.idtflor
                GROUP BY t.idtflor, t.nombre, t.descripcion, t.disponible
                ORDER BY t.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear tipo de flor.
     */
    public function crearTipo($data) {
        $sql = "INSERT INTO tflor (nombre, descripcion, fecha_creacion, disponible) VALUES (:nombre, :descripcion, NOW(), :disp)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':disp' => isset($data['disponible']) ? 1 : 0
        ]);
        return $this->db->lastInsertId();
    }

    public function actualizarTipo($id, $data) {
        $sql = "UPDATE tflor SET nombre = :nombre, descripcion = :descripcion, disponible = :disp WHERE idtflor = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':disp' => isset($data['disponible']) ? 1 : 0,
            ':id' => $id
        ]);
    }

    public function eliminarTipo($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tflor WHERE idtflor = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            // Probable FK inv -> tflor
            throw new Exception('No se pudo eliminar la categoria (tiene productos asociados)');
        }
    }
}


