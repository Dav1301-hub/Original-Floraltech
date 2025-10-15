<?php
// Test específico para la tabla de pedidos
require_once 'models/conexion.php';

echo "<h3>🔍 Diagnóstico de la tabla 'ped'</h3>";

try {
    $conexion = new Conexion();
    $db = $conexion->getConexion();
    
    // Verificar si existe la tabla ped
    $stmt = $db->query("SHOW TABLES LIKE 'ped'");
    if($stmt->rowCount() > 0) {
        echo "✅ Tabla 'ped' existe<br><br>";
        
        // Mostrar estructura completa
        echo "<h4>📋 Estructura de la tabla 'ped':</h4>";
        $columns = $db->query("DESCRIBE ped")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Contar total de registros
        $totalRegistros = $db->query("SELECT COUNT(*) FROM ped")->fetchColumn();
        echo "<h4>📊 Total de registros en 'ped': $totalRegistros</h4>";
        
        if($totalRegistros > 0) {
            // Mostrar algunos registros de ejemplo
            echo "<h4>📝 Ejemplos de registros (primeros 5):</h4>";
            $ejemplos = $db->query("SELECT * FROM ped LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1' style='border-collapse: collapse;'>";
            if(count($ejemplos) > 0) {
                // Headers
                echo "<tr>";
                foreach(array_keys($ejemplos[0]) as $header) {
                    echo "<th>$header</th>";
                }
                echo "</tr>";
                
                // Data
                foreach($ejemplos as $row) {
                    echo "<tr>";
                    foreach($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table><br>";
            
            // Test específico de las consultas del dashboard
            echo "<h4>🧪 Test de consultas específicas:</h4>";
            
            // Buscar columnas de fecha y estado
            $fechaCol = null;
            $estadoCol = null;
            
            foreach($columns as $column) {
                $field = $column['Field'];
                if(stripos($field, 'fecha') !== false) {
                    $fechaCol = $field;
                    echo "📅 Columna de fecha encontrada: <strong>$field</strong><br>";
                }
                if(stripos($field, 'estado') !== false) {
                    $estadoCol = $field;
                    echo "🏷️ Columna de estado encontrada: <strong>$field</strong><br>";
                }
            }
            
            if($fechaCol) {
                echo "<br><h5>📈 Test consultas por fecha:</h5>";
                $mesActual = date('m');
                $anoActual = date('Y');
                
                try {
                    $pedidosMes = $db->query("SELECT COUNT(*) FROM ped WHERE MONTH($fechaCol) = $mesActual AND YEAR($fechaCol) = $anoActual")->fetchColumn();
                    echo "• Pedidos este mes ($mesActual/$anoActual): <strong>$pedidosMes</strong><br>";
                    
                    // Ver pedidos de cualquier mes para verificar que hay datos
                    $pedidosTotales = $db->query("SELECT COUNT(*) FROM ped WHERE YEAR($fechaCol) = $anoActual")->fetchColumn();
                    echo "• Pedidos este año ($anoActual): <strong>$pedidosTotales</strong><br>";
                    
                    // Ver todos los meses con pedidos
                    $mesesConPedidos = $db->query("SELECT DISTINCT MONTH($fechaCol) as mes, YEAR($fechaCol) as ano, COUNT(*) as cantidad FROM ped GROUP BY YEAR($fechaCol), MONTH($fechaCol) ORDER BY YEAR($fechaCol) DESC, MONTH($fechaCol) DESC")->fetchAll(PDO::FETCH_ASSOC);
                    echo "<br><h6>📅 Meses con pedidos:</h6>";
                    foreach($mesesConPedidos as $periodo) {
                        echo "• {$periodo['mes']}/{$periodo['ano']}: {$periodo['cantidad']} pedidos<br>";
                    }
                    
                } catch(Exception $e) {
                    echo "❌ Error en consulta por fecha: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "<br>❌ No se encontró columna de fecha en la tabla 'ped'<br>";
            }
            
            if($estadoCol) {
                echo "<br><h5>🏷️ Test consultas por estado:</h5>";
                try {
                    $estados = $db->query("SELECT DISTINCT $estadoCol as estado, COUNT(*) as cantidad FROM ped GROUP BY $estadoCol")->fetchAll(PDO::FETCH_ASSOC);
                    echo "Estados disponibles:<br>";
                    foreach($estados as $estado) {
                        echo "• {$estado['estado']}: {$estado['cantidad']} pedidos<br>";
                    }
                } catch(Exception $e) {
                    echo "❌ Error en consulta por estado: " . $e->getMessage() . "<br>";
                }
            } else {
                echo "<br>❌ No se encontró columna de estado en la tabla 'ped'<br>";
            }
            
        } else {
            echo "<h4>⚠️ La tabla 'ped' está vacía</h4>";
        }
        
    } else {
        echo "❌ Tabla 'ped' no existe<br>";
        
        // Buscar tablas similares
        echo "<br><h4>🔍 Buscando tablas similares:</h4>";
        $tablas = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach($tablas as $tabla) {
            if(stripos($tabla, 'ped') !== false || stripos($tabla, 'pedido') !== false || stripos($tabla, 'order') !== false) {
                echo "• Tabla encontrada: <strong>$tabla</strong><br>";
            }
        }
    }
    
} catch(Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>