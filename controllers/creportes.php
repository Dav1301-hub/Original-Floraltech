<?php
// filepath: c:\xampp\htdocs\Floraltech\controllers\creportes.php

require_once(__DIR__ . '/../models/mreportes.php');

$mreportes = new Mreportes();

$idped = isset($_REQUEST['idped']) ? $_REQUEST['idped'] : null;
$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : null;

$dtOne = null;

// Si se solicita ver un solo reporte
if ($ope === "ver" && $idped) {
    $todos = $mreportes->getAll();
    foreach ($todos as $reporte) {
        if ($reporte['idped'] == $idped) {
            $dtOne = $reporte;
            break;
        }
    }
}

// Obtener todos los reportes
$dtAll = $mreportes->getAll();

// --- FILTRO PARA EL MODAL ---
$modalPedidos = $dtAll ?? [];

if (!empty($_GET['fecha_inicio'])) {
    $modalPedidos = array_filter($modalPedidos, function($p) {
        return strtotime($p['fecha_pedido']) >= strtotime($_GET['fecha_inicio']);
    });
}
if (!empty($_GET['fecha_fin'])) {
    $modalPedidos = array_filter($modalPedidos, function($p) {
        return strtotime($p['fecha_pedido']) <= strtotime($_GET['fecha_fin'] . ' 23:59:59');
    });
}
if (!empty($_GET['estado'])) {
    $modalPedidos = array_filter($modalPedidos, function($p) {
        return strtolower($p['estado']) === strtolower($_GET['estado']);
    });
}

// Aquí puedes incluir la vista y pasarle $dtAll y $dtOne
// Ejemplo de uso para pruebas (puedes eliminar estos print_r en producción):
/*
if ($dtOne) {
    echo "<pre>Reporte seleccionado:\n";
    print_r($dtOne);
    echo "</pre>";
}

echo "<pre>Todos los reportes:\n";
print_r($dtAll);
echo "</pre>";
*/
?>