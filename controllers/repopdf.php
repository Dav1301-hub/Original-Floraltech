<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/../models/mreportes.php';

use Mpdf\Mpdf;

$mreportes = new Mreportes();

// ---------------------- //
//  📄 PDF DE USUARIOS
// ---------------------- //
if (isset($_POST['accion']) && $_POST['accion'] === 'usuarios_pdf') {

    $ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : [];
    $ids = array_filter($ids);
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;

    $usuarios = $mreportes->getAllusu($tipo);

    $usuariosSeleccionados = array_filter($usuarios, function($u) use ($ids) {
        return in_array((string)$u['idusu'], $ids, true);
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
//  🌸 PDF DE INVENTARIO (Flores)
// ---------------------- //
if (isset($_POST['accion']) && $_POST['accion'] === 'flores_pdf') {
    $ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : [];
    $ids = array_filter($ids);

    $flores = $mreportes->getAllInventario();

    $floresSeleccionadas = array_filter($flores, function($f) use ($ids) {
        return in_array((string)$f['idtflor'], $ids, true);
    });

    $totalStock = 0;
    $totalValor = 0;

    $html = '
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #2C3E50; font-size: 12px; }
        h1 { text-align: center; color: #145A32; border-bottom: 2px solid #145A32; padding-bottom: 10px; margin-bottom: 30px; }
        p { text-align: right; color: #555; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; box-shadow: 0 0 6px rgba(0,0,0,0.1); }
        thead { background-color: #229954; color: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        tr:nth-child(even) { background-color: #f8f9f9; }
        tr:hover { background-color: #E9F7EF; }
    </style>

    <h1>🌸 Reporte de Inventario de Flores</h1>
    <p>Generado el ' . date("d/m/Y H:i") . '</p>
    <table>
        <thead>
            <tr>
                <th>ID Flor</th>
                <th>Producto</th>
                <th>Naturaleza</th>
                <th>Color</th>
                <th>Stock</th>
                <th>Precio Unitario</th>
                <th>Valor Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>';

    if (!empty($floresSeleccionadas)) {
        foreach ($floresSeleccionadas as $f) {
            $totalStock += (int)$f['stock'];
            $totalValor += (float)$f['valor_total'];

            $html .= '
            <tr>
                <td>' . htmlspecialchars($f['idtflor']) . '</td>
                <td>' . htmlspecialchars($f['producto']) . '</td>
                <td>' . htmlspecialchars($f['naturaleza']) . '</td>
                <td>' . htmlspecialchars($f['color']) . '</td>
                <td>' . htmlspecialchars($f['stock']) . '</td>
                <td>$' . number_format($f['precio_unitario'], 2) . '</td>
                <td>$' . number_format($f['valor_total'], 2) . '</td>
                <td>' . htmlspecialchars($f['estado']) . '</td>
            </tr>';
        }

        // Fila de totales
        $html .= '
        <tr style="font-weight:bold; background-color:#D5F5E3;">
            <td colspan="4" style="text-align:right;">Totales:</td>
            <td>' . $totalStock . '</td>
            <td></td>
            <td>$' . number_format($totalValor, 2) . '</td>
            <td></td>
        </tr>';
    } else {
        $html .= '<tr><td colspan="8">No se encontraron flores seleccionadas</td></tr>';
    }

    $html .= '</tbody></table>';

    ob_clean();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Inventario_Flores.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    exit;

    echo '<pre>';
print_r($f);
echo '</pre>';

}

if (isset($_POST['accion']) && $_POST['accion'] === 'pagos_pdf') {
    $ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : [];
    $ids = array_filter($ids);

    // Obtener todos los pagos
    $pagos = $mreportes->getAllPagos();

    // Filtrar los pagos seleccionados
    $pagosSeleccionados = array_filter($pagos, function($p) use ($ids) {
        return in_array((string)$p['idpago'], $ids, true);
    });

    // Variables para totales
    $totalMonto = 0;

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

    <h1>💰 Reporte de Pagos Seleccionados</h1>
    <p>Generado el ' . date("d/m/Y H:i") . '</p>
    <table>
        <thead>
            <tr>
                <th>ID Pago</th>
                <th>Fecha de Pago</th>
                <th>Método de Pago</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>ID Transacción</th>
                <th>Comprobante</th>
            </tr>
        </thead>
        <tbody>';

    if (!empty($pagosSeleccionados)) {
        foreach ($pagosSeleccionados as $pago) {
            $monto = (float)$pago['monto'];
            $totalMonto += $monto;

            $html .= '
            <tr>
                <td>' . htmlspecialchars($pago['idpago']) . '</td>
                <td>' . date('d/m/Y', strtotime($pago['fecha_pago'])) . '</td>
                <td>' . htmlspecialchars($pago['metodo_pago']) . '</td>
                <td>$' . number_format($monto, 2) . '</td>
                <td>' . htmlspecialchars($pago['estado_pag']) . '</td>
                <td>' . htmlspecialchars($pago['transaccion_id']) . '</td>
                <td>' . htmlspecialchars($pago['comprobante_transferencia']) . '</td>
            </tr>';
        }

        // Fila de total general
        $html .= '
        <tr style="font-weight:bold; background-color:#D6EAF8;">
            <td colspan="3" style="text-align:right;">Total General:</td>
            <td>$' . number_format($totalMonto, 2) . '</td>
            <td colspan="3"></td>
        </tr>';
    } else {
        $html .= '<tr><td colspan="7">No se encontraron pagos seleccionados</td></tr>';
    }

    $html .= '</tbody></table>';

    // Generar PDF
    ob_clean();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Pagos_Seleccionados.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    exit;
}


// ---------------------- //
//  📦 PDF DE PEDIDOS
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
