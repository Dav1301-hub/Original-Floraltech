<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CinventarioApi {
    private $db;
    
    public function __construct() {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        // Conectar a la base de datos
        require_once __DIR__ . '/../views/config/database.php';
        $db = new Database();
        $this->db = $db->getConnection();
    }
    
    public function getListado() {
        try {
            error_log("=== INICIO getListado ===");
            
            // Obtener parámetros
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? max(10, min(100, intval($_GET['limit']))) : 10;
            $offset = ($page - 1) * $limit;
            
            error_log("Parámetros: page=$page, limit=$limit, offset=$offset");
            
            // Filtros
            $where_conditions = [];
            $params = [];
            
            if (!empty($_GET['buscar'])) {
                $where_conditions[] = "t.nombre LIKE ?";
                $params[] = '%' . $_GET['buscar'] . '%';
            }
            
            if (!empty($_GET['categoria']) && $_GET['categoria'] !== '') {
                $where_conditions[] = "t.naturaleza = ?";
                $params[] = $_GET['categoria'];
            }
            
            if (!empty($_GET['estado_stock'])) {
                switch($_GET['estado_stock']) {
                    case 'bajo':
                        $where_conditions[] = "i.stock BETWEEN 1 AND 19";
                        break;
                    case 'sin_stock':
                        $where_conditions[] = "(i.stock = 0 OR i.stock IS NULL)";
                        break;
                    case 'normal':
                        $where_conditions[] = "i.stock >= 20";
                        break;
                }
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            error_log("WHERE clause: $where_clause");
            
            // Query principal - volver a sintaxis que funcionaba
            $sql = "
                SELECT 
                    i.idinv,
                    t.nombre as producto,
                    i.stock,
                    i.precio,
                    t.naturaleza,
                    t.color,
                    CASE 
                        WHEN i.stock = 0 THEN 'Sin Stock'
                        WHEN i.stock < 20 THEN 'Bajo'
                        ELSE 'Normal'
                    END as estado_stock
                FROM inv i
                INNER JOIN tflor t ON i.tflor_idtflor = t.idtflor
                $where_clause
                ORDER BY i.stock ASC
                LIMIT $limit OFFSET $offset
            ";
            
            error_log("SQL: $sql");
            error_log("Params: " . print_r($params, true));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Productos encontrados: " . count($inventario));
            
            // Contar total para paginación
            $count_sql = "SELECT COUNT(*) as total 
                         FROM inv i
                         INNER JOIN tflor t ON i.tflor_idtflor = t.idtflor
                         $where_clause";
            
            // Para el conteo, solo usar los parámetros de filtros (sin LIMIT/OFFSET)
            $count_params = [];
            
            if (!empty($_GET['buscar'])) {
                $count_params[] = '%' . $_GET['buscar'] . '%';
            }
            
            if (!empty($_GET['categoria']) && $_GET['categoria'] !== '') {
                $count_params[] = $_GET['categoria'];
            }
            
            $count_stmt = $this->db->prepare($count_sql);
            $count_stmt->execute($count_params);
            $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $total_pages = ceil($total / $limit);
            
            error_log("Total: $total, Total pages: $total_pages");
            
            // Generar HTML del listado
            ob_start();
            $this->renderListado($inventario, $page, $total_pages, $total, $limit, $offset);
            $html = ob_get_clean();
            
            error_log("HTML generado, longitud: " . strlen($html));
            
            $response = [
                'success' => true,
                'html' => $html,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_items' => $total,
                    'per_page' => $limit,
                    'offset' => $offset
                ],
                'debug' => [
                    'sql' => $sql,
                    'where_clause' => $where_clause,
                    'params_count' => count($params),
                    'filters' => $_GET,
                    'found_items' => count($inventario)
                ]
            ];
            
            error_log("=== FIN getListado - SUCCESS ===");
            echo json_encode($response);
            
        } catch (Exception $e) {
            error_log("ERROR en getListado: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar listado: ' . $e->getMessage()]);
        }
    }
    
    private function renderListado($inventario, $current_page, $total_pages, $total, $limit, $offset) {
        ?>
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Naturaleza</th>
                    <th>Color</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Precio Unitario</th>
                    <th>Valor Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="inventarioTableBody">
                <?php if (!empty($inventario)): ?>
                    <?php foreach ($inventario as $item): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($item['producto']) ?></td>
                            <td><?= htmlspecialchars($item['naturaleza']) ?></td>
                            <td><?= htmlspecialchars($item['color']) ?></td>
                            <td>
                                <span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning' : 'bg-success') ?>">
                                    <?= $item['stock'] ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $estado_class = '';
                                switch($item['estado_stock']) {
                                    case 'Sin Stock':
                                        $estado_class = 'text-danger';
                                        break;
                                    case 'Bajo':
                                        $estado_class = 'text-warning';
                                        break;
                                    default:
                                        $estado_class = 'text-success';
                                }
                                ?>
                                <span class="<?= $estado_class ?> fw-bold"><?= $item['estado_stock'] ?></span>
                            </td>
                            <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                            <td>$<?= number_format($item['stock'] * $item['precio'], 0, ',', '.') ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" title="Agregar Stock">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-warning py-4">
                            <i class="fas fa-search" style="font-size:2rem;"></i>
                            <h5 class="mt-2">No se encontraron productos</h5>
                            <p>No hay productos en el inventario o no coinciden con los filtros aplicados</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Paginación AJAX actualizada -->
        <?php if ($total_pages > 1): ?>
        <div id="paginationContainer">
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted" id="paginationInfo">
                    Mostrando <?= min($offset + 1, $total) ?> - <?= min($offset + $limit, $total) ?> 
                    de <?= $total ?> productos
                </div>
                
                <nav aria-label="Paginación del inventario">
                    <ul class="pagination pagination-sm mb-0">
                        <!-- Botón anterior -->
                        <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="#" onclick="cargarPagina(<?= $current_page - 1 ?>)" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php
                        // Mostrar páginas
                        $inicio = max(1, $current_page - 2);
                        $fin = min($total_pages, $current_page + 2);
                        
                        // Primera página si no está en el rango visible
                        if ($inicio > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="cargarPagina(1)">1</a>
                            </li>
                            <?php if ($inicio > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php
                        // Páginas en el rango visible
                        for ($i = $inicio; $i <= $fin; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="#" onclick="cargarPagina(<?= $i ?>)"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php
                        // Última página si no está en el rango visible
                        if ($fin < $total_pages): ?>
                            <?php if ($fin < $total_pages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="cargarPagina(<?= $total_pages ?>)"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Botón siguiente -->
                        <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="#" onclick="cargarPagina(<?= $current_page + 1 ?>)" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Selector de elementos por página -->
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2 small">Mostrar:</span>
                    <select class="form-select form-select-sm" style="width: auto;" onchange="cambiarElementosPorPagina(this.value)">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
            </div>
        </div>
        <?php endif;
    }
}

// Manejar la solicitud si se llama directamente
if (isset($_GET['action'])) {
    $controller = new CinventarioApi();
    $action = $_GET['action'];
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Acción no encontrada']);
    }
}
?>