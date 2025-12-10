<?php
require_once 'conexion.php';

class Mlotes {
    private $db;

    public function __construct() {
        $conexion = new conexion();
        $this->db = $conexion->get_conexion();
    }

    /**
     * Obtener todos los lotes de un producto
     */
    public function getLotesPorProducto($inv_idinv) {
        try {
                $sql = "SELECT l.*, i.stock, t.nombre as producto
                    FROM lotes l
                    INNER JOIN inv i ON l.inv_idinv = i.idinv
                    LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                    WHERE l.inv_idinv = :inv_idinv
                    ORDER BY l.fecha_caducidad ASC, l.fecha_ingreso DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':inv_idinv', $inv_idinv, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener lotes: " . $e->getMessage());
            throw new Exception("Error al obtener lotes del producto");
        }
    }

    /**
     * Obtener un lote específico por ID
     */
    public function getLotePorId($idlote) {
        try {
                $sql = "SELECT l.*, i.stock, t.nombre as producto
                    FROM lotes l
                    INNER JOIN inv i ON l.inv_idinv = i.idinv
                    LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                    WHERE l.idlote = :idlote";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idlote', $idlote, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener lote: " . $e->getMessage());
            throw new Exception("Error al obtener información del lote");
        }
    }

    /**
     * Crear un nuevo lote
     */
    public function crearLote($datos) {
        try {
            $this->db->beginTransaction();

            // Validar datos requeridos
            if (empty($datos['inv_idinv']) || empty($datos['numero_lote']) || 
                empty($datos['cantidad']) || empty($datos['fecha_caducidad'])) {
                throw new Exception("Faltan datos obligatorios");
            }

            // Validar que el número de lote no exista para ese producto
            $sqlCheck = "SELECT COUNT(*) as count FROM lotes 
                        WHERE inv_idinv = :inv_idinv AND numero_lote = :numero_lote";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->bindParam(':inv_idinv', $datos['inv_idinv']);
            $stmtCheck->bindParam(':numero_lote', $datos['numero_lote']);
            $stmtCheck->execute();
            
            if ($stmtCheck->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
                throw new Exception("El número de lote ya existe para este producto");
            }

            // Insertar lote
            $sql = "INSERT INTO lotes (inv_idinv, numero_lote, cantidad, fecha_ingreso, 
                    fecha_caducidad, proveedor, precio_compra, observaciones)
                    VALUES (:inv_idinv, :numero_lote, :cantidad, :fecha_ingreso, 
                    :fecha_caducidad, :proveedor, :precio_compra, :observaciones)";
            
            $stmt = $this->db->prepare($sql);
            
            // Preparar variables para bindParam
            $fecha_ingreso = $datos['fecha_ingreso'] ?? date('Y-m-d');
            $proveedor = $datos['proveedor'] ?? null;
            $precio_compra = $datos['precio_compra'] ?? null;
            $observaciones = $datos['observaciones'] ?? null;
            
            $stmt->bindParam(':inv_idinv', $datos['inv_idinv']);
            $stmt->bindParam(':numero_lote', $datos['numero_lote']);
            $stmt->bindParam(':cantidad', $datos['cantidad']);
            $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
            $stmt->bindParam(':fecha_caducidad', $datos['fecha_caducidad']);
            $stmt->bindParam(':proveedor', $proveedor);
            $stmt->bindParam(':precio_compra', $precio_compra);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->execute();

            // Actualizar stock del producto sumando la cantidad del lote
            $sqlUpdateStock = "UPDATE inv SET stock = stock + :cantidad WHERE idinv = :inv_idinv";
            $stmtUpdate = $this->db->prepare($sqlUpdateStock);
            $stmtUpdate->bindParam(':cantidad', $datos['cantidad']);
            $stmtUpdate->bindParam(':inv_idinv', $datos['inv_idinv']);
            $stmtUpdate->execute();

            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Lote agregado exitosamente',
                'idlote' => $this->db->lastInsertId()
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear lote: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar un lote existente
     */
    public function actualizarLote($idlote, $datos) {
        try {
            $this->db->beginTransaction();

            // Obtener cantidad anterior del lote
            $sqlOld = "SELECT cantidad, inv_idinv FROM lotes WHERE idlote = :idlote";
            $stmtOld = $this->db->prepare($sqlOld);
            $stmtOld->bindParam(':idlote', $idlote);
            $stmtOld->execute();
            $loteAnterior = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if (!$loteAnterior) {
                throw new Exception("Lote no encontrado");
            }

            // Actualizar lote
            $sql = "UPDATE lotes SET 
                    numero_lote = :numero_lote,
                    cantidad = :cantidad,
                    fecha_caducidad = :fecha_caducidad,
                    proveedor = :proveedor,
                    precio_compra = :precio_compra,
                    estado = :estado,
                    observaciones = :observaciones
                    WHERE idlote = :idlote";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idlote', $idlote);
            $stmt->bindParam(':numero_lote', $datos['numero_lote']);
            $stmt->bindParam(':cantidad', $datos['cantidad']);
            $stmt->bindParam(':fecha_caducidad', $datos['fecha_caducidad']);
            $stmt->bindParam(':proveedor', $datos['proveedor'] ?? null);
            $stmt->bindParam(':precio_compra', $datos['precio_compra'] ?? null);
            $stmt->bindParam(':estado', $datos['estado'] ?? 'activo');
            $stmt->bindParam(':observaciones', $datos['observaciones'] ?? null);
            $stmt->execute();

            // Ajustar stock si cambió la cantidad
            $diferencia = $datos['cantidad'] - $loteAnterior['cantidad'];
            if ($diferencia != 0) {
                $sqlUpdateStock = "UPDATE inv SET stock = stock + :diferencia WHERE idinv = :inv_idinv";
                $stmtUpdate = $this->db->prepare($sqlUpdateStock);
                $stmtUpdate->bindParam(':diferencia', $diferencia);
                $stmtUpdate->bindParam(':inv_idinv', $loteAnterior['inv_idinv']);
                $stmtUpdate->execute();
            }

            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Lote actualizado exitosamente'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar lote: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar un lote
     */
    public function eliminarLote($idlote) {
        try {
            $this->db->beginTransaction();

            // Obtener datos del lote antes de eliminarlo
            $sqlGet = "SELECT cantidad, inv_idinv FROM lotes WHERE idlote = :idlote";
            $stmtGet = $this->db->prepare($sqlGet);
            $stmtGet->bindParam(':idlote', $idlote);
            $stmtGet->execute();
            $lote = $stmtGet->fetch(PDO::FETCH_ASSOC);

            if (!$lote) {
                throw new Exception("Lote no encontrado");
            }

            // Eliminar lote
            $sql = "DELETE FROM lotes WHERE idlote = :idlote";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idlote', $idlote);
            $stmt->execute();

            // Restar cantidad del stock
            $sqlUpdateStock = "UPDATE inv SET stock = GREATEST(0, stock - :cantidad) WHERE idinv = :inv_idinv";
            $stmtUpdate = $this->db->prepare($sqlUpdateStock);
            $stmtUpdate->bindParam(':cantidad', $lote['cantidad']);
            $stmtUpdate->bindParam(':inv_idinv', $lote['inv_idinv']);
            $stmtUpdate->execute();

            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Lote eliminado exitosamente'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar lote: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener lotes próximos a caducar (7 días)
     */
    public function getLotesProximosCaducar($dias = 7) {
        try {
                $sql = "SELECT l.*, t.nombre as producto, i.stock
                    FROM lotes l
                    INNER JOIN inv i ON l.inv_idinv = i.idinv
                    LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                    WHERE l.estado = 'activo'
                    AND l.cantidad > 0
                    AND l.fecha_caducidad BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                    ORDER BY l.fecha_caducidad ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener lotes próximos a caducar: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Marcar lotes caducados automáticamente
     */
    public function marcarLotesCaducados() {
        try {
            $sql = "UPDATE lotes 
                    SET estado = 'caducado' 
                    WHERE estado = 'activo' 
                    AND fecha_caducidad < CURDATE()";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error al marcar lotes caducados: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener resumen de lotes por producto
     */
    public function getResumenLotesPorProducto($inv_idinv) {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_lotes,
                    SUM(CASE WHEN estado = 'activo' THEN cantidad ELSE 0 END) as cantidad_activa,
                    SUM(CASE WHEN estado = 'caducado' THEN cantidad ELSE 0 END) as cantidad_caducada,
                    MIN(CASE WHEN estado = 'activo' THEN fecha_caducidad END) as proxima_caducidad
                    FROM lotes
                    WHERE inv_idinv = :inv_idinv";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':inv_idinv', $inv_idinv, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener resumen de lotes: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generar el siguiente número de lote secuencial para un producto
     */
    public function generarNumeroLote($inv_idinv) {
        try {
            // Obtener el nombre del producto
            $sqlProducto = "SELECT t.nombre 
                           FROM inv i 
                           LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor 
                           WHERE i.idinv = :inv_idinv";
            $stmtProducto = $this->db->prepare($sqlProducto);
            $stmtProducto->bindParam(':inv_idinv', $inv_idinv, PDO::PARAM_INT);
            $stmtProducto->execute();
            $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);
            
            // Nombre del producto en mayúsculas sin espacios
            $nombreProducto = strtoupper(str_replace(' ', '', $producto['nombre'] ?? 'PROD'));
            
            // Contar cuántos lotes tiene este producto
            $sqlCount = "SELECT COUNT(*) as total FROM lotes WHERE inv_idinv = :inv_idinv";
            $stmtCount = $this->db->prepare($sqlCount);
            $stmtCount->bindParam(':inv_idinv', $inv_idinv, PDO::PARAM_INT);
            $stmtCount->execute();
            $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
            
            // Siguiente número secuencial
            $siguiente = ($result['total'] ?? 0) + 1;
            
            // Formato: LOTE-NOMBREPRODUCTO-001
            return sprintf('LOTE-%s-%03d', $nombreProducto, $siguiente);
            
        } catch (PDOException $e) {
            error_log("Error al generar número de lote: " . $e->getMessage());
            // Fallback: usar timestamp
            return 'LOTE-' . time();
        }
    }
    
    /**
     * Sincronizar stock del producto con la suma de lotes activos
     * Útil para corregir inconsistencias
     */
    public function sincronizarStockProducto($inv_idinv) {
        try {
            $this->db->beginTransaction();
            
            // Calcular stock real sumando lotes activos
            $sqlSuma = "SELECT COALESCE(SUM(cantidad), 0) as stock_real 
                       FROM lotes 
                       WHERE inv_idinv = :inv_idinv 
                       AND estado = 'activo'";
            $stmtSuma = $this->db->prepare($sqlSuma);
            $stmtSuma->bindParam(':inv_idinv', $inv_idinv, PDO::PARAM_INT);
            $stmtSuma->execute();
            $resultado = $stmtSuma->fetch(PDO::FETCH_ASSOC);
            
            $stock_real = $resultado['stock_real'] ?? 0;
            
            // Actualizar stock del producto
            $sqlUpdate = "UPDATE inv SET stock = :stock_real WHERE idinv = :inv_idinv";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':stock_real', $stock_real, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':inv_idinv', $inv_idinv, PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'stock_anterior' => null,
                'stock_nuevo' => $stock_real,
                'message' => "Stock sincronizado: {$stock_real} unidades"
            ];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al sincronizar stock: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al sincronizar stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Sincronizar stock de TODOS los productos con lotes
     */
    public function sincronizarTodosLosStocks() {
        try {
            // Obtener todos los productos que tienen lotes
            $sqlProductos = "SELECT DISTINCT inv_idinv FROM lotes";
            $stmt = $this->db->prepare($sqlProductos);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $sincronizados = 0;
            foreach ($productos as $inv_idinv) {
                $resultado = $this->sincronizarStockProducto($inv_idinv);
                if ($resultado['success']) {
                    $sincronizados++;
                }
            }
            
            return [
                'success' => true,
                'productos_sincronizados' => $sincronizados,
                'message' => "Se sincronizaron {$sincronizados} productos"
            ];
            
        } catch (PDOException $e) {
            error_log("Error al sincronizar todos los stocks: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al sincronizar stocks: ' . $e->getMessage()
            ];
        }
    }
}
?>
