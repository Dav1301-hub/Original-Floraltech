<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/../models/mreportes.php';

use Mpdf\Mpdf;

$mreportes = new Mreportes();

// ---------------------- //
//  📄 PDF DE USUARIOS
// ---------------------- //
// ---------------------- //
// 👥 PDF DE USUARIOS
// ---------------------- //
if (isset($_POST['accion']) && $_POST['accion'] === 'usuarios_pdf') {

    // IDs seleccionados desde el formulario
    $ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : [];
    $ids = array_filter($ids); // elimina vacíos

    // Obtener todos los usuarios (puedes pasar $tipo si tu modelo lo soporta)
    $usuarios = $mreportes->getAllusu($tipo);

    // Filtrar solo los seleccionados
    $usuariosSeleccionados = array_filter($usuarios, function($u) use ($ids) {
        return in_array((string)$u['idusu'], $ids, true);
    });

    // Generar HTML del PDF
    $html = '
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #2C3E50; font-size: 12px; }
        h1 { text-align: center; color: #1A5276; border-bottom: 2px solid #1A5276; padding-bottom: 10px; margin-bottom: 30px; }
        p { text-align: right; color: #555; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; box-shadow: 0 0 6px rgba(0,0,0,0.1); }
        thead { background-color: #2471A3; color: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        tr:nth-child(even) { background-color: #f8f9f9; }
        tr:hover { background-color: #EBF5FB; }
    </style>

    <h1>👥 Reporte de Usuarios Seleccionados</h1>
    <p>Generado el ' . date("d/m/Y H:i") . '</p>';

    if ($tipo) {
        $html .= '<p>Filtro aplicado: <strong>' . htmlspecialchars($tipo) . '</strong></p>';
    }

    $html .= '
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Nombre completo</th>
                <th>Tipo</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Activo</th>
            </tr>
        </thead>
        <tbody>';

    if (!empty($usuariosSeleccionados)) {
        foreach ($usuariosSeleccionados as $u) {
            $html .= '
            <tr>
                <td>' . htmlspecialchars($u['idusu']) . '</td>
                <td>' . htmlspecialchars($u['username']) . '</td>
                <td>' . htmlspecialchars($u['nombre_completo']) . '</td>
                <td>' . htmlspecialchars($u['tipo_usuario']) . '</td>
                <td>' . htmlspecialchars($u['telefono']) . '</td>
                <td>' . htmlspecialchars($u['email']) . '</td>
                <td>' . ($u['activo'] ? 'Sí' : 'No') . '</td>
            </tr>';
        }
    } else {
        $html .= '<tr><td colspan="7">No se encontraron usuarios seleccionados</td></tr>';
    }

    $html .= '</tbody></table>';

    ob_clean();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Usuarios_Seleccionados.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    exit;
}



// ---------------------- //
//  📦 PDF DE PEDIDOS
// ---------------------- //

$ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : [];
$pedidos = $mreportes->getAll();
$pedidosSeleccionados = array_filter($pedidos, function($p) use ($ids) {
    return in_array((string)$p['idped'], $ids);
});

$html = '
<style>
    body { font-family: Arial, Helvetica, sans-serif; color: #2C3E50; font-size: 12px; }
    h1 { text-align: center; color: #1A5276; border-bottom: 2px solid #1A5276; padding-bottom: 10px; margin-bottom: 30px; }
    p { text-align: right; color: #555; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; box-shadow: 0 0 6px rgba(0,0,0,0.1); }
    thead { background-color: #2471A3; color: white; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    tr:nth-child(even) { background-color: #f8f9f9; }
    tr:hover { background-color: #EBF5FB; }
</style>

<h1>📄 Reporte de Pedidos Seleccionados</h1>
<p>Generado el ' . date("d/m/Y H:i") . '</p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Número Pedido</th>
            <th>Fecha Pedido</th>
            <th>Monto Total</th>
            <th>Cliente</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>';

foreach ($pedidosSeleccionados as $pedido) {
    $html .= '
    <tr>
        <td>' . htmlspecialchars($pedido['idped']) . '</td>
        <td>' . htmlspecialchars($pedido['numped']) . '</td>
        <td>' . date('d/m/Y', strtotime($pedido['fecha_pedido'])) . '</td>
        <td>$' . number_format($pedido['monto_total'], 2) . '</td>
        <td>' . htmlspecialchars($pedido['cli_idcli']) . '</td>
        <td>' . htmlspecialchars($pedido['estado']) . '</td>
    </tr>';
}
$html .= '</tbody></table>';

ob_clean();
$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('Pedidos_Seleccionados.pdf', \Mpdf\Output\Destination::DOWNLOAD);
exit;
?>