<?php
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

// Obtener el pedido que mencionaste (ID 26)
$stmt = $db->prepare('SELECT idped, numped, monto_total FROM ped WHERE idped = 26');
$stmt->execute();
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Pedido ID 26:\n";
echo "  Numped: " . $pedido['numped'] . "\n";
echo "  Monto Total en BD: " . $pedido['monto_total'] . "\n";

// Obtener los productos del pedido
$stmt2 = $db->prepare('
    SELECT 
        dp.iddetped,
        dp.cantidad,
        dp.precio_unitario,
        (dp.cantidad * dp.precio_unitario) AS subtotal
    FROM detped dp
    WHERE dp.idped = 26
');
$stmt2->execute();
$productos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "\nProductos del pedido:\n";
$total_calculado = 0;
foreach ($productos as $prod) {
    echo "  - Cantidad: {$prod['cantidad']}, Precio: {$prod['precio_unitario']}, Subtotal: {$prod['subtotal']}\n";
    $total_calculado += $prod['subtotal'];
}

echo "\nTotal calculado desde productos: " . $total_calculado . "\n";
?>
