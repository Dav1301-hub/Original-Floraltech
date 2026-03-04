<?php
header('Content-Type: text/plain');
require_once 'models/conexion.php';

try {
    $conexion = (new conexion())->get_conexion();
    
    $tables = ['pagos', 'ped', 'inv', 'tflor', 'proyecciones_pagos', 'cli', 'usu', 'tpusu'];
    
    foreach ($tables as $table) {
        echo "--- TABLE: $table ---\n";
        try {
            $stmt = $conexion->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $col) {
                echo "  {$col['Field']} ({$col['Type']})\n";
            }
        } catch (Exception $e) {
            echo "  ERROR: Table '$table' does not exist or cannot be accessed.\n";
        }
        echo "\n";
    }

    // Check recent entries in proyecciones_pagos
    echo "--- RECENT PROYECCIONES ---\n";
    try {
        $stmt = $conexion->query("SELECT * FROM proyecciones_pagos ORDER BY fecha_creacion DESC LIMIT 2");
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($res);
    } catch (Exception $e) {
        echo "  Table 'proyecciones_pagos' might be missing.\n";
    }

} catch (Exception $e) {
    echo "CONNECTION ERROR: " . $e->getMessage();
}
