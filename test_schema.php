<?php
// Test database schema
require_once 'models/conexion.php';

echo "<h3>Verificación de estructura de base de datos</h3>";

try {
    $conexion = new Conexion();
    $db = $conexion->getConexion();
    
    // Verificar existencia de tablas principales
    $tables = ['pagos', 'usu', 'ped', 'inv'];
    foreach($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if($stmt->rowCount() > 0) {
            echo "✅ Tabla '$table' existe<br>";
            
            // Mostrar estructura de cada tabla
            $columns = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
            echo "<details><summary>Ver columnas de $table</summary>";
            echo "<ul>";
            foreach($columns as $column) {
                echo "<li>{$column['Field']} ({$column['Type']})</li>";
            }
            echo "</ul></details><br>";
        } else {
            echo "❌ Tabla '$table' no existe<br>";
        }
    }
    
    // Test específico para columnas críticas
    echo "<hr><h4>Test de consultas críticas:</h4>";
    
    // Test count pagos
    try {
        $totalPagos = $db->query("SELECT COUNT(*) FROM pagos")->fetchColumn();
        echo "✅ Total pagos: $totalPagos<br>";
    } catch(Exception $e) {
        echo "❌ Error en COUNT pagos: " . $e->getMessage() . "<br>";
    }
    
    // Test estado_pag column
    try {
        $stmt = $db->query("SHOW COLUMNS FROM pagos LIKE 'estado_pag'");
        if($stmt->rowCount() > 0) {
            $pagosPendientes = $db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente'")->fetchColumn();
            echo "✅ Pagos pendientes: $pagosPendientes<br>";
        } else {
            echo "❌ Columna 'estado_pag' no existe en tabla pagos<br>";
        }
    } catch(Exception $e) {
        echo "❌ Error verificando estado_pag: " . $e->getMessage() . "<br>";
    }
    
    // Test usuarios
    try {
        $totalUsuarios = $db->query("SELECT COUNT(*) FROM usu")->fetchColumn();
        echo "✅ Total usuarios: $totalUsuarios<br>";
    } catch(Exception $e) {
        echo "❌ Error en COUNT usuarios: " . $e->getMessage() . "<br>";
    }
    
    // Test fecha_registro column
    try {
        $stmt = $db->query("SHOW COLUMNS FROM usu LIKE 'fecha_registro'");
        if($stmt->rowCount() > 0) {
            $nuevosUsuarios = $db->query("SELECT COUNT(*) FROM usu WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            echo "✅ Nuevos usuarios (7 días): $nuevosUsuarios<br>";
        } else {
            echo "❌ Columna 'fecha_registro' no existe en tabla usu<br>";
        }
    } catch(Exception $e) {
        echo "❌ Error verificando fecha_registro: " . $e->getMessage() . "<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>