<?php
header('Content-Type: text/plain');
require_once 'models/conexion.php';
require_once 'models/AuditoriaModel.php';

try {
    $conexion = (new conexion())->get_conexion();
    $model = new AuditoriaModel($conexion);
    
    echo "--- AUDIT VALUES ---\n";
    $resumen = $model->resumenAuditoriaPagos();
    echo "Resumen (acciones, usuarios, ultima, incidencias):\n";
    print_r($resumen);
    
    echo "\nAcciones por Estado:\n";
    print_r($model->accionesPorEstado());
    
    echo "\nUsuarios (Totales, Activos, Activos Hoy):\n";
    print_r($model->usuariosActivosResumen());
    
    echo "\nProductos Activos (Conteo, Detalle - primeros 3):\n";
    [$count, $detalle] = $model->productosActivosResumen();
    echo "Count: $count\n";
    print_r(array_slice($detalle, 0, 3));
    
    echo "\nPagos Mes Actual:\n";
    print_r($model->pagosMes());
    
    echo "\nProyeccion Activa:\n";
    print_r($model->proyeccionActiva());

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
