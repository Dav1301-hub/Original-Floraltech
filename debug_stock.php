<?php
/**
 * Script de diagnóstico para verificar datos de stock
 */

include(__DIR__ . "/models/conexion.php");

try {
    $db = new conexion();
    $conn = $db->get_conexion();
    
    echo "<h1>Diagnóstico de Inventario</h1>";
    
    // 1. Verificar tabla inv existe
    echo "<h2>1. Verificando estructura de tabla inv</h2>";
    $sql = "DESCRIBE inv";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // 2. Contar total de productos
    echo "<h2>2. Total de productos en tabla inv</h2>";
    $sql = "SELECT COUNT(*) as total FROM inv";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total: " . $result['total'] . "<br>";
    
    // 3. Ver distribución de stock
    echo "<h2>3. Distribución de stock</h2>";
    $sql = "SELECT stock, COUNT(*) as cantidad FROM inv GROUP BY stock ORDER BY stock";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>Stock</th><th>Cantidad</th></tr>";
    foreach($results as $row) {
        echo "<tr><td>" . $row['stock'] . "</td><td>" . $row['cantidad'] . "</td></tr>";
    }
    echo "</table>";
    
    // 4. Contar stock crítico (1-9)
    echo "<h2>4. Productos con stock crítico (1-9 unidades)</h2>";
    $sql = "SELECT COUNT(*) as critico FROM inv WHERE stock >= 1 AND stock <= 9";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Stock Crítico: " . $result['critico'] . "<br>";
    
    // 5. Listar productos con stock 1-9
    echo "<h2>5. Productos con stock 1-9</h2>";
    $sql = "SELECT idinv, alimentacion, stock FROM inv WHERE stock >= 1 AND stock <= 9 LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($results) > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Stock</th></tr>";
        foreach($results as $row) {
            echo "<tr><td>" . $row['idinv'] . "</td><td>" . $row['alimentacion'] . "</td><td>" . $row['stock'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<strong>No hay productos con stock 1-9</strong><br>";
    }
    
    // 6. Mostrar distribución de stock más detallada
    echo "<h2>6. Todos los productos ordenados por stock</h2>";
    $sql = "SELECT idinv, alimentacion, stock FROM inv ORDER BY stock ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Stock</th></tr>";
    foreach($results as $row) {
        echo "<tr><td>" . $row['idinv'] . "</td><td>" . $row['alimentacion'] . "</td><td>" . $row['stock'] . "</td></tr>";
    }
    echo "</table>";
    
    // 6. Verificar NULL values
    echo "<h2>6. Verificar valores NULL en stock</h2>";
    $sql = "SELECT COUNT(*) as null_count FROM inv WHERE stock IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Registros con stock NULL: " . $result['null_count'] . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
