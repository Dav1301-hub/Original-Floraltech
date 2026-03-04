<?php
require_once 'models/conexion.php';
try {
    $conexion = (new conexion())->get_conexion();
    // Eliminar proyecciones de prueba con el título genérico
    $stmt = $conexion->prepare("DELETE FROM proyecciones_pagos WHERE titulo = 'Sin proyección activa' OR monto_objetivo = 0");
    $stmt->execute();
    echo "CLEANUP: Deleted dummy projections.\n";
} catch (Exception $e) {
    echo "CLEANUP ERROR: " . $e->getMessage();
}
