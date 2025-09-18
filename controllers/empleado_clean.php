<?php
class empleado {
    private $db;
    private $empleado_id;
    
    public function __construct() {
        // Verificar que el usuario esté logueado y sea empleado (tipos 2, 3, 4)
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['tpusu_idtpusu'], [2, 3, 4])) {
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }
        
        // Conectar a la base de datos
        require_once 'models/conexion.php';
        $conn = new conexion();
        $this->db = $conn->get_conexion();
        
        $this->empleado_id = $_SESSION['user']['idusu'];
    }
    
    public function index() {
        $this->dashboard();
    }
    
    public function dashboard() {
        $user = $_SESSION['user'];
        
        // Obtener estadísticas del empleado
        $stats = $this->obtenerEstadisticas();
        $pedidos_pendientes = $this->obtenerPedidosPendientes();
        $pagos_pendientes = $this->obtenerPagosPendientes();
        
        include 'views/empleado/dashboard.php';
    }
    
    public function gestion_pedidos() {
        $user = $_SESSION['user'];
        $pedidos = $this->obtenerTodosPedidos();
        include 'views/empleado/gestion_pedidos.php';
    }
    
    public function procesar_pagos() {
        $user = $_SESSION['user'];
        $stats = $this->obtenerEstadisticasPagos();
        $pagos_pendientes = $this->obtenerPagosPendientesDetallados();
        $pagos_verificados = $this->obtenerPagosVerificadosRecientes();
        
        include 'views/empleado/procesar_pagos.php';
    }
    
    public function inventario() {
        $user = $_SESSION['user'];
        $stats = $this->obtenerEstadisticasInventario();
        $productos = $this->obtenerProductosInventario();
        
        include 'views/empleado/inventario.php';
    }
    
    public function verificar_pago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pago = $_POST['id_pago'] ?? 0;
            $accion = $_POST['accion'] ?? '';
            $observaciones = $_POST['observaciones_empleado'] ?? '';
            
            if ($id_pago && in_array($accion, ['aprobar', 'rechazar'])) {
                $nuevo_estado = $accion === 'aprobar' ? 'Completado' : 'Rechazado';
                $this->actualizarEstadoPago($id_pago, $nuevo_estado, $observaciones);
                
                $_SESSION['mensaje'] = $accion === 'aprobar' ? 'Pago aprobado correctamente' : 'Pago rechazado';
                $_SESSION['tipo_mensaje'] = $accion === 'aprobar' ? 'success' : 'warning';
            }
        }
        
        header('Location: index.php?ctrl=empleado&action=procesar_pagos');
        exit();
    }
    
    public function actualizar_stock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto_id = $_POST['producto_id'] ?? 0;
            $nuevo_stock = $_POST['nuevo_stock'] ?? 0;
            $motivo = $_POST['motivo'] ?? '';
            $observaciones = $_POST['observaciones'] ?? '';
            
            if ($producto_id && $nuevo_stock >= 0) {
                $this->actualizarStockProducto($producto_id, $nuevo_stock, $motivo, $observaciones);
                $_SESSION['mensaje'] = 'Stock actualizado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            }
        }
        
        header('Location: index.php?ctrl=empleado&action=inventario');
        exit();
    }
    
    public function actualizar_estado_pedido() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pedido = $_POST['id_pedido'] ?? 0;
            $nuevo_estado = $_POST['nuevo_estado'] ?? '';
            
            if ($id_pedido && $nuevo_estado) {
                $this->actualizarEstadoPedido($id_pedido, $nuevo_estado);
                $_SESSION['mensaje'] = "Estado del pedido actualizado a: $nuevo_estado";
                $_SESSION['tipo_mensaje'] = 'success';
            }
        }
        
        header('Location: index.php?ctrl=empleado&action=gestion_pedidos');
        exit();
    }
    
    // Métodos privados para obtener datos
    private function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Pedidos de hoy
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM ped 
                WHERE DATE(fecha_pedido) = CURDATE()
            ");
            $stmt->execute();
            $stats['pedidos_hoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pedidos pendientes
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM ped 
                WHERE estado IN ('Pendiente', 'En proceso')
            ");
            $stmt->execute();
            $stats['pedidos_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pagos pendientes
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM pagos 
                WHERE estado_pag = 'Pendiente'
            ");
            $stmt->execute();
            $stats['pagos_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Ventas del mes
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(monto_total), 0) as total 
                FROM ped 
                WHERE MONTH(fecha_pedido) = MONTH(CURDATE()) 
                AND YEAR(fecha_pedido) = YEAR(CURDATE())
                AND estado = 'Completado'
            ");
            $stmt->execute();
            $stats['ventas_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return $stats;
        } catch (Exception $e) {
            return [
                'pedidos_hoy' => 0,
                'pedidos_pendientes' => 0,
                'pagos_pendientes' => 0,
                'ventas_mes' => 0
            ];
        }
    }
    
    private function obtenerPedidosPendientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.idped,
                    p.numped,
                    p.fecha_pedido,
                    p.monto_total,
                    p.estado,
                    c.nombre as cliente_nombre,
                    pg.estado_pag
                FROM ped p
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN pagos pg ON p.idped = pg.ped_idped
                WHERE p.estado IN ('Pendiente', 'En proceso')
                ORDER BY p.fecha_pedido DESC
                LIMIT 10
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function obtenerPagosPendientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    pg.idpago,
                    pg.monto,
                    pg.metodo_pago,
                    pg.fecha_pago,
                    p.numped,
                    c.nombre as cliente_nombre
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Pendiente'
                ORDER BY pg.fecha_pago DESC
                LIMIT 10
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function obtenerEstadisticasPagos() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(CASE WHEN estado_pag = 'Pendiente' THEN 1 END) as pendientes,
                    SUM(CASE WHEN estado_pag = 'Pendiente' THEN monto ELSE 0 END) as monto_pendiente,
                    COUNT(CASE WHEN estado_pag = 'Completado' AND DATE(fecha_verificacion) = CURDATE() THEN 1 END) as verificados_hoy,
                    SUM(CASE WHEN estado_pag = 'Completado' AND DATE(fecha_verificacion) = CURDATE() THEN monto ELSE 0 END) as verificados_monto
                FROM pagos
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'pendientes' => 0,
                'monto_pendiente' => 0,
                'verificados_hoy' => 0,
                'verificados_monto' => 0
            ];
        } catch (Exception $e) {
            return [
                'pendientes' => 0,
                'monto_pendiente' => 0,
                'verificados_hoy' => 0,
                'verificados_monto' => 0
            ];
        }
    }
    
    private function obtenerPagosPendientesDetallados() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    p.numped,
                    c.nombre as cliente_nombre
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Pendiente'
                ORDER BY pg.fecha_pago DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function obtenerPagosVerificadosRecientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    p.numped,
                    c.nombre as cliente_nombre,
                    u.nombre_completo as verificado_por
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN usu u ON pg.verificado_por = u.idusu
                WHERE pg.estado_pag IN ('Completado', 'Rechazado')
                ORDER BY pg.fecha_verificacion DESC
                LIMIT 10
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function obtenerEstadisticasInventario() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_productos,
                    COUNT(CASE WHEN inv.cantidad_disponible < 5 AND inv.cantidad_disponible > 0 THEN 1 END) as stock_bajo,
                    COUNT(CASE WHEN inv.cantidad_disponible = 0 THEN 1 END) as sin_stock,
                    SUM(tflor.precio * inv.cantidad_disponible) as valor_total
                FROM tflor
                LEFT JOIN inv ON tflor.idtflor = inv.tflor_idtflor
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_productos' => 0,
                'stock_bajo' => 0,
                'sin_stock' => 0,
                'valor_total' => 0
            ];
        } catch (Exception $e) {
            return [
                'total_productos' => 0,
                'stock_bajo' => 0,
                'sin_stock' => 0,
                'valor_total' => 0
            ];
        }
    }
    
    private function obtenerProductosInventario() {
        try {
            $where_conditions = [];
            $params = [];
            
            // Filtros de búsqueda
            if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
                $where_conditions[] = "tflor.nombre LIKE ?";
                $params[] = '%' . $_GET['categoria'] . '%';
            }
            
            if (isset($_GET['stock_estado']) && !empty($_GET['stock_estado'])) {
                switch ($_GET['stock_estado']) {
                    case 'alto':
                        $where_conditions[] = "COALESCE(inv.cantidad_disponible, 0) > 20";
                        break;
                    case 'medio':
                        $where_conditions[] = "COALESCE(inv.cantidad_disponible, 0) BETWEEN 5 AND 20";
                        break;
                    case 'bajo':
                        $where_conditions[] = "COALESCE(inv.cantidad_disponible, 0) BETWEEN 1 AND 4";
                        break;
                    case 'sin_stock':
                        $where_conditions[] = "COALESCE(inv.cantidad_disponible, 0) = 0";
                        break;
                }
            }
            
            if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
                $where_conditions[] = "tflor.nombre LIKE ?";
                $params[] = '%' . $_GET['buscar'] . '%';
            }
            
            $where_clause = '';
            if (!empty($where_conditions)) {
                $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    tflor.idtflor,
                    tflor.nombre,
                    tflor.naturaleza,
                    tflor.precio,
                    tflor.imagen,
                    COALESCE(inv.cantidad_disponible, 0) as cantidad_disponible
                FROM tflor
                LEFT JOIN inv ON tflor.idtflor = inv.tflor_idtflor
                $where_clause
                ORDER BY tflor.nombre
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function obtenerTodosPedidos() {
        try {
            $where_conditions = [];
            $params = [];
            
            // Filtros de búsqueda
            if (isset($_GET['estado_pedido']) && !empty($_GET['estado_pedido'])) {
                $where_conditions[] = "p.estado = ?";
                $params[] = $_GET['estado_pedido'];
            }
            
            if (isset($_GET['estado_pago']) && !empty($_GET['estado_pago'])) {
                $where_conditions[] = "pg.estado_pag = ?";
                $params[] = $_GET['estado_pago'];
            }
            
            if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) {
                $where_conditions[] = "DATE(p.fecha_pedido) >= ?";
                $params[] = $_GET['fecha_desde'];
            }
            
            if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) {
                $where_conditions[] = "DATE(p.fecha_pedido) <= ?";
                $params[] = $_GET['fecha_hasta'];
            }
            
            $where_clause = '';
            if (!empty($where_conditions)) {
                $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.idped,
                    p.numped,
                    p.fecha_pedido,
                    p.monto_total,
                    p.estado,
                    p.cantidad,
                    c.nombre as cliente_nombre,
                    c.email as cliente_email,
                    pg.estado_pag,
                    1 as total_productos
                FROM ped p
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN pagos pg ON p.idped = pg.ped_idped
                $where_clause
                ORDER BY p.fecha_pedido DESC
                LIMIT 100
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function actualizarEstadoPago($id_pago, $nuevo_estado, $observaciones = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE pagos 
                SET estado_pag = ?, 
                    observaciones_empleado = ?,
                    verificado_por = ?,
                    fecha_verificacion = NOW()
                WHERE idpago = ?
            ");
            return $stmt->execute([$nuevo_estado, $observaciones, $this->empleado_id, $id_pago]);
        } catch (Exception $e) {
            return false;
        }  
    }
    
    private function actualizarStockProducto($producto_id, $nuevo_stock, $motivo, $observaciones) {
        try {
            // Primero verificar si existe registro en inventario
            $stmt = $this->db->prepare("SELECT cantidad_disponible FROM inv WHERE tflor_idtflor = ?");
            $stmt->execute([$producto_id]);
            $inventario_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($inventario_actual) {
                // Actualizar inventario existente
                $stmt = $this->db->prepare("
                    UPDATE inv 
                    SET cantidad_disponible = ?, 
                        fecha_actualizacion = NOW()
                    WHERE tflor_idtflor = ?
                ");
                $result = $stmt->execute([$nuevo_stock, $producto_id]);
            } else {
                // Crear nuevo registro de inventario
                $stmt = $this->db->prepare("
                    INSERT INTO inv (tflor_idtflor, cantidad_disponible, fecha_actualizacion) 
                    VALUES (?, ?, NOW())
                ");
                $result = $stmt->execute([$producto_id, $nuevo_stock]);
            }
            
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function actualizarEstadoPedido($id_pedido, $nuevo_estado) {
        try {
            $stmt = $this->db->prepare("
                UPDATE ped 
                SET estado = ?, 
                    fecha_actualizacion = NOW(),
                    empleado_actualiza = ?
                WHERE idped = ?
            ");
            return $stmt->execute([$nuevo_estado, $this->empleado_id, $id_pedido]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
