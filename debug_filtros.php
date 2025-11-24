<?php
/**
 * Script de diagnóstico para revisar los filtros del inventario
 * Ejecuta este archivo directamente en el navegador: http://localhost/Original-Floraltech/debug_filtros.php
 */

require_once 'models/conexion.php';

echo "<h1>🔍 Diagnóstico de Filtros de Inventario</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .section { margin: 30px 0; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
</style>";

try {
    $conexion = new conexion();
    $db = $conexion->get_conexion();
    
    // 1. Mostrar todos los productos del inventario con su naturaleza
    echo "<div class='section'>";
    echo "<h2>📦 Productos en Inventario</h2>";
    
    $sql = "SELECT 
                i.idinv,
                COALESCE(t.nombre, CONCAT('Producto ID-', i.idinv)) as producto,
                i.stock,
                i.precio,
                COALESCE(t.naturaleza, 'Sin clasificar') as naturaleza,
                COALESCE(t.color, 'Sin especificar') as color,
                i.tflor_idtflor
            FROM inv i
            LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
            ORDER BY i.idinv ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p class='success'>✅ Total de productos: " . count($productos) . "</p>";
    
    echo "<table>";
    echo "<tr>
            <th>ID Inv</th>
            <th>Producto</th>
            <th>Stock</th>
            <th>Precio</th>
            <th>Naturaleza</th>
            <th>Color</th>
            <th>ID tflor</th>
          </tr>";
    
    foreach ($productos as $prod) {
        echo "<tr>";
        echo "<td>" . $prod['idinv'] . "</td>";
        echo "<td>" . htmlspecialchars($prod['producto']) . "</td>";
        echo "<td>" . $prod['stock'] . "</td>";
        echo "<td>$" . number_format($prod['precio'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($prod['naturaleza']) . "</td>";
        echo "<td>" . htmlspecialchars($prod['color']) . "</td>";
        echo "<td>" . ($prod['tflor_idtflor'] ?? '<span class="warning">NULL</span>') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // 2. Contar por naturaleza
    echo "<div class='section'>";
    echo "<h2>📊 Distribución por Naturaleza</h2>";
    
    $sql_dist = "SELECT 
                    COALESCE(t.naturaleza, 'Sin clasificar') as naturaleza,
                    COUNT(*) as cantidad
                 FROM inv i
                 LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                 GROUP BY COALESCE(t.naturaleza, 'Sin clasificar')
                 ORDER BY cantidad DESC";
    
    $stmt_dist = $db->prepare($sql_dist);
    $stmt_dist->execute();
    $distribucion = $stmt_dist->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Naturaleza</th><th>Cantidad</th></tr>";
    foreach ($distribucion as $dist) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($dist['naturaleza']) . "</td>";
        echo "<td>" . $dist['cantidad'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // 3. Probar el filtro de "Natural"
    echo "<div class='section'>";
    echo "<h2>🧪 Prueba del Filtro 'Natural'</h2>";
    
    $sql_filtro = "SELECT 
                    i.idinv,
                    COALESCE(t.nombre, CONCAT('Producto ID-', i.idinv)) as producto,
                    COALESCE(t.naturaleza, 'Sin clasificar') as naturaleza
                   FROM inv i
                   LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor
                   WHERE COALESCE(t.naturaleza, 'Sin clasificar') = 'Natural'";
    
    $stmt_filtro = $db->prepare($sql_filtro);
    $stmt_filtro->execute();
    $productos_natural = $stmt_filtro->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p class='success'>✅ Productos con naturaleza 'Natural': " . count($productos_natural) . "</p>";
    
    if (count($productos_natural) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Producto</th><th>Naturaleza</th></tr>";
        foreach ($productos_natural as $prod) {
            echo "<tr>";
            echo "<td>" . $prod['idinv'] . "</td>";
            echo "<td>" . htmlspecialchars($prod['producto']) . "</td>";
            echo "<td>" . htmlspecialchars($prod['naturaleza']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No se encontraron productos con naturaleza 'Natural'</p>";
    }
    echo "</div>";
    
    // 4. Ver tabla tflor
    echo "<div class='section'>";
    echo "<h2>🌸 Tabla tflor (Catálogo de Flores)</h2>";
    
    $sql_tflor = "SELECT idtflor, nombre, naturaleza, color FROM tflor ORDER BY idtflor ASC";
    $stmt_tflor = $db->prepare($sql_tflor);
    $stmt_tflor->execute();
    $flores = $stmt_tflor->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p class='success'>✅ Total de flores en catálogo: " . count($flores) . "</p>";
    
    if (count($flores) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Naturaleza</th><th>Color</th></tr>";
        foreach ($flores as $flor) {
            echo "<tr>";
            echo "<td>" . $flor['idtflor'] . "</td>";
            echo "<td>" . htmlspecialchars($flor['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($flor['naturaleza'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($flor['color'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // 5. Verificar productos sin tflor asociado
    echo "<div class='section'>";
    echo "<h2>⚠️ Productos sin flor asociada</h2>";
    
    $sql_sin_tflor = "SELECT idinv, stock, precio, tflor_idtflor FROM inv WHERE tflor_idtflor IS NULL";
    $stmt_sin_tflor = $db->prepare($sql_sin_tflor);
    $stmt_sin_tflor->execute();
    $sin_tflor = $stmt_sin_tflor->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($sin_tflor) > 0) {
        echo "<p class='warning'>⚠️ Hay " . count($sin_tflor) . " productos sin flor asociada (tflor_idtflor es NULL)</p>";
        echo "<table>";
        echo "<tr><th>ID Inv</th><th>Stock</th><th>Precio</th></tr>";
        foreach ($sin_tflor as $prod) {
            echo "<tr>";
            echo "<td>" . $prod['idinv'] . "</td>";
            echo "<td>" . $prod['stock'] . "</td>";
            echo "<td>$" . number_format($prod['precio'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='warning'>💡 Estos productos aparecerán como 'Sin clasificar' en el filtro</p>";
    } else {
        echo "<p class='success'>✅ Todos los productos tienen una flor asociada</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
