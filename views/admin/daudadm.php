<?php
// auditoria_inventario.php
require_once 'config/database.php'; // Ajusta según tu estructura

class AuditoriaInventario {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function obtenerHistorialInventario($filtros = []) {
        $sql = "SELECT 
                    ih.idhistorial,
                    ih.fecha_cambio,
                    t.nombre as nombre_flor,
                    i.alimentacion,
                    ih.stock_anterior,
                    ih.stock_nuevo,
                    ih.motivo,
                    u.nombre_completo as usuario,
                    u.username
                FROM inv_historial ih
                INNER JOIN inv i ON ih.idinv = i.idinv
                INNER JOIN tflor t ON i.tflor_idtflor = t.idtflor
                LEFT JOIN usu u ON ih.idusu = u.idusu
                WHERE 1=1";
        
        $params = [];
        
        // Filtros
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(ih.fecha_cambio) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(ih.fecha_cambio) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($filtros['id_flor'])) {
            $sql .= " AND t.idtflor = ?";
            $params[] = $filtros['id_flor'];
        }
        
        if (!empty($filtros['id_usuario'])) {
            $sql .= " AND u.idusu = ?";
            $params[] = $filtros['id_usuario'];
        }
        
        $sql .= " ORDER BY ih.fecha_cambio DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function renderizarTablaAuditoria($datos) {
        if (empty($datos)) {
            return '<div class="alert alert-info">No hay registros de auditoría</div>';
        }
        
        $html = '
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Flor</th>
                        <th>Alimentación</th>
                        <th>Stock Anterior</th>
                        <th>Stock Nuevo</th>
                        <th>Diferencia</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($datos as $registro) {
            $diferencia = $registro['stock_nuevo'] - $registro['stock_anterior'];
            $claseDiferencia = $diferencia >= 0 ? 'text-success' : 'text-danger';
            $signo = $diferencia >= 0 ? '+' : '';
            
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($registro['fecha_cambio']) . '</td>
                        <td>' . htmlspecialchars($registro['nombre_flor']) . '</td>
                        <td>' . htmlspecialchars($registro['alimentacion']) . '</td>
                        <td>' . $registro['stock_anterior'] . '</td>
                        <td>' . $registro['stock_nuevo'] . '</td>
                        <td class="' . $claseDiferencia . '">' . $signo . $diferencia . '</td>
                        <td>' . htmlspecialchars($registro['motivo']) . '</td>
                        <td>' . htmlspecialchars($registro['usuario'] ?? 'Sistema') . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>';
        
        return $html;
    }
    
    public function obtenerFiltrosDisponibles() {
        // Obtener lista de flores para filtro
        $flores = $this->conn->query("
            SELECT idtflor, nombre 
            FROM tflor 
            ORDER BY nombre
        ")->fetch_all(MYSQLI_ASSOC);
        
        // Obtener lista de usuarios para filtro
        $usuarios = $this->conn->query("
            SELECT idusu, nombre_completo, username 
            FROM usu 
            WHERE activo = 1 
            ORDER BY nombre_completo
        ")->fetch_all(MYSQLI_ASSOC);
        
        return [
            'flores' => $flores,
            'usuarios' => $usuarios
        ];
    }
}
?>

<!-- Formulario de filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtros de Auditoría</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Fecha Desde</label>
                <input type="date" name="fecha_desde" class="form-control" 
                       value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" 
                       value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Flor</label>
                <select name="id_flor" class="form-select">
                    <option value="">Todas las flores</option>
                    <?php foreach ($filtrosDisponibles['flores'] as $flor): ?>
                        <option value="<?php echo $flor['idtflor']; ?>" 
                            <?php echo (($_GET['id_flor'] ?? '') == $flor['idtflor']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($flor['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Usuario</label>
                <select name="id_usuario" class="form-select">
                    <option value="">Todos los usuarios</option>
                    <?php foreach ($filtrosDisponibles['usuarios'] as $usuario): ?>
                        <option value="<?php echo $usuario['idusu']; ?>" 
                            <?php echo (($_GET['id_usuario'] ?? '') == $usuario['idusu']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($usuario['nombre_completo'] . ' (' . $usuario['username'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                <a href="?" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Resultados -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Historial de Cambios en Inventario</h5>
    </div>
    <div class="card-body">
        <?php
        // Uso de la clase
        $auditoria = new AuditoriaInventario($conn);
        $filtrosDisponibles = $auditoria->obtenerFiltrosDisponibles();
        
        $filtrosAplicados = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'id_flor' => $_GET['id_flor'] ?? '',
            'id_usuario' => $_GET['id_usuario'] ?? ''
        ];
        
        $datosAuditoria = $auditoria->obtenerHistorialInventario($filtrosAplicados);
        echo $auditoria->renderizarTablaAuditoria($datosAuditoria);
        ?>
    </div>
</div>
