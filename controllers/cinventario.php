<?php
require_once 'models/Minventario.php';

class Cinventario {
    private $inventarioModel;
    private $mensaje_exito = '';
    private $mensaje_error = '';
    private $error_message = '';
    
    // Variables de datos
    private $total_productos = 0;
    private $stock_bajo = 0;
    private $stock_critico = 0;
    private $sin_stock = 0;
    private $valor_total = 0;
    private $inventario = [];
    private $inventario_perecederos = [];
    private $inventario_no_perecederos = [];
    private $proveedores = [];
    private $total_elementos = 0;
    private $total_paginas = 1;
    private $todas_las_flores = [];
    private $flores_para_select = [];
    private $elementos_por_pagina = 10;
    private $pagina_actual = 1;
    private $offset = 0;
    
    // Variables para paginación de perecederos
    private $elementos_por_pagina_perecederos = 10;
    private $pagina_actual_perecederos = 1;
    private $offset_perecederos = 0;
    private $total_elementos_perecederos = 0;
    private $total_paginas_perecederos = 1;
    
    // Variables para paginación de no perecederos
    private $elementos_por_pagina_no_perecederos = 10;
    private $pagina_actual_no_perecederos = 1;
    private $offset_no_perecederos = 0;
    private $total_elementos_no_perecederos = 0;
    private $total_paginas_no_perecederos = 1;
    
    // Variables para paginación de proveedores
    private $elementos_por_pagina_proveedores = 10;
    private $pagina_actual_proveedores = 1;
    private $offset_proveedores = 0;
    private $total_elementos_proveedores = 0;
    private $total_paginas_proveedores = 1;

    public function __construct() {
        // Verificar sesión
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?ctrl=login');
            exit();
        }
        
        // Verificar que el usuario tenga permisos para acceder al inventario
        // Administradores (1) e Inventario (3) pueden acceder
        $userType = $_SESSION['user']['tpusu_idtpusu'];
        if (!in_array($userType, [1, 3])) {
            // Redirigir a empleados que no sean de inventario a su dashboard específico
            header('Location: index.php?ctrl=empleado&action=inventario');
            exit();
        }
        
        try {
            $this->inventarioModel = new Minventario();
        } catch (Exception $e) {
            error_log("Error crítico al cargar Minventario: " . $e->getMessage());
            $this->error_message = "Error de conexión a la base de datos. Por favor, inténtelo más tarde.";
            $this->inventarioModel = null;
        }
    }

    /**
     * Acción principal del inventario
     */
    public function index() {
        $this->procesarFormularios();
        $this->cargarDatos();
        $this->cargarVista();
    }
    
    /**
     * Procesar formularios POST
     */
    private function procesarFormularios() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->inventarioModel) {
            return;
        }
        
        try {
            if (isset($_POST['accion'])) {
                switch ($_POST['accion']) {
                    case 'nuevo_producto':
                        try {
                            // Validación de precios
                            $precio_compra = floatval($_POST['precio_compra'] ?? 0);
                            $precio_venta = floatval($_POST['precio'] ?? 0);
                            
                            if ($precio_compra <= 0) {
                                throw new Exception('El precio de compra debe ser mayor a cero');
                            }
                            
                            if ($precio_venta <= 0) {
                                throw new Exception('El precio de venta debe ser mayor a cero');
                            }
                            
                            if ($precio_compra >= $precio_venta) {
                                throw new Exception('El precio de venta debe ser mayor al precio de compra para generar ganancia');
                            }
                            
                            $this->inventarioModel->agregarProducto($_POST);
                            $this->mensaje_exito = 'Producto agregado al inventario exitosamente';
                            header('Location: ?ctrl=Cinventario&success=1');
                            exit;
                        } catch (Exception $e) {
                            $mensaje = $e->getMessage();
                            
                            // Verificar si es un error de producto duplicado (JSON)
                            if (strpos($mensaje, '{"tipo":"producto_duplicado"') === 0) {
                                $datos_duplicado = json_decode($mensaje, true);
                                // Redirigir con parámetros especiales para mostrar modal de confirmación
                                header('Location: ?ctrl=Cinventario&duplicado=1&producto_id=' . 
                                       $datos_duplicado['producto_id'] . 
                                       '&producto_nombre=' . urlencode($datos_duplicado['producto_nombre']) .
                                       '&stock_actual=' . $datos_duplicado['stock_actual'] .
                                       '&mensaje=' . urlencode($datos_duplicado['mensaje']));
                                exit;
                            }
                            
                            error_log('Error al agregar producto: ' . $mensaje);
                            header('Location: ?ctrl=Cinventario&error=' . urlencode($mensaje));
                            exit;
                        }
                        break;
                        
                    case 'actualizar_parametros':
                        $this->inventarioModel->actualizarParametros($_POST);
                        $this->mensaje_exito = 'Parámetros de inventario actualizados correctamente';
                        header('Location: ?ctrl=Cinventario&success=parametros_actualizados');
                        exit;
                        break;
                        
                    case 'nueva_flor':
                        $this->inventarioModel->crearNuevaFlor($_POST);
                        $this->mensaje_exito = 'Nueva flor creada exitosamente';
                        header('Location: ?ctrl=Cinventario&success=nueva_flor');
                        exit;
                        break;
                        
                    case 'editar_flor':
                        $this->inventarioModel->actualizarFlor($_POST);
                        $this->mensaje_exito = 'Flor actualizada exitosamente';
                        header('Location: ?ctrl=Cinventario&success=flor_editada');
                        exit;
                        break;
                        
                    case 'eliminar_flor':
                        $this->inventarioModel->eliminarFlor($_POST['id_flor']);
                        $this->mensaje_exito = 'Flor eliminada exitosamente';
                        header('Location: ?ctrl=Cinventario&success=flor_eliminada');
                        exit;
                        break;
                        
                    case 'agregar_a_inventario':
                        $this->inventarioModel->agregarFlorAInventario($_POST['id_flor']);
                        $this->mensaje_exito = 'Flor agregada al inventario exitosamente. Puedes actualizar el stock y precio desde la gestión de inventario.';
                        header('Location: ?ctrl=Cinventario&success=agregada_inventario');
                        exit;
                        break;
                        
                    case 'nuevo_proveedor':
                        $this->inventarioModel->crearProveedor($_POST);
                        $this->mensaje_exito = 'Proveedor agregado exitosamente';
                        header('Location: ?ctrl=Cinventario&success=proveedor_agregado');
                        exit;
                        break;
                        
                    case 'editar_proveedor':
                        // LOG TEMPORAL PARA DEBUG
                        error_log('=== EDITAR PROVEEDOR DEBUG ===');
                        error_log('POST recibido: ' . print_r($_POST, true));
                        
                        $resultado = $this->inventarioModel->editarProveedor($_POST);
                        
                        error_log('Resultado del modelo: ' . print_r($resultado, true));
                        
                        if ($resultado['success']) {
                            $this->mensaje_exito = 'Proveedor actualizado exitosamente';
                            header('Location: ?ctrl=cinventario&success=proveedor_editado');
                        } else {
                            $this->mensaje_error = $resultado['message'];
                            header('Location: ?ctrl=cinventario&error=proveedor_editar_fallido');
                        }
                        exit;
                        break;
                        
                    case 'eliminar_proveedor':
                        $resultado = $this->inventarioModel->eliminarProveedor($_POST['proveedor_id']);
                        if ($resultado['success']) {
                            $this->mensaje_exito = 'Proveedor eliminado exitosamente';
                            header('Location: ?ctrl=Cinventario&success=proveedor_eliminado');
                        } else {
                            $this->mensaje_error = $resultado['message'];
                            header('Location: ?ctrl=Cinventario&error=proveedor_eliminar_fallido');
                        }
                        exit;
                        break;
                        
                    case 'editar_producto':
                        try {
                            // Validación de precios
                            $precio_compra = floatval($_POST['precio_compra'] ?? 0);
                            $precio_venta = floatval($_POST['precio'] ?? 0);
                            
                            if ($precio_compra <= 0) {
                                echo json_encode(['success' => false, 'message' => 'El precio de compra debe ser mayor a cero']);
                                exit;
                            }
                            
                            if ($precio_venta <= 0) {
                                echo json_encode(['success' => false, 'message' => 'El precio de venta debe ser mayor a cero']);
                                exit;
                            }
                            
                            if ($precio_compra >= $precio_venta) {
                                echo json_encode(['success' => false, 'message' => 'El precio de venta debe ser mayor al precio de compra']);
                                exit;
                            }
                            
                            $resultado = $this->inventarioModel->editarProducto($_POST);
                            if ($resultado['success']) {
                                echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
                            } else {
                                echo json_encode(['success' => false, 'message' => $resultado['message']]);
                            }
                        } catch (Exception $e) {
                            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                        }
                        exit;
                        break;
                        
                    case 'obtener_producto':
                        $id = $_GET['id'] ?? $_POST['id'] ?? null;
                        if ($id) {
                            $producto = $this->inventarioModel->obtenerProductoPorId($id);
                            if ($producto) {
                                echo json_encode(['success' => true, 'producto' => $producto]);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
                        }
                        exit;
                        break;
                        
                    case 'agregar_stock':
                        $id = $_GET['id'] ?? $_POST['id'] ?? null;
                        $cantidad = $_GET['cantidad'] ?? $_POST['cantidad'] ?? null;
                        $motivo = $_GET['motivo'] ?? $_POST['motivo'] ?? '';
                        
                        if ($id && $cantidad && $cantidad > 0) {
                            $resultado = $this->inventarioModel->agregarStock($id, $cantidad, $motivo);
                            if ($resultado['success']) {
                                echo json_encode(['success' => true, 'message' => 'Stock agregado correctamente']);
                            } else {
                                echo json_encode(['success' => false, 'message' => $resultado['message']]);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                        }
                        exit;
                        break;
                        
                    case 'eliminar_producto':
                        $id = $_POST['id'] ?? $_GET['id'] ?? null;
                        
                        if (!$id) {
                            // Respuesta JSON para peticiones AJAX
                            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                                echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
                                exit;
                            }
                            header('Location: ?ctrl=Cinventario&error=' . urlencode('ID de producto requerido'));
                            exit;
                        }
                        
                        try {
                            $resultado = $this->inventarioModel->eliminarProducto($id);
                            
                            // Respuesta JSON para peticiones AJAX
                            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                                if ($resultado['success']) {
                                    echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
                                } else {
                                    echo json_encode(['success' => false, 'message' => $resultado['message']]);
                                }
                                exit;
                            }
                            
                            // Redirect para formularios POST tradicionales
                            if ($resultado['success']) {
                                header('Location: ?ctrl=Cinventario&success=producto_eliminado');
                            } else {
                                header('Location: ?ctrl=Cinventario&error=' . urlencode($resultado['message']));
                            }
                        } catch (Exception $e) {
                            // Respuesta JSON para peticiones AJAX
                            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                                exit;
                            }
                            header('Location: ?ctrl=Cinventario&error=' . urlencode($e->getMessage()));
                        }
                        exit;
                        break;
                        
                    case 'exportar_inventario':
                        $this->exportarInventarioExcel();
                        exit;
                        break;
                }
            }
        } catch (Exception $e) {
            $this->mensaje_error = $e->getMessage();
        }
    }
    
    /**
     * Cargar todos los datos necesarios para la vista
     */
    private function cargarDatos() {
        if (!$this->inventarioModel) {
            // Si no hay modelo, asegurar que todas las variables tengan valores por defecto
            $this->elementos_por_pagina = 10;
            $this->pagina_actual = 1;
            $this->offset = 0;
            $this->total_elementos = 0;
            $this->total_paginas = 1;
            return;
        }
        
        try {
            // Configuración de paginación para perecederos
            $this->elementos_por_pagina_perecederos = isset($_GET['per_page_perecederos']) ? max(5, min(50, intval($_GET['per_page_perecederos']))) : 10;
            $this->pagina_actual_perecederos = isset($_GET['pagina_perecederos']) ? max(1, intval($_GET['pagina_perecederos'])) : 1;
            $this->offset_perecederos = ($this->pagina_actual_perecederos - 1) * $this->elementos_por_pagina_perecederos;
            
            // Configuración de paginación para no perecederos
            $this->elementos_por_pagina_no_perecederos = isset($_GET['per_page_no_perecederos']) ? max(5, min(50, intval($_GET['per_page_no_perecederos']))) : 10;
            $this->pagina_actual_no_perecederos = isset($_GET['pagina_no_perecederos']) ? max(1, intval($_GET['pagina_no_perecederos'])) : 1;
            $this->offset_no_perecederos = ($this->pagina_actual_no_perecederos - 1) * $this->elementos_por_pagina_no_perecederos;
            
            // Configuración de paginación para proveedores
            $this->elementos_por_pagina_proveedores = isset($_GET['per_page_proveedores']) ? max(5, min(50, intval($_GET['per_page_proveedores']))) : 10;
            $this->pagina_actual_proveedores = isset($_GET['pagina_proveedores']) ? max(1, intval($_GET['pagina_proveedores'])) : 1;
            $this->offset_proveedores = ($this->pagina_actual_proveedores - 1) * $this->elementos_por_pagina_proveedores;
            
            // Obtener filtros
            $filtros = [
                'categoria' => $_GET['categoria'] ?? '',
                'estado_stock' => $_GET['estado_stock'] ?? '',
                'buscar' => $_GET['buscar'] ?? ''
            ];
            
            // Obtener estadísticas
            $this->cargarEstadisticas();
            
            // Obtener inventario completo (para compatibilidad con código existente)
            $this->cargarInventario($filtros);
            
            // Obtener inventario de perecederos paginado
            $filtros_perecederos = array_merge($filtros, ['tipo_producto' => 'perecedero']);
            $this->inventario_perecederos = $this->inventarioModel->getInventarioPaginado($this->elementos_por_pagina_perecederos, $this->offset_perecederos, $filtros_perecederos);
            
            // Enriquecer datos de perecederos con información de lotes
            require_once 'models/Mlotes.php';
            $lotesModel = new Mlotes();
            foreach ($this->inventario_perecederos as &$producto) {
                $resumen = $lotesModel->getResumenLotesPorProducto($producto['idinv']);
                $producto['lote_proxima_caducidad'] = $resumen['proxima_caducidad'] ?? null;
                $producto['lote_cantidad_activa'] = $resumen['cantidad_activa'] ?? 0;
                
                // Calcular días restantes hasta próxima caducidad
                if ($producto['lote_proxima_caducidad']) {
                    $hoy = new DateTime();
                    $fecha_cad = new DateTime($producto['lote_proxima_caducidad']);
                    $diferencia = $hoy->diff($fecha_cad);
                    $producto['dias_hasta_caducidad'] = $diferencia->invert ? -$diferencia->days : $diferencia->days;
                } else {
                    $producto['dias_hasta_caducidad'] = null;
                }
            }
            unset($producto); // Romper la referencia
            
            $this->total_elementos_perecederos = $this->inventarioModel->getTotalElementos($filtros_perecederos);
            $this->total_paginas_perecederos = max(1, ceil($this->total_elementos_perecederos / $this->elementos_por_pagina_perecederos));
            
            // Obtener inventario de no perecederos paginado
            $filtros_no_perecederos = array_merge($filtros, ['tipo_producto' => 'no_perecedero']);
            $this->inventario_no_perecederos = $this->inventarioModel->getInventarioPaginado($this->elementos_por_pagina_no_perecederos, $this->offset_no_perecederos, $filtros_no_perecederos);
            $this->total_elementos_no_perecederos = $this->inventarioModel->getTotalElementos($filtros_no_perecederos);
            $this->total_paginas_no_perecederos = max(1, ceil($this->total_elementos_no_perecederos / $this->elementos_por_pagina_no_perecederos));
            
            // Obtener proveedores paginados para la tabla
            $this->proveedores = $this->inventarioModel->getProveedoresConProductos($this->elementos_por_pagina_proveedores, $this->offset_proveedores);
            $this->total_elementos_proveedores = $this->inventarioModel->getTotalProveedores();
            $this->total_paginas_proveedores = max(1, ceil($this->total_elementos_proveedores / $this->elementos_por_pagina_proveedores));
            
            // Obtener total de elementos y calcular páginas (para compatibilidad)
            $this->cargarPaginacion($filtros);
            
            // Obtener flores para los selectores
            $this->cargarFlores();
            
        } catch (Exception $e) {
            error_log("Error en cargarDatos: " . $e->getMessage());
            $this->error_message = "Error al cargar los datos del inventario.";
        }
    }
    
    /**
     * Cargar estadísticas del inventario
     */
    private function cargarEstadisticas() {
        try {
            if ($this->inventarioModel) {
                $estadisticas = $this->inventarioModel->getEstadisticasInventario();
                if (is_array($estadisticas)) {
                    $this->total_productos = $estadisticas['total_productos'] ?? 0;
                    $this->stock_bajo = $estadisticas['stock_bajo'] ?? 0;
                    $this->stock_critico = $estadisticas['stock_critico'] ?? 0;
                    $this->sin_stock = $estadisticas['sin_stock'] ?? 0;
                    $this->valor_total = $estadisticas['valor_total'] ?? 0;
                }
            }
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            // Las variables ya están inicializadas con 0 en el constructor
        }
    }
    
    /**
     * Cargar inventario paginado
     */
    private function cargarInventario($filtros) {
        try {
            if ($this->inventarioModel) {
                $this->inventario = $this->inventarioModel->getInventarioPaginado($this->elementos_por_pagina, $this->offset, $filtros);
            }
        } catch (Exception $e) {
            error_log("Error al obtener inventario: " . $e->getMessage());
            $this->inventario = [];
        }
    }
    
    /**
     * Cargar información de paginación
     */
    /**
     * Cargar información de paginación
     */
    private function cargarPaginacion($filtros) {
        try {
            if ($this->inventarioModel) {
                $this->total_elementos = $this->inventarioModel->getTotalElementos($filtros);
                // Asegurar que siempre haya al menos 1 página
                $this->total_paginas = max(1, ceil($this->total_elementos / $this->elementos_por_pagina));
            }
        } catch (Exception $e) {
            error_log("Error al obtener total de elementos: " . $e->getMessage());
            $this->total_elementos = 0;
            $this->total_paginas = 1;
        }
    }
    
    /**
     * Cargar flores para los selectores
     */
    private function cargarFlores() {
        try {
            $this->todas_las_flores = $this->inventarioModel->getTodasLasFlores();
        } catch (Exception $e) {
            error_log("Error al obtener todas las flores: " . $e->getMessage());
            $this->todas_las_flores = [];
        }
        
        try {
            $this->flores_para_select = $this->inventarioModel->getFloresParaSelect();
        } catch (Exception $e) {
            error_log("Error al obtener flores para select: " . $e->getMessage());
            $this->flores_para_select = [];
        }
    }
    
    /**
     * Cargar la vista del inventario
     */
    private function cargarVista() {
    // Hacer las variables accesibles en la vista
    $mensaje_exito = $this->mensaje_exito;
    $mensaje_error = $this->mensaje_error;
    $error_message = $this->error_message;

    // Variables de estadísticas - siempre definidas
    $total_productos = $this->total_productos;
    $stock_bajo = $this->stock_bajo;
    $stock_critico = $this->stock_critico;
    $sin_stock = $this->sin_stock;
    $valor_total = $this->valor_total;

    // Variables de inventario - siempre definidas
    $inventario = $this->inventario;
    $inventario_perecederos = $this->inventario_perecederos;
    $inventario_no_perecederos = $this->inventario_no_perecederos;
    $total_elementos = $this->total_elementos;
    $total_paginas = $this->total_paginas;
    $todas_las_flores = $this->todas_las_flores;
    $flores_para_select = $this->flores_para_select;

    // Productos para el select de proveedores (todos los productos)
    $productos_inventario = $this->inventarioModel ? $this->inventarioModel->getInventarioPaginado(9999, 0, []) : [];

    // Proveedores para la tabla de proveedores (paginados con productos)
    $proveedores = $this->proveedores;
    
    // Todos los proveedores para el select (sin paginar)
    $todos_proveedores = $this->inventarioModel ? $this->inventarioModel->getProveedores() : [];

    // Variables de paginación - siempre definidas
    $elementos_por_pagina = $this->elementos_por_pagina;
    $pagina_actual = $this->pagina_actual;
    $offset = $this->offset;
    
    // Variables de paginación de perecederos
    $elementos_por_pagina_perecederos = $this->elementos_por_pagina_perecederos;
    $pagina_actual_perecederos = $this->pagina_actual_perecederos;
    $offset_perecederos = $this->offset_perecederos;
    $total_elementos_perecederos = $this->total_elementos_perecederos;
    $total_paginas_perecederos = $this->total_paginas_perecederos;
    
    // Variables de paginación de no perecederos
    $elementos_por_pagina_no_perecederos = $this->elementos_por_pagina_no_perecederos;
    $pagina_actual_no_perecederos = $this->pagina_actual_no_perecederos;
    $offset_no_perecederos = $this->offset_no_perecederos;
    $total_elementos_no_perecederos = $this->total_elementos_no_perecederos;
    $total_paginas_no_perecederos = $this->total_paginas_no_perecederos;
    
    // Variables de paginación de proveedores
    $elementos_por_pagina_proveedores = $this->elementos_por_pagina_proveedores;
    $pagina_actual_proveedores = $this->pagina_actual_proveedores;
    $offset_proveedores = $this->offset_proveedores;
    $total_elementos_proveedores = $this->total_elementos_proveedores;
    $total_paginas_proveedores = $this->total_paginas_proveedores;

    // Variables para el layout
    $usu = $_SESSION['user'];
    $page = 'inventario'; // Forzar siempre la vista de inventario
    $_GET['page'] = 'inventario'; // Forzar también el parámetro GET por si la vista lo usa
    $totalUsuarios = 0; // Variable requerida por el dashboard

    // Cargar el dashboard de admin con la vista del inventario
    include 'views/admin/VadashboardPrincipal.php';
    }

    /**
     * Método para exportar inventario a Excel
     */
    public function exportarInventarioExcel() {
        try {
            if (!$this->inventarioModel) {
                throw new Exception('Modelo de inventario no disponible');
            }

            // Obtener todos los datos del inventario
            $inventario = $this->inventarioModel->getInventarioPaginado(9999, 0, []);
            
            // Configurar headers para descarga de Excel
            $filename = 'inventario_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            
            // Crear el contenido CSV
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($output, [
                'ID',
                'Producto',
                'Categoría',
                'Stock',
                'Precio Compra',
                'Precio Venta',
                'Margen Unit.',
                'Color',
                'Naturaleza',
                'Estado Stock',
                'Inversión Total',
                'Ingresos Potenciales',
                'Ganancia Potencial',
                'Fecha Actualización'
            ], ';');
            
            // Datos
            foreach ($inventario as $item) {
                $precio_compra = floatval($item['precio_compra'] ?? 0);
                $precio_venta = floatval($item['precio'] ?? 0);
                $stock = intval($item['stock'] ?? 0);
                $margen_unitario = $precio_venta - $precio_compra;
                $inversion_total = $precio_compra * $stock;
                $ingresos_potenciales = $precio_venta * $stock;
                $ganancia_potencial = $margen_unitario * $stock;
                
                fputcsv($output, [
                    $item['idinv'] ?? '',
                    $item['producto'] ?? '',
                    $item['categoria_producto'] ?? $item['naturaleza'] ?? '',
                    $stock,
                    number_format($precio_compra, 2),
                    number_format($precio_venta, 2),
                    number_format($margen_unitario, 2),
                    $item['color'] ?? '',
                    $item['naturaleza'] ?? '',
                    $item['estado_stock'] ?? '',
                    number_format($inversion_total, 2),
                    number_format($ingresos_potenciales, 2),
                    number_format($ganancia_potencial, 2),
                    $item['fecha_actualizacion'] ?? ''
                ], ';');
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            // En caso de error, redirigir con mensaje
            header('Location: ?ctrl=Cinventario&error=export_failed');
            exit;
        }
    }

    /**
     * Exportar inventario a PDF con análisis financiero completo
     */
    public function exportarInventarioPDF() {
        try {
            if (!$this->inventarioModel) {
                throw new Exception('Modelo de inventario no disponible');
            }

            // Incluir librería FPDF
            require_once __DIR__ . '/../libs/FPDF/fpdf.php';
            
            // Obtener filtros de URL
            $tipo = $_GET['tipo'] ?? 'todos'; // perecedero, no_perecedero, todos
            $filtros = [];
            
            if ($tipo === 'perecedero') {
                $filtros['tipo_producto'] = 'perecedero';
            } elseif ($tipo === 'no_perecedero') {
                $filtros['tipo_producto'] = 'no_perecedero';
            }
            
            // Obtener datos del inventario
            $inventario = $this->inventarioModel->getInventarioPaginado(9999, 0, $filtros);
            
            // Crear PDF
            $pdf = new FPDF('L', 'mm', 'A4'); // Landscape, mm, A4
            $pdf->SetMargins(8, 10, 8);
            $pdf->AddPage();
            
            // Helper para UTF-8
            $limpiar = function($texto) {
                return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
            };
            
            // HEADER
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 8, $limpiar('FLORALTECH - ANÁLISIS FINANCIERO DE INVENTARIO'), 0, 1, 'C');
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(0, 5, 'Fecha: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
            
            // Tipo de reporte
            $tipoTexto = $tipo === 'perecedero' ? 'Productos Perecederos (Flores Naturales)' : 
                         ($tipo === 'no_perecedero' ? 'Productos No Perecederos (Duraderos)' : 
                          'Inventario Completo');
            $pdf->Cell(0, 5, $limpiar($tipoTexto), 0, 1, 'C');
            $pdf->Ln(3);
            
            // TABLA - Headers
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetFillColor(52, 152, 219); // Azul
            $pdf->SetTextColor(255, 255, 255); // Blanco
            
            $pdf->Cell(8, 7, 'ID', 1, 0, 'C', true);
            $pdf->Cell(48, 7, 'Producto', 1, 0, 'C', true);
            $pdf->Cell(15, 7, 'Stock', 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'P. Compra', 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'P. Venta', 1, 0, 'C', true);
            $pdf->Cell(18, 7, 'Margen', 1, 0, 'C', true);
            $pdf->Cell(28, 7, $limpiar('Inversión'), 1, 0, 'C', true);
            $pdf->Cell(28, 7, 'Ingresos Pot.', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Ganancia', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Naturaleza', 1, 0, 'C', true);
            $pdf->Cell(23, 7, 'Estado', 1, 1, 'C', true);
            
            // Datos
            $pdf->SetFont('Arial', '', 6.5);
            $pdf->SetTextColor(0, 0, 0); // Negro
            
            $total_productos = 0;
            $inversion_total_general = 0;
            $ingresos_potenciales_general = 0;
            $ganancia_potencial_general = 0;
            $stock_bajo_count = 0;
            $sin_stock_count = 0;
            
            foreach ($inventario as $item) {
                $stock = intval($item['stock'] ?? 0);
                $precio_compra = floatval($item['precio_compra'] ?? 0);
                $precio_venta = floatval($item['precio'] ?? 0);
                $margen_unitario = $precio_venta - $precio_compra;
                $inversion = $precio_compra * $stock;
                $ingresos = $precio_venta * $stock;
                $ganancia = $margen_unitario * $stock;
                
                $inversion_total_general += $inversion;
                $ingresos_potenciales_general += $ingresos;
                $ganancia_potencial_general += $ganancia;
                $total_productos++;
                
                // Contar estados
                if ($stock == 0) {
                    $sin_stock_count++;
                } elseif ($stock < 20) {
                    $stock_bajo_count++;
                }
                
                // Color de fondo según stock
                $aplicar_fill = false;
                if ($stock == 0) {
                    $pdf->SetFillColor(231, 76, 60); // Rojo
                    $pdf->SetTextColor(255, 255, 255); // Blanco
                    $aplicar_fill = true;
                } elseif ($stock < 20) {
                    $pdf->SetFillColor(241, 196, 15); // Amarillo
                    $pdf->SetTextColor(0, 0, 0); // Negro
                    $aplicar_fill = true;
                } else {
                    $pdf->SetFillColor(240, 240, 240); // Gris claro
                    $pdf->SetTextColor(0, 0, 0); // Negro
                }
                
                $pdf->Cell(8, 6, $item['idinv'], 1, 0, 'C', $aplicar_fill);
                $pdf->Cell(48, 6, $limpiar(substr($item['producto'] ?? '', 0, 35)), 1, 0, 'L', $aplicar_fill);
                $pdf->Cell(15, 6, $stock, 1, 0, 'C', $aplicar_fill);
                $pdf->Cell(20, 6, '$' . number_format($precio_compra, 2), 1, 0, 'R', $aplicar_fill);
                $pdf->Cell(20, 6, '$' . number_format($precio_venta, 2), 1, 0, 'R', $aplicar_fill);
                $pdf->Cell(18, 6, '$' . number_format($margen_unitario, 2), 1, 0, 'R', $aplicar_fill);
                $pdf->Cell(28, 6, '$' . number_format($inversion, 2), 1, 0, 'R', $aplicar_fill);
                $pdf->Cell(28, 6, '$' . number_format($ingresos, 2), 1, 0, 'R', $aplicar_fill);
                
                // Color especial para ganancia negativa
                if ($ganancia < 0) {
                    $pdf->SetFillColor(255, 0, 0); // Rojo brillante
                    $pdf->SetTextColor(255, 255, 255);
                }
                $pdf->Cell(25, 6, '$' . number_format($ganancia, 2), 1, 0, 'R', $aplicar_fill || $ganancia < 0);
                
                // Resetear color
                if ($ganancia < 0 || $aplicar_fill) {
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->SetTextColor(0, 0, 0);
                }
                
                $pdf->Cell(25, 6, $limpiar(substr($item['naturaleza'] ?? 'N/A', 0, 12)), 1, 0, 'C', false);
                $pdf->Cell(23, 6, $limpiar($item['estado_stock'] ?? 'Normal'), 1, 1, 'C', false);
                
                // Salto de página si es necesario
                if ($pdf->GetY() > 175) {
                    $pdf->AddPage();
                    
                    // Re-dibujar headers
                    $pdf->SetFont('Arial', 'B', 7);
                    $pdf->SetFillColor(52, 152, 219);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(8, 7, 'ID', 1, 0, 'C', true);
                    $pdf->Cell(48, 7, 'Producto', 1, 0, 'C', true);
                    $pdf->Cell(15, 7, 'Stock', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, 'P. Compra', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, 'P. Venta', 1, 0, 'C', true);
                    $pdf->Cell(18, 7, 'Margen', 1, 0, 'C', true);
                    $pdf->Cell(28, 7, $limpiar('Inversión'), 1, 0, 'C', true);
                    $pdf->Cell(28, 7, 'Ingresos Pot.', 1, 0, 'C', true);
                    $pdf->Cell(25, 7, 'Ganancia', 1, 0, 'C', true);
                    $pdf->Cell(25, 7, 'Naturaleza', 1, 0, 'C', true);
                    $pdf->Cell(23, 7, 'Estado', 1, 1, 'C', true);
                    $pdf->SetFont('Arial', '', 6.5);
                    $pdf->SetTextColor(0, 0, 0);
                }
            }
            
            // RESUMEN FINANCIERO
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(46, 204, 113); // Verde
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, $limpiar('RESUMEN FINANCIERO GENERAL'), 0, 1, 'C', true);
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(0, 0, 0);
            $margen_porcentaje = $inversion_total_general > 0 ? 
                                 ($ganancia_potencial_general / $inversion_total_general) * 100 : 0;
            
            $pdf->Cell(65, 6, $limpiar('Total de productos:'), 0, 0, 'L');
            $pdf->Cell(0, 6, $total_productos, 0, 1, 'L');
            
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(220, 53, 69); // Rojo bootstrap
            $pdf->Cell(65, 6, $limpiar('Inversión total (lo que pagaste):'), 0, 0, 'L');
            $pdf->Cell(0, 6, '$' . number_format($inversion_total_general, 2), 0, 1, 'L');
            
            $pdf->SetTextColor(25, 135, 84); // Verde bootstrap
            $pdf->Cell(65, 6, $limpiar('Ingresos potenciales (si vendes todo):'), 0, 0, 'L');
            $pdf->Cell(0, 6, '$' . number_format($ingresos_potenciales_general, 2), 0, 1, 'L');
            
            $pdf->SetTextColor(13, 110, 253); // Azul bootstrap
            $pdf->Cell(65, 6, $limpiar('Ganancia potencial:'), 0, 0, 'L');
            $pdf->Cell(0, 6, '$' . number_format($ganancia_potencial_general, 2) . 
                             ' (' . number_format($margen_porcentaje, 1) . '% margen)', 0, 1, 'L');
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(65, 6, $limpiar('Productos con stock bajo (<20):'), 0, 0, 'L');
            $pdf->Cell(0, 6, $stock_bajo_count, 0, 1, 'L');
            
            $pdf->Cell(65, 6, $limpiar('Productos sin stock:'), 0, 0, 'L');
            $pdf->Cell(0, 6, $sin_stock_count, 0, 1, 'L');
            
            // FOOTER
            $pdf->SetY(-15);
            $pdf->SetFont('Arial', 'I', 7);
            $pdf->Cell(0, 10, $limpiar('Página ') . $pdf->PageNo() . ' - ' . date('d/m/Y H:i:s'), 0, 0, 'C');
            
            // Output
            $filename = 'inventario_analisis_' . $tipo . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output('D', $filename);
            
        } catch (Exception $e) {
            error_log('Error al exportar PDF: ' . $e->getMessage());
            header('Location: ?ctrl=Cinventario&error=export_pdf_failed');
            exit;
        }
    }

    /**
     * Método simple para compatibilidad (antes en InventarioController.php)
     */
    public function obtenerInventario() {
        if (!$this->inventarioModel) {
            return [];
        }
        return $this->inventarioModel->getInventario();
    }
    
    /**
     * Búsqueda en tiempo real de productos (AJAX)
     */
    public function buscar() {
        header('Content-Type: application/json');
        
        try {
            if (!$this->inventarioModel) {
                throw new Exception('Modelo no disponible');
            }
            
            $termino = $_GET['termino'] ?? '';
            $tipo = $_GET['tipo'] ?? 'perecedero'; // 'perecedero' o 'no_perecedero'
            $categoria = $_GET['categoria'] ?? '';
            $estadoStock = $_GET['estado_stock'] ?? '';
            $orden = $_GET['orden'] ?? '';
            $direccion = $_GET['direccion'] ?? 'ASC';
            
            // Filtros para la búsqueda
            $filtros = [
                'tipo_producto' => $tipo
            ];
            
            // Agregar término de búsqueda si existe
            if (!empty($termino)) {
                $filtros['buscar'] = $termino;
            }
            
            // Agregar filtro de categoría si existe
            if (!empty($categoria)) {
                $filtros['categoria'] = $categoria;
            }
            
            // Agregar filtro de estado de stock si existe
            if (!empty($estadoStock)) {
                $filtros['estado_stock'] = $estadoStock;
            }
            
            // Agregar parámetros de ordenamiento
            if (!empty($orden)) {
                $filtros['orden'] = $orden;
                $filtros['direccion'] = $direccion;
            }
            
            // Obtener productos (sin límite para búsqueda)
            $productos = $this->inventarioModel->getInventarioPaginado(1000, 0, $filtros);
            
            // Enriquecer productos perecederos con datos de lotes
            if ($tipo === 'perecedero') {
                require_once 'models/Mlotes.php';
                $lotesModel = new Mlotes();
                
                foreach ($productos as &$producto) {
                    $resumen = $lotesModel->getResumenLotesPorProducto($producto['idinv']);
                    $producto['lote_proxima_caducidad'] = $resumen['proxima_caducidad'] ?? null;
                    $producto['lote_cantidad_activa'] = $resumen['cantidad_activa'] ?? 0;
                    
                    // Calcular días restantes
                    if ($producto['lote_proxima_caducidad']) {
                        $hoy = new DateTime();
                        $fecha_cad = new DateTime($producto['lote_proxima_caducidad']);
                        $diferencia = $hoy->diff($fecha_cad);
                        $producto['dias_hasta_caducidad'] = $diferencia->invert ? -$diferencia->days : $diferencia->days;
                    } else {
                        $producto['dias_hasta_caducidad'] = null;
                    }
                }
                unset($producto);
            }
            
            echo json_encode([
                'success' => true,
                'productos' => $productos,
                'total' => count($productos)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}
?>