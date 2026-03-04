<?php
header('Content-Type: text/plain');
require_once 'models/conexion.php';

try {
    $conexion = (new conexion())->get_conexion();
    
    $total_inv = (int)$conexion->query("SELECT COUNT(*) FROM inv WHERE stock > 0")->fetchColumn();
    $unique_tflor = (int)$conexion->query("SELECT COUNT(DISTINCT tflor_idtflor) FROM inv WHERE stock > 0")->fetchColumn();
    
    echo "Total registros en inv con stock > 0: $total_inv\n";
    echo "Total tipos de flor únicos en inv con stock > 0: $unique_tflor\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
