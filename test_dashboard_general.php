<?php
// Test específico para Dashboard General
require_once 'controllers/CDashboardGeneral.php';

echo "<h2>🧪 Test Dashboard General</h2>";

try {
    // Instanciar controlador
    $controller = new CDashboardGeneral();
    
    echo "<h3>✅ Controller CDashboardGeneral instanciado correctamente</h3>";
    
    // Obtener datos del dashboard
    $dashboardData = $controller->getDashboardData();
    
    echo "<h3>📊 Datos obtenidos del Dashboard:</h3>";
    echo "<pre>";
    print_r($dashboardData);
    echo "</pre>";
    
    echo "<hr>";
    echo "<h3>🔍 Detalle por secciones:</h3>";
    
    if(isset($dashboardData['kpis'])) {
        echo "<h4>📈 KPIs:</h4>";
        foreach($dashboardData['kpis'] as $key => $value) {
            echo "• $key: $value<br>";
        }
    }
    
    if(isset($dashboardData['tendencias'])) {
        echo "<h4>📊 Tendencias:</h4>";
        foreach($dashboardData['tendencias'] as $key => $value) {
            echo "• $key: $value%<br>";
        }
    }
    
    if(isset($dashboardData['actividadReciente'])) {
        echo "<h4>🕒 Actividad Reciente:</h4>";
        foreach($dashboardData['actividadReciente'] as $actividad) {
            echo "• {$actividad['fecha']}: {$actividad['descripcion']}<br>";
        }
    }
    
    if(isset($dashboardData['resumenPedidos'])) {
        echo "<h4>📦 Resumen Pedidos:</h4>";
        foreach($dashboardData['resumenPedidos'] as $key => $value) {
            echo "• $key: $value<br>";
        }
    }
    
} catch(Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>