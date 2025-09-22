<?php
// Conexión a la base de datos
require_once(__DIR__ . '/../config/database.php');

try {
    $db = new Database();
    $conexion = $db->getConnection();
    
    // Obtener estadísticas del inventario
    $sql_total = "SELECT COUNT(*) as total FROM inv";
    $stmt_total = $conexion->prepare($sql_total);
    $stmt_total->execute();
    $total_productos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos con stock bajo (menos de 20)
    $sql_bajo = "SELECT COUNT(*) as bajo FROM inv WHERE stock < 20";
    $stmt_bajo = $conexion->prepare($sql_bajo);
    $stmt_bajo->execute();
    $stock_bajo = $stmt_bajo->fetch(PDO::FETCH_ASSOC)['bajo'];
    
    // Productos sin stock
    $sql_sin = "SELECT COUNT(*) as sin_stock FROM inv WHERE stock = 0";
    $stmt_sin = $conexion->prepare($sql_sin);
    $stmt_sin->execute();
    $sin_stock = $stmt_sin->fetch(PDO::FETCH_ASSOC)['sin_stock'];
    
    // Valor total del inventario
    $sql_valor = "SELECT SUM(stock * precio) as valor_total FROM inv";
    $stmt_valor = $conexion->prepare($sql_valor);
    $stmt_valor->execute();
    $valor_total = $stmt_valor->fetch(PDO::FETCH_ASSOC)['valor_total'] ?? 0;
    
    // Obtener inventario con información de flores
    $sql_inventario = "
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
        ORDER BY i.stock ASC
    ";
    $stmt_inventario = $conexion->prepare($sql_inventario);
    $stmt_inventario->execute();
    $inventario = $stmt_inventario->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener todas las flores para gestión
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
    $stmt_todas_flores = $conexion->prepare($sql_todas_flores);
    $stmt_todas_flores->execute();
    $todas_las_flores = $stmt_todas_flores->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Error al conectar con la base de datos: " . $e->getMessage();
    $inventario = [];
    $total_productos = 0;
    $stock_bajo = 0;
    $sin_stock = 0;
    $valor_total = 0;
}

// Procesamiento de formularios
$mensaje_exito = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'nuevo_producto':
                    // Validar datos requeridos
                    if (empty($_POST['tflor_idtflor']) || empty($_POST['stock']) || empty($_POST['precio'])) {
                        throw new Exception('Todos los campos marcados con (*) son obligatorios');
                    }
                    
                    // Verificar si la flor ya existe en inventario
                    $sql_verificar = "SELECT idinv FROM inv WHERE tflor_idtflor = :tflor_id";
                    $stmt_verificar = $conexion->prepare($sql_verificar);
                    $stmt_verificar->bindParam(':tflor_id', $_POST['tflor_idtflor'], PDO::PARAM_INT);
                    $stmt_verificar->execute();
                    
                    if ($stmt_verificar->fetch()) {
                        throw new Exception('Esta flor ya existe en el inventario. Use la opción de actualizar stock.');
                    }
                    
                    // Insertar nuevo producto en inventario
                    $sql_insertar = "INSERT INTO inv (tflor_idtflor, stock, precio) VALUES (:tflor_id, :stock, :precio)";
                    $stmt_insertar = $conexion->prepare($sql_insertar);
                    $stmt_insertar->bindParam(':tflor_id', $_POST['tflor_idtflor'], PDO::PARAM_INT);
                    $stmt_insertar->bindParam(':stock', $_POST['stock'], PDO::PARAM_INT);
                    $stmt_insertar->bindParam(':precio', $_POST['precio'], PDO::PARAM_STR);
                    
                    if ($stmt_insertar->execute()) {
                        $mensaje_exito = 'Producto agregado al inventario exitosamente';
                        // Recargar datos para mostrar el nuevo producto
                        header('Location: ?page=inventarios&success=1');
                        exit;
                    } else {
                        throw new Exception('Error al insertar el producto en inventario');
                    }
                    break;
                    
                case 'actualizar_parametros':
                    // Aquí se pueden guardar parámetros en una tabla de configuración
                    $mensaje_exito = 'Parámetros de inventario actualizados correctamente';
                    break;
                    
                case 'nueva_flor':
                    // Validar datos de nueva flor
                    if (empty($_POST['nombre']) || empty($_POST['naturaleza']) || empty($_POST['color'])) {
                        throw new Exception('Nombre, naturaleza y color son obligatorios');
                    }
                    
                    // Verificar si ya existe una flor con el mismo nombre
                    $sql_verificar_flor = "SELECT idtflor FROM tflor WHERE nombre = :nombre";
                    $stmt_verificar_flor = $conexion->prepare($sql_verificar_flor);
                    $stmt_verificar_flor->bindParam(':nombre', $_POST['nombre']);
                    $stmt_verificar_flor->execute();
                    
                    if ($stmt_verificar_flor->fetch()) {
                        throw new Exception('Ya existe una flor con ese nombre');
                    }
                    
                    // Insertar nueva flor
                    $sql_nueva_flor = "INSERT INTO tflor (nombre, naturaleza, color, descripcion) VALUES (:nombre, :naturaleza, :color, :descripcion)";
                    $stmt_nueva_flor = $conexion->prepare($sql_nueva_flor);
                    $stmt_nueva_flor->bindParam(':nombre', $_POST['nombre']);
                    $stmt_nueva_flor->bindParam(':naturaleza', $_POST['naturaleza']);
                    $stmt_nueva_flor->bindParam(':color', $_POST['color']);
                    $stmt_nueva_flor->bindParam(':descripcion', $_POST['descripcion']);
                    
                    if ($stmt_nueva_flor->execute()) {
                        $nuevo_id = $conexion->lastInsertId();
                        
                        // Si se especifica stock y precio, agregar al inventario
                        if (!empty($_POST['stock_inicial']) && !empty($_POST['precio_inicial'])) {
                            $sql_inv = "INSERT INTO inv (tflor_idtflor, stock, precio) VALUES (:tflor_id, :stock, :precio)";
                            $stmt_inv = $conexion->prepare($sql_inv);
                            $stmt_inv->bindParam(':tflor_id', $nuevo_id);
                            $stmt_inv->bindParam(':stock', $_POST['stock_inicial']);
                            $stmt_inv->bindParam(':precio', $_POST['precio_inicial']);
                            $stmt_inv->execute();
                        }
                        
                        $mensaje_exito = 'Nueva flor creada exitosamente';
                        header('Location: ?page=inventarios&success=nueva_flor');
                        exit;
                    }
                    break;
                    
                case 'editar_flor':
                    // Validar ID de flor
                    if (empty($_POST['idtflor'])) {
                        throw new Exception('ID de flor requerido');
                    }
                    
                    // Actualizar datos de la flor
                    $sql_actualizar = "UPDATE tflor SET nombre = :nombre, naturaleza = :naturaleza, color = :color, descripcion = :descripcion WHERE idtflor = :id";
                    $stmt_actualizar = $conexion->prepare($sql_actualizar);
                    $stmt_actualizar->bindParam(':nombre', $_POST['nombre']);
                    $stmt_actualizar->bindParam(':naturaleza', $_POST['naturaleza']);
                    $stmt_actualizar->bindParam(':color', $_POST['color']);
                    $stmt_actualizar->bindParam(':descripcion', $_POST['descripcion']);
                    $stmt_actualizar->bindParam(':id', $_POST['idtflor']);
                    
                    if ($stmt_actualizar->execute()) {
                        // Actualizar inventario si existe
                        if (!empty($_POST['stock']) && !empty($_POST['precio'])) {
                            $sql_update_inv = "UPDATE inv SET stock = :stock, precio = :precio WHERE tflor_idtflor = :tflor_id";
                            $stmt_update_inv = $conexion->prepare($sql_update_inv);
                            $stmt_update_inv->bindParam(':stock', $_POST['stock']);
                            $stmt_update_inv->bindParam(':precio', $_POST['precio']);
                            $stmt_update_inv->bindParam(':tflor_id', $_POST['idtflor']);
                            $stmt_update_inv->execute();
                        }
                        
                        $mensaje_exito = 'Flor actualizada exitosamente';
                        header('Location: ?page=inventarios&success=flor_editada');
                        exit;
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $mensaje_error = $e->getMessage();
    } catch (PDOException $e) {
        $mensaje_error = 'Error de base de datos: ' . $e->getMessage();
    }
}
?>

<main class="container-fluid py-4">
    <h2 class="mb-4 fw-bold">Gestión de Inventario</h2>
    
    <!-- Mensajes de éxito y error -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Tarjetas métricas -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-boxes h2 text-primary"></i>
                    <h6 class="fw-bold mt-2">Total Productos</h6>
                    <div class="h4"><?= $total_productos ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle h2 text-warning"></i>
                    <h6 class="fw-bold mt-2">Stock Bajo</h6>
                    <div class="h4"><?= $stock_bajo ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-times-circle h2 text-danger"></i>
                    <h6 class="fw-bold mt-2">Sin Stock</h6>
                    <div class="h4"><?= $sin_stock ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-dollar-sign h2 text-success"></i>
                    <h6 class="fw-bold mt-2">Valor Total</h6>
                    <div class="h4">$<?= number_format($valor_total, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Inventario -->
    <fieldset class="border rounded p-3 mb-4 bg-light">
        <legend class="float-none w-auto px-2 mb-2 fw-bold"><i class="fas fa-filter"></i> Filtros de Inventario</legend>
        <form class="row g-2 flex-wrap align-items-end" method="get" action="">
            <div class="col-12 col-md-3">
                <label for="categoria" class="form-label mb-1">Naturaleza</label>
                <select id="categoria" name="categoria" class="form-select">
                    <option value="">Todas las naturalezas</option>
                    <option value="Natural">Natural</option>
                    <option value="Artificial">Artificial</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="estado_stock" class="form-label mb-1">Estado de Stock</label>
                <select id="estado_stock" name="estado_stock" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="bajo">Bajo</option>
                    <option value="sin_stock">Sin Stock</option>
                    <option value="normal">Normal</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="buscar" class="form-label mb-1">Buscar</label>
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Nombre de la flor...">
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                <a href="?page=inventarios" class="d-block mt-2 text-secondary">Limpiar Filtros</a>
            </div>
        </form>
    </fieldset>

    <!-- Botones de acción de inventario -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <button class="btn btn-success shadow-sm" onclick="abrirproducto()">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </button>
        <button class="btn btn-info shadow-sm" onclick="abrirproveedor()">
            <i class="fas fa-truck me-2"></i>Proveedores
        </button>
        <button class="btn btn-primary shadow-sm" onclick="abrirReportes()">
            <i class="fas fa-chart-bar me-2"></i>Reportes Inventario
        </button>
        <button class="btn btn-secondary shadow-sm" onclick="abrirParametros()">
            <i class="fas fa-cog me-2"></i>Parámetros Inventario
        </button>
        <button class="btn btn-warning shadow-sm" onclick="gestionarFlores()">
            <i class="fas fa-seedling me-2"></i>Gestión de Flores
        </button>
    </div>

    <!-- Tabla de inventario -->
    <div class="table-responsive">
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
            <tbody>
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
    </div>
</main>

<!-- Modales -->
<!-- Modal Nuevo Producto Mejorado -->
<div class="modal fade" id="modal-nuevo-producto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nuevo Producto al Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="form-nuevo-producto">
                    <input type="hidden" name="accion" value="nuevo_producto">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tag me-1"></i>Seleccionar Flor *</label>
                            <select class="form-select" name="tflor_idtflor" required>
                                <option value="">Selecciona una flor...</option>
                                <?php
                                try {
                                    $sql_flores = "SELECT idtflor, nombre, naturaleza, color FROM tflor ORDER BY nombre";
                                    $stmt_flores = $conexion->prepare($sql_flores);
                                    $stmt_flores->execute();
                                    $flores = $stmt_flores->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($flores as $flor) {
                                        echo "<option value='{$flor['idtflor']}'>{$flor['nombre']} ({$flor['naturaleza']} - {$flor['color']})</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<option value=''>Error al cargar flores</option>";
                                }
                                ?>
                            </select>
                            <small class="text-muted">Selecciona la flor para agregar al inventario</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Inicial *</label>
                            <input type="number" class="form-control" name="stock" min="0" required placeholder="Ej: 50">
                            <small class="text-muted">Cantidad inicial en inventario</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Unitario *</label>
                            <input type="number" step="0.01" class="form-control" name="precio" min="0" required placeholder="0.00">
                            <small class="text-muted">Precio de venta por unidad</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-exclamation-triangle me-1"></i>Stock Mínimo</label>
                            <input type="number" class="form-control" name="stock_minimo" min="0" value="10" placeholder="Ej: 10">
                            <small class="text-muted">Cantidad mínima antes de alerta</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-sticky-note me-1"></i>Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="3" placeholder="Notas adicionales sobre el producto..."></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Agregar al Inventario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gestión de Flores Avanzado -->
<div class="modal fade" id="modal-gestion-flores" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-seedling me-2"></i>Gestión Completa de Flores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Pestañas de navegación -->
                <ul class="nav nav-tabs mb-3" id="floresTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista-flores" type="button" role="tab">
                            <i class="fas fa-list me-1"></i>Lista de Flores
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nueva-tab" data-bs-toggle="tab" data-bs-target="#nueva-flor" type="button" role="tab">
                            <i class="fas fa-plus me-1"></i>Nueva Flor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas-flores" type="button" role="tab">
                            <i class="fas fa-chart-pie me-1"></i>Estadísticas
                        </button>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="floresTabContent">
                    <!-- Lista de todas las flores -->
                    <div class="tab-pane fade show active" id="lista-flores" role="tabpanel">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="buscar-flor" placeholder="🔍 Buscar flores por nombre, naturaleza o color...">
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Naturaleza</th>
                                        <th>Color</th>
                                        <th>En Inventario</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-flores">
                                    <?php foreach ($todas_las_flores as $flor): ?>
                                    <tr data-flor-id="<?= $flor['idtflor'] ?>">
                                        <td><span class="badge bg-secondary"><?= $flor['idtflor'] ?></span></td>
                                        <td><strong><?= htmlspecialchars($flor['nombre']) ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= $flor['naturaleza'] === 'Natural' ? 'success' : 'info' ?>">
                                                <?= htmlspecialchars($flor['naturaleza']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: <?= $flor['color'] === 'Multicolor' ? '#ff6b6b' : ($flor['color'] === 'Blanco' ? '#f8f9fa; color: #000' : '#' . substr(md5($flor['color']), 0, 6)) ?>">
                                                <?= htmlspecialchars($flor['color']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($flor['idinv']): ?>
                                                <i class="fas fa-check text-success"></i> Sí
                                            <?php else: ?>
                                                <i class="fas fa-times text-danger"></i> No
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $flor['stock'] ? $flor['stock'] : '<span class="text-muted">N/A</span>' ?>
                                        </td>
                                        <td>
                                            <?= $flor['precio'] ? '$' . number_format($flor['precio'], 2) : '<span class="text-muted">N/A</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $flor['estado_inventario'] === 'Disponible' ? 'success' : 
                                                ($flor['estado_inventario'] === 'Stock Bajo' ? 'warning' : 
                                                ($flor['estado_inventario'] === 'Sin Stock' ? 'danger' : 'secondary')) 
                                            ?>">
                                                <?= $flor['estado_inventario'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary" onclick="editarFlor(<?= htmlspecialchars(json_encode($flor)) ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if (!$flor['idinv']): ?>
                                                <button class="btn btn-outline-success" onclick="agregarAInventario(<?= $flor['idtflor'] ?>, '<?= htmlspecialchars($flor['nombre']) ?>')" title="Agregar a inventario">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <?php endif; ?>
                                                <button class="btn btn-outline-danger" onclick="eliminarFlor(<?= $flor['idtflor'] ?>, '<?= htmlspecialchars($flor['nombre']) ?>')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Nueva flor -->
                    <div class="tab-pane fade" id="nueva-flor" role="tabpanel">
                        <form method="POST" action="" id="form-nueva-flor">
                            <input type="hidden" name="accion" value="nueva_flor">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-tag me-1"></i>Nombre de la Flor *</label>
                                    <input type="text" class="form-control" name="nombre" required placeholder="Ej: Rosa Roja Premium">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><i class="fas fa-leaf me-1"></i>Naturaleza *</label>
                                    <select class="form-select" name="naturaleza" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Natural">Natural</option>
                                        <option value="Artificial">Artificial</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><i class="fas fa-palette me-1"></i>Color *</label>
                                    <select class="form-select" name="color" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Rojo">Rojo</option>
                                        <option value="Blanco">Blanco</option>
                                        <option value="Rosa">Rosa</option>
                                        <option value="Amarillo">Amarillo</option>
                                        <option value="Morado">Morado</option>
                                        <option value="Azul">Azul</option>
                                        <option value="Multicolor">Multicolor</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><i class="fas fa-file-alt me-1"></i>Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="3" placeholder="Descripción detallada de la flor..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Inicial (Opcional)</label>
                                    <input type="number" class="form-control" name="stock_inicial" min="0" placeholder="Cantidad inicial">
                                    <small class="text-muted">Si especificas stock, se agregará automáticamente al inventario</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Inicial (Opcional)</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_inicial" min="0" placeholder="0.00">
                                    <small class="text-muted">Precio de venta por unidad</small>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="limpiarFormularioFlor()">
                                    <i class="fas fa-eraser me-1"></i>Limpiar
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-seedling me-1"></i>Crear Flor
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Estadísticas -->
                    <div class="tab-pane fade" id="estadisticas-flores" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-seedling fa-2x text-success mb-2"></i>
                                        <h6>Total Flores</h6>
                                        <h4 class="text-success"><?= count($todas_las_flores) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-leaf fa-2x text-primary mb-2"></i>
                                        <h6>Naturales</h6>
                                        <h4 class="text-primary"><?= count(array_filter($todas_las_flores, fn($f) => $f['naturaleza'] === 'Natural')) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-cog fa-2x text-info mb-2"></i>
                                        <h6>Artificiales</h6>
                                        <h4 class="text-info"><?= count(array_filter($todas_las_flores, fn($f) => $f['naturaleza'] === 'Artificial')) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-boxes fa-2x text-warning mb-2"></i>
                                        <h6>En Inventario</h6>
                                        <h4 class="text-warning"><?= count(array_filter($todas_las_flores, fn($f) => $f['idinv'] !== null)) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráfico de distribución por color -->
                        <div class="mt-4">
                            <h6><i class="fas fa-chart-pie me-2"></i>Distribución por Colores</h6>
                            <div class="row">
                                <?php 
                                $colores = array_count_values(array_column($todas_las_flores, 'color'));
                                foreach ($colores as $color => $cantidad): 
                                ?>
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge me-2" style="background-color: <?= $color === 'Multicolor' ? '#ff6b6b' : ($color === 'Blanco' ? '#f8f9fa; color: #000' : '#' . substr(md5($color), 0, 6)) ?>">
                                            <?= htmlspecialchars($color) ?>
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" style="width: <?= ($cantidad / count($todas_las_flores)) * 100 ?>%; background-color: <?= $color === 'Multicolor' ? '#ff6b6b' : ($color === 'Blanco' ? '#6c757d' : '#' . substr(md5($color), 0, 6)) ?>"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2 small"><?= $cantidad ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-proveedores" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría:</label>
                        <select class="form-select">
                            <option value="flores">Flores</option>
                            <option value="plantas">Plantas</option>
                            <option value="accesorios">Accesorios</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono:</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección:</label>
                        <input type="text" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nuevo producto
function abrirproducto() {
    const modal = new bootstrap.Modal(document.getElementById('modal-nuevo-producto'));
    modal.show();
}

// Función para abrir modal de proveedores
function abrirproveedor() {
    const modal = new bootstrap.Modal(document.getElementById('modal-proveedores'));
    modal.show();
}

// Función para abrir reportes de inventario
function abrirReportes() {
    // Crear modal dinámico para reportes
    const modalHTML = `
        <div class="modal fade" id="modal-reportes" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-chart-bar me-2"></i>Reportes de Inventario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button class="btn btn-outline-primary w-100" onclick="generarReporte('stock-bajo')">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Productos con Stock Bajo
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-danger w-100" onclick="generarReporte('sin-stock')">
                                    <i class="fas fa-times-circle me-2"></i>
                                    Productos sin Stock
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-success w-100" onclick="generarReporte('valoracion')">
                                    <i class="fas fa-dollar-sign me-2"></i>
                                    Valoración de Inventario
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-info w-100" onclick="generarReporte('movimientos')">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Movimientos de Stock
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const existingModal = document.getElementById('modal-reportes');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar y mostrar nuevo modal
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('modal-reportes'));
    modal.show();
}

// Función para abrir parámetros de inventario
function abrirParametros() {
    // Crear modal dinámico para parámetros
    const modalHTML = `
        <div class="modal fade" id="modal-parametros" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Parámetros de Inventario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="actualizar_parametros">
                            <div class="mb-3">
                                <label class="form-label">Stock Mínimo para Alerta</label>
                                <input type="number" class="form-control" name="stock_minimo" value="20" min="1">
                                <small class="text-muted">Productos con stock menor a este número aparecerán como "Stock Bajo"</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Días para Reporte de Vencimiento</label>
                                <input type="number" class="form-control" name="dias_vencimiento" value="30" min="1">
                                <small class="text-muted">Días de anticipación para alertas de vencimiento</small>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="alertas_email" id="alertas_email" checked>
                                    <label class="form-check-label" for="alertas_email">
                                        Enviar alertas por email
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const existingModal = document.getElementById('modal-parametros');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar y mostrar nuevo modal
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('modal-parametros'));
    modal.show();
}

// Función para generar reportes específicos
function generarReporte(tipo) {
    let url = '';
    switch(tipo) {
        case 'stock-bajo':
            url = '?page=inventarios&reporte=stock_bajo';
            break;
        case 'sin-stock':
            url = '?page=inventarios&reporte=sin_stock';
            break;
        case 'valoracion':
            url = '?page=inventarios&reporte=valoracion';
            break;
        case 'movimientos':
            url = '?page=inventarios&reporte=movimientos';
            break;
    }
    
    // Abrir reporte en nueva ventana
    window.open(url, '_blank');
}

// Función para mostrar alertas de stock
function verificarStockBajo() {
    const stockBajo = <?= $stock_bajo ?>;
    const sinStock = <?= $sin_stock ?>;
    
    if (stockBajo > 0 || sinStock > 0) {
        let mensaje = '';
        if (sinStock > 0) {
            mensaje += `⚠️ ${sinStock} producto(s) sin stock\n`;
        }
        if (stockBajo > 0) {
            mensaje += `⚠️ ${stockBajo} producto(s) con stock bajo\n`;
        }
        mensaje += '\n¿Deseas ver los reportes de inventario?';
        
        if (confirm(mensaje)) {
            abrirReportes();
        }
    }
}

// Ejecutar verificación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar stock bajo al cargar
    setTimeout(verificarStockBajo, 2000);
    
    // Configurar búsqueda de flores
    const buscarFlor = document.getElementById('buscar-flor');
    if (buscarFlor) {
        buscarFlor.addEventListener('input', function() {
            const busqueda = this.value.toLowerCase();
            const filas = document.querySelectorAll('#tabla-flores tr');
            
            filas.forEach(function(fila) {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(busqueda) ? '' : 'none';
            });
        });
    }
});

// Función para gestionar flores
function gestionarFlores() {
    const modal = new bootstrap.Modal(document.getElementById('modal-gestion-flores'));
    modal.show();
}

// Editar flor inline
function editarFlor(florData) {
    // Cambiar a la pestaña de nueva flor
    const nuevaTab = new bootstrap.Tab(document.getElementById('nueva-tab'));
    nuevaTab.show();
    
    // Rellenar el formulario con los datos existentes
    document.querySelector('input[name="nombre"]').value = florData.nombre;
    document.querySelector('select[name="naturaleza"]').value = florData.naturaleza;
    document.querySelector('select[name="color"]').value = florData.color;
    
    const descripcion = document.querySelector('textarea[name="descripcion"]');
    if (descripcion) {
        descripcion.value = florData.descripcion || '';
    }
    
    // Cambiar el formulario para edición
    const accionInput = document.querySelector('#form-nueva-flor input[name="accion"]');
    accionInput.value = 'editar_flor';
    
    // Agregar campo ID si no existe
    let idInput = document.querySelector('#form-nueva-flor input[name="id_flor"]');
    if (!idInput) {
        idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_flor';
        document.getElementById('form-nueva-flor').appendChild(idInput);
    }
    idInput.value = florData.idtflor;
    
    // Cambiar texto del botón
    const submitBtn = document.querySelector('#form-nueva-flor button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Flor';
    submitBtn.className = 'btn btn-primary';
    
    // Agregar botón para cancelar edición
    if (!document.getElementById('cancelar-edicion')) {
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-secondary';
        cancelBtn.id = 'cancelar-edicion';
        cancelBtn.innerHTML = '<i class="fas fa-times me-1"></i>Cancelar Edición';
        cancelBtn.onclick = cancelarEdicion;
        
        const buttonGroup = document.querySelector('#form-nueva-flor .mt-4 .gap-2');
        buttonGroup.insertBefore(cancelBtn, buttonGroup.firstChild);
    }
}

// Cancelar edición
function cancelarEdicion() {
    // Limpiar formulario
    document.getElementById('form-nueva-flor').reset();
    
    // Restaurar acción
    document.querySelector('#form-nueva-flor input[name="accion"]').value = 'nueva_flor';
    
    // Remover campo ID
    const idInput = document.querySelector('#form-nueva-flor input[name="id_flor"]');
    if (idInput) {
        idInput.remove();
    }
    
    // Restaurar botón
    const submitBtn = document.querySelector('#form-nueva-flor button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-seedling me-1"></i>Crear Flor';
    submitBtn.className = 'btn btn-warning';
    
    // Remover botón cancelar
    const cancelBtn = document.getElementById('cancelar-edicion');
    if (cancelBtn) {
        cancelBtn.remove();
    }
}

// Agregar flor al inventario
function agregarAInventario(idFlor, nombreFlor) {
    if (confirm('¿Deseas agregar "' + nombreFlor + '" al inventario?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const accionInput = document.createElement('input');
        accionInput.name = 'accion';
        accionInput.value = 'agregar_a_inventario';
        
        const idInput = document.createElement('input');
        idInput.name = 'id_flor';
        idInput.value = idFlor;
        
        form.appendChild(accionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Eliminar flor
function eliminarFlor(idFlor, nombreFlor) {
    if (confirm('¿Estás seguro de que deseas eliminar "' + nombreFlor + '"?\n\nEsta acción no se puede deshacer.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const accionInput = document.createElement('input');
        accionInput.name = 'accion';
        accionInput.value = 'eliminar_flor';
        
        const idInput = document.createElement('input');
        idInput.name = 'id_flor';
        idInput.value = idFlor;
        
        form.appendChild(accionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Limpiar formulario de nueva flor
function limpiarFormularioFlor() {
    document.getElementById('form-nueva-flor').reset();
    cancelarEdicion();
}
</script>