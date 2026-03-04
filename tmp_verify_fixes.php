<?php
require_once 'models/conexion.php';
try {
    $conexion = (new conexion())->get_conexion();
    $hoy = '2026-03-04'; // Hoy según metadatos
    
    // Simular lógica de VagestionarEmpleados.php
    $stmt = $conexion->query("SELECT * FROM vacaciones");
    $vacaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $vacaciones_activas = 0;
    foreach ($vacaciones as $vacacion) {
        if (($vacacion['estado'] == 'Aprobada' || $vacacion['estado'] == 'En curso') && 
            $hoy >= $vacacion['fecha_inicio'] && $hoy <= $vacacion['fecha_fin']) {
            $vacaciones_activas++;
        }
    }
    echo "VERIFICATION: Active Vacations Count (Logic matches Vview): " . $vacaciones_activas . "\n";

    // Simular carga de empresa si existiera el error daría fallo aquí
    $stmt = $conexion->query("SELECT email_contacto, horarios_apertura FROM empresa LIMIT 1");
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "VERIFICATION: Empresa columns Loaded: email_contacto=" . $empresa['email_contacto'] . ", horarios_apertura=" . $empresa['horarios_apertura'] . "\n";

} catch (Exception $e) {
    echo "VERIFICATION ERROR: " . $e->getMessage();
}
