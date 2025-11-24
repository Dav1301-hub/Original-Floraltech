<?php
require_once 'config/database.php';
$conn = $conn ?? (new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME));

// --- AUDITORÍA INVENTARIO ---
function obtenerHistorialInventario($conn, $filtros = []) {
    $sql = "SELECT ih.idhistorial, ih.fecha_cambio, t.nombre as nombre_flor, i.alimentacion, ih.stock_anterior, ih.stock_nuevo, ih.motivo, u.nombre_completo as usuario, u.username FROM inv_historial ih INNER JOIN inv i ON ih.idinv = i.idinv INNER JOIN tflor t ON i.tflor_idtflor = t.idtflor LEFT JOIN usu u ON ih.idusu = u.idusu WHERE 1=1";
    $params = [];
    $types = '';
    if (!empty($filtros['fecha_desde'])) {
        $sql .= " AND DATE(ih.fecha_cambio) >= ?";
        $params[] = $filtros['fecha_desde'];
        $types .= 's';
    }
    if (!empty($filtros['fecha_hasta'])) {
        $sql .= " AND DATE(ih.fecha_cambio) <= ?";
        $params[] = $filtros['fecha_hasta'];
        $types .= 's';
    }
    if (!empty($filtros['id_usuario'])) {
        $sql .= " AND u.idusu = ?";
        $params[] = $filtros['id_usuario'];
        $types .= 's';
    }
    $sql .= " ORDER BY ih.fecha_cambio DESC";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// --- AUDITORÍA PAGOS (SIMULADO, reemplaza por consulta real si tienes tabla de pagos) ---
$auditoriaPagos = [
    [ 'fecha' => '2025-10-30 15:25', 'usuario' => 'maria_vtas', 'modulo' => 'Facturación', 'accion' => 'Crear', 'descripcion' => 'Factura #00423 por $85.000', 'ip' => 'PC-Ventas' ],
    [ 'fecha' => '2025-10-30 15:27', 'usuario' => 'admin', 'modulo' => 'Usuarios', 'accion' => 'Asignar rol', 'descripcion' => 'Se dio rol “Cajero” a usuario “maria_vtas”', 'ip' => 'PC-Admin' ]
];

// --- UNIFICAR AUDITORÍA ---
$filtrosDisponibles = [
    'usuarios' => $conn->query("SELECT idusu, nombre_completo, username FROM usu WHERE activo = 1 ORDER BY nombre_completo")->fetch_all(MYSQLI_ASSOC),
];
$filtrosAplicados = [
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
    'id_usuario' => $_GET['id_usuario'] ?? '',
    'modulo' => $_GET['modulo'] ?? '',
    'accion' => $_GET['accion'] ?? ''
];
$auditoriaInventario = obtenerHistorialInventario($conn, $filtrosAplicados);
$auditoriaGeneral = [];
foreach ($auditoriaInventario as $row) {
    $auditoriaGeneral[] = [
        'fecha' => $row['fecha_cambio'],
        'usuario' => $row['usuario'] ?? 'Sistema',
        'modulo' => 'Inventario',
        'accion' => 'Modificar',
        'descripcion' => 'Stock de "' . $row['nombre_flor'] . '" de ' . $row['stock_anterior'] . ' a ' . $row['stock_nuevo'] . ' (Motivo: ' . $row['motivo'] . ')',
        'ip' => '-',
    ];
}
foreach ($auditoriaPagos as $row) {
    $auditoriaGeneral[] = $row;
}
// Filtros adicionales
$auditoriaFiltrada = array_filter($auditoriaGeneral, function($r) use ($filtrosAplicados) {
    if ($filtrosAplicados['fecha_desde'] && substr($r['fecha'],0,10) < $filtrosAplicados['fecha_desde']) return false;
    if ($filtrosAplicados['fecha_hasta'] && substr($r['fecha'],0,10) > $filtrosAplicados['fecha_hasta']) return false;
    if ($filtrosAplicados['id_usuario'] && $r['usuario'] != $filtrosAplicados['id_usuario']) return false;
    if ($filtrosAplicados['modulo'] && $r['modulo'] != $filtrosAplicados['modulo']) return false;
    if ($filtrosAplicados['accion'] && $r['accion'] != $filtrosAplicados['accion']) return false;
    return true;
});
usort($auditoriaFiltrada, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
?>

<!-- Formulario de filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtros de Auditoría</h5>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="d-flex flex-wrap justify-content-center align-items-end gap-3">
                <div>
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
                </div>
                <div>
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
                </div>
                <div>
                    <label class="form-label">Usuario</label>
                    <select name="id_usuario" class="form-select">
                        <option value="">Todos los usuarios</option>
                        <?php foreach ($filtrosDisponibles['usuarios'] as $usuario): ?>
                            <option value="<?php echo $usuario['nombre_completo']; ?>" <?php echo (($_GET['id_usuario'] ?? '') == $usuario['nombre_completo']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($usuario['nombre_completo'] . ' (' . $usuario['username'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Módulo</label>
                    <select name="modulo" class="form-select">
                        <option value="">Todos</option>
                        <option value="Inventario" <?php if(($_GET['modulo'] ?? '')=='Inventario') echo 'selected'; ?>>Inventario</option>
                        <option value="Facturación" <?php if(($_GET['modulo'] ?? '')=='Facturación') echo 'selected'; ?>>Facturación</option>
                        <option value="Usuarios" <?php if(($_GET['modulo'] ?? '')=='Usuarios') echo 'selected'; ?>>Usuarios</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Acción</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas</option>
                        <option value="Modificar" <?php if(($_GET['accion'] ?? '')=='Modificar') echo 'selected'; ?>>Modificar</option>
                        <option value="Crear" <?php if(($_GET['accion'] ?? '')=='Crear') echo 'selected'; ?>>Crear</option>
                        <option value="Asignar rol" <?php if(($_GET['accion'] ?? '')=='Asignar rol') echo 'selected'; ?>>Asignar rol</option>
                    </select>
                </div>
                <div class="d-flex flex-column justify-content-end">
                    <button type="submit" class="btn btn-primary mb-2">Aplicar Filtros</button>
                    <a href="?" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Gráfica pequeña de estados de pagos (solo si hay datos de pagos) -->
<div class="container my-3" style="max-width:600px;">
    <div class="card shadow-sm mb-3">
        <div class="card-body p-2">
            <canvas id="graficaEstadosPagos" height="80"></canvas>
        </div>
    </div>
</div>
<!-- Resultados -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Auditoría General del Sistema</h5>
    </div>
    <div class="card-body">
        <?php if (empty($auditoriaFiltrada)): ?>
            <div class="alert alert-info">No hay registros de auditoría</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP/Equipo</th>
                        <th>🔍</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; foreach ($auditoriaFiltrada as $registro): ?>
                    <tr style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalDetalle<?php echo $i; ?>">
                        <td><?php echo htmlspecialchars($registro['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($registro['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($registro['modulo']); ?></td>
                        <td><?php echo htmlspecialchars($registro['accion']); ?></td>
                        <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($registro['ip']); ?></td>
                        <td><button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetalle<?php echo $i; ?>">Ver</button></td>
                    </tr>
                    <!-- Modal Detalle -->
                    <div class="modal fade" id="modalDetalle<?php echo $i; ?>" tabindex="-1" aria-labelledby="modalDetalleLabel<?php echo $i; ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="modalDetalleLabel<?php echo $i; ?>">Detalle del Evento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                          </div>
                          <div class="modal-body">
                            <p><b>🗂️ Módulo:</b> <?php echo htmlspecialchars($registro['modulo']); ?></p>
                            <p><b>🕒 Fecha:</b> <?php echo htmlspecialchars($registro['fecha']); ?></p>
                            <p><b>👤 Usuario:</b> <?php echo htmlspecialchars($registro['usuario']); ?></p>
                            <p><b>⚙️ Acción:</b> <?php echo htmlspecialchars($registro['accion']); ?></p>
                            <p><b>📝 Descripción:</b> <?php echo htmlspecialchars($registro['descripcion']); ?></p>
                            <p><b>💻 IP/Dispositivo:</b> <?php echo htmlspecialchars($registro['ip']); ?></p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Contar estados de pagos en la auditoría
const auditoria = <?php echo json_encode($auditoriaFiltrada); ?>;
const estados = ['Aprobado','Pendiente','Fallido','Reembolsado'];
const colores = ['#2ecc40','#00bcd4','#e74c3c','#f1c40f'];
let conteo = [0,0,0,0];
auditoria.forEach(r => {
    if(r.modulo === 'Facturación' && r.accion) {
        if(r.accion === 'Aprobado') conteo[0]++;
        if(r.accion === 'Pendiente') conteo[1]++;
        if(r.accion === 'Fallido') conteo[2]++;
        if(r.accion === 'Reembolsado') conteo[3]++;
    }
});
const ctx = document.getElementById('graficaEstadosPagos').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: estados,
        datasets: [{
            label: 'Cantidad',
            data: conteo,
            backgroundColor: colores
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision:0 } } },
        responsive: true,
        maintainAspectRatio: false,
        height: 80
    }
});
</script>
