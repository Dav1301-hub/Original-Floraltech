<?php
// Test simple para verificar el controlador API
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simular sesión para testing
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'idusu' => 1,
        'tpusu_idtpusu' => 1,
        'nombre_completo' => 'Test Admin'
    ];
}

// Simular parámetros GET
$_GET['action'] = 'getListado';
$_GET['page'] = 1;
$_GET['limit'] = 10;

echo "<h2>Test del Controlador API</h2>";
echo "<p>✅ Sesión usuario configurada</p>";
echo "<p>📋 Parámetros: page=1, limit=10</p>";
echo "<hr>";

try {
    include 'controllers/CinventarioApi.php';
    echo "<p style='color: green;'>✅ Controlador ejecutado - revisa arriba para ver el JSON</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>📍 Trace: " . $e->getTraceAsString() . "</p>";
}
?>