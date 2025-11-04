<?php
// Conexión y obtención de datos reales para la vista de auditoría
try {
    require_once __DIR__ . '/../../models/conexion.php';
    require_once __DIR__ . '/../../models/PagoModel.php';

    $conexion = new conexion();
    $db = $conexion->get_conexion();
    $pagoModel = new PagoModel($db);

    // Resumen general de auditoría (si el modelo lo proporciona)
    $resumenAuditoria = [];
    try {
        if (method_exists($pagoModel, 'getResumenAuditoria')) {
            $resumenAuditoria = $pagoModel->getResumenAuditoria();
        }
    } catch (Exception $e) {
        error_log('Error al obtener resumen de auditoría: ' . $e->getMessage());
        $resumenAuditoria = [];
    }

    // Obtener conteo por tipo de acción para la gráfica
    $accionesPorTipo = [];
    try {
        $stmt = $db->prepare("SELECT tipo, COUNT(*) as cantidad FROM auditoria GROUP BY tipo ORDER BY cantidad DESC");
        $stmt->execute();
        $accionesPorTipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error al obtener acciones por tipo: ' . $e->getMessage());
        $accionesPorTipo = [];
    }

    // Actividad semanal (últimos 7 días)
    $actividadSemanal = [];
    try {
        $stmt = $db->prepare("SELECT DATE(fecha) as dia, COUNT(*) as cantidad FROM auditoria WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(fecha) ORDER BY DATE(fecha)");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Normalizar a los 7 días (llenar con ceros donde no existan registros)
        $dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dias[$d] = 0;
        }
        foreach ($rows as $r) {
            $dias[$r['dia']] = (int)$r['cantidad'];
        }
        $actividadSemanal = $dias;
    } catch (Exception $e) {
        error_log('Error al obtener actividad semanal: ' . $e->getMessage());
        $actividadSemanal = [];
    }

    // Listado detallado (últimos 200 eventos)
    $listadoAuditoria = [];
    try {
        $query = "SELECT a.id as id, COALESCE(u.nombre, a.usuario) as usuario, a.tipo, a.fecha, a.ip, a.descripcion FROM auditoria a LEFT JOIN usu u ON a.usuario_id = u.idusu ORDER BY a.fecha DESC LIMIT 200";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $listadoAuditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error al obtener listado de auditoría: ' . $e->getMessage());
        $listadoAuditoria = [];
    }

    // Bloque de diagnóstico (activable con ?debug_aud=1)
    if (isset($_GET['debug_aud']) && $_GET['debug_aud'] == '1') {
        echo '<div class="container mt-3">';
        echo '<div class="alert alert-info"><strong>Debug auditoría:</strong><pre>';
        try {
            // Verificar existencia de la tabla
            $tab = $db->query("SHOW TABLES LIKE 'auditoria'")->fetchAll(PDO::FETCH_ASSOC);
            echo "SHOW TABLES LIKE 'auditoria' => ";
            var_export($tab);
            echo "\n";

            // Contar filas
            $cnt = $db->query("SELECT COUNT(*) as c FROM auditoria")->fetch(PDO::FETCH_ASSOC);
            echo "Count => ";
            var_export($cnt);
            echo "\n";

            // Últimos registros (limit 5)
            $last = $db->query("SELECT id, usuario_id, usuario, tipo, fecha, ip, descripcion FROM auditoria ORDER BY fecha DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "Últimos 5 => ";
            var_export($last);
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        echo '</pre></div></div>';
    }

} catch (Exception $e) {
    // Si falla la conexión, dejamos estructuras vacías y mostraremos mensaje en la vista
    error_log('Error general en vista auditoría: ' . $e->getMessage());
    $resumenAuditoria = [];
    $accionesPorTipo = [];
    $actividadSemanal = [];
    $listadoAuditoria = [];
}
?>

<main>
    <div class="container-fluid my-4 px-4">
        <h1 class="fw-bold text-center mb-4">Panel de Auditoría</h1>

        <!-- Resumen general -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted">Acciones totales</h6>
                        <h3 class="fw-bold text-primary"><?= isset($resumenAuditoria['acciones']) ? number_format($resumenAuditoria['acciones']) : '0' ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted">Usuarios distintos</h6>
                        <h3 class="fw-bold text-success"><?= isset($resumenAuditoria['usuarios']) ? number_format($resumenAuditoria['usuarios']) : '0' ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted">Incidencias</h6>
                        <h3 class="fw-bold text-warning"><?= isset($resumenAuditoria['incidencias']) ? number_format($resumenAuditoria['incidencias']) : '0' ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-danger border-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted">Última acción</h6>
                        <h3 class="fw-bold text-danger"><?= isset($resumenAuditoria['ultima']) ? htmlspecialchars($resumenAuditoria['ultima']) : '—' ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row g-4">
            <!-- Gráfica de barras -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-bold">
                        Acciones por tipo
                    </div>
                    <div class="card-body">
                        <canvas id="accionesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfica de líneas -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-bold">
                        Actividad semanal
                    </div>
                    <div class="card-body">
                        <canvas id="actividadChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de auditoría -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Registro detallado de eventos
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Fecha</th>
                                    <th>IP</th>
                                    <th>Descripción</th>
                                <tbody>
                                    <?php if (!empty($listadoAuditoria)): ?>
                                        <?php $i = 1; foreach ($listadoAuditoria as $row): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(sprintf('%03d', $i++)) ?></td>
                                                <td><?= htmlspecialchars($row['usuario'] ?? 'Sistema') ?></td>
                                                <td><?= htmlspecialchars($row['tipo']) ?></td>
                                                <td><?= htmlspecialchars($row['fecha']) ?></td>
                                                <td><?= htmlspecialchars($row['ip'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['descripcion'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No se encontraron registros de auditoría.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para la gráfica de acciones por tipo (provenientes de PHP)
        const accionesLabels = <?= json_encode(array_column($accionesPorTipo, 'tipo')) ?>;
        const accionesData = <?= json_encode(array_map(function($r){ return (int)$r['cantidad']; }, $accionesPorTipo)) ?>;

        const ctxAcciones = document.getElementById('accionesChart');
        if (ctxAcciones) {
            new Chart(ctxAcciones, {
                type: 'bar',
                data: {
                    labels: accionesLabels,
                    datasets: [{
                        label: 'Cantidad de acciones',
                        data: accionesData,
                        backgroundColor: accionesLabels.map((_,i) => ['#6a5af9','#4ade80','#f87171','#facc15','#a3a3a3'][i % 5])
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }

        // Datos para actividad semanal
        const actividadLabels = <?= json_encode(array_map(function($d){ return date('D', strtotime($d)); }, array_keys($actividadSemanal))) ?>;
        const actividadData = <?= json_encode(array_values($actividadSemanal)) ?>;

        const ctxActividad = document.getElementById('actividadChart');
        if (ctxActividad) {
            new Chart(ctxActividad, {
                type: 'line',
                data: {
                    labels: actividadLabels,
                    datasets: [{
                        label: 'Eventos registrados',
                        data: actividadData,
                        fill: true,
                        backgroundColor: 'rgba(106,90,249,0.12)',
                        borderColor: '#6a5af9',
                        tension: 0.3
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }
    </script>
</main>
