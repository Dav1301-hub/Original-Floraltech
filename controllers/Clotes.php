<?php
require_once 'models/Mlotes.php';

class Clotes {
    private $lotesModel;

    public function __construct() {
        // Verificar sesión
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?ctrl=login');
            exit();
        }

        $this->lotesModel = new Mlotes();
    }

    /**
     * Obtener lotes de un producto (AJAX)
     */
    public function obtenerLotes() {
        header('Content-Type: application/json');
        
        try {
            $inv_idinv = $_GET['inv_idinv'] ?? null;
            
            if (!$inv_idinv) {
                echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
                exit;
            }

            $lotes = $this->lotesModel->getLotesPorProducto($inv_idinv);
            $resumen = $this->lotesModel->getResumenLotesPorProducto($inv_idinv);
            
            echo json_encode([
                'success' => true,
                'lotes' => $lotes,
                'resumen' => $resumen
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Crear nuevo lote (AJAX)
     */
    public function crearLote() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $datos = [
                'inv_idinv' => $_POST['inv_idinv'] ?? null,
                'numero_lote' => $_POST['numero_lote'] ?? null,
                'cantidad' => $_POST['cantidad'] ?? null,
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d'),
                'fecha_caducidad' => $_POST['fecha_caducidad'] ?? null,
                'proveedor' => $_POST['proveedor'] ?? null,
                'precio_compra' => $_POST['precio_compra'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? null
            ];

            // Log para debug
            error_log('Datos recibidos para crear lote: ' . print_r($datos, true));

            $resultado = $this->lotesModel->crearLote($datos);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log('Error al crear lote: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Obtener un lote específico por ID (AJAX)
     */
    public function obtenerLote() {
        header('Content-Type: application/json');
        
        try {
            $idlote = $_GET['idlote'] ?? null;
            
            if (!$idlote) {
                throw new Exception('ID de lote requerido');
            }
            
            $lote = $this->lotesModel->getLotePorId($idlote);
            
            if ($lote) {
                echo json_encode([
                    'success' => true,
                    'lote' => $lote
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lote no encontrado'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Actualizar lote (AJAX)
     */
    public function actualizarLote() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $idlote = $_POST['idlote'] ?? null;
            
            if (!$idlote) {
                throw new Exception('ID de lote requerido');
            }

            $datos = [
                'numero_lote' => $_POST['numero_lote'] ?? null,
                'cantidad' => $_POST['cantidad'] ?? null,
                'fecha_caducidad' => $_POST['fecha_caducidad'] ?? null,
                'proveedor' => $_POST['proveedor'] ?? null,
                'precio_compra' => $_POST['precio_compra'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo',
                'observaciones' => $_POST['observaciones'] ?? null
            ];

            $resultado = $this->lotesModel->actualizarLote($idlote, $datos);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Eliminar lote (AJAX)
     */
    public function eliminarLote() {
        header('Content-Type: application/json');
        
        try {
            $idlote = $_POST['idlote'] ?? $_GET['idlote'] ?? null;
            
            if (!$idlote) {
                throw new Exception('ID de lote requerido');
            }

            $resultado = $this->lotesModel->eliminarLote($idlote);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Obtener lotes próximos a caducar
     */
    public function lotesProximosCaducar() {
        header('Content-Type: application/json');
        
        try {
            $dias = $_GET['dias'] ?? 7;
            $lotes = $this->lotesModel->getLotesProximosCaducar($dias);
            
            echo json_encode([
                'success' => true,
                'lotes' => $lotes
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Generar número de lote sugerido
     */
    public function generarNumeroLote() {
        header('Content-Type: application/json');
        
        try {
            $inv_idinv = $_GET['inv_idinv'] ?? null;
            
            if (!$inv_idinv) {
                throw new Exception('ID de producto requerido');
            }
            
            $numeroLote = $this->lotesModel->generarNumeroLote($inv_idinv);
            
            echo json_encode([
                'success' => true,
                'numero_lote' => $numeroLote
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Sincronizar stock de un producto con sus lotes activos
     */
    public function sincronizarStock() {
        header('Content-Type: application/json');
        
        try {
            $inv_idinv = $_GET['inv_idinv'] ?? $_POST['inv_idinv'] ?? null;
            
            if (!$inv_idinv) {
                throw new Exception('ID de producto requerido');
            }
            
            $resultado = $this->lotesModel->sincronizarStockProducto($inv_idinv);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Sincronizar stock de TODOS los productos
     */
    public function sincronizarTodosStocks() {
        header('Content-Type: application/json');
        
        try {
            $resultado = $this->lotesModel->sincronizarTodosLosStocks();
            echo json_encode($resultado);
            
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
