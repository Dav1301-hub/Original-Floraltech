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
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Debug: Simple database test first
            $testQuery = "SELECT COUNT(*) as total FROM ped";
            $stmt = $this->db->prepare($testQuery);
            $stmt->execute();
            $testResult = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("DEBUG - Total pedidos in database: " . $testResult['total']);
            
            // Debug: Log incoming parameters
            error_log("DEBUG gestion_pedidos - GET parameters: " . print_r($_GET, true));
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Procesar actualización de estado
                if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_estado') {
                    $idped = $_POST['idped'];
                    $nuevo_estado = $_POST['estado'];
                    
                    if ($this->actualizarEstadoPedido($idped, $nuevo_estado)) {
                        $_SESSION['success'] = "Estado del pedido actualizado correctamente";
                    } else {
                        $_SESSION['error'] = "Error al actualizar el estado del pedido";
                    }
                    
                    // Redirigir para evitar reenvío del formulario
                    $redirect_url = "index.php?ctrl=empleado&action=gestion_pedidos";
                    
                    // Mantener parámetros de paginación y filtros
                    $params = [];
                    if (isset($_GET['pagina'])) $params['pagina'] = $_GET['pagina'];
                    if (isset($_GET['estado_pedido']) && !empty($_GET['estado_pedido'])) $params['estado_pedido'] = $_GET['estado_pedido'];
                    if (isset($_GET['estado_pago']) && !empty($_GET['estado_pago'])) $params['estado_pago'] = $_GET['estado_pago'];
                    if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) $params['fecha_desde'] = $_GET['fecha_desde'];
                    if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) $params['fecha_hasta'] = $_GET['fecha_hasta'];
                    
                    if (!empty($params)) {
                        $redirect_url .= "&" . http_build_query($params);
                    }
                    
                    header("Location: " . $redirect_url);
                    exit;
                }
            }
            
            // Obtener pedidos con filtros aplicados
            $pedidos = $this->obtenerTodosPedidos();
            error_log("DEBUG gestion_pedidos - Pedidos count: " . count($pedidos));
            
            // Configuración de paginación
            $pedidosPorPagina = 5;
            $paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $totalPedidos = count($pedidos);
            $totalPaginas = ceil($totalPedidos / $pedidosPorPagina);
            $offset = ($paginaActual - 1) * $pedidosPorPagina;
            
            // Aplicar paginación
            $pedidosPaginados = array_slice($pedidos, $offset, $pedidosPorPagina);
            error_log("DEBUG gestion_pedidos - Paginated pedidos count: " . count($pedidosPaginados));
            
            // Incluir la vista
            include __DIR__ . '/../views/empleado/gestion_pedidos.php';
            
        } catch (Exception $e) {
            error_log("Error en gestion_pedidos: " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar la gestión de pedidos";
            header("Location: dashboard.php");
            exit;
        }
    }
    
    public function procesar_pagos() {
        error_log("=== INICIO procesar_pagos ===");
        
        $user = $_SESSION['user'];
        error_log("Usuario: " . $user['nombre_completo']);
        
        try {
            $stats = $this->obtenerEstadisticasPagos();
            error_log("Estadísticas obtenidas: " . print_r($stats, true));
            
            $pagos_pendientes = $this->obtenerPagosPendientesDetallados();
            error_log("Pagos pendientes obtenidos: " . count($pagos_pendientes));
            
            $pagos_verificados = $this->obtenerPagosVerificadosRecientes();
            error_log("Pagos verificados obtenidos: " . count($pagos_verificados));
            
        } catch (Exception $e) {
            error_log("Error en procesar_pagos: " . $e->getMessage());
            // Valores por defecto en caso de error
            $stats = [
                'pendientes' => 0,
                'monto_pendiente' => 0.0,
                'verificados_hoy' => 0,
                'verificados_monto' => 0.0
            ];
            $pagos_pendientes = [];
            $pagos_verificados = [];
        }
        
        error_log("=== FIN procesar_pagos - Incluyendo vista ===");
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
                    COALESCE(SUM(CASE WHEN estado_pag = 'Pendiente' THEN monto ELSE 0 END), 0) as monto_pendiente,
                    COUNT(CASE WHEN estado_pag IN ('Completado', 'Rechazado') AND DATE(fecha_pago) = CURDATE() THEN 1 END) as verificados_hoy,
                    COALESCE(SUM(CASE WHEN estado_pag IN ('Completado', 'Rechazado') AND DATE(fecha_pago) = CURDATE() THEN monto ELSE 0 END), 0) as verificados_monto
                FROM pagos
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Asegurar que todos los valores sean numéricos
                return [
                    'pendientes' => intval($result['pendientes']),
                    'monto_pendiente' => floatval($result['monto_pendiente']),
                    'verificados_hoy' => intval($result['verificados_hoy']),
                    'verificados_monto' => floatval($result['verificados_monto'])
                ];
            } else {
                return [
                    'pendientes' => 0,
                    'monto_pendiente' => 0.0,
                    'verificados_hoy' => 0,
                    'verificados_monto' => 0.0
                ];
            }
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasPagos: " . $e->getMessage());
            return [
                'pendientes' => 0,
                'monto_pendiente' => 0.0,
                'verificados_hoy' => 0,
                'verificados_monto' => 0.0
            ];
        }
    }
    
    private function obtenerPagosPendientesDetallados() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    p.numped,
                    c.nombre as cliente_nombre,
                    COALESCE(pg.referencia_pago, '') as referencia_pago,
                    COALESCE(pg.observaciones, '') as observaciones,
                    COALESCE(pg.archivo_comprobante, '') as archivo_comprobante
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Pendiente'
                ORDER BY pg.fecha_pago DESC
            ");
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asegurar que todos los campos existan con valores por defecto
            foreach ($resultados as &$pago) {
                $pago['referencia_pago'] = $pago['referencia_pago'] ?? '';
                $pago['observaciones'] = $pago['observaciones'] ?? '';
                $pago['archivo_comprobante'] = $pago['archivo_comprobante'] ?? '';
            }
            
            error_log("obtenerPagosPendientesDetallados: Encontrados " . count($resultados) . " pagos pendientes");
            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerPagosPendientesDetallados: " . $e->getMessage());
            // Intentar una consulta más simple si falla la completa
            try {
                $stmt = $this->db->prepare("
                    SELECT 
                        pg.*,
                        'Sin pedido' as numped,
                        'Cliente desconocido' as cliente_nombre,
                        '' as referencia_pago,
                        '' as observaciones,
                        '' as archivo_comprobante
                    FROM pagos pg
                    WHERE pg.estado_pag = 'Pendiente'
                    ORDER BY pg.fecha_pago DESC
                ");
                $stmt->execute();
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("obtenerPagosPendientesDetallados (consulta simple): Encontrados " . count($resultados) . " pagos pendientes");
                return $resultados;
            } catch (Exception $e2) {
                error_log("Error en consulta simple de pagos pendientes: " . $e2->getMessage());
                return [];
            }
        }
    }
    
    private function obtenerPagosVerificadosRecientes() {
        try {
            // Primero verificar si existe la columna fecha_verificacion
            $stmt = $this->db->prepare("SHOW COLUMNS FROM pagos LIKE 'fecha_verificacion'");
            $stmt->execute();
            $fecha_verificacion_exists = $stmt->fetch();
            
            if ($fecha_verificacion_exists) {
                // Si existe la columna fecha_verificacion, usarla
                $stmt = $this->db->prepare("
                    SELECT 
                        pg.*,
                        p.numped,
                        c.nombre as cliente_nombre,
                        u.nombre_completo as verificado_por_nombre,
                        COALESCE(pg.referencia_pago, '') as referencia_pago,
                        COALESCE(pg.observaciones, '') as observaciones,
                        COALESCE(pg.archivo_comprobante, '') as archivo_comprobante,
                        COALESCE(pg.observaciones_empleado, '') as observaciones_empleado
                    FROM pagos pg
                    INNER JOIN ped p ON pg.ped_idped = p.idped
                    INNER JOIN cli c ON p.cli_idcli = c.idcli
                    LEFT JOIN usu u ON pg.verificado_por = u.idusu
                    WHERE pg.estado_pag IN ('Completado', 'Rechazado')
                    ORDER BY pg.fecha_verificacion DESC
                    LIMIT 10
                ");
            } else {
                // Si no existe, usar fecha_pago como alternativa
                $stmt = $this->db->prepare("
                    SELECT 
                        pg.*,
                        p.numped,
                        c.nombre as cliente_nombre,
                        '' as verificado_por_nombre,
                        COALESCE(pg.referencia_pago, '') as referencia_pago,
                        COALESCE(pg.observaciones, '') as observaciones,
                        COALESCE(pg.archivo_comprobante, '') as archivo_comprobante,
                        COALESCE(pg.observaciones_empleado, '') as observaciones_empleado
                    FROM pagos pg
                    INNER JOIN ped p ON pg.ped_idped = p.idped
                    INNER JOIN cli c ON p.cli_idcli = c.idcli
                    WHERE pg.estado_pag IN ('Completado', 'Rechazado')
                    ORDER BY pg.fecha_pago DESC
                    LIMIT 10
                ");
            }
            
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asegurar que todos los campos existan con valores por defecto
            foreach ($resultados as &$pago) {
                $pago['referencia_pago'] = $pago['referencia_pago'] ?? '';
                $pago['observaciones'] = $pago['observaciones'] ?? '';
                $pago['archivo_comprobante'] = $pago['archivo_comprobante'] ?? '';
                $pago['observaciones_empleado'] = $pago['observaciones_empleado'] ?? '';
                $pago['verificado_por_nombre'] = $pago['verificado_por_nombre'] ?? '';
            }
            
            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerPagosVerificadosRecientes: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerEstadisticasInventario() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT tflor.idtflor) as total_productos,
                    COUNT(CASE WHEN COALESCE(inv.cantidad_disponible, 0) < 5 AND COALESCE(inv.cantidad_disponible, 0) > 0 THEN 1 END) as stock_bajo,
                    COUNT(CASE WHEN COALESCE(inv.cantidad_disponible, 0) = 0 THEN 1 END) as sin_stock,
                    COALESCE(SUM(tflor.precio * COALESCE(inv.cantidad_disponible, 0)), 0) as valor_total
                FROM tflor
                LEFT JOIN inv ON tflor.idtflor = inv.tflor_idtflor
                WHERE tflor.activo = 1
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_productos' => 0,
                'stock_bajo' => 0,
                'sin_stock' => 0,
                'valor_total' => 0
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasInventario: " . $e->getMessage());
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
                $where_conditions[] = "tflor.naturaleza LIKE ?";
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
                // Validar entrada contra inyección SQL
                require_once __DIR__ . '/../helpers/security_helper.php';
                
                $busqueda_limpia = sanitizarCampoBusqueda($_GET['buscar'], 'buscar_inventario');
                
                if ($busqueda_limpia === false) {
                    $_SESSION['error_seguridad'] = "Entrada inválida detectada. Por seguridad, tu búsqueda fue bloqueada.";
                    header('Location: index.php?ctrl=empleado&action=inventario');
                    exit();
                }
                
                $where_conditions[] = "(tflor.nombre LIKE ? OR tflor.naturaleza LIKE ?)";
                $params[] = '%' . $busqueda_limpia . '%';
                $params[] = '%' . $busqueda_limpia . '%';
            }
            
            $where_clause = 'WHERE tflor.activo = 1';
            if (!empty($where_conditions)) {
                $where_clause .= ' AND ' . implode(' AND ', $where_conditions);
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    tflor.idtflor,
                    tflor.nombre,
                    tflor.naturaleza,
                    tflor.precio,
                    tflor.color,
                    COALESCE(inv.cantidad_disponible, 0) as cantidad_disponible,
                    inv.fecha_actualizacion
                FROM tflor
                LEFT JOIN inv ON tflor.idtflor = inv.tflor_idtflor
                $where_clause
                ORDER BY tflor.nombre
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerProductosInventario: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerTodosPedidos() {
        try {
            error_log("=== DEBUG obtenerTodosPedidos ===");
            
            $where_conditions = [];
            $params = [];
            
            // Debug: mostrar parámetros GET
            error_log("Parámetros GET: " . print_r($_GET, true));
            
            // Filtros de búsqueda
            if (isset($_GET['estado_pedido']) && !empty($_GET['estado_pedido'])) {
                $where_conditions[] = "p.estado = ?";
                $params[] = $_GET['estado_pedido'];
                error_log("Filtro estado_pedido: " . $_GET['estado_pedido']);
            }
            
            if (isset($_GET['estado_pago']) && !empty($_GET['estado_pago'])) {
                if ($_GET['estado_pago'] === 'Sin pago') {
                    $where_conditions[] = "pg.estado_pag IS NULL";
                } else {
                    $where_conditions[] = "pg.estado_pag = ?";
                    $params[] = $_GET['estado_pago'];
                }
                error_log("Filtro estado_pago: " . $_GET['estado_pago']);
            }
            
            if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) {
                $where_conditions[] = "DATE(p.fecha_pedido) >= ?";
                $params[] = $_GET['fecha_desde'];
                error_log("Filtro fecha_desde: " . $_GET['fecha_desde']);
            }
            
            if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) {
                $where_conditions[] = "DATE(p.fecha_pedido) <= ?";
                $params[] = $_GET['fecha_hasta'];
                error_log("Filtro fecha_hasta: " . $_GET['fecha_hasta']);
            }
            
            $where_clause = '';
            if (!empty($where_conditions)) {
                $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
            }
            
            // Consulta con LEFT JOIN para incluir información de pagos
            $sql = "
                SELECT 
                    p.idped,
                    p.numped,
                    p.fecha_pedido,
                    p.monto_total,
                    p.estado,
                    c.nombre as cliente_nombre,
                    c.email as cliente_email,
                    COALESCE(pg.estado_pag, 'Sin pago') as estado_pag
                FROM ped p
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN pagos pg ON p.idped = pg.ped_idped
                $where_clause
                ORDER BY p.fecha_pedido DESC
                LIMIT 100
            ";
            
            error_log("=== UPDATED SQL Query with JOIN ===");
            error_log("SQL Query: " . $sql);
            error_log("Params: " . print_r($params, true));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Resultados encontrados: " . count($result));
            if (count($result) > 0) {
                error_log("Primer resultado: " . print_r($result[0], true));
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en obtenerTodosPedidos: " . $e->getMessage());
            return [];
        }
    }
    
    private function actualizarEstadoPago($id_pago, $nuevo_estado, $observaciones = '') {
        try {
            $empleado_id = $_SESSION['user']['idusu'] ?? null;
            $stmt = $this->db->prepare("
                UPDATE pagos 
                SET estado_pag = ?, 
                    observaciones_empleado = ?,
                    verificado_por = ?,
                    fecha_verificacion = NOW()
                WHERE idpago = ?
            ");
            return $stmt->execute([$nuevo_estado, $observaciones, $empleado_id, $id_pago]);
        } catch (Exception $e) {
            error_log("Error en actualizarEstadoPago: " . $e->getMessage());
            return false;
        }  
    }
    
    private function actualizarStockProducto($producto_id, $nuevo_stock, $motivo, $observaciones) {
        try {
            $this->db->beginTransaction();
            
            // Primero verificar si existe registro en inventario
            $stmt = $this->db->prepare("SELECT idinv, cantidad_disponible FROM inv WHERE tflor_idtflor = ?");
            $stmt->execute([$producto_id]);
            $inventario_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($inventario_actual) {
                // Actualizar inventario existente
                $stmt = $this->db->prepare("
                    UPDATE inv 
                    SET cantidad_disponible = ?, 
                        stock = ?,
                        empleado_id = ?,
                        motivo = ?,
                        fecha_actualizacion = NOW()
                    WHERE tflor_idtflor = ?
                ");
                $result = $stmt->execute([$nuevo_stock, $nuevo_stock, $this->empleado_id, $motivo, $producto_id]);
                
                // Registrar en historial si existe la tabla
                if ($result) {
                    try {
                        $stmt_hist = $this->db->prepare("
                            INSERT INTO inv_historial 
                            (inv_idinv, stock_anterior, stock_nuevo, motivo, empleado_id, fecha_movimiento, observaciones) 
                            VALUES (?, ?, ?, ?, ?, NOW(), ?)
                        ");
                        $stmt_hist->execute([
                            $inventario_actual['idinv'], 
                            $inventario_actual['cantidad_disponible'], 
                            $nuevo_stock, 
                            $motivo, 
                            $this->empleado_id, 
                            $observaciones
                        ]);
                    } catch (Exception $e) {
                        // Si falla el historial, continuar (no es crítico)
                        error_log("Error registrando historial: " . $e->getMessage());
                    }
                }
            } else {
                // Obtener información del producto
                $stmt = $this->db->prepare("SELECT precio FROM tflor WHERE idtflor = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto) {
                    throw new Exception("Producto no encontrado");
                }
                
                // Crear nuevo registro de inventario
                $stmt = $this->db->prepare("
                    INSERT INTO inv (tflor_idtflor, cantidad_disponible, stock, precio, empleado_id, motivo, fecha_actualizacion) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $result = $stmt->execute([$producto_id, $nuevo_stock, $nuevo_stock, $producto['precio'], $this->empleado_id, $motivo]);
            }
            
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en actualizarStockProducto: " . $e->getMessage());
            return false;
        }
    }
    
    private function actualizarEstadoPedido($id_pedido, $nuevo_estado) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar estado en tabla ped
            $stmt = $this->db->prepare("
                UPDATE ped 
                SET estado = ?, 
                    fecha_actualizacion = NOW(),
                    empleado_actualizacion = ?
                WHERE idped = ?
            ");
            $stmt->execute([$nuevo_estado, $this->empleado_id, $id_pedido]);
            
            // 🔑 Si el estado cambia a "En proceso", descontar del inventario
            if ($nuevo_estado === 'En proceso') {
                $alertas = $this->descontarInventarioPorPedido($id_pedido);
                
                // Guardar alertas en sesión para mostrar al usuario
                if (!empty($alertas)) {
                    $_SESSION['alertas_inventario'] = $alertas;
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error actualizando estado pedido: " . $e->getMessage());
            $_SESSION['error'] = "Error al actualizar estado: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Descuenta automáticamente las flores del inventario cuando se confirma un pedido
     * OPCIÓN B: Descuenta lo disponible + alerta de faltante
     * 
     * @param int $id_pedido ID del pedido
     * @return array Lista de alertas generadas
     */
    private function descontarInventarioPorPedido($id_pedido) {
        $alertas = [];
        
        try {
            // Validar que el pedido exista
            $stmt_ped = $this->db->prepare("SELECT idped FROM ped WHERE idped = ?");
            $stmt_ped->execute([$id_pedido]);
            if (!$stmt_ped->fetch()) {
                throw new Exception("Pedido #$id_pedido no encontrado");
            }
            
            // Obtener todas las flores del pedido
            $stmt = $this->db->prepare("
                SELECT 
                    dp.iddetped,
                    dp.idtflor,
                    dp.cantidad,
                    i.idinv,
                    i.stock,
                    tf.nombre as nombre_flor
                FROM detped dp
                JOIN tflor tf ON dp.idtflor = tf.idtflor
                LEFT JOIN inv i ON dp.idtflor = i.tflor_idtflor
                WHERE dp.idped = ?
            ");
            $stmt->execute([$id_pedido]);
            $flores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($flores)) {
                error_log("Pedido $id_pedido no tiene detalles de flores");
                return $alertas;
            }
            
            // Procesar cada flor
            foreach ($flores as $flor) {
                $alerta = $this->verificarYDescontarFlor(
                    $flor['idtflor'],
                    $flor['idinv'] ?? null,
                    $flor['cantidad'],
                    $flor['stock'] ?? 0,
                    $flor['nombre_flor'],
                    $id_pedido
                );
                
                if ($alerta) {
                    $alertas[] = $alerta;
                }
            }
            
        } catch (Exception $e) {
            error_log("Error en descontarInventarioPorPedido: " . $e->getMessage());
            $alertas[] = [
                'tipo' => 'error',
                'flor' => 'Sistema',
                'mensaje' => 'Error al procesar descuentos: ' . $e->getMessage()
            ];
        }
        
        return $alertas;
    }

    /**
     * Verifica stock y descuenta una flor individual
     * VALIDACIONES:
     * - No descuenta 2 veces (verifica inv_historial)
     * - Descuento parcial si stock insuficiente
     * - Genera alerta si hay problemas
     * 
     * @param int $idtflor ID del tipo de flor
     * @param int|null $idinv ID del inventario (puede ser null si no existe)
     * @param int $cantidad_solicitada Cantidad requerida en el pedido
     * @param int $stock_actual Stock disponible
     * @param string $nombre_flor Nombre de la flor (para alertas)
     * @param int $id_pedido ID del pedido (para auditoría)
     * @return array|null Alerta si hay, null si sin problemas
     */
    private function verificarYDescontarFlor($idtflor, $idinv, $cantidad_solicitada, $stock_actual, $nombre_flor, $id_pedido) {
        try {
            // VALIDACIÓN 1: Si no existe registro en inv, crear uno
            if ($idinv === null) {
                $stmt_tflor = $this->db->prepare("SELECT precio FROM tflor WHERE idtflor = ?");
                $stmt_tflor->execute([$idtflor]);
                $tflor = $stmt_tflor->fetch(PDO::FETCH_ASSOC);
                
                if (!$tflor) {
                    throw new Exception("Flor #$idtflor no existe");
                }
                
                // Crear registro en inv
                $stmt_ins = $this->db->prepare("
                    INSERT INTO inv (tflor_idtflor, stock, precio, fecha_actualizacion)
                    VALUES (?, 0, ?, NOW())
                ");
                $stmt_ins->execute([$idtflor, $tflor['precio']]);
                
                $idinv = $this->db->lastInsertId();
                $stock_actual = 0;
            }
            
            // VALIDACIÓN 2: Evitar doble descuento
            // Verificar si ya existe un registro en inv_historial para este pedido y flor
            $stmt_check = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM inv_historial 
                WHERE idinv = ? 
                AND motivo LIKE CONCAT('Descuento por pedido #', ?)
            ");
            $stmt_check->execute([$idinv, $id_pedido]);
            $resultado = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                error_log("Descuento duplicado detectado: pedido $id_pedido, flor $idtflor");
                return null; // Ya fue descontado, no hacer nada
            }
            
            // VALIDACIÓN 3: Calcular cantidad a descontar
            $cantidad_a_descontar = min($cantidad_solicitada, $stock_actual);
            $nuevo_stock = $stock_actual - $cantidad_a_descontar;
            
            // ACTUALIZAR INVENTARIO
            $stmt_upd = $this->db->prepare("
                UPDATE inv 
                SET stock = ?, 
                    fecha_actualizacion = NOW()
                WHERE idinv = ?
            ");
            $stmt_upd->execute([$nuevo_stock, $idinv]);
            
            // REGISTRAR EN HISTORIAL (campos correctos según BD)
            $motivo = "Descuento por pedido #$id_pedido";
            $stmt_hist = $this->db->prepare("
                INSERT INTO inv_historial (idinv, stock_anterior, stock_nuevo, idusu, motivo, fecha_cambio)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt_hist->execute([
                $idinv,
                $stock_actual,
                $nuevo_stock,
                $this->empleado_id,
                $motivo
            ]);
            
            // GENERAR ALERTAS (Opción B)
            $alerta = null;
            
            // Alerta: Stock insuficiente
            if ($cantidad_solicitada > $stock_actual) {
                $faltante = $cantidad_solicitada - $stock_actual;
                $alerta = [
                    'tipo' => 'advertencia',
                    'flor' => $nombre_flor,
                    'mensaje' => "⚠️ Stock Insuficiente: Solicitó $cantidad_solicitada " . 
                                 ($nombre_flor === 'Rosas' ? 'Rosas' : $nombre_flor) . 
                                 " pero solo había $stock_actual. Se descubrió $cantidad_a_descontar. Faltan $faltante."
                ];
            }
            // Alerta: Stock agotado
            elseif ($nuevo_stock == 0) {
                $alerta = [
                    'tipo' => 'crítica',
                    'flor' => $nombre_flor,
                    'mensaje' => "🔴 AGOTADO: $nombre_flor está SIN STOCK. Se requiere reorden urgente."
                ];
            }
            // Alerta: Stock bajo
            elseif ($nuevo_stock > 0 && $nuevo_stock < 5) {
                $alerta = [
                    'tipo' => 'baja',
                    'flor' => $nombre_flor,
                    'mensaje' => "⚡ Stock Bajo: Solo quedan $nuevo_stock de $nombre_flor."
                ];
            }
            
            return $alerta;
            
        } catch (Exception $e) {
            error_log("Error en verificarYDescontarFlor (flor: $idtflor): " . $e->getMessage());
            return [
                'tipo' => 'error',
                'flor' => $nombre_flor,
                'mensaje' => 'Error al descontar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crea un nuevo pedido desde el panel de empleado
     * Flujo similar al cliente pero registra al empleado como quien lo creó
     */
    public function crearPedidoEmpleado() {
        try {
            // Validar datos POST
            if (!isset($_POST['cli_id']) || !isset($_POST['flores'])) {
                throw new Exception("Datos incompletos");
            }

            $cli_id = intval($_POST['cli_id']);
            $flores = $_POST['flores']; // Array de [idtflor => cantidad]
            $direccion_entrega = $_POST['direccion_entrega'] ?? null;
            $fecha_entrega = $_POST['fecha_entrega'] ?? null;
            $notas = $_POST['notas'] ?? null;
            $monto_total = floatval($_POST['monto_total'] ?? 0);

            // Crear pedido base
            require_once 'models/data.php';
            $modelData = new data();
            
            // Generar número de pedido
            $numped = 'PED-' . date('YmdHis') . '-' . $cli_id;
            
            // INSERT en ped tabla
            $stmt = $this->db->prepare("
                INSERT INTO ped (numped, fecha_pedido, monto_total, estado, cli_idcli, direccion_entrega, fecha_entrega_solicitada, empleado_id, notas)
                VALUES (?, NOW(), ?, 'Pendiente', ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $numped,
                $monto_total,
                $cli_id,
                $direccion_entrega,
                $fecha_entrega,
                $this->empleado_id,
                $notas
            ]);

            $pedido_id = $this->db->lastInsertId();

            // Agregar detalles (flores) al pedido
            $stmt_det = $this->db->prepare("
                INSERT INTO detped (idped, idtflor, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($flores as $idtflor => $data) {
                $cantidad = intval($data['cantidad']);
                $precio = floatval($data['precio']);
                
                if ($cantidad > 0) {
                    $stmt_det->execute([
                        $pedido_id,
                        $idtflor,
                        $cantidad,
                        $precio
                    ]);
                }
            }

            $_SESSION['mensaje'] = "Pedido #$pedido_id creado exitosamente";
            $_SESSION['tipo_mensaje'] = 'success';
            
            header('Location: index.php?ctrl=empleado&action=gestion_pedidos');
            exit();

        } catch (Exception $e) {
            error_log("Error en crearPedidoEmpleado: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al crear pedido: " . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: index.php?ctrl=empleado&action=gestion_pedidos');
            exit();
        }
    }

    /**
     * Sirve el formulario de nuevo pedido como modal/fragmento
     */
    public function nuevoPedidoForm() {
        // Similar a ajax_nuevo_pedido.php pero para empleados
        try {
            // Obtener clientes disponibles
            $stmt = $this->db->prepare("
                SELECT idcli, nombre, email, telefono, direccion 
                FROM cli 
                ORDER BY nombre ASC
            ");
            $stmt->execute();
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener flores disponibles
            $stmt = $this->db->prepare("
                SELECT 
                    tf.idtflor,
                    tf.nombre,
                    tf.naturaleza as color,
                    tf.descripcion,
                    tf.precio,
                    COALESCE(i.stock, 0) as stock
                FROM tflor tf
                LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
                ORDER BY tf.nombre
            ");
            $stmt->execute();
            $flores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            include 'views/empleado/nuevo_pedido.php';
        } catch (Exception $e) {
            error_log("Error en nuevoPedidoForm: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
