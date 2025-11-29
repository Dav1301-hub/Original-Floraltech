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
            $stmt = $this->db->prepare("
                UPDATE ped 
                SET estado = ?, 
                    fecha_actualizacion = NOW(),
                    empleado_actualizacion = ?
                WHERE idped = ?
            ");
            return $stmt->execute([$nuevo_estado, $this->empleado_id, $id_pedido]);
        } catch (Exception $e) {
            error_log("Error actualizando estado pedido: " . $e->getMessage());
            return false;
        }
    }
}
?>
