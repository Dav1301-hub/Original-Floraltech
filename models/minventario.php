<?php
require_once __DIR__ . '/conexion.php';

class Minventario {
    private $db;
    
    public function __construct() {
        try {
            $conexion = new conexion();
            $this->db = $conexion->get_conexion();
            
            // Verificar que la conexión sea válida
            if ($this->db === null) {
                throw new Exception("No se pudo establecer conexión con la base de datos");
            }
            
            // Verificar que las tablas necesarias existan
            $this->verificarTablas();
            
        } catch (Exception $e) {
            throw new Exception("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar que las tablas necesarias existan
     */
    private function verificarTablas() {
        $tablas_necesarias = ['inv', 'tflor'];
        
        foreach ($tablas_necesarias as $tabla) {
            $sql = "SHOW TABLES LIKE '$tabla'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch();
            
            if (!$resultado) {
                throw new Exception("La tabla '$tabla' no existe en la base de datos");
            }
        }
    }
    
    /**
     * Obtener estadísticas del inventario
     */
    public function getEstadisticasInventario() {
        try {
            // Total productos
            $sql_total = "SELECT COUNT(*) as total FROM inv";
            $stmt_total = $this->db->prepare($sql_total);
            $stmt_total->execute();
            $total_productos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Productos con stock bajo (menos de 20)
            $sql_bajo = "SELECT COUNT(*) as bajo FROM inv WHERE stock < 20";
            $stmt_bajo = $this->db->prepare($sql_bajo);
            $stmt_bajo->execute();
            $stock_bajo = $stmt_bajo->fetch(PDO::FETCH_ASSOC)['bajo'];
            
            // Productos sin stock
            $sql_sin = "SELECT COUNT(*) as sin_stock FROM inv WHERE stock = 0";
            $stmt_sin = $this->db->prepare($sql_sin);
            $stmt_sin->execute();
            $sin_stock = $stmt_sin->fetch(PDO::FETCH_ASSOC)['sin_stock'];
            
            // Valor total del inventario
            $sql_valor = "SELECT SUM(stock * precio) as valor_total FROM inv";
            $stmt_valor = $this->db->prepare($sql_valor);
            $stmt_valor->execute();
            $valor_total = $stmt_valor->fetch(PDO::FETCH_ASSOC)['valor_total'] ?? 0;
            
            return [
                'total_productos' => $total_productos,
                'stock_bajo' => $stock_bajo,
                'sin_stock' => $sin_stock,
                'valor_total' => $valor_total
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener inventario con paginación
     */
    public function getInventarioPaginado($elementos_por_pagina = 10, $offset = 0, $filtros = []) {
        try {
            $where_conditions = [];
            $params = [];
            
            // Aplicar filtros si existen
            if (!empty($filtros['categoria'])) {
                $where_conditions[] = "COALESCE(t.naturaleza, 'N/A') = :categoria";
                $params[':categoria'] = $filtros['categoria'];
            }
            
            if (!empty($filtros['estado_stock'])) {
                switch($filtros['estado_stock']) {
                    case 'bajo':
                        $where_conditions[] = "i.stock < 20 AND i.stock > 0";
                        break;
                    case 'sin_stock':
                        $where_conditions[] = "i.stock = 0";
                        break;
                    case 'normal':
                        $where_conditions[] = "i.stock >= 20";
                        break;
                }
            }
            
            if (!empty($filtros['buscar'])) {
                $where_conditions[] = "(COALESCE(t.nombre, 'Producto sin nombre') LIKE :buscar OR i.alimentacion LIKE :buscar)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            // Consulta mejorada con LEFT JOIN para mostrar todos los productos del inventario
            $sql_inventario = "
                SELECT 
                    i.idinv,
                    COALESCE(t.nombre, CONCAT('Producto ID-', i.idinv)) as producto,
                    i.stock,
                    i.precio,
                    COALESCE(t.naturaleza, 'Sin clasificar') as naturaleza,
                    COALESCE(t.color, 'Sin especificar') as color,
                    COALESCE(i.alimentacion, 'N/A') as categoria_producto,
                    i.tflor_idtflor,
                    i.fecha_actualizacion,
                    CASE 
                        WHEN i.stock = 0 THEN 'Sin Stock'
                        WHEN i.stock < 20 THEN 'Bajo'
                        ELSE 'Normal'
                    END as estado_stock
                FROM inv i
                LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                {$where_clause}
                ORDER BY i.stock ASC, i.idinv ASC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt_inventario = $this->db->prepare($sql_inventario);
            $stmt_inventario->bindParam(':limit', $elementos_por_pagina, PDO::PARAM_INT);
            $stmt_inventario->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            // Bind filtros
            foreach ($params as $param => $value) {
                $stmt_inventario->bindParam($param, $value);
            }
            
            $stmt_inventario->execute();
            $resultado = $stmt_inventario->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error SQL en getInventarioPaginado: " . $e->getMessage());
            throw new Exception("Error al obtener inventario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener total de elementos para paginación
     */
    public function getTotalElementos($filtros = []) {
        try {
            $where_conditions = [];
            $params = [];
            
            // Aplicar mismos filtros que en getInventarioPaginado
            if (!empty($filtros['categoria'])) {
                $where_conditions[] = "COALESCE(t.naturaleza, 'N/A') = :categoria";
                $params[':categoria'] = $filtros['categoria'];
            }
            
            if (!empty($filtros['estado_stock'])) {
                switch($filtros['estado_stock']) {
                    case 'bajo':
                        $where_conditions[] = "i.stock < 20 AND i.stock > 0";
                        break;
                    case 'sin_stock':
                        $where_conditions[] = "i.stock = 0";
                        break;
                    case 'normal':
                        $where_conditions[] = "i.stock >= 20";
                        break;
                }
            }
            
            if (!empty($filtros['buscar'])) {
                $where_conditions[] = "(COALESCE(t.nombre, 'Producto sin nombre') LIKE :buscar OR i.alimentacion LIKE :buscar)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            $sql = "SELECT COUNT(*) as total FROM inv i LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor {$where_clause}";
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $param => $value) {
                $stmt->bindParam($param, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = (int)$result['total'];
            
            return $total;
            
        } catch (PDOException $e) {
            error_log("Error SQL en getTotalElementos: " . $e->getMessage());
            throw new Exception("Error al contar elementos: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener todas las flores para gestión
     */
    public function getTodasLasFlores() {
        try {
            $sql_todas_flores = "
                SELECT 
                    t.idtflor,
                    t.nombre,
                    t.naturaleza,
                    t.color,
                    t.descripcion,
                    i.stock,
                    i.precio,
                    i.idinv,
                    CASE 
                        WHEN i.idinv IS NULL THEN 'No en inventario'
                        WHEN i.stock = 0 THEN 'Sin Stock'
                        WHEN i.stock < 20 THEN 'Stock Bajo'
                        ELSE 'Disponible'
                    END as estado_inventario
                FROM tflor t
                LEFT JOIN inv i ON t.idtflor = i.tflor_idtflor
                ORDER BY t.nombre
            ";
            $stmt_todas_flores = $this->db->prepare($sql_todas_flores);
            $stmt_todas_flores->execute();
            return $stmt_todas_flores->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener flores: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener lista de flores para select
     */
    public function getFloresParaSelect() {
        try {
            $sql_flores = "SELECT idtflor, nombre, naturaleza, color FROM tflor ORDER BY nombre";
            $stmt_flores = $this->db->prepare($sql_flores);
            $stmt_flores->execute();
            return $stmt_flores->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al cargar flores para select: " . $e->getMessage());
        }
    }
    
    /**
     * Agregar nuevo producto al inventario (flores, chocolates, tarjetas, etc.)
     */
    public function agregarProducto($data) {
        try {
            // Validar datos básicos requeridos
            if (empty($data['nombre_producto']) || empty($data['stock']) || empty($data['precio'])) {
                throw new Exception('Nombre del producto, stock y precio son obligatorios');
            }
            
            $this->db->beginTransaction();
            
            $tflor_id = null;
            
            // Si se seleccionó una flor existente
            if (!empty($data['tflor_idtflor'])) {
                $tflor_id = $data['tflor_idtflor'];
                
                // Verificar si esta flor ya existe en inventario
                $sql_verificar = "SELECT idinv FROM inv WHERE tflor_idtflor = :tflor_id";
                $stmt_verificar = $this->db->prepare($sql_verificar);
                $stmt_verificar->bindParam(':tflor_id', $tflor_id, PDO::PARAM_INT);
                $stmt_verificar->execute();
                
                if ($stmt_verificar->fetch()) {
                    $this->db->rollBack();
                    throw new Exception('Esta flor ya existe en el inventario. Use la opción de actualizar stock.');
                }
            } else {
                // Crear nueva entrada en tflor
                $sql_tflor = "INSERT INTO tflor (nombre, naturaleza, color, descripcion, precio, precio_venta, estado, fecha_creacion, activo) 
                             VALUES (:nombre, :naturaleza, :color, :descripcion, :precio, :precio_venta, :estado, NOW(), 1)";
                $stmt_tflor = $this->db->prepare($sql_tflor);
                $stmt_tflor->bindValue(':nombre', $data['nombre_producto']);
                $stmt_tflor->bindValue(':naturaleza', $data['categoria'] ?? 'No especificado');
                $stmt_tflor->bindValue(':color', $data['color'] ?? 'Multicolor');
                $stmt_tflor->bindValue(':descripcion', $data['descripcion'] ?? '');
                $stmt_tflor->bindValue(':precio', $data['precio']);
                $stmt_tflor->bindValue(':precio_venta', $data['precio']);
                $stmt_tflor->bindValue(':estado', 'activo');
                
                if (!$stmt_tflor->execute()) {
                    $this->db->rollBack();
                    throw new Exception('Error al crear el producto en el catálogo');
                }
                
                $tflor_id = $this->db->lastInsertId();
            }
            
            // Determinar el tipo de alimentación basado en el tipo de producto
            $alimentacion = $this->determinarAlimentacion($data['tipo_producto'] ?? 'otro');
            
            // Insertar producto en inventario
            $sql_inventario = "INSERT INTO inv (tflor_idtflor, stock, precio, alimentacion, fecha_actualizacion, empleado_id, motivo, cantidad_disponible) 
                              VALUES (:tflor_id, :stock, :precio, :alimentacion, NOW(), :empleado_id, :motivo, :stock)";
            $stmt_inventario = $this->db->prepare($sql_inventario);
            $stmt_inventario->bindParam(':tflor_id', $tflor_id, PDO::PARAM_INT);
            $stmt_inventario->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
            $stmt_inventario->bindParam(':precio', $data['precio']);
            $stmt_inventario->bindParam(':alimentacion', $alimentacion);
            $stmt_inventario->bindValue(':empleado_id', $_SESSION['user']['idusu'] ?? null, PDO::PARAM_INT);
            $stmt_inventario->bindValue(':motivo', 'Producto nuevo agregado al inventario');
            
            if ($stmt_inventario->execute()) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                throw new Exception('Error al agregar el producto al inventario');
            }
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Determinar tipo de alimentación basado en el tipo de producto
     */
    private function determinarAlimentacion($tipo) {
        switch (strtolower($tipo)) {
            case 'flor':
                return 'Agua y nutrientes';
            case 'chocolate':
                return 'Ambiente fresco y seco';
            case 'peluche':
                return 'No requiere';
            case 'globo':
                return 'No requiere';
            case 'tarjeta':
                return 'No requiere';
            case 'accesorio':
                return 'No requiere';
            default:
                return 'Según especificaciones';
        }
    }
    
    /**
     * Registrar movimiento en historial de inventario
     */
    private function registrarMovimientoInventario($id_inventario, $stock_anterior, $stock_nuevo, $motivo) {
        try {
            // Verificar si existe la tabla inv_historial, si no, crearla
            $sql_crear_historial = "CREATE TABLE IF NOT EXISTS inv_historial (
                idhistorial INT AUTO_INCREMENT PRIMARY KEY,
                idinv INT NOT NULL,
                stock_anterior INT NOT NULL,
                stock_nuevo INT NOT NULL,
                fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                motivo VARCHAR(255),
                idusu INT,
                FOREIGN KEY (idinv) REFERENCES inv(idinv)
            )";
            $this->db->exec($sql_crear_historial);
            
            // Insertar registro
            $sql_historial = "INSERT INTO inv_historial (idinv, stock_anterior, stock_nuevo, motivo, idusu) 
                            VALUES (:idinv, :stock_anterior, :stock_nuevo, :motivo, :idusu)";
            $stmt_historial = $this->db->prepare($sql_historial);
            $stmt_historial->bindParam(':idinv', $id_inventario, PDO::PARAM_INT);
            $stmt_historial->bindParam(':stock_anterior', $stock_anterior, PDO::PARAM_INT);
            $stmt_historial->bindParam(':stock_nuevo', $stock_nuevo, PDO::PARAM_INT);
            $stmt_historial->bindParam(':motivo', $motivo);
            $stmt_historial->bindValue(':idusu', $_SESSION['user']['idusu'] ?? null, PDO::PARAM_INT);
            
            $stmt_historial->execute();
            
        } catch (PDOException $e) {
            // No lanzar excepción para no interrumpir el proceso principal
            error_log("Error al registrar historial: " . $e->getMessage());
        }
    }
    
    /**
     * Crear nueva flor
     */
    public function crearNuevaFlor($data) {
        try {
            // Validar datos de nueva flor
            if (empty($data['nombre']) || empty($data['naturaleza']) || empty($data['color'])) {
                throw new Exception('Nombre, naturaleza y color son obligatorios');
            }
            
            // Verificar si ya existe una flor con el mismo nombre
            $sql_verificar_flor = "SELECT idtflor FROM tflor WHERE nombre = :nombre";
            $stmt_verificar_flor = $this->db->prepare($sql_verificar_flor);
            $stmt_verificar_flor->bindParam(':nombre', $data['nombre']);
            $stmt_verificar_flor->execute();
            
            if ($stmt_verificar_flor->fetch()) {
                throw new Exception('Ya existe una flor con ese nombre');
            }
            
            // Insertar nueva flor
            $sql_nueva_flor = "INSERT INTO tflor (nombre, naturaleza, color, descripcion) VALUES (:nombre, :naturaleza, :color, :descripcion)";
            $stmt_nueva_flor = $this->db->prepare($sql_nueva_flor);
            $stmt_nueva_flor->bindParam(':nombre', $data['nombre']);
            $stmt_nueva_flor->bindParam(':naturaleza', $data['naturaleza']);
            $stmt_nueva_flor->bindParam(':color', $data['color']);
            $stmt_nueva_flor->bindParam(':descripcion', $data['descripcion']);
            
            if ($stmt_nueva_flor->execute()) {
                $nuevo_id = $this->db->lastInsertId();
                
                // Si se especifica stock y precio, agregar al inventario
                if (!empty($data['stock_inicial']) && !empty($data['precio_inicial'])) {
                    $sql_inv = "INSERT INTO inv (tflor_idtflor, stock, precio) VALUES (:tflor_id, :stock, :precio)";
                    $stmt_inv = $this->db->prepare($sql_inv);
                    $stmt_inv->bindParam(':tflor_id', $nuevo_id);
                    $stmt_inv->bindParam(':stock', $data['stock_inicial']);
                    $stmt_inv->bindParam(':precio', $data['precio_inicial']);
                    $stmt_inv->execute();
                }
                
                return $nuevo_id;
            } else {
                throw new Exception('Error al crear la flor');
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualizar flor existente
     */
    public function actualizarFlor($data) {
        try {
            // Validar ID de flor
            if (empty($data['idtflor'])) {
                throw new Exception('ID de flor requerido');
            }
            
            // Actualizar datos de la flor
            $sql_actualizar = "UPDATE tflor SET nombre = :nombre, naturaleza = :naturaleza, color = :color, descripcion = :descripcion WHERE idtflor = :id";
            $stmt_actualizar = $this->db->prepare($sql_actualizar);
            $stmt_actualizar->bindParam(':nombre', $data['nombre']);
            $stmt_actualizar->bindParam(':naturaleza', $data['naturaleza']);
            $stmt_actualizar->bindParam(':color', $data['color']);
            $stmt_actualizar->bindParam(':descripcion', $data['descripcion']);
            $stmt_actualizar->bindParam(':id', $data['idtflor']);
            
            if ($stmt_actualizar->execute()) {
                // Actualizar inventario si existe
                if (!empty($data['stock']) && !empty($data['precio'])) {
                    $sql_update_inv = "UPDATE inv SET stock = :stock, precio = :precio WHERE tflor_idtflor = :tflor_id";
                    $stmt_update_inv = $this->db->prepare($sql_update_inv);
                    $stmt_update_inv->bindParam(':stock', $data['stock']);
                    $stmt_update_inv->bindParam(':precio', $data['precio']);
                    $stmt_update_inv->bindParam(':tflor_id', $data['idtflor']);
                    $stmt_update_inv->execute();
                }
                
                return true;
            } else {
                throw new Exception('Error al actualizar la flor');
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Eliminar flor
     */
    public function eliminarFlor($id_flor) {
        try {
            // Validar ID de flor
            if (empty($id_flor)) {
                throw new Exception('ID de flor requerido');
            }
            
            // Verificar si la flor está en inventario
            $sql_verificar_inv = "SELECT idinv FROM inv WHERE tflor_idtflor = :tflor_id";
            $stmt_verificar_inv = $this->db->prepare($sql_verificar_inv);
            $stmt_verificar_inv->bindParam(':tflor_id', $id_flor, PDO::PARAM_INT);
            $stmt_verificar_inv->execute();
            
            if ($stmt_verificar_inv->fetch()) {
                throw new Exception('No se puede eliminar la flor porque está en el inventario. Primero remuévela del inventario.');
            }
            
            // Eliminar la flor
            $sql_eliminar = "DELETE FROM tflor WHERE idtflor = :id";
            $stmt_eliminar = $this->db->prepare($sql_eliminar);
            $stmt_eliminar->bindParam(':id', $id_flor, PDO::PARAM_INT);
            
            if ($stmt_eliminar->execute()) {
                return true;
            } else {
                throw new Exception('Error al eliminar la flor');
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Agregar flor al inventario
     */
    public function agregarFlorAInventario($id_flor) {
        try {
            // Validar ID de flor
            if (empty($id_flor)) {
                throw new Exception('ID de flor requerido');
            }
            
            // Verificar si ya existe en inventario
            $sql_verificar = "SELECT idinv FROM inv WHERE tflor_idtflor = :tflor_id";
            $stmt_verificar = $this->db->prepare($sql_verificar);
            $stmt_verificar->bindParam(':tflor_id', $id_flor, PDO::PARAM_INT);
            $stmt_verificar->execute();
            
            if ($stmt_verificar->fetch()) {
                throw new Exception('Esta flor ya está en el inventario');
            }
            
            // Agregar al inventario con valores por defecto
            $sql_agregar = "INSERT INTO inv (tflor_idtflor, stock, precio) VALUES (:tflor_id, 0, 0.00)";
            $stmt_agregar = $this->db->prepare($sql_agregar);
            $stmt_agregar->bindParam(':tflor_id', $id_flor, PDO::PARAM_INT);
            
            if ($stmt_agregar->execute()) {
                return true;
            } else {
                throw new Exception('Error al agregar la flor al inventario');
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear nuevo proveedor
     */
    public function crearProveedor($data) {
        try {
            // Validar datos de proveedor
            if (empty($data['nombre_proveedor']) || empty($data['categoria_proveedor'])) {
                throw new Exception('Nombre y categoría son obligatorios');
            }
            
            // Crear tabla de proveedores si no existe
            $sql_crear_tabla = "CREATE TABLE IF NOT EXISTS proveedores (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                categoria VARCHAR(100) NOT NULL,
                telefono VARCHAR(20),
                email VARCHAR(255),
                direccion TEXT,
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->db->exec($sql_crear_tabla);
            
            // Insertar nuevo proveedor
            $sql_proveedor = "INSERT INTO proveedores (nombre, categoria, telefono, email, direccion) VALUES (:nombre, :categoria, :telefono, :email, :direccion)";
            $stmt_proveedor = $this->db->prepare($sql_proveedor);
            $stmt_proveedor->bindParam(':nombre', $data['nombre_proveedor']);
            $stmt_proveedor->bindParam(':categoria', $data['categoria_proveedor']);
            $stmt_proveedor->bindParam(':telefono', $data['telefono_proveedor']);
            $stmt_proveedor->bindParam(':email', $data['email_proveedor']);
            $stmt_proveedor->bindParam(':direccion', $data['direccion_proveedor']);
            
            if ($stmt_proveedor->execute()) {
                return true;
            } else {
                throw new Exception('Error al agregar el proveedor');
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualizar parámetros de inventario
     */
    public function actualizarParametros($data) {
        try {
            // Crear tabla de configuración si no existe
            $sql_crear_config = "CREATE TABLE IF NOT EXISTS configuracion_inventario (
                id INT AUTO_INCREMENT PRIMARY KEY,
                stock_minimo INT DEFAULT 20,
                dias_vencimiento INT DEFAULT 30,
                alertas_email BOOLEAN DEFAULT TRUE,
                moneda VARCHAR(10) DEFAULT 'USD',
                iva_porcentaje DECIMAL(5,2) DEFAULT 13.00,
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->db->exec($sql_crear_config);
            
            // Verificar si existe configuración
            $sql_verificar_config = "SELECT id FROM configuracion_inventario LIMIT 1";
            $stmt_verificar_config = $this->db->prepare($sql_verificar_config);
            $stmt_verificar_config->execute();
            $config_existe = $stmt_verificar_config->fetch();
            
            if ($config_existe) {
                // Actualizar configuración existente
                $sql_actualizar = "UPDATE configuracion_inventario SET 
                                 stock_minimo = :stock_minimo, 
                                 dias_vencimiento = :dias_vencimiento, 
                                 alertas_email = :alertas_email,
                                 moneda = :moneda,
                                 iva_porcentaje = :iva_porcentaje
                                 WHERE id = :id";
                $stmt_actualizar = $this->db->prepare($sql_actualizar);
                $stmt_actualizar->bindParam(':id', $config_existe['id']);
            } else {
                // Insertar nueva configuración
                $sql_actualizar = "INSERT INTO configuracion_inventario 
                                 (stock_minimo, dias_vencimiento, alertas_email, moneda, iva_porcentaje) 
                                 VALUES (:stock_minimo, :dias_vencimiento, :alertas_email, :moneda, :iva_porcentaje)";
                $stmt_actualizar = $this->db->prepare($sql_actualizar);
            }
            
            $stmt_actualizar->bindParam(':stock_minimo', $data['stock_minimo'], PDO::PARAM_INT);
            $stmt_actualizar->bindParam(':dias_vencimiento', $data['dias_vencimiento'], PDO::PARAM_INT);
            $stmt_actualizar->bindParam(':alertas_email', isset($data['alertas_email']) ? 1 : 0, PDO::PARAM_INT);
            $stmt_actualizar->bindParam(':moneda', $data['moneda'] ?? 'USD');
            $stmt_actualizar->bindParam(':iva_porcentaje', $data['iva_porcentaje'] ?? 13.00);
            
            if ($stmt_actualizar->execute()) {
                return true;
            } else {
                throw new Exception('Error al actualizar los parámetros');
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new Exception('Error de base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener un producto por su ID
     */
    public function obtenerProductoPorId($id) {
        try {
            $sql = "SELECT idinv, producto, naturaleza, color, stock, precio, estado 
                    FROM inv 
                    WHERE idinv = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error al obtener producto: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Editar un producto del inventario
     */
    public function editarProducto($data) {
        try {
            $sql = "UPDATE inv SET 
                    producto = :nombre_producto,
                    naturaleza = :naturaleza,
                    color = :color,
                    stock = :stock,
                    precio = :precio
                    WHERE idinv = :producto_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre_producto', $data['nombre_producto']);
            $stmt->bindParam(':naturaleza', $data['naturaleza']);
            $stmt->bindParam(':color', $data['color']);
            $stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
            $stmt->bindParam(':precio', $data['precio']);
            $stmt->bindParam(':producto_id', $data['producto_id'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Producto actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar el producto'];
            }
        } catch (PDOException $e) {
            error_log('Error al editar producto: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Agregar stock a un producto
     */
    public function agregarStock($id, $cantidad, $motivo = '') {
        try {
            $this->db->beginTransaction();
            
            // Obtener stock actual
            $sql_stock = "SELECT stock FROM inv WHERE idinv = :id";
            $stmt_stock = $this->db->prepare($sql_stock);
            $stmt_stock->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_stock->execute();
            $resultado = $stmt_stock->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Producto no encontrado'];
            }
            
            $nuevo_stock = $resultado['stock'] + $cantidad;
            
            // Actualizar stock
            $sql_update = "UPDATE inv SET stock = :nuevo_stock WHERE idinv = :id";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->bindParam(':nuevo_stock', $nuevo_stock, PDO::PARAM_INT);
            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                // Registrar el movimiento en historial si existe la tabla
                try {
                    $sql_historial = "INSERT INTO inv_historial (producto_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, fecha) 
                                      VALUES (:producto_id, 'ENTRADA', :cantidad, :stock_anterior, :stock_nuevo, :motivo, NOW())";
                    $stmt_historial = $this->db->prepare($sql_historial);
                    $stmt_historial->bindParam(':producto_id', $id, PDO::PARAM_INT);
                    $stmt_historial->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                    $stmt_historial->bindParam(':stock_anterior', $resultado['stock'], PDO::PARAM_INT);
                    $stmt_historial->bindParam(':stock_nuevo', $nuevo_stock, PDO::PARAM_INT);
                    $stmt_historial->bindParam(':motivo', $motivo);
                    $stmt_historial->execute();
                } catch (PDOException $e) {
                    // Si no existe la tabla de historial, continuar sin error
                    error_log('Tabla inv_historial no existe o error: ' . $e->getMessage());
                }
                
                $this->db->commit();
                return ['success' => true, 'message' => 'Stock agregado correctamente'];
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Error al actualizar el stock'];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Error al agregar stock: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar un producto del inventario
     */
    public function eliminarProducto($id) {
        try {
            $sql = "DELETE FROM inv WHERE idinv = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ['success' => true, 'message' => 'Producto eliminado correctamente'];
                } else {
                    return ['success' => false, 'message' => 'Producto no encontrado'];
                }
            } else {
                return ['success' => false, 'message' => 'Error al eliminar el producto'];
            }
        } catch (PDOException $e) {
            error_log('Error al eliminar producto: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
}
?>