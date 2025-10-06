<?php
echo "<h2>🔧 Diagnóstico de Conexión a la Base de Datos</h2>";

// 1. Verificar configuración
echo "<h3>1. Verificando configuración...</h3>";
include("models/data.php");
echo "✅ Host: $host<br>";
echo "✅ Base de datos: $db<br>";
echo "✅ Usuario: $user<br>";
echo "✅ Contraseña: " . (empty($pass) ? "vacía" : "configurada") . "<br><br>";

// 2. Verificar si MySQL está ejecutándose
echo "<h3>2. Verificando servidor MySQL...</h3>";
$handle = @fsockopen($host, 3306, $errno, $errstr, 5);
if ($handle) {
    echo "✅ MySQL está ejecutándose en $host:3306<br>";
    fclose($handle);
} else {
    echo "❌ MySQL NO está ejecutándose en $host:3306<br>";
    echo "Error: $errstr ($errno)<br>";
    echo "<strong>Solución:</strong> Inicia XAMPP y asegúrate de que MySQL esté corriendo.<br><br>";
    exit;
}

// 3. Intentar conexión sin especificar base de datos
echo "<h3>3. Probando conexión al servidor...</h3>";
try {
    $pdo_test = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
    echo "✅ Conexión al servidor MySQL exitosa<br>";
    
    // 4. Verificar si la base de datos existe
    echo "<h3>4. Verificando base de datos...</h3>";
    $stmt = $pdo_test->query("SHOW DATABASES LIKE '$db'");
    if ($stmt->rowCount() > 0) {
        echo "✅ La base de datos '$db' existe<br>";
        
        // 5. Probar conexión completa
        echo "<h3>5. Probando conexión completa...</h3>";
        $pdo_full = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        echo "✅ Conexión completa exitosa<br>";
        
        // 6. Verificar tablas principales
        echo "<h3>6. Verificando tablas...</h3>";
        $tables = ['inv', 'tflor', 'usu'];
        foreach ($tables as $table) {
            $stmt = $pdo_full->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $count_stmt = $pdo_full->query("SELECT COUNT(*) as count FROM $table");
                $count = $count_stmt->fetch()['count'];
                echo "✅ Tabla '$table' existe con $count registros<br>";
            } else {
                echo "⚠️ Tabla '$table' NO existe<br>";
            }
        }
        
        echo "<br><h3>🎉 ¡Todo parece estar funcionando correctamente!</h3>";
        echo "<a href='index.php' class='btn btn-success'>Volver a la aplicación</a>";
        
    } else {
        echo "❌ La base de datos '$db' NO existe<br>";
        echo "<strong>Solución:</strong> Crea la base de datos usando phpMyAdmin o ejecuta:<br>";
        echo "<code>CREATE DATABASE $db;</code><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<strong>Problema:</strong> Usuario o contraseña incorrectos<br>";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<strong>Problema:</strong> MySQL no está ejecutándose<br>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h2 { color: #007bff; }
h3 { color: #28a745; margin-top: 20px; }
code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
.btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
</style>