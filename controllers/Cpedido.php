<?php
// controllers/PedidoController.php
require_once __DIR__ . '/../models/conexion.php';

class Cpedido {
    private $db;

    public function __construct() {
        $conn = new conexion();
        $this->db = $conn->get_conexion();
    }

    public function obtenerPedidos() {
        $stmt = $this->db->prepare("SELECT p.idped AS id, c.nombre AS cliente, p.estado FROM ped p JOIN cli c ON p.cli_idcli = c.idcli");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoPedido($pedidoId, $nuevoEstado) {
        $stmt = $this->db->prepare("UPDATE ped SET estado = :estado WHERE idped = :id");
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $pedidoId);
        return $stmt->execute();
    }

    // Nuevo metodo para obtener pedidos filtrados
    public function obtenerPedidosFiltrados($estadoPedido = '', $estadoPago = '', $fechaDesde = '', $fechaHasta = '') {
        $sql = "SELECT p.idped AS id, c.nombre AS cliente, p.estado
                FROM ped p
                JOIN cli c ON p.cli_idcli = c.idcli";
        $where = [];
        $params = [];

        if ($estadoPedido) {
            $where[] = "p.estado = :estadoPedido";
            $params[':estadoPedido'] = $estadoPedido;
        }
        // Si tienes estado de pago en la tabla, agrega aqui el filtro
        // if ($estadoPago) { ... }
        if ($fechaDesde) {
            $where[] = "p.fecha_pedido >= :fechaDesde";
            $params[':fechaDesde'] = $fechaDesde;
        }
        if ($fechaHasta) {
            $where[] = "p.fecha_pedido <= :fechaHasta";
            $params[':fechaHasta'] = $fechaHasta;
        }
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY p.idped DESC";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el pedido con datos de cliente y pago.
     */
    public function obtenerDetallePedido($idPedido) {
        $stmt = $this->db->prepare("
                SELECT 
                    p.idped,
                    p.cli_idcli,
                    p.numped,
                    p.fecha_pedido,
                    p.monto_total,
                    p.estado,
                    p.direccion_entrega,
                    p.fecha_entrega_solicitada,
                    p.notas,
                    p.empleado_id,
                    c.nombre AS cliente_nombre,
                    c.email AS cliente_email,
                    c.telefono AS cliente_telefono,
                    c.direccion AS cliente_direccion,
                    pg.metodo_pago,
                    pg.estado_pag AS estado_pago,
                    pg.fecha_pago
            FROM ped p
            INNER JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN pagos pg ON pg.ped_idped = p.idped
                WHERE p.idped = ?
                ORDER BY pg.idpago DESC
                LIMIT 1
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los productos asociados a un pedido.
     */
    public function obtenerProductosPedido($idPedido) {
        $stmt = $this->db->prepare("
            SELECT 
                dp.iddetped,
                dp.idtflor,
                tf.nombre,
                tf.naturaleza,
                dp.cantidad,
                dp.precio_unitario,
                (dp.cantidad * dp.precio_unitario) AS subtotal,
                COALESCE(SUM(i.stock), 0) AS stock
            FROM detped dp
            INNER JOIN tflor tf ON dp.idtflor = tf.idtflor
            LEFT JOIN inv i ON i.tflor_idtflor = tf.idtflor
            WHERE dp.idped = ?
            GROUP BY dp.iddetped, dp.idtflor, tf.nombre, tf.naturaleza, dp.cantidad, dp.precio_unitario
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un pedido basico (solo cabecera).
     */
    public function crearPedido($cliId, $montoTotal, $estado, $direccionEntrega = null, $fechaEntrega = null, $empleadoId = null, $notas = null) {
        $numped = 'PED-' . date('YmdHis') . '-' . $cliId;
        $stmt = $this->db->prepare("
            INSERT INTO ped (numped, fecha_pedido, monto_total, estado, cli_idcli, direccion_entrega, fecha_entrega_solicitada, empleado_id, notas)
            VALUES (:numped, NOW(), :monto_total, :estado, :cli_id, :direccion, :fecha_entrega, :empleado_id, :notas)
        ");
        $stmt->bindValue(':numped', $numped);
        $stmt->bindValue(':monto_total', $montoTotal);
        $stmt->bindValue(':estado', $estado);
        $stmt->bindValue(':cli_id', $cliId, PDO::PARAM_INT);
        $stmt->bindValue(':direccion', $direccionEntrega);
        $stmt->bindValue(':fecha_entrega', $fechaEntrega);
        $stmt->bindValue(':empleado_id', $empleadoId ?: null, PDO::PARAM_INT);
        $stmt->bindValue(':notas', $notas);
        $stmt->execute();
        return ['id' => $this->db->lastInsertId(), 'numped' => $numped];
    }

    /**
     * Crear pedido rápido desde el dashboard
     */
    public function crearRapido() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?ctrl=dashboard&action=admin&page=general');
            exit();
        }

        try {
            $idcli = intval($_POST['idcli'] ?? 0);
            $fecha_entrega = $_POST['fecha_entrega'] ?? null;
            $direccion = trim($_POST['direccion_entrega'] ?? '');
            $notas = trim($_POST['notas'] ?? '');
            $tipo_entrega = $_POST['tipo_entrega'] ?? 'Domicilio';
            $prioridad = $_POST['prioridad'] ?? 'Normal';
            
            if (!$idcli || !$fecha_entrega) {
                $_SESSION['error_message'] = 'Faltan datos obligatorios: cliente y fecha de entrega';
                header('Location: index.php?ctrl=dashboard&action=admin&page=general');
                exit();
            }
            
            // Agregar info de tipo y prioridad a las notas
            $notasCompletas = "Tipo: $tipo_entrega | Prioridad: $prioridad";
            if ($notas) {
                $notasCompletas .= "\n$notas";
            }
            
            // Crear pedido con monto 0 (se actualizará al agregar productos)
            $resultado = $this->crearPedido($idcli, 0, 'Pendiente', $direccion, $fecha_entrega, null, $notasCompletas);
            
            $_SESSION['success_message'] = "Pedido #{$resultado['numped']} creado exitosamente. Ahora agrega productos desde Gestión de Pedidos.";
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al crear pedido: ' . $e->getMessage();
            header('Location: index.php?ctrl=dashboard&action=admin&page=general');
            exit();
        }
    }

    /**
     * Actualiza campos editables de un pedido.
     */
    public function actualizarPedido($idPedido, array $data) {
        $campos = [];
        $params = [':id' => $idPedido];
        $permitidos = ['direccion_entrega', 'fecha_entrega_solicitada', 'estado', 'notas', 'empleado_id'];

        foreach ($permitidos as $campo) {
            if (array_key_exists($campo, $data) && $data[$campo] !== null && $data[$campo] !== '') {
                $campos[] = "$campo = :$campo";
                $params[":$campo"] = $data[$campo];
            }
        }

        if (empty($campos)) {
            return false;
        }

        $sql = "UPDATE ped SET " . implode(', ', $campos) . " WHERE idped = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Actualiza el monto_total de un pedido.
     */
    public function actualizarMontoTotalPedido($idPedido, $montoTotal) {
        $stmt = $this->db->prepare("UPDATE ped SET monto_total = :monto WHERE idped = :id");
        return $stmt->execute([':monto' => $montoTotal, ':id' => $idPedido]);
    }

    /**
     * Lista empleados activos (roles distintos a cliente).
     */
    public function listarEmpleadosActivos() {
        // Solo perfiles admin o empleados (tpusu: 1,2,3,4)
        $stmt = $this->db->prepare("
            SELECT u.idusu, u.nombre_completo, u.tpusu_idtpusu, t.nombre AS rol
            FROM usu u
            INNER JOIN tpusu t ON u.tpusu_idtpusu = t.idtpusu
            WHERE u.activo = 1 AND u.tpusu_idtpusu IN (1,2,3,4)
            ORDER BY CASE u.tpusu_idtpusu WHEN 1 THEN 0 ELSE 1 END, u.nombre_completo ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista clientes para asignar pedidos: mezcla clientes de cli y usuarios con perfil cliente (tpusu=5).
     * Si el usuario de usu no existe en cli se crea de forma silenciosa para poder asociar el pedido.
     */
    public function listarClientes() {
        // Clientes ya existentes
        $stmt = $this->db->prepare("SELECT idcli, nombre, email, telefono, direccion FROM cli ORDER BY nombre ASC");
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mapas para evitar duplicados
        $emailMap = [];
        $telMap = [];
        foreach ($clientes as $c) {
            if (!empty($c['email'])) {
                $emailMap[strtolower(trim($c['email']))] = $c['idcli'];
            }
            if (!empty($c['telefono'])) {
                $telMap[trim($c['telefono'])] = $c['idcli'];
            }
        }

        // Usuarios con perfil cliente
        $stmtU = $this->db->prepare("SELECT idusu, nombre_completo, email, telefono, naturaleza FROM usu WHERE tpusu_idtpusu = 5");
        $stmtU->execute();
        $usuariosCli = $stmtU->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuariosCli as $u) {
            $email = trim($u['email'] ?? '');
            $tel   = trim($u['telefono'] ?? '');
            $dir   = $u['naturaleza'] ?: 'Sin direccion';
            $nombre = $u['nombre_completo'] ?: 'Cliente ' . $u['idusu'];

            // Reutilizar por email o telefono si ya existe
            $cliId = null;
            if ($email && isset($emailMap[strtolower($email)])) {
                $cliId = $emailMap[strtolower($email)];
            } elseif ($tel && isset($telMap[$tel])) {
                $cliId = $telMap[$tel];
            }

            // Si no existe, crearlo de forma silenciosa
            if (!$cliId) {
                try {
                    $cliId = $this->crearClienteRapido($nombre, $email ?: null, $tel ?: null, $dir);
                } catch (Exception $e) {
                    // Si el error es por duplicado, intentar obtener el ID existente en cli
                    if ($email) {
                        $chk = $this->db->prepare("SELECT idcli FROM cli WHERE email = :email LIMIT 1");
                        $chk->execute([':email' => $email]);
                        $row = $chk->fetch(PDO::FETCH_ASSOC);
                        if ($row && isset($row['idcli'])) {
                            $cliId = $row['idcli'];
                        }
                    }
                    if (!$cliId && $tel) {
                        $chk = $this->db->prepare("SELECT idcli FROM cli WHERE telefono = :tel LIMIT 1");
                        $chk->execute([':tel' => $tel]);
                        $row = $chk->fetch(PDO::FETCH_ASSOC);
                        if ($row && isset($row['idcli'])) {
                            $cliId = $row['idcli'];
                        }
                    }
                }
            }

            if ($cliId) {
                $emailKey = $email ? strtolower($email) : '';
                $telKey = $tel ?: '';
                // Evitar duplicar en el arreglo final
                if (($emailKey && isset($emailMap[$emailKey])) || ($telKey && isset($telMap[$telKey]))) {
                    continue;
                }
                $clientes[] = [
                    'idcli'     => $cliId,
                    'nombre'    => $nombre,
                    'email'     => $email ?: null,
                    'telefono'  => $tel ?: null,
                    'direccion' => $dir
                ];
                if ($emailKey) $emailMap[$emailKey] = $cliId;
                if ($telKey) $telMap[$telKey] = $cliId;
            }
        }

        // Ordenar por nombre para mostrar uniforme
        usort($clientes, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        return $clientes;
    }

    /**
     * Crea cliente rapido si no existe. Devuelve idcli.
     */
    public function crearClienteRapido($nombre, $email = null, $telefono = null, $direccion = null) {
        if (!$nombre) {
            throw new Exception('El nombre del cliente es obligatorio');
        }

        // Validar duplicados por email
        if ($email) {
            $chk = $this->db->prepare("SELECT idcli FROM cli WHERE email = :email LIMIT 1");
            $chk->execute([':email' => $email]);
            $row = $chk->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['idcli'])) {
                throw new Exception('Ya existe un cliente con ese email');
            }
        }
        // Validar duplicados por telefono
        if ($telefono) {
            $chk = $this->db->prepare("SELECT idcli FROM cli WHERE telefono = :tel LIMIT 1");
            $chk->execute([':tel' => $telefono]);
            $row = $chk->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['idcli'])) {
                throw new Exception('Ya existe un cliente con ese telefono');
            }
        }

        $stmt = $this->db->prepare("
            INSERT INTO cli (nombre, direccion, telefono, email, fecha_registro)
            VALUES (:nombre, :direccion, :telefono, :email, CURDATE())
        ");
        $stmt->execute([
            ':nombre' => $nombre,
            ':direccion' => $direccion ?: 'Sin direccion',
            ':telefono' => $telefono ?: 'Sin telefono',
            ':email' => $email ?: null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Lista productos (flores) con stock y precio.
     */
    public function listarProductos() {
        $stmt = $this->db->prepare("
            SELECT tf.idtflor AS id, tf.nombre, tf.descripcion,
                   COALESCE(i.stock, 0) AS stock,
                   COALESCE(i.precio, 0) AS precio,
                   tf.idtflor AS categoria_id
            FROM tflor tf
            LEFT JOIN inv i ON i.tflor_idtflor = tf.idtflor
            ORDER BY tf.nombre ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista categorias (tipos de flor).
     */
    public function listarCategorias() {
        $stmt = $this->db->prepare("SELECT idtflor AS id, nombre FROM tflor ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista productos por categoria, filtrando solo disponibles.
     */
    public function listarProductosPorCategoria($categoriaId) {
        $stmt = $this->db->prepare("
            SELECT 
                   tf.idtflor AS id, 
                   tf.nombre, 
                   tf.descripcion,
                   COALESCE(SUM(i.stock), 0) AS stock,
                   COALESCE(AVG(i.precio), tf.precio) AS precio
            FROM tflor tf
            LEFT JOIN inv i ON i.tflor_idtflor = tf.idtflor
            WHERE tf.idtflor = :cat
            GROUP BY tf.idtflor, tf.nombre, tf.descripcion, tf.precio
            ORDER BY tf.nombre ASC
        ");
        $stmt->execute([':cat' => $categoriaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina todos los detalles de un pedido (productos).
     * También restaura el stock en inventario.
     */
    public function eliminarDetallesPedido($pedidoId) {
        try {
            // Obtener todos los detalles para restaurar stock
            $stmt = $this->db->prepare("SELECT idtflor, cantidad FROM detped WHERE idped = ?");
            $stmt->execute([$pedidoId]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Restaurar stock para cada producto
            foreach ($detalles as $det) {
                $upd = $this->db->prepare("UPDATE inv SET stock = stock + ? WHERE tflor_idtflor = ?");
                $upd->execute([$det['cantidad'], $det['idtflor']]);
            }
            
            // Eliminar todos los detalles
            $del = $this->db->prepare("DELETE FROM detped WHERE idped = ?");
            return $del->execute([$pedidoId]);
        } catch (Exception $e) {
            throw new Exception('Error al eliminar detalles del pedido: ' . $e->getMessage());
        }
    }

    /**
     * Inserta detalles de pedido.
     */
    public function agregarDetallesPedido($pedidoId, array $items) {
        if (empty($items)) {
            return;
        }
        foreach ($items as $it) {
            // Obtener stock actual del producto
            $inv = $this->db->prepare("SELECT stock FROM inv WHERE tflor_idtflor = ? LIMIT 1");
            $inv->execute([$it['id']]);
            $invRow = $inv->fetch(PDO::FETCH_ASSOC);
            
            // Validar stock
            if (!$invRow || (isset($invRow['stock']) && $invRow['stock'] < $it['cantidad'])) {
                throw new Exception('Stock insuficiente para el producto ' . $it['id'] . '.');
            }

            // Insertar detalle del pedido
            $stmt = $this->db->prepare("
                INSERT INTO detped (idped, idtflor, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $pedidoId,
                $it['id'],
                $it['cantidad'],
                $it['precio_unitario']
            ]);

            // Restar stock en inventario
            $upd = $this->db->prepare("UPDATE inv SET stock = stock - ? WHERE tflor_idtflor = ? AND stock >= ?");
            $upd->execute([$it['cantidad'], $it['id'], $it['cantidad']]);
            if ($upd->rowCount() === 0) {
                throw new Exception('No se pudo descontar stock para el producto ' . $it['id'] . '.');
            }
        }
    }

    /**
     * Crea registro de pago para el pedido.
     */
    public function crearPago($pedidoId, $monto, $metodo, $estadoPag) {
        $stmt = $this->db->prepare("
            INSERT INTO pagos (ped_idped, monto, metodo_pago, estado_pag, fecha_pago)
            VALUES (:ped, :monto, :metodo, :estado, NOW())
        ");
        $stmt->execute([
            ':ped' => $pedidoId,
            ':monto' => $monto,
            ':metodo' => $metodo,
            ':estado' => $estadoPag
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza pago existente del pedido.
     */
    public function actualizarPago($pagoId, $metodo, $estadoPag, $monto = null) {
        $campos = ['metodo_pago = :metodo', 'estado_pag = :estado'];
        $params = [':metodo' => $metodo, ':estado' => $estadoPag, ':id' => $pagoId];
        if ($monto !== null) {
            $campos[] = 'monto = :monto';
            $params[':monto'] = $monto;
        }
        $sql = "UPDATE pagos SET " . implode(', ', $campos) . ", fecha_pago = NOW() WHERE idpago = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Obtiene pago por pedido.
     */
    public function obtenerPagoPorPedido($pedidoId) {
        $stmt = $this->db->prepare("SELECT * FROM pagos WHERE ped_idped = :id ORDER BY idpago DESC LIMIT 1");
        $stmt->execute([':id' => $pedidoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPagoPorPedido($pedidoId, $estadoPag, $metodo = null, $monto = null) {
        $campos = ['estado_pag = :estado'];
        $params = [':estado' => $estadoPag, ':id' => $pedidoId];
        if ($metodo !== null) {
            $campos[] = 'metodo_pago = :metodo';
            $params[':metodo'] = $metodo;
        }
        if ($monto !== null) {
            $campos[] = 'monto = :monto';
            $params[':monto'] = $monto;
        }
        $sql = "UPDATE pagos SET " . implode(', ', $campos) . ", fecha_pago = NOW() WHERE ped_idped = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function obtenerDetallesPedido($pedidoId) {
        $stmt = $this->db->prepare("SELECT idtflor AS id, cantidad, precio_unitario FROM detped WHERE idped = :id");
        $stmt->execute([':id' => $pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sumarStock($idFlor, $cantidad) {
        $stmt = $this->db->prepare("UPDATE inv SET stock = stock + :cant WHERE tflor_idtflor = :id");
        $stmt->execute([':cant' => $cantidad, ':id' => $idFlor]);
    }

    public function restarStock($idFlor, $cantidad) {
        $stmt = $this->db->prepare("UPDATE inv SET stock = stock - :cant WHERE tflor_idtflor = :id AND stock >= :cant");
        $stmt->execute([':cant' => $cantidad, ':id' => $idFlor]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Stock insuficiente para reactivar pedido.');
        }
    }

    public function beginTx() { $this->db->beginTransaction(); }
    public function commitTx() { $this->db->commit(); }
    public function rollbackTx() { if ($this->db->inTransaction()) $this->db->rollBack(); }
}

// Endpoint sencillo para peticiones AJAX directas desde la vista de gestion de pedidos
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json');
    $action = $_REQUEST['action'] ?? '';

    try {
        $controller = new Cpedido();

        switch ($action) {
            case 'detalle':
                $idPedido = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                if ($idPedido <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de pedido invalido']);
                    break;
                }
                $pedido = $controller->obtenerDetallePedido($idPedido);
                $productos = $controller->obtenerProductosPedido($idPedido);

                if (!$pedido) {
                    echo json_encode(['success' => false, 'mensaje' => 'Pedido no encontrado']);
                    break;
                }

                echo json_encode([
                    'success'   => true,
                    'pedido'    => $pedido,
                    'productos' => $productos
                ]);
                break;

            case 'cambiar_estado':
                $id = isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0;
                $estado = trim($_POST['nuevo_estado'] ?? '');
                if ($id <= 0 || $estado === '') {
                    echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
                    break;
                }
                // Manejar stock al cancelar/reactivar
                $controller->beginTx();
                // Estado anterior
                $prev = $controller->obtenerDetallePedido($id); // obtiene pedido con estado
                $estadoPrev = $prev['estado'] ?? null;
                $detalles = $controller->obtenerDetallesPedido($id);
                if ($estado === 'Cancelado' && $estadoPrev !== 'Cancelado') {
                    foreach ($detalles as $d) {
                        $controller->sumarStock($d['id'], $d['cantidad']);
                    }
                    // marcar pago como cancelado y monto en 0
                    $controller->actualizarPagoPorPedido($id, 'Cancelado', null, 0);
                }
                if ($estadoPrev === 'Cancelado' && $estado !== 'Cancelado') {
                    foreach ($detalles as $d) {
                        $controller->restarStock($d['id'], $d['cantidad']);
                    }
                }
                $ok = $controller->actualizarEstadoPedido($id, $estado);
                $controller->commitTx();
                echo json_encode($ok
                    ? ['success' => true, 'mensaje' => 'Estado actualizado correctamente']
                    : ['success' => false, 'mensaje' => 'No se pudo actualizar el estado']
                );
                break;

            case 'editar_pedido':
                $id = isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0;
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de pedido invalido']);
                    break;
                }
                $data = [
                    'direccion_entrega' => $_POST['direccion_entrega'] ?? null,
                    'fecha_entrega_solicitada' => $_POST['fecha_entrega_solicitada'] ?? null,
                    'estado' => $_POST['estado'] ?? null,
                    'notas' => $_POST['notas'] ?? null,
                    'empleado_id' => $_POST['empleado_id'] ?? null
                ];
                
                // Procesar productos si se proporcionan
                $items = [];
                $totalItems = 0;
                if (!empty($_POST['producto_id']) && is_array($_POST['producto_id'])) {
                    $productos = $_POST['producto_id'];
                    $cantidades = $_POST['cantidad'] ?? [];
                    $precios = $_POST['precio_unitario'] ?? [];
                    foreach ($productos as $idx => $prodId) {
                        $prodId = (int)$prodId;
                        $cant = isset($cantidades[$idx]) ? (float)$cantidades[$idx] : 0;
                        $precioU = isset($precios[$idx]) ? (float)$precios[$idx] : 0;
                        if ($prodId > 0 && $cant > 0 && $precioU > 0) {
                            $items[] = ['id' => $prodId, 'cantidad' => $cant, 'precio_unitario' => $precioU];
                            $totalItems += $cant * $precioU;
                        }
                    }
                }
                
                try {
                    $controller->beginTx();
                    
                    // Actualizar datos del pedido
                    $ok = $controller->actualizarPedido($id, $data);
                    
                    // Si hay items, eliminar los antiguos y agregar los nuevos
                    if (!empty($items)) {
                        $controller->eliminarDetallesPedido($id);
                        $controller->agregarDetallesPedido($id, $items);
                        // Actualizar monto_total con la suma de los items
                        $controller->actualizarMontoTotalPedido($id, $totalItems);
                    }
                    
                    $controller->commitTx();
                    
                    echo json_encode(['success' => true, 'mensaje' => 'Pedido actualizado correctamente']);
                } catch (Exception $e) {
                    $controller->rollbackTx();
                    echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
                }
                break;

            case 'empleados_activos':
                $empleados = $controller->listarEmpleadosActivos();
                echo json_encode(['success' => true, 'empleados' => $empleados]);
                break;

            case 'clientes':
                $clientes = $controller->listarClientes();
                echo json_encode(['success' => true, 'clientes' => $clientes]);
                break;

            case 'productos':
                $productos = $controller->listarProductos();
                echo json_encode(['success' => true, 'productos' => $productos]);
                break;

            case 'categorias':
                $cats = $controller->listarCategorias();
                echo json_encode(['success' => true, 'categorias' => $cats]);
                break;

            case 'productos_por_categoria':
                $catId = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
                if ($catId <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'Categoria invalida']);
                    break;
                }
                $prods = $controller->listarProductosPorCategoria($catId);
                echo json_encode(['success' => true, 'productos' => $prods]);
                break;

            case 'crear_pedido':
                $cliId = isset($_POST['cli_id']) ? (int)$_POST['cli_id'] : 0;
                $monto = isset($_POST['monto_total']) ? (float)$_POST['monto_total'] : 0;
                $estado = $_POST['estado'] ?? 'Pendiente';
                $metodoPago = $_POST['metodo_pago'] ?? 'efectivo';
                $estadoPag = $_POST['estado_pago'] ?? 'Pendiente';
                $direccion = $_POST['direccion_entrega'] ?? null;
                $fechaEntrega = $_POST['fecha_entrega_solicitada'] ?? null;
                $empleadoId = isset($_POST['empleado_id']) && $_POST['empleado_id'] !== '' ? (int)$_POST['empleado_id'] : null;
                $notas = $_POST['notas'] ?? null;
                $nombreCliente = $_POST['nombre_cliente'] ?? '';
                $telCliente = $_POST['telefono_cliente'] ?? '';
                $emailCliente = $_POST['email_cliente'] ?? '';
                $dirCliente = $_POST['direccion_cliente'] ?? '';
                $items = [];
                if (!empty($_POST['producto_id']) && is_array($_POST['producto_id'])) {
                    $productos = $_POST['producto_id'];
                    $cantidades = $_POST['cantidad'] ?? [];
                    $precios = $_POST['precio_unitario'] ?? [];
                    foreach ($productos as $idx => $prodId) {
                        $prodId = (int)$prodId;
                        $cant = isset($cantidades[$idx]) ? (float)$cantidades[$idx] : 0;
                        $precioU = isset($precios[$idx]) ? (float)$precios[$idx] : 0;
                        if ($prodId > 0 && $cant > 0 && $precioU > 0) {
                            $items[] = ['id' => $prodId, 'cantidad' => $cant, 'precio_unitario' => $precioU];
                        }
                    }
                }
                $totalItems = 0;
                foreach ($items as $it) {
                    $totalItems += $it['cantidad'] * $it['precio_unitario'];
                }

                // Monto: usar suma de items si existe; si no, usar el ingresado
                if ($totalItems > 0) {
                    $monto = $totalItems;
                }
                if ($monto <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'El monto total es requerido y debe ser mayor a cero (agrega productos o ingresa monto manual)']);
                    break;
                }
                if ($cliId <= 0) {
                    if (trim($nombreCliente) === '') {
                        echo json_encode(['success' => false, 'mensaje' => 'Ingresa un nombre de cliente o selecciona uno existente']);
                        break;
                    }
                    $cliId = $controller->crearClienteRapido($nombreCliente, $emailCliente, $telCliente, $dirCliente ?: $direccion);
                }

                $estadosValidos = ['Pendiente','En proceso','Completado','Cancelado'];
                if (!in_array($estado, $estadosValidos, true)) {
                    $estado = 'Pendiente';
                }
                $controller->beginTx();
                $pedido = $controller->crearPedido($cliId, $monto, $estado, $direccion, $fechaEntrega, $empleadoId, $notas);
                if (!empty($items)) {
                    $controller->agregarDetallesPedido($pedido['id'], $items);
                }
                // registrar pago
                $controller->crearPago($pedido['id'], $monto, $metodoPago, $estadoPag);
                $controller->commitTx();
                echo json_encode(['success' => true, 'mensaje' => 'Pedido creado', 'pedido' => $pedido]);
                break;

            case 'editar_pago':
                $id = isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0;
                $metodo = $_POST['metodo_pago'] ?? 'efectivo';
                $estadoPago = $_POST['estado_pago'] ?? 'Pendiente';
                $montoPago = isset($_POST['monto_total']) ? (float)$_POST['monto_total'] : null;
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de pedido invalido']);
                    break;
                }
                $pago = $controller->obtenerPagoPorPedido($id);
                if ($pago && isset($pago['idpago'])) {
                    $controller->actualizarPago($pago['idpago'], $metodo, $estadoPago, $montoPago);
                } else {
                    $controller->crearPago($id, $montoPago ?? 0, $metodo, $estadoPago);
                }
                echo json_encode(['success' => true, 'mensaje' => 'Pago actualizado']);
                break;

            default:
                echo json_encode(['success' => false, 'mensaje' => 'Accion no reconocida']);
                break;
        }
    } catch (Exception $e) {
        // Devolver mensaje detallado para mostrar en frontend
        $controller->rollbackTx();
        echo json_encode(['success' => false, 'mensaje' => $e->getMessage(), 'error' => $e->getMessage()]);
    }
    exit;
}
