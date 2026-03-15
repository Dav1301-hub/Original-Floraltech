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
        
        // Manejar filtros de periodo
        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : null;
        $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;
        
        if (isset($_GET['periodo']) && !empty($_GET['periodo'])) {
            $parts = explode('-', $_GET['periodo']);
            if (count($parts) === 2) {
                $mes = (int)$parts[0];
                $ano = (int)$parts[1];
            }
        }

        // Obtener periodos disponibles para el filtro
        require_once 'models/MDashboardGeneral.php';
        $modeloGeneral = new MDashboardGeneral($this->db);
        $periodos = $modeloGeneral->getPeriodosDisponibles();
        $filtro = ['mes' => $mes, 'ano' => $ano];

        // Obtener estadísticas del empleado con filtros
        $stats = $this->obtenerEstadisticas($mes, $ano);
        $pedidos_pendientes = $this->obtenerPedidosPendientes();
        $pagos_pendientes = $this->obtenerPagosPendientes();
        $stock_critico = $this->obtenerStockCritico();
        
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
            
            $filtro_completados = [
                'estado'      => isset($_GET['filtro_estado']) && in_array($_GET['filtro_estado'], ['Completado', 'Rechazado'], true) ? $_GET['filtro_estado'] : '',
                'fecha_desde' => isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '',
                'fecha_hasta' => isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : ''
            ];
            $pagos_verificados = $this->obtenerPagosVerificadosRecientes($filtro_completados);
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
        require_once __DIR__ . '/../models/minventario.php';
        $invModel = new Minventario();
        
        $stats = $invModel->getEstadisticasInventario();
        $categorias = $invModel->listarTipos();
        
        $por_pagina = 10;
        $pagina_p = max(1, (int)($_GET['pagina_perecederos'] ?? 1));
        $pagina_np = max(1, (int)($_GET['pagina_no_perecederos'] ?? 1));
        $pagina_prov = max(1, (int)($_GET['pagina_proveedores'] ?? 1));
        
        $filtros_base = [];
        $buscar = isset($_GET['buscar']) ? trim((string)$_GET['buscar']) : '';
        if ($buscar !== '') {
            require_once __DIR__ . '/../helpers/security_helper.php';
            $buscar_ok = sanitizarCampoBusqueda($buscar, 'buscar_inventario');
            if ($buscar_ok !== false && $buscar_ok !== '') {
                $filtros_base['buscar'] = $buscar_ok;
            }
        }
        if (!empty($_GET['categoria'])) {
            $filtros_base['categoria'] = $_GET['categoria'];
        }
        if (!empty($_GET['estado_stock']) && in_array($_GET['estado_stock'], ['normal', 'bajo', 'critico', 'sin_stock'], true)) {
            $filtros_base['estado_stock'] = $_GET['estado_stock'];
        }
        
        $filtros_p = array_merge($filtros_base, ['tipo_producto' => 'perecedero']);
        $filtros_np = array_merge($filtros_base, ['tipo_producto' => 'no_perecedero']);
        
        $offset_p = ($pagina_p - 1) * $por_pagina;
        $offset_np = ($pagina_np - 1) * $por_pagina;
        $offset_prov = ($pagina_prov - 1) * $por_pagina;
        
        $inventario_perecederos = $invModel->getInventarioPaginado($por_pagina, $offset_p, $filtros_p);
        $total_perecederos = $invModel->getTotalElementos($filtros_p);
        $total_paginas_perecederos = max(1, (int)ceil($total_perecederos / $por_pagina));
        
        $inventario_no_perecederos = $invModel->getInventarioPaginado($por_pagina, $offset_np, $filtros_np);
        $total_no_perecederos = $invModel->getTotalElementos($filtros_np);
        $total_paginas_no_perecederos = max(1, (int)ceil($total_no_perecederos / $por_pagina));
        
        $proveedores = $invModel->getProveedoresConProductos($por_pagina, $offset_prov);
        $total_proveedores = $invModel->getTotalProveedores();
        $total_paginas_proveedores = max(1, (int)ceil($total_proveedores / $por_pagina));
        
        $total_elementos_perecederos = $total_perecederos;
        $total_elementos_no_perecederos = $total_no_perecederos;
        $total_elementos_proveedores = $total_proveedores;
        $pagina_actual_perecederos = $pagina_p;
        $pagina_actual_no_perecederos = $pagina_np;
        $pagina_actual_proveedores = $pagina_prov;
        $offset_perecederos = $offset_p;
        $offset_no_perecederos = $offset_np;
        $offset_proveedores = $offset_prov;
        $elementos_por_pagina = $por_pagina;
        $parametros_inventario = ['stock_minimo' => 20, 'dias_vencimiento' => 7];
        $todos_proveedores = $invModel->getProveedoresConProductos(500, 0);
        
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
    
    /**
     * Obtener un producto del inventario por ID (JSON para modal editar empleado).
     */
    public function obtener_producto_inventario() {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
            return;
        }
        require_once __DIR__ . '/../models/minventario.php';
        $invModel = new Minventario();
        $producto = $invModel->obtenerProductoPorId($id);
        if ($producto) {
            echo json_encode(['success' => true, 'producto' => $producto]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        }
    }
    
    /**
     * Editar producto del inventario (solo actualizar; empleado no puede crear ni eliminar).
     */
    public function editar_producto_inventario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        header('Content-Type: application/json; charset=utf-8');
        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
        if (!$producto_id) {
            echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
            return;
        }
        require_once __DIR__ . '/../models/minventario.php';
        $invModel = new Minventario();
        $data = [
            'producto_id'   => $producto_id,
            'nombre_producto' => $_POST['nombre_producto'] ?? '',
            'naturaleza'    => $_POST['naturaleza'] ?? '',
            'color'         => $_POST['color'] ?? '',
            'stock'         => isset($_POST['stock']) ? (int)$_POST['stock'] : 0,
            'precio'        => isset($_POST['precio']) ? (float)$_POST['precio'] : 0,
            'precio_compra' => isset($_POST['precio_compra']) ? (float)$_POST['precio_compra'] : 0,
            'tipo_producto' => $_POST['tipo_producto'] ?? 'otro',
            'estado'        => $_POST['estado'] ?? 'activo',
        ];
        $resultado = $invModel->editarProducto($data);
        if ($resultado['success']) {
            echo json_encode(['success' => true, 'message' => $resultado['message']]);
        } else {
            echo json_encode(['success' => false, 'message' => $resultado['message']]);
        }
    }
    
    /**
     * Agregar stock a un producto (no perecederos). Respuesta JSON.
     */
    public function agregar_stock() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
        $motivo = isset($_POST['motivo']) ? trim((string)$_POST['motivo']) : '';
        if (!$id || $cantidad <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        require_once __DIR__ . '/../models/minventario.php';
        $invModel = new Minventario();
        $resultado = $invModel->agregarStock($id, $cantidad, $motivo);
        if ($resultado['success']) {
            echo json_encode(['success' => true, 'message' => $resultado['message'] ?? 'Stock agregado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => $resultado['message'] ?? 'Error al agregar stock']);
        }
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

    /**
     * Genera y descarga la factura PDF de un pedido (igual que en cliente).
     */
    public function generar_factura() {
        $idPedido = $_GET['idpedido'] ?? 0;
        try {
            if (empty($idPedido)) {
                throw new Exception("No se ha especificado un número de pedido");
            }
            $pedido = $this->obtenerDetallesPedidoParaFactura($idPedido);
            if (!$pedido) {
                throw new Exception("El pedido no existe");
            }
            $pago = $this->obtenerPagoPorPedido($idPedido);
            $detalles = $this->obtenerDetallesItemsPedidoFactura($idPedido);

            if (!function_exists('cliente_cargarFacturaPDF')) {
                require_once __DIR__ . '/../controllers/cliente.php';
            }
            cliente_cargarFacturaPDF();
            $pdf = new FacturaPDF();
            $pdf->AliasNbPages();
            $pdf->SetMargins(10, 30, 10);
            $pdf->SetAutoPageBreak(true, 25);
            $pdf->AddPage();

            $colorSecundario = array(220, 230, 241);
            $colorTexto = array(50, 50, 50);
            $pdf->SetTextColor($colorTexto[0], $colorTexto[1], $colorTexto[2]);

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'FACTURA #' . $pedido['numped'], 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])), 0, 1);
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(95, 7, 'DATOS DEL CLIENTE', 0, 0);
            $pdf->Cell(95, 7, 'INFORMACION DE PAGO', 0, 1);
            $pdf->SetFont('Arial', '', 10);

            $nombreCliente = $pedido['nombre_cliente'] ?? 'Cliente';
            $emailCliente = $pedido['email'] ?? '';
            $naturalezaCliente = $pedido['naturaleza'] ?? '';

            $pdf->Cell(95, 6, $nombreCliente, 0, 0);
            $pdf->Cell(95, 6, 'Metodo: ' . ($pago ? $pago['metodo_pago'] : 'No registrado'), 0, 1);
            $pdf->Cell(95, 6, $emailCliente, 0, 0);
            $pdf->Cell(95, 6, 'Estado: ' . ($pago ? $pago['estado_pag'] : 'No registrado'), 0, 1);
            $pdf->Cell(95, 6, $naturalezaCliente, 0, 0);
            $pdf->Cell(95, 6, 'Fecha pago: ' . ($pago ? date('d/m/Y', strtotime($pago['fecha_pago'])) : 'N/A'), 0, 1);
            $pdf->Ln(10);

            $pdf->SetFillColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(100, 8, 'DESCRIPCION', 1, 0, 'L', true);
            $pdf->Cell(30, 8, 'CANTIDAD', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'PRECIO UNIT.', 1, 0, 'R', true);
            $pdf->Cell(30, 8, 'SUBTOTAL', 1, 1, 'R', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetFillColor(255, 255, 255);

            foreach ($detalles as $item) {
                if ($pdf->GetY() > 240) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 11);
                    $pdf->Cell(100, 8, 'DESCRIPCION', 'LRB', 0, 'L', true);
                    $pdf->Cell(30, 8, 'CANTIDAD', 'LRB', 0, 'C', true);
                    $pdf->Cell(30, 8, 'PRECIO UNIT.', 'LRB', 0, 'R', true);
                    $pdf->Cell(30, 8, 'SUBTOTAL', 'LRB', 1, 'R', true);
                    $pdf->SetFont('Arial', '', 10);
                }
                $pdf->Cell(100, 7, $item['nombre'], 'LR', 0, 'L');
                $pdf->Cell(30, 7, $item['cantidad'], 'LR', 0, 'C');
                $pdf->Cell(30, 7, '$' . number_format($item['precio_unitario'], 2), 'LR', 0, 'R');
                $pdf->Cell(30, 7, '$' . number_format($item['subtotal'], 2), 'LR', 1, 'R');
            }

            $pdf->Cell(190, 0, '', 'T');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(160, 8, 'TOTAL:', 0, 0, 'R');
            $pdf->Cell(30, 8, '$' . number_format($pedido['monto_total'], 2), 0, 1, 'R');
            $pdf->SetY(-33);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->MultiCell(0, 4, "Términos y condiciones: El pago debe realizarse dentro de los 5 días hábiles.\nCualquier retraso puede incurrir en intereses moratorios.", 0, 'C');

            $pdf->Output('D', 'factura_' . $pedido['numped'] . '.pdf');
            exit();
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al generar factura: " . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: index.php?ctrl=empleado&action=gestion_pedidos');
            exit();
        }
    }

    private function obtenerDetallesPedidoParaFactura($idPedido) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre as nombre_cliente, c.email, COALESCE(c.direccion, '') as naturaleza
            FROM ped p
            JOIN cli c ON p.cli_idcli = c.idcli
            WHERE p.idped = ?
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function obtenerPagoPorPedido($idPedido) {
        $stmt = $this->db->prepare("
            SELECT * FROM pagos WHERE ped_idped = ? ORDER BY idpago DESC LIMIT 1
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function obtenerDetallesItemsPedidoFactura($idPedido) {
        $stmt = $this->db->prepare("
            SELECT dp.*, tf.nombre FROM detped dp
            JOIN tflor tf ON dp.idtflor = tf.idtflor
            WHERE dp.idped = ?
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Genera el PDF de la factura en memoria (para adjuntar al correo).
     */
    private function generarFacturaPdfEnMemoria($pedido, $pago, $detalles) {
        try {
            if (!function_exists('cliente_cargarFacturaPDF')) {
                require_once __DIR__ . '/../controllers/cliente.php';
            }
            cliente_cargarFacturaPDF();
            $pdf = new FacturaPDF();
            $pdf->AliasNbPages();
            $pdf->SetMargins(10, 30, 10);
            $pdf->SetAutoPageBreak(true, 25);
            $pdf->AddPage();
            $colorSecundario = array(220, 230, 241);
            $colorTexto = array(50, 50, 50);
            $pdf->SetTextColor($colorTexto[0], $colorTexto[1], $colorTexto[2]);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'FACTURA #' . $pedido['numped'], 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])), 0, 1);
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(95, 7, 'DATOS DEL CLIENTE', 0, 0);
            $pdf->Cell(95, 7, 'INFORMACION DE PAGO', 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $nombreCliente = $pedido['nombre_cliente'] ?? 'Cliente';
            $emailCliente = $pedido['email'] ?? '';
            $naturalezaCliente = $pedido['naturaleza'] ?? '';
            $pdf->Cell(95, 6, $nombreCliente, 0, 0);
            $pdf->Cell(95, 6, 'Metodo: ' . ($pago ? $pago['metodo_pago'] : 'No registrado'), 0, 1);
            $pdf->Cell(95, 6, $emailCliente, 0, 0);
            $pdf->Cell(95, 6, 'Estado: ' . ($pago ? $pago['estado_pag'] : 'No registrado'), 0, 1);
            $pdf->Cell(95, 6, $naturalezaCliente, 0, 0);
            $pdf->Cell(95, 6, 'Fecha pago: ' . ($pago ? date('d/m/Y', strtotime($pago['fecha_pago'])) : 'N/A'), 0, 1);
            $pdf->Ln(10);
            $pdf->SetFillColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(100, 8, 'DESCRIPCION', 1, 0, 'L', true);
            $pdf->Cell(30, 8, 'CANTIDAD', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'PRECIO UNIT.', 1, 0, 'R', true);
            $pdf->Cell(30, 8, 'SUBTOTAL', 1, 1, 'R', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetFillColor(255, 255, 255);
            foreach ($detalles as $item) {
                if ($pdf->GetY() > 240) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 11);
                    $pdf->Cell(100, 8, 'DESCRIPCION', 'LRB', 0, 'L', true);
                    $pdf->Cell(30, 8, 'CANTIDAD', 'LRB', 0, 'C', true);
                    $pdf->Cell(30, 8, 'PRECIO UNIT.', 'LRB', 0, 'R', true);
                    $pdf->Cell(30, 8, 'SUBTOTAL', 'LRB', 1, 'R', true);
                    $pdf->SetFont('Arial', '', 10);
                }
                $pdf->Cell(100, 7, $item['nombre'], 'LR', 0, 'L');
                $pdf->Cell(30, 7, $item['cantidad'], 'LR', 0, 'C');
                $pdf->Cell(30, 7, '$' . number_format($item['precio_unitario'], 2), 'LR', 0, 'R');
                $pdf->Cell(30, 7, '$' . number_format($item['subtotal'], 2), 'LR', 1, 'R');
            }
            $pdf->Cell(190, 0, '', 'T');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(160, 8, 'TOTAL:', 0, 0, 'R');
            $pdf->Cell(30, 8, '$' . number_format($pedido['monto_total'], 2), 0, 1, 'R');
            $pdf->SetY(-33);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->MultiCell(0, 4, "Términos y condiciones: El pago debe realizarse dentro de los 5 días hábiles.\nCualquier retraso puede incurrir en intereses moratorios.", 0, 'C');
            return $pdf->Output('S');
        } catch (Exception $e) {
            error_log("Error generarFacturaPdfEnMemoria empleado: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Envía la factura por correo al cliente (misma config que cliente).
     */
    private function enviarFacturaPorEmailEmpleado($email_destino, $pedido, $pdf_content) {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
        if (file_exists(__DIR__ . '/../config/email_config.php')) {
            require_once __DIR__ . '/../config/email_config.php';
        }
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("Empleado: PHPMailer no disponible, no se envía factura por correo.");
            return false;
        }
        $host = defined('MAIL_HOST') ? MAIL_HOST : 'smtp.gmail.com';
        $port = defined('MAIL_PORT') ? (int) MAIL_PORT : 587;
        $user = defined('MAIL_USERNAME') ? MAIL_USERNAME : 'epymes270@gmail.com';
        $pass = defined('MAIL_PASSWORD') ? MAIL_PASSWORD : '';
        $from = defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : $user;
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'FloralTech';
        $enc = defined('MAIL_ENCRYPTION') ? strtolower(MAIL_ENCRYPTION) : 'tls';
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $pass;
            $mail->SMTPSecure = ($enc === 'ssl') ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $port;
            $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
            $mail->setFrom($from, $fromName);
            $mail->addAddress($email_destino, $pedido['nombre_cliente'] ?? 'Cliente');
            $mail->addReplyTo($from, $fromName);
            $mail->Subject = 'Factura #' . $pedido['numped'] . ' - FloralTech';
            $mail->isHTML(true);
            $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">'
                . '<h2 style="color: #4CAF50;">Factura #' . htmlspecialchars($pedido['numped']) . '</h2>'
                . '<p>Estimado/a ' . htmlspecialchars($pedido['nombre_cliente'] ?? 'Cliente') . ',</p>'
                . '<p>Adjunto encontrará la factura del pedido <strong>#' . htmlspecialchars($pedido['numped']) . '</strong>.</p>'
                . '<div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0;">'
                . '<p><strong>Resumen del pedido:</strong></p>'
                . '<p>📅 Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</p>'
                . '<p>💰 Total: <strong>$' . number_format($pedido['monto_total'], 2) . '</strong></p>'
                . '<p>📦 Estado: ' . htmlspecialchars($pedido['estado'] ?? '') . '</p></div>'
                . '<p>El archivo PDF adjunto contiene la factura completa con todos los detalles.</p>'
                . '<p>Gracias por su compra,<br><strong>El equipo de FloralTech</strong></p>'
                . '<hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">'
                . '<p style="color: #666; font-size: 12px; text-align: center;">© ' . date('Y') . ' FloralTech</p></div>';
            $mail->AltBody = 'Factura #' . $pedido['numped'] . ' - FloralTech' . PHP_EOL . PHP_EOL
                . 'Estimado/a ' . ($pedido['nombre_cliente'] ?? 'Cliente') . ',' . PHP_EOL . PHP_EOL
                . 'Adjunto encontrará la factura del pedido #' . $pedido['numped'] . '.' . PHP_EOL . PHP_EOL
                . 'Gracias por su compra, El equipo de FloralTech';
            $mail->addStringAttachment($pdf_content, 'Factura_' . $pedido['numped'] . '.pdf');
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;
            return $mail->send();
        } catch (Exception $e) {
            error_log("Error enviarFacturaPorEmailEmpleado: " . $e->getMessage());
            return false;
        }
    }

    // Métodos privados para obtener datos
    private function obtenerEstadisticas($mes = null, $ano = null) {
        try {
            $stats = [];
            
            // Si no se especifica mes/año, usar el mes actual
            $where_date = "";
            $params = [];
            
            if ($mes && $ano) {
                $where_month_year = "WHERE MONTH(fecha_pedido) = ? AND YEAR(fecha_pedido) = ?";
                $params = [$mes, $ano];
                
                // Pedidos del periodo
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total 
                    FROM ped 
                    $where_month_year
                ");
                $stmt->execute($params);
            } else {
                $where_month_year = "WHERE MONTH(fecha_pedido) = MONTH(CURDATE()) AND YEAR(fecha_pedido) = YEAR(CURDATE())";
                
                // Pedidos de hoy
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total 
                    FROM ped 
                    WHERE DATE(fecha_pedido) = CURDATE()
                ");
                $stmt->execute();
            }
            $stats['pedidos_hoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pedidos pendientes (Globales, no filtrados por fecha usualmente)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM ped 
                WHERE estado IN ('Pendiente', 'En proceso')
            ");
            $stmt->execute();
            $stats['pedidos_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pagos pendientes (Globales)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM pagos 
                WHERE estado_pag = 'Pendiente'
            ");
            $stmt->execute();
            $stats['pagos_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Ventas del mes (ESTO SÍ SE FILTRA TOTALMENTE)
            $sql_ventas = "
                SELECT COALESCE(SUM(monto_total), 0) as total 
                FROM ped 
                $where_month_year
                AND estado = 'Completado'
            ";
            $stmt = $this->db->prepare($sql_ventas);
            $stmt->execute($params);
            $stats['ventas_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
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

    /**
     * Stock crítico: mismo criterio que inventario admin (0-9 unidades).
     * Solo productos que existen en inv, ordenados por stock ascendente.
     */
    private function obtenerStockCritico() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    tflor.nombre,
                    tflor.naturaleza,
                    COALESCE(inv.stock, 0) as stock
                FROM inv
                INNER JOIN tflor ON tflor.idtflor = inv.tflor_idtflor
                WHERE tflor.activo = 1 
                AND COALESCE(inv.stock, 0) < 10
                ORDER BY COALESCE(inv.stock, 0) ASC
                LIMIT 15
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Excluir productos sin nombre o con nombre vacío (evitar datos de prueba)
            return array_values(array_filter($rows, function ($item) {
                $nombre = trim($item['nombre'] ?? '');
                return $nombre !== '';
            }));
        } catch (Exception $e) {
            error_log('Error en obtenerStockCritico: ' . $e->getMessage());
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
            // Usar solo columnas que existen en la tabla pagos (transaccion_id, comprobante_transferencia)
            // para evitar fallo y que no se use la consulta de respaldo con "Cliente desconocido"
            $stmt = $this->db->prepare("
                SELECT 
                    pg.idpago,
                    pg.ped_idped,
                    pg.monto,
                    pg.metodo_pago,
                    pg.estado_pag,
                    pg.fecha_pago,
                    pg.transaccion_id,
                    pg.comprobante_transferencia,
                    pg.verificado_por,
                    pg.fecha_verificacion,
                    p.numped,
                    COALESCE(c.nombre, 'Sin nombre') as cliente_nombre,
                    COALESCE(pg.transaccion_id, '') as referencia,
                    COALESCE(pg.comprobante_transferencia, '') as comprobante,
                    (pg.comprobante_imagen IS NOT NULL) as tiene_comprobante_bd
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Pendiente'
                ORDER BY pg.fecha_pago DESC
            ");
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $dirLegacy = __DIR__ . '/../assets/comprobantes';
            foreach ($resultados as &$pago) {
                $pago['referencia'] = $pago['referencia'] ?? '';
                $pago['observaciones'] = '';
                $pago['comprobante'] = $pago['comprobante'] ?? '';
                $tieneBd = !empty($pago['tiene_comprobante_bd']);
                $legacy = trim((string)($pago['comprobante'] ?? ''));
                $legacyExiste = $legacy !== '' && is_dir($dirLegacy) && file_exists($dirLegacy . '/' . $legacy);
                $pago['tiene_comprobante'] = $tieneBd || $legacyExiste;
            }
            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerPagosPendientesDetallados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Pagos verificados (Completado/Rechazado) con filtro opcional por estado y rango de fechas.
     * @param array $filtros ['estado' => 'Completado'|'Rechazado'|'', 'fecha_desde' => 'Y-m-d', 'fecha_hasta' => 'Y-m-d']
     */
    private function obtenerPagosVerificadosRecientes(array $filtros = []) {
        try {
            $where = ["pg.estado_pag IN ('Completado', 'Rechazado')"];
            $params = [];
            if (!empty($filtros['estado'])) {
                $where[] = "pg.estado_pag = ?";
                $params[] = $filtros['estado'];
            }
            if (!empty($filtros['fecha_desde'])) {
                $where[] = "DATE(COALESCE(pg.fecha_verificacion, pg.fecha_pago)) >= ?";
                $params[] = $filtros['fecha_desde'];
            }
            if (!empty($filtros['fecha_hasta'])) {
                $where[] = "DATE(COALESCE(pg.fecha_verificacion, pg.fecha_pago)) <= ?";
                $params[] = $filtros['fecha_hasta'];
            }
            $whereSQL = implode(' AND ', $where);
            $limit = (!empty($filtros['estado']) || !empty($filtros['fecha_desde']) || !empty($filtros['fecha_hasta'])) ? 50 : 10;
            $sql = "
                SELECT 
                    pg.idpago,
                    pg.ped_idped,
                    pg.monto,
                    pg.metodo_pago,
                    pg.estado_pag,
                    pg.fecha_pago,
                    pg.transaccion_id,
                    pg.comprobante_transferencia,
                    pg.verificado_por,
                    pg.fecha_verificacion,
                    p.numped,
                    COALESCE(c.nombre, 'Sin nombre') as cliente_nombre,
                    u.nombre_completo as verificado_por_nombre,
                    COALESCE(pg.transaccion_id, '') as referencia,
                    COALESCE(pg.comprobante_transferencia, '') as comprobante,
                    (pg.comprobante_imagen IS NOT NULL) as tiene_comprobante_bd
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN usu u ON pg.verificado_por = u.idusu
                WHERE $whereSQL
                ORDER BY COALESCE(pg.fecha_verificacion, pg.fecha_pago) DESC
                LIMIT $limit
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $dirLegacy = __DIR__ . '/../assets/comprobantes';
            foreach ($resultados as &$pago) {
                $pago['referencia'] = $pago['referencia'] ?? '';
                $pago['comprobante'] = $pago['comprobante'] ?? '';
                $pago['verificado_por_nombre'] = $pago['verificado_por_nombre'] ?? '';
                $tieneBd = !empty($pago['tiene_comprobante_bd']);
                $legacy = trim((string)($pago['comprobante'] ?? ''));
                $legacyExiste = $legacy !== '' && is_dir($dirLegacy) && file_exists($dirLegacy . '/' . $legacy);
                $pago['tiene_comprobante'] = $tieneBd || $legacyExiste;
            }
            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerPagosVerificadosRecientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Estadísticas de inventario: mismo criterio que admin (tabla inv, stock, precio).
     */
    private function obtenerEstadisticasInventario() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_productos,
                    COUNT(CASE WHEN stock >= 10 AND stock < 20 THEN 1 END) as stock_bajo,
                    COUNT(CASE WHEN stock >= 1 AND stock <= 9 THEN 1 END) as stock_critico,
                    COUNT(CASE WHEN stock = 0 OR stock IS NULL THEN 1 END) as sin_stock,
                    COALESCE(SUM(stock * precio), 0) as valor_total
                FROM inv
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_productos' => 0,
                'stock_bajo' => 0,
                'stock_critico' => 0,
                'sin_stock' => 0,
                'valor_total' => 0
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasInventario: " . $e->getMessage());
            return [
                'total_productos' => 0,
                'stock_bajo' => 0,
                'stock_critico' => 0,
                'sin_stock' => 0,
                'valor_total' => 0
            ];
        }
    }
    
    /**
     * Lista productos del inventario (tabla inv + tflor). Paginación en BD. Criterios alineados con admin.
     * @return array ['items' => [...], 'total' => int]
     */
    private function obtenerProductosInventario() {
        try {
            $where_conditions = ["t.activo = 1"];
            $params = [];
            
            if (isset($_GET['categoria']) && $_GET['categoria'] !== '') {
                $where_conditions[] = "t.naturaleza = ?";
                $params[] = $_GET['categoria'];
            }
            
            if (isset($_GET['stock_estado']) && $_GET['stock_estado'] !== '') {
                switch ($_GET['stock_estado']) {
                    case 'normal':
                        $where_conditions[] = "COALESCE(i.stock, 0) >= 20";
                        break;
                    case 'bajo':
                        $where_conditions[] = "COALESCE(i.stock, 0) >= 10 AND COALESCE(i.stock, 0) < 20";
                        break;
                    case 'critico':
                        $where_conditions[] = "COALESCE(i.stock, 0) >= 1 AND COALESCE(i.stock, 0) <= 9";
                        break;
                    case 'sin_stock':
                        $where_conditions[] = "(COALESCE(i.stock, 0) = 0 OR i.stock IS NULL)";
                        break;
                }
            }
            
            if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {
                require_once __DIR__ . '/../helpers/security_helper.php';
                $busqueda_limpia = sanitizarCampoBusqueda($_GET['buscar'], 'buscar_inventario');
                if ($busqueda_limpia === false) {
                    $_SESSION['error_seguridad'] = "Entrada inválida detectada. Por seguridad, tu búsqueda fue bloqueada.";
                    header('Location: index.php?ctrl=empleado&action=inventario');
                    exit();
                }
                $where_conditions[] = "(t.nombre LIKE ? OR t.naturaleza LIKE ?)";
                $params[] = '%' . $busqueda_limpia . '%';
                $params[] = '%' . $busqueda_limpia . '%';
            }
            
            $where_sql = implode(' AND ', $where_conditions);
            $params_count = $params;
            
            $sql_count = "SELECT COUNT(*) as total FROM inv i INNER JOIN tflor t ON i.tflor_idtflor = t.idtflor WHERE $where_sql";
            $stmt_count = $this->db->prepare($sql_count);
            $stmt_count->execute($params_count);
            $total = (int) $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
            
            $productos_por_pagina = 10;
            $pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $offset = ($pagina_actual - 1) * $productos_por_pagina;
            $total_paginas = $total > 0 ? (int) ceil($total / $productos_por_pagina) : 1;
            
            $stmt = $this->db->prepare("
                SELECT 
                    i.idinv,
                    i.tflor_idtflor as idtflor,
                    COALESCE(t.nombre, CONCAT('Producto ', i.idinv)) as nombre,
                    COALESCE(t.naturaleza, '') as naturaleza,
                    i.precio,
                    COALESCE(i.stock, 0) as stock,
                    i.fecha_actualizacion
                FROM inv i
                INNER JOIN tflor t ON i.tflor_idtflor = t.idtflor
                WHERE $where_sql
                ORDER BY i.stock ASC, t.nombre ASC
                LIMIT ? OFFSET ?
            ");
            $params[] = $productos_por_pagina;
            $params[] = $offset;
            $stmt->execute($params);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'items' => $items,
                'total' => $total,
                'pagina_actual' => $pagina_actual,
                'total_paginas' => $total_paginas,
                'productos_por_pagina' => $productos_por_pagina,
                'offset' => $offset
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerProductosInventario: " . $e->getMessage());
            return [
                'items' => [],
                'total' => 0,
                'pagina_actual' => 1,
                'total_paginas' => 1,
                'productos_por_pagina' => 10,
                'offset' => 0
            ];
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
                    $where_conditions[] = "pg.idpago IS NULL";
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
            
            // Una fila por pedido: datos del pedido, último pago (para detalle expandible) e items
            $sql = "
                SELECT 
                    p.idped,
                    p.numped,
                    p.fecha_pedido,
                    p.fecha_entrega_solicitada,
                    p.monto_total,
                    p.estado,
                    p.direccion_entrega,
                    p.notas,
                    c.nombre AS cliente_nombre,
                    c.email AS cliente_email,
                    COALESCE(pg.estado_pag, 'Sin pago') AS estado_pago,
                    (SELECT COUNT(*) FROM detped d WHERE d.idped = p.idped) AS total_productos,
                    (SELECT GROUP_CONCAT(CONCAT(tf.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ' ) FROM detped dp JOIN tflor tf ON dp.idtflor = tf.idtflor WHERE dp.idped = p.idped) AS items_detalle,
                    pg.idpago,
                    pg.metodo_pago,
                    pg.fecha_pago,
                    pg.transaccion_id,
                    pg.comprobante_transferencia,
                    pg.tiene_comprobante_bd
                FROM ped p
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                LEFT JOIN (
                    SELECT pago.*, (pago.comprobante_imagen IS NOT NULL) AS tiene_comprobante_bd
                    FROM pagos pago
                    INNER JOIN (SELECT ped_idped, MAX(idpago) AS max_id FROM pagos GROUP BY ped_idped) last ON pago.ped_idped = last.ped_idped AND pago.idpago = last.max_id
                ) pg ON pg.ped_idped = p.idped
                $where_clause
                ORDER BY p.fecha_pedido DESC
                LIMIT 500
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
            error_log("Error en obtenerTodosPedidos (intentando consulta sin detalle pago): " . $e->getMessage());
            try {
                $where_conditions_fb = [];
                $params_fb = [];
                if (isset($_GET['estado_pedido']) && $_GET['estado_pedido'] !== '') {
                    $where_conditions_fb[] = "p.estado = ?";
                    $params_fb[] = $_GET['estado_pedido'];
                }
                if (isset($_GET['estado_pago']) && $_GET['estado_pago'] !== '') {
                    if ($_GET['estado_pago'] === 'Sin pago') {
                        $where_conditions_fb[] = "NOT EXISTS (SELECT 1 FROM pagos WHERE ped_idped = p.idped)";
                    } else {
                        $where_conditions_fb[] = "(SELECT estado_pag FROM pagos WHERE ped_idped = p.idped ORDER BY idpago DESC LIMIT 1) = ?";
                        $params_fb[] = $_GET['estado_pago'];
                    }
                }
                if (isset($_GET['fecha_desde']) && $_GET['fecha_desde'] !== '') {
                    $where_conditions_fb[] = "DATE(p.fecha_pedido) >= ?";
                    $params_fb[] = $_GET['fecha_desde'];
                }
                if (isset($_GET['fecha_hasta']) && $_GET['fecha_hasta'] !== '') {
                    $where_conditions_fb[] = "DATE(p.fecha_pedido) <= ?";
                    $params_fb[] = $_GET['fecha_hasta'];
                }
                $where_fb = empty($where_conditions_fb) ? '' : 'WHERE ' . implode(' AND ', $where_conditions_fb);
                $sql_fb = "SELECT p.idped, p.numped, p.fecha_pedido, p.fecha_entrega_solicitada, p.monto_total, p.estado, p.direccion_entrega, p.notas,
                    c.nombre AS cliente_nombre, c.email AS cliente_email,
                    COALESCE((SELECT estado_pag FROM pagos WHERE ped_idped = p.idped ORDER BY idpago DESC LIMIT 1), 'Sin pago') AS estado_pago,
                    (SELECT COUNT(*) FROM detped d WHERE d.idped = p.idped) AS total_productos,
                    (SELECT GROUP_CONCAT(CONCAT(tf.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ') FROM detped dp JOIN tflor tf ON dp.idtflor = tf.idtflor WHERE dp.idped = p.idped) AS items_detalle
                    FROM ped p INNER JOIN cli c ON p.cli_idcli = c.idcli $where_fb ORDER BY p.fecha_pedido DESC LIMIT 500";
                $stmt_fb = $this->db->prepare($sql_fb);
                $stmt_fb->execute($params_fb);
                $result = $stmt_fb->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as &$row) {
                    $row['idpago'] = null;
                    $row['metodo_pago'] = null;
                    $row['fecha_pago'] = null;
                    $row['transaccion_id'] = null;
                    $row['comprobante_transferencia'] = null;
                    $row['tiene_comprobante_bd'] = null;
                }
                return $result;
            } catch (Exception $e2) {
                error_log("Error fallback obtenerTodosPedidos: " . $e2->getMessage());
                return [];
            }
        }
    }
    
    private function actualizarEstadoPago($id_pago, $nuevo_estado, $observaciones = '') {
        try {
            $empleado_id = $_SESSION['user']['idusu'] ?? null;
            // Intentar con observaciones_empleado si existe la columna
            try {
                $stmt = $this->db->prepare("
                    UPDATE pagos 
                    SET estado_pag = ?, observaciones_empleado = ?, verificado_por = ?, fecha_verificacion = NOW()
                    WHERE idpago = ?
                ");
                $stmt->execute([$nuevo_estado, $observaciones, $empleado_id, $id_pago]);
            } catch (Exception $e) {
                $stmt = $this->db->prepare("
                    UPDATE pagos SET estado_pag = ?, verificado_por = ?, fecha_verificacion = NOW() WHERE idpago = ?
                ");
                $stmt->execute([$nuevo_estado, $empleado_id, $id_pago]);
            }
        
        // 🔑 Si el pago es "Rechazado", restaurar stock
        if ($nuevo_estado === 'Rechazado') {
            // Obtener el ID del pedido asociado al pago
            $stmt_ped = $this->db->prepare("SELECT ped_idped FROM pagos WHERE idpago = ?");
            $stmt_ped->execute([$id_pago]);
            $pago = $stmt_ped->fetch(PDO::FETCH_ASSOC);
            
            if ($pago) {
                $id_pedido = $pago['ped_idped'];
                require_once 'models/minventario.php';
                $invModel = new Minventario();
                
                // Obtener flores del pedido
                $stmt_det = $this->db->prepare("SELECT idtflor, cantidad FROM detped WHERE idped = ?");
                $stmt_det->execute([$id_pedido]);
                $detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($detalles as $detalle) {
                    $invModel->restaurarStock($detalle['idtflor'], $detalle['cantidad'], "Pago rechazado (Pedido #$id_pedido) - Restauración de stock");
                }
            }
        }
        
        return true;
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
            
            // 🔑 Si el estado cambia a "Cancelado", restaurar al inventario
            if ($nuevo_estado === 'Cancelado') {
                require_once 'models/minventario.php';
                $invModel = new Minventario();
                
                // Obtener detalles del pedido
                $stmt_det = $this->db->prepare("SELECT idtflor, cantidad FROM detped WHERE idped = ?");
                $stmt_det->execute([$id_pedido]);
                $detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($detalles as $detalle) {
                    $invModel->restaurarStock($detalle['idtflor'], $detalle['cantidad'], "Pedido #$id_pedido cancelado - Restauración de stock");
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
            if (!isset($_POST['cli_id']) || !isset($_POST['flores'])) {
                throw new Exception("Datos incompletos");
            }

            $cli_id = intval($_POST['cli_id']);
            $flores = $_POST['flores'] ?? [];
            $tipo_entrega = $_POST['tipo_entrega'] ?? 'recoger';
            $direccion_entrega = trim($_POST['direccion_entrega'] ?? '') ?: null;
            $fecha_entrega = trim($_POST['fecha_entrega'] ?? '') ?: null;
            $notas = trim($_POST['notas'] ?? '') ?: null;
            $monto_total = floatval($_POST['monto_total'] ?? 0);
            if ($tipo_entrega === 'domicilio') {
                if (empty(trim($_POST['direccion_entrega'] ?? ''))) {
                    throw new Exception("Para envío a domicilio debes indicar la dirección de entrega.");
                }
                try {
                    $st_env = $this->db->query("SELECT COALESCE(cobrar_envio, 0) as cobrar_envio, COALESCE(precio_envio, 0) as precio_envio FROM empresa LIMIT 1");
                    if ($st_env && ($row_env = $st_env->fetch(PDO::FETCH_ASSOC)) && !empty($row_env['cobrar_envio'])) {
                        $monto_total += (float)$row_env['precio_envio'];
                    }
                } catch (PDOException $e) {}
            }
            $metodo_pago = in_array($_POST['metodo_pago'] ?? '', ['efectivo', 'nequi'], true) ? $_POST['metodo_pago'] : 'efectivo';
            $estado_pago = in_array($_POST['estado_pago'] ?? '', ['Pendiente','Completado'], true) ? $_POST['estado_pago'] : 'Pendiente';

            $numped = 'PED-' . date('YmdHis') . '-' . $cli_id;

            $this->db->beginTransaction();

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

            $stmt_det = $this->db->prepare("
                INSERT INTO detped (idped, idtflor, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?)
            ");

            $items_para_descontar = [];
            foreach ($flores as $idtflor => $data) {
                $cantidad = (int)($data['cantidad'] ?? 0);
                $precio = (float)($data['precio'] ?? 0);
                if ($cantidad <= 0 || $precio <= 0) continue;
                $stmt_nom = $this->db->prepare("SELECT nombre FROM tflor WHERE idtflor = ? AND COALESCE(activo, 1) = 1");
                $stmt_nom->execute([$idtflor]);
                $nombre_flor = $stmt_nom->fetchColumn();
                if (!$nombre_flor) {
                    throw new Exception("El producto seleccionado ya no está disponible (desactivado). Actualice la página e intente de nuevo.");
                }
                $nombre_flor = $nombre_flor ?: 'Producto';
                $stmt_inv = $this->db->prepare("SELECT COALESCE(stock, cantidad_disponible, 0) as disp FROM inv WHERE tflor_idtflor = ? LIMIT 1");
                $stmt_inv->execute([$idtflor]);
                $row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC);
                $stock_actual = (int)($row_inv['disp'] ?? 0);
                if ($cantidad > $stock_actual) {
                    throw new Exception("La cantidad de \"$nombre_flor\" no puede ser mayor al stock disponible ($stock_actual).");
                }
                $stmt_det->execute([$pedido_id, $idtflor, $cantidad, $precio]);
                $items_para_descontar[] = ['idtflor' => (int)$idtflor, 'cantidad' => $cantidad];
            }
            if (empty($items_para_descontar)) {
                throw new Exception("Debe agregar al menos un producto con cantidad mayor a 0.");
            }

            // Descontar del inventario al crear el pedido (igual que cliente y admin)
            require_once __DIR__ . '/../models/minventario.php';
            $invModel = new Minventario();
            $motivo = "Descuento por pedido #$pedido_id";
            foreach ($items_para_descontar as $item) {
                $invModel->descontarStock($item['idtflor'], $item['cantidad'], $motivo);
            }

            $stmt_pago = $this->db->prepare("
                INSERT INTO pagos (ped_idped, monto, metodo_pago, estado_pag, fecha_pago) VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt_pago->execute([$pedido_id, $monto_total, $metodo_pago, $estado_pago]);

            $this->db->commit();

            // Enviar factura por correo al cliente (si está activado en config y el cliente tiene email)
            if (file_exists(__DIR__ . '/../config/email_config.php')) {
                require_once __DIR__ . '/../config/email_config.php';
            }
            $enviar_factura_activo = defined('ENVIAR_FACTURA_AL_CREAR_PEDIDO') ? ENVIAR_FACTURA_AL_CREAR_PEDIDO : true;
            $stmt_cli = $this->db->prepare("SELECT email, nombre FROM cli WHERE idcli = ?");
            $stmt_cli->execute([$cli_id]);
            $cli_row = $stmt_cli->fetch(PDO::FETCH_ASSOC);
            $email_cliente = trim($cli_row['email'] ?? '');
            if ($enviar_factura_activo && $email_cliente !== '' && filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
                $pedido = $this->obtenerDetallesPedidoParaFactura($pedido_id);
                $pago = $this->obtenerPagoPorPedido($pedido_id);
                $detalles = $this->obtenerDetallesItemsPedidoFactura($pedido_id);
                if ($pedido && !empty($detalles)) {
                    $pdf_content = $this->generarFacturaPdfEnMemoria($pedido, $pago, $detalles);
                    if ($pdf_content) {
                        $enviado = $this->enviarFacturaPorEmailEmpleado($email_cliente, $pedido, $pdf_content);
                        if ($enviado) {
                            $_SESSION['mensaje'] = "Pedido #$pedido_id creado exitosamente. Se descontó el inventario y se envió la factura al correo del cliente.";
                        } else {
                            $_SESSION['mensaje'] = "Pedido #$pedido_id creado exitosamente. Se descontó el inventario. No se pudo enviar la factura por correo.";
                        }
                    } else {
                        $_SESSION['mensaje'] = "Pedido #$pedido_id creado exitosamente. Se descontó el inventario.";
                    }
                } else {
                    $_SESSION['mensaje'] = "Pedido #$pedido_id creado exitosamente. Se descontó el inventario.";
                }
            } else {
                $_SESSION['mensaje'] = "Pedido #$pedido_id creado exitosamente. Se descontó el inventario.";
            }
            $_SESSION['tipo_mensaje'] = 'success';
            header('Location: index.php?ctrl=empleado&action=gestion_pedidos');
            exit();

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
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
        try {
            $user = $_SESSION['user'];
            // Lista unificada: cli + usuarios con rol cliente (5), igual que admin
            require_once __DIR__ . '/Cpedido.php';
            $cpedido = new Cpedido();
            $clientes = $cpedido->listarClientes();

            // Solo productos activos que existen en inventario (inv) — no mostrar desactivados ni tflor sin inv
            $stmt = $this->db->prepare("
                SELECT 
                    tf.idtflor,
                    tf.nombre,
                    tf.naturaleza AS color,
                    tf.descripcion,
                    i.precio,
                    COALESCE(i.stock, 0) AS stock
                FROM inv i
                INNER JOIN tflor tf ON tf.idtflor = i.tflor_idtflor
                WHERE COALESCE(tf.activo, 1) = 1
                ORDER BY tf.nombre
            ");
            $stmt->execute();
            $flores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $cobrar_envio = 0;
            $precio_envio = 0.0;
            try {
                $st_env = $this->db->query("SELECT COALESCE(cobrar_envio, 0) as cobrar_envio, COALESCE(precio_envio, 0) as precio_envio FROM empresa LIMIT 1");
                if ($st_env && ($row_env = $st_env->fetch(PDO::FETCH_ASSOC))) {
                    $cobrar_envio = (int)$row_env['cobrar_envio'];
                    $precio_envio = (float)$row_env['precio_envio'];
                }
            } catch (PDOException $e) {}

            include 'views/empleado/nuevo_pedido.php';
        } catch (Exception $e) {
            error_log("Error en nuevoPedidoForm: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Configuración de cuenta del empleado (perfil y cambio de contraseña).
     */
    public function configuracion() {
        include 'views/empleado/configuracion.php';
    }
}
?>
