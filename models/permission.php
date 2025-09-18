<?php
require_once 'config/db.php'; // Incluye la conexión

require_once 'MDashboardGeneral.php';

$dashboardGeneral = new MDashboardGeneral($conn);
$data = $dashboardGeneral->getKPIs();
?>