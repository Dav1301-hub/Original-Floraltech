<?php
// Archivo de prueba para verificar si llega el gráfico

header('Content-Type: text/html; charset=utf-8');

echo "<h1>TEST: Verificar datos POST</h1>";

echo "<h2>Datos recibidos:</h2>";
echo "<pre>";
print_r(array_keys($_POST));
echo "</pre>";

if (isset($_POST['grafico_inventario'])) {
    $len = strlen($_POST['grafico_inventario']);
    echo "<p style='color: green; font-weight: bold;'>✅ grafico_inventario RECIBIDO</p>";
    echo "<p>Tamaño: $len caracteres</p>";
    echo "<p>Primeros 100 caracteres: " . htmlspecialchars(substr($_POST['grafico_inventario'], 0, 100)) . "</p>";
    
    // Intentar mostrar la imagen
    echo "<h3>Imagen capturada:</h3>";
    echo "<img src='{$_POST['grafico_inventario']}' style='max-width: 600px; border: 2px solid #ccc;' />";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ grafico_inventario NO RECIBIDO</p>";
}

if (isset($_POST['ids'])) {
    echo "<p>IDs recibidos: " . htmlspecialchars($_POST['ids']) . "</p>";
}
?>
