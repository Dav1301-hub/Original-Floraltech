<?php
// Añadir pedidos de prueba para octubre 2025
require_once 'models/conexion.php';

echo "<h3>🔧 Añadiendo pedidos de prueba para octubre 2025</h3>";

try {
    $conexion = new Conexion();
    $db = $conexion->getConexion();
    
    // Verificar si ya existen pedidos para octubre 2025
    $pedidosOctubre = $db->query("SELECT COUNT(*) FROM ped WHERE MONTH(fecha_pedido) = 10 AND YEAR(fecha_pedido) = 2025")->fetchColumn();
    
    echo "📊 Pedidos existentes en octubre 2025: $pedidosOctubre<br><br>";
    
    if($pedidosOctubre == 0) {
        echo "➕ Añadiendo pedidos de prueba para octubre 2025...<br><br>";
        
        // Preparar consulta de inserción
        $stmt = $db->prepare("INSERT INTO ped (numped, fecha_pedido, monto_total, cli_idcli, estado, notas) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Datos de pedidos de prueba para octubre 2025
        $pedidosPrueba = [
            ['PED-OCT-001', '2025-10-01 09:15:00', 125.50, 1, 'Completado', 'Pedido de octubre - Ramo de rosas'],
            ['PED-OCT-002', '2025-10-03 14:30:00', 89.75, 2, 'En proceso', 'Pedido de octubre - Arreglo floral'],
            ['PED-OCT-003', '2025-10-05 11:45:00', 156.00, 3, 'Pendiente', 'Pedido de octubre - Bouquet especial'],
            ['PED-OCT-004', '2025-10-07 16:20:00', 67.25, 4, 'Completado', 'Pedido de octubre - Flores para evento'],
            ['PED-OCT-005', '2025-10-10 10:10:00', 234.80, 5, 'Completado', 'Pedido de octubre - Decoración boda'],
            ['PED-OCT-006', '2025-10-12 13:30:00', 45.50, 1, 'Cancelado', 'Pedido de octubre - Cancelado por cliente'],
            ['PED-OCT-007', '2025-10-14 15:45:00', 98.75, 2, 'En proceso', 'Pedido de octubre - Ramo cumpleaños'],
            ['PED-OCT-008', '2025-10-15 08:30:00', 112.00, 3, 'Pendiente', 'Pedido de octubre - Arreglo oficina']
        ];
        
        foreach($pedidosPrueba as $pedido) {
            $stmt->execute($pedido);
            echo "✅ Añadido: {$pedido[0]} - {$pedido[1]} - {$pedido[4]} - \${$pedido[2]}<br>";
        }
        
        echo "<br>🎉 ¡Pedidos de prueba añadidos exitosamente!<br><br>";
        
        // Verificar que se añadieron correctamente
        $nuevosOctubre = $db->query("SELECT COUNT(*) FROM ped WHERE MONTH(fecha_pedido) = 10 AND YEAR(fecha_pedido) = 2025")->fetchColumn();
        echo "📊 Total pedidos en octubre 2025: $nuevosOctubre<br><br>";
        
        // Mostrar resumen por estado
        echo "<h4>📈 Resumen por estado (octubre 2025):</h4>";
        $resumen = $db->query("
            SELECT estado, COUNT(*) as cantidad, SUM(monto_total) as total_monto 
            FROM ped 
            WHERE MONTH(fecha_pedido) = 10 AND YEAR(fecha_pedido) = 2025 
            GROUP BY estado
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($resumen as $item) {
            echo "• {$item['estado']}: {$item['cantidad']} pedidos (\${$item['total_monto']})<br>";
        }
        
    } else {
        echo "✅ Ya existen pedidos para octubre 2025, no es necesario añadir más.<br><br>";
        
        // Mostrar resumen actual
        echo "<h4>📈 Resumen actual (octubre 2025):</h4>";
        $resumen = $db->query("
            SELECT estado, COUNT(*) as cantidad, SUM(monto_total) as total_monto 
            FROM ped 
            WHERE MONTH(fecha_pedido) = 10 AND YEAR(fecha_pedido) = 2025 
            GROUP BY estado
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($resumen as $item) {
            echo "• {$item['estado']}: {$item['cantidad']} pedidos (\${$item['total_monto']})<br>";
        }
    }
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>