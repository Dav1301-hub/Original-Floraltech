<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Mpago.php';

// Conexión a la base de datos
$db = (new Database())->connect();
$model = new Mpago($db);

// Obtener todos los datos reales para el dashboard
$datos = [
    'resumen'      => $model->getResumenGanancias(),
    'ventas'       => $model->getResumenVentas(),
    'costos'       => $model->getResumenCostos(),
    'inventario'   => $model->getResumenInventario(),
    'cuentas'      => $model->getResumenCuentas(),
    'pagos'        => $model->getResumenPagos(),
    'proyecciones' => $model->getResumenProyecciones(),
    'auditoria'    => $model->getResumenAuditoria(),
];

// Puedes agregar lógica para filtros si lo necesitas
$tipo = $_GET['tipo'] ?? 'ganancias';

// Incluir la vista con los datos
include __DIR__ . '/../views/admin/VareportesPagos.php';
