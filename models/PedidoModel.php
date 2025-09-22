<?php
class PedidoModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Obtiene las flores disponibles con stock real
     */
public function obtenerFloresDisponibles() {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                tf.idtflor,
                tf.nombre,
                tf.naturaleza as color,
                tf.descripcion,
                tf.precio,
                COALESCE(i.cantidad_disponible, 0) as stock_disponible,
                COALESCE(i.stock, 0) as stock_total
            FROM tflor tf
            LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
            WHERE tf.estado = 'activo' 
                AND (tf.activo = 1 OR tf.activo IS NULL)
                AND COALESCE(i.cantidad_disponible, 0) > 0
            ORDER BY tf.nombre
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error obteniendo flores disponibles: " . $e->getMessage());
        return [];
    }
}

    /**
     * Verifica el stock disponible para una flor específica
     */
    public function verificarStock($idFlor, $cantidadSolicitada) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(i.cantidad_disponible, 0) as stock_disponible,
                    tf.nombre,
                    i.idinv
                FROM tflor tf
                LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
                WHERE tf.idtflor = ?
            ");
            $stmt->execute([$idFlor]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ['disponible' => false, 'mensaje' => 'Flor no encontrada'];
            }

            $stockDisponible = (int)$result['stock_disponible'];
            $disponible = $stockDisponible >= $cantidadSolicitada;

            return [
                'disponible' => $disponible,
                'stock_actual' => $stockDisponible,
                'nombre' => $result['nombre'],
                'idinv' => $result['idinv'],
                'mensaje' => $disponible ? '' : "Stock insuficiente. Disponible: $stockDisponible unidades"
            ];

        } catch (PDOException $e) {
            error_log("Error verificando stock: " . $e->getMessage());
            return ['disponible' => false, 'mensaje' => 'Error al verificar stock'];
        }
    }

    /**
     * Actualiza el stock después de realizar un pedido
     */
    public function actualizarStock($idFlor, $cantidadVendida, $idUsuario = null) {
        try {
            // 1. Obtener el stock actual
            $stmt = $this->db->prepare("
                SELECT i.idinv, i.cantidad_disponible, i.stock 
                FROM inv i 
                WHERE i.tflor_idtflor = ?
            ");
            $stmt->execute([$idFlor]);
            $inventario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$inventario) {
                throw new Exception("No se encontró inventario para la flor ID: $idFlor");
            }

            $stockAnterior = (int)$inventario['cantidad_disponible'];
            $nuevoStock = $stockAnterior - $cantidadVendida;

            if ($nuevoStock < 0) {
                throw new Exception("No se puede actualizar stock a negativo");
            }

            // 2. Actualizar el inventario
            $stmt = $this->db->prepare("
                UPDATE inv 
                SET cantidad_disponible = ?, 
                    stock = ?, 
                    fecha_actualizacion = NOW(),
                    empleado_id = ?
                WHERE tflor_idtflor = ?
            ");
            $stmt->execute([
                $nuevoStock, 
                $nuevoStock,
                $idUsuario, 
                $idFlor
            ]);

            // 3. Registrar en el historial de inventario
            $stmt = $this->db->prepare("
                INSERT INTO inv_historial 
                (idinv, stock_anterior, stock_nuevo, fecha_cambio, idusu, motivo) 
                VALUES (?, ?, ?, NOW(), ?, ?)
            ");
            $stmt->execute([
                $inventario['idinv'],
                $stockAnterior,
                $nuevoStock,
                $idUsuario,
                "Venta - Reducción de stock por pedido"
            ]);

            return true;

        } catch (Exception $e) {
            error_log("Error actualizando stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo pedido y actualiza el stock
     */
    public function crearPedido($datosPedido) {
        try {
            $this->db->beginTransaction();

            // 1. Verificar stock para todos los items
            foreach ($datosPedido['detalles'] as $detalle) {
                $verificacion = $this->verificarStock($detalle['idtflor'], $detalle['cantidad']);
                if (!$verificacion['disponible']) {
                    throw new Exception($verificacion['mensaje']);
                }
            }

            // 2. Crear el pedido
            $stmt = $this->db->prepare("
                INSERT INTO ped 
                (numped, fecha_pedido, monto_total, estado, cli_idcli, direccion_entrega, fecha_entrega_solicitada) 
                VALUES (?, NOW(), ?, 'Pendiente', ?, ?, ?)
            ");
            $stmt->execute([
                $datosPedido['numped'],
                $datosPedido['monto_total'],
                $datosPedido['cli_idcli'],
                $datosPedido['direccion_entrega'],
                $datosPedido['fecha_entrega']
            ]);
            $pedidoId = $this->db->lastInsertId();

            // 3. Insertar detalles del pedido y actualizar stock
            foreach ($datosPedido['detalles'] as $detalle) {
                // Insertar detalle del pedido
                $stmt = $this->db->prepare("
                    INSERT INTO detped 
                    (idped, idtflor, cantidad, precio_unitario) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $pedidoId,
                    $detalle['idtflor'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario']
                ]);

                // Actualizar stock
                if (!$this->actualizarStock($detalle['idtflor'], $detalle['cantidad'], $datosPedido['id_usuario'])) {
                    throw new Exception("Error al actualizar stock para la flor ID: " . $detalle['idtflor']);
                }
            }

            // 4. Registrar el pago
            $stmt = $this->db->prepare("
                INSERT INTO pagos 
                (ped_idped, monto, metodo_pago, estado_pag, fecha_pago) 
                VALUES (?, ?, ?, 'Pendiente', NOW())
            ");
            $stmt->execute([
                $pedidoId,
                $datosPedido['monto_total'],
                $datosPedido['metodo_pago']
            ]);

            $this->db->commit();
            return $pedidoId;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error creando pedido: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene el stock actualizado de todas las flores
     */
    public function obtenerStockActual() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    tf.idtflor,
                    tf.nombre,
                    COALESCE(i.cantidad_disponible, 0) as stock_disponible,
                    tf.precio
                FROM tflor tf
                LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
                WHERE tf.estado = 'activo' AND (tf.activo = 1 OR tf.activo IS NULL)
                ORDER BY tf.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo stock actual: " . $e->getMessage());
            return [];
        }
    }
}
?>