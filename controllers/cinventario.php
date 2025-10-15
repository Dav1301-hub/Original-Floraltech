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
    private $sin_stock = 0;
    private $valor_total = 0;
    private $inventario = [];
    private $total_elementos = 0;
    private $total_paginas = 1;
    private $todas_las_flores = [];
    private $flores_para_select = [];
    private $elementos_por_pagina = 10;
    private $pagina_actual = 1;
    private $offset = 0;

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
                        $this->inventarioModel->agregarProducto($_POST);
                        $this->mensaje_exito = 'Producto agregado al inventario exitosamente';
                        header('Location: ?ctrl=Cinventario&success=1');
                        exit;
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
            // Configuración de paginación
            $this->elementos_por_pagina = isset($_GET['per_page']) ? max(10, min(100, intval($_GET['per_page']))) : 10;
            $this->pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $this->offset = ($this->pagina_actual - 1) * $this->elementos_por_pagina;
            
            // Obtener filtros
            $filtros = [
                'categoria' => $_GET['categoria'] ?? '',
                'estado_stock' => $_GET['estado_stock'] ?? '',
                'buscar' => $_GET['buscar'] ?? ''
            ];
            
            // Obtener estadísticas
            $this->cargarEstadisticas();
            
            // Obtener inventario paginado
            $this->cargarInventario($filtros);
            
            // Obtener total de elementos y calcular páginas
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
        $sin_stock = $this->sin_stock;
        $valor_total = $this->valor_total;
        
        // Variables de inventario - siempre definidas
        $inventario = $this->inventario;
        $total_elementos = $this->total_elementos;
        $total_paginas = $this->total_paginas;
        $todas_las_flores = $this->todas_las_flores;
        $flores_para_select = $this->flores_para_select;
        
        // Variables de paginación - siempre definidas
        $elementos_por_pagina = $this->elementos_por_pagina;
        $pagina_actual = $this->pagina_actual;
        $offset = $this->offset;
        
        // Variables para el layout
        $usu = $_SESSION['user'];
        $page = 'inventario'; // Usar un nombre diferente que no active la redirección
        $totalUsuarios = 0; // Variable requerida por el dashboard
        
        // Cargar el dashboard de admin con la vista del inventario
        include 'views/admin/VadashboardPrincipal.php';
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
}
?>