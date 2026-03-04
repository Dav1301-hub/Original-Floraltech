<?php
require_once 'models/conexion.php';
try {
    $conexion = (new conexion())->get_conexion();
    $stmt = $conexion->query("DESCRIBE usu");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "COLUMNS IN 'usu' TABLE:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    echo "\nCOLUMNS IN 'empresa' TABLE:\n";
    $stmt = $conexion->query("DESCRIBE empresa");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }

    echo "\nCOLUMNS IN 'vacaciones' TABLE:\n";
    $stmt = $conexion->query("DESCRIBE vacaciones");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }

    echo "\nCOUNT OF ACTIVE VACATIONS:\n";
    $stmt = $conexion->query("SELECT COUNT(*) FROM vacaciones WHERE estado = 'Aprobadas' AND CURDATE() BETWEEN fecha_inicio AND fecha_fin");
    echo "Active (Aprobadas & Today between dates): " . $stmt->fetchColumn() . "\n";
    
    $stmt = $conexion->query("SELECT * FROM vacaciones");
    $vacs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nALL VACATIONS:\n";
    print_r($vacs);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
