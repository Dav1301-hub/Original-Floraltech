<?php
require_once __DIR__ . '/models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

try {
    $stmt = $db->query('SELECT COUNT(*) as total FROM usu');
    $row = $stmt->fetch();
    echo "Conexión exitosa. Total de empleados en la tabla usu: " . $row['total'];
} catch (Exception $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
