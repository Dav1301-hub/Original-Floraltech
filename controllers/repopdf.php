<?php
require_once __DIR__ . '/../models/mreportes.php';

// Seleccionar motor PDF disponible (mPDF si existe, FPDF si no)
$mpdfPath = dirname(__DIR__) . '/vendor/autoload.php';
$fpdfPath = __DIR__ . '/../libs/FPDF/fpdf.php';
$pdfEngine = file_exists($mpdfPath) ? 'mpdf' : (file_exists($fpdfPath) ? 'fpdf' : null);

if ($pdfEngine === 'mpdf') {
    require_once $mpdfPath;
} elseif ($pdfEngine === 'fpdf') {
    require_once $fpdfPath;
}

$mreportes = new Mreportes();

/**
 * Fallback sencillo con FPDF si no hay vendor/autoload.
 */
function renderWithFPDF($titulo, $headers, $rows, $fileName) {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 7, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
    // Headers
    $pdf->SetFont('Arial', 'B', 9);
    foreach ($headers as $h) {
        $pdf->Cell(277 / max(1, count($headers)), 8, utf8_decode($h), 1, 0, 'C');
    }
    $pdf->Ln();
    // Rows
    $pdf->SetFont('Arial', '', 8);
    if (empty($rows)) {
        $pdf->Cell(277, 8, 'Sin datos para exportar', 1, 1, 'C');
    } else {
        foreach ($rows as $row) {
            foreach ($row as $cell) {
                $pdf->Cell(277 / max(1, count($headers)), 7, utf8_decode($cell), 1, 0, 'C');
            }
            $pdf->Ln();
        }
    }
    $pdf->Output('D', $fileName);
    exit;
}

// Estilos base para todos los reportes
$baseCss = '
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #2c3e50; font-size: 12px; }
        h1 { text-align: center; color: #1a5276; border-bottom: 2px solid #1a5276; padding-bottom: 8px; margin-bottom: 18px; }
        .meta { text-align: right; color: #555; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        thead { background-color: #2471a3; color: white; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        tr:nth-child(even) { background-color: #f8f9f9; }
        tr:hover { background-color: #ebf5fb; }
        .totales { font-weight: bold; background: #d6eaf8; }
        .badge { padding: 2px 6px; border-radius: 4px; color: #fff; font-size: 11px; }
        .success { background: #27ae60; }
        .warning { background: #f1c40f; color: #1f2937; }
        .secondary { background: #7f8c8d; }
    </style>
';

// ---------------------- //
//  PDF DE USUARIOS
// ---------------------- //
if (isset($_POST['accion']) && $_POST['accion'] === 'usuarios_pdf') {
    $ids = array_filter(explode(',', $_POST['ids'] ?? ''));
    $tipo = $_POST['tipo'] ?? null;

    $usuarios = $mreportes->getAllusu($tipo);
    $usuariosSeleccionados = array_filter($usuarios, fn($u) => in_array((string)$u['idusu'], $ids, true));

    $totalActivos = count(array_filter($usuariosSeleccionados, fn($u) => $u['activo']));

    if ($pdfEngine === 'fpdf') {
        $headers = ['ID', 'Usuario', 'Nombre', 'Tipo', 'Telefono', 'Email', 'Activo'];
        $rows = [];
        foreach ($usuariosSeleccionados as $u) {
            $rows[] = [
                $u['idusu'] ?? '',
                $u['username'] ?? '',
                $u['nombre_completo'] ?? '',
                $u['tipo_usuario'] ?? '',
                $u['telefono'] ?? '',
                $u['email'] ?? '',
                ($u['activo'] ? 'Si' : 'No')
            ];
        }
        renderWithFPDF('Reporte de Usuarios', $headers, $rows, 'Usuarios_Seleccionados.pdf');
    }

    $html = $baseCss;
    $html .= '<h1>Reporte de Usuarios Seleccionados</h1>';
    $html .= '<p class="meta">Generado el ' . date("d/m/Y H:i") . '</p>';
    if ($tipo) {
        $html .= '<p class="meta">Filtro: ' . htmlspecialchars($tipo) . '</p>';
    }
    $html .= '<p class="meta">Total seleccionados: ' . count($usuariosSeleccionados) . ' | Activos: ' . $totalActivos . '</p>';

    $html .= '
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Nombre completo</th>
                <th>Tipo</th>
                <th>Telefono</th>
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
                <td>' . ($u['activo'] ? 'Si' : 'No') . '</td>
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
//  PDF DE INVENTARIO (Flores)
// ---------------------- //
if (isset($_POST['accion']) && $_POST['accion'] === 'flores_pdf') {
    $ids = array_filter(explode(',', $_POST['ids'] ?? ''));
    $flores = $mreportes->getAllInventario();
    $floresSeleccionadas = array_filter($flores, fn($f) => in_array((string)$f['idinv'], $ids, true));

    $totalStock = array_sum(array_column($floresSeleccionadas, 'stock'));
    $totalValor = array_sum(array_column($floresSeleccionadas, 'valor_total'));

    if ($pdfEngine === 'fpdf') {
        $headers = ['ID Prod','Categoría','Producto','Naturaleza','Color','Stock','P. Unit','Valor','Estado'];
        $rows = [];
        foreach ($floresSeleccionadas as $f) {
            $rows[] = [
                $f['idinv'] ?? '',
                $f['categoria'] ?? '',
                $f['producto'] ?? '',
                $f['naturaleza'] ?? '',
                $f['color'] ?? '',
                $f['stock'] ?? 0,
                number_format($f['precio_unitario'] ?? 0, 2),
                number_format($f['valor_total'] ?? 0, 2),
                $f['estado'] ?? ''
            ];
        }
        renderWithFPDF('Inventario de Flores', $headers, $rows, 'Inventario_Flores.pdf');
    }

    $html = $baseCss;
    $html .= '<h1>Reporte de Inventario de Flores</h1>';
    $html .= '<p class="meta">Generado el ' . date("d/m/Y H:i") . '</p>';
    $html .= '<p class="meta">Items: ' . count($floresSeleccionadas) . ' | Stock total: ' . $totalStock . ' | Valor: $' . number_format($totalValor, 2) . '</p>';

    $html .= '
    <table>
        <thead>
            <tr>
                <th>ID Prod</th>
                <th>Categoría</th>
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
            $html .= '
            <tr>
                <td>' . htmlspecialchars($f['idinv']) . '</td>
                <td>' . htmlspecialchars($f['categoria'] ?? 'Sin categoría') . '</td>
                <td>' . htmlspecialchars($f['producto']) . '</td>
                <td>' . htmlspecialchars($f['naturaleza']) . '</td>
                <td>' . htmlspecialchars($f['color']) . '</td>
                <td>' . htmlspecialchars($f['stock']) . '</td>
                <td>$' . number_format($f['precio_unitario'], 2) . '</td>
                <td>$' . number_format($f['valor_total'], 2) . '</td>
                <td>' . htmlspecialchars($f['estado']) . '</td>
            </tr>';
        }
        $html .= '
        <tr class="totales">
            <td colspan="5" style="text-align:right;">Totales:</td>
            <td>' . $totalStock . '</td>
            <td></td>
            <td>$' . number_format($totalValor, 2) . '</td>
            <td></td>
        </tr>';
    } else {
        $html .= '<tr><td colspan="9">No se encontraron flores seleccionadas</td></tr>';
    }

    $html .= '</tbody></table>';

    ob_clean();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Inventario_Flores.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    exit;
}

// ---------------------- //
//  PDF DE PAGOS
// ---------------------- //
if (isset($_POST['accion']) && $_POST['accion'] === 'pagos_pdf') {
    $ids = array_filter(explode(',', $_POST['ids'] ?? ''));
    $pagos = $mreportes->getAllPagos();
    $pagosSeleccionados = array_filter($pagos, fn($p) => in_array((string)$p['idpago'], $ids, true));

    $totalMonto = array_sum(array_column($pagosSeleccionados, 'monto'));
    $completados = count(array_filter($pagosSeleccionados, fn($p) => strtolower($p['estado_pag']) === 'completado'));
    $pendientes = count(array_filter($pagosSeleccionados, fn($p) => strtolower($p['estado_pag']) === 'pendiente'));

    if ($pdfEngine === 'fpdf') {
        $headers = ['ID','Fecha','Metodo','Monto','Estado','Pedido','Cliente','Transaccion','Comprobante'];
        $rows = [];
        foreach ($pagosSeleccionados as $pago) {
            $rows[] = [
                $pago['idpago'] ?? '',
                !empty($pago['fecha_pago']) ? date('d/m/Y', strtotime($pago['fecha_pago'])) : '',
                $pago['metodo_pago'] ?? '',
                number_format((float)($pago['monto'] ?? 0), 2),
                $pago['estado_pag'] ?? '',
                $pago['numped'] ?? '-',
                $pago['cliente'] ?? '-',
                $pago['transaccion_id'] ?? '',
                !empty($pago['comprobante_transferencia']) ? $pago['comprobante_transferencia'] : 'Sin comprobante'
            ];
        }
        renderWithFPDF('Pagos Seleccionados', $headers, $rows, 'Pagos_Seleccionados.pdf');
    }

    $html = $baseCss;
    $html .= '<h1>Reporte de Pagos Seleccionados</h1>';
    $html .= '<p class="meta">Generado el ' . date("d/m/Y H:i") . '</p>';
    $html .= '<p class="meta">Pagos: ' . count($pagosSeleccionados) . ' | Completados: ' . $completados . ' | Pendientes: ' . $pendientes . ' | Monto total: $' . number_format($totalMonto, 2) . '</p>';

    $html .= '
    <table>
        <thead>
            <tr>
                <th>ID Pago</th>
                <th>Fecha</th>
                <th>Metodo</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>ID Transaccion</th>
                <th>Comprobante</th>
            </tr>
        </thead>
        <tbody>';

    if (!empty($pagosSeleccionados)) {
        foreach ($pagosSeleccionados as $pago) {
            $estado = strtolower($pago['estado_pag']);
            $badgeClass = $estado === 'completado' ? 'success' : ($estado === 'pendiente' ? 'warning' : 'secondary');
            $html .= '
            <tr>
                <td>' . htmlspecialchars($pago['idpago']) . '</td>
                <td>' . date('d/m/Y', strtotime($pago['fecha_pago'])) . '</td>
                <td>' . htmlspecialchars($pago['metodo_pago']) . '</td>
                <td>$' . number_format((float)$pago['monto'], 2) . '</td>
                <td><span class="badge ' . $badgeClass . '">' . htmlspecialchars($pago['estado_pag']) . '</span></td>
                <td>' . htmlspecialchars($pago['numped'] ?? '-') . '</td>
                <td>' . htmlspecialchars($pago['cliente'] ?? '-') . '</td>
                <td>' . htmlspecialchars($pago['transaccion_id']) . '</td>
                <td>' . (!empty($pago['comprobante_transferencia']) ? htmlspecialchars($pago['comprobante_transferencia']) : 'Sin comprobante') . '</td>
            </tr>';
        }
        $html .= '
        <tr class="totales">
            <td colspan="3" style="text-align:right;">Total general:</td>
            <td>$' . number_format($totalMonto, 2) . '</td>
            <td colspan="5"></td>
        </tr>';
    } else {
        $html .= '<tr><td colspan="9">No se encontraron pagos seleccionados</td></tr>';
    }

    $html .= '</tbody></table>';

    ob_clean();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Pagos_Seleccionados.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    exit;
}

// ---------------------- //
//  PDF DE PEDIDOS (default)
// ---------------------- //
$ids = array_filter(explode(',', $_POST['ids'] ?? ''));
$pedidos = $mreportes->getAll();
$pedidosSeleccionados = array_filter($pedidos, fn($p) => in_array((string)$p['idped'], $ids, true));

$totalPedidos = count($pedidosSeleccionados);
$montoTotal = array_sum(array_column($pedidosSeleccionados, 'monto_total'));
$completados = count(array_filter($pedidosSeleccionados, fn($p) => strtolower($p['estado']) === 'completado'));
$pendientes = count(array_filter($pedidosSeleccionados, fn($p) => strtolower($p['estado']) === 'pendiente'));

$html = $baseCss;
$html .= '<h1>Reporte de Pedidos Seleccionados</h1>';
$html .= '<p class="meta">Generado el ' . date("d/m/Y H:i") . '</p>';
$html .= '<p class="meta">Pedidos: ' . $totalPedidos . ' | Completados: ' . $completados . ' | Pendientes: ' . $pendientes . ' | Monto total: $' . number_format($montoTotal, 2) . '</p>';

$html .= '
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Numero</th>
            <th>Fecha Pedido</th>
            <th>Entrega solicitada</th>
            <th>Monto Total</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Empleado</th>
        </tr>
    </thead>
    <tbody>';

if (!empty($pedidosSeleccionados)) {
    foreach ($pedidosSeleccionados as $pedido) {
        $html .= '
        <tr>
            <td>' . htmlspecialchars($pedido['idped']) . '</td>
            <td>' . htmlspecialchars($pedido['numped']) . '</td>
            <td>' . date('d/m/Y', strtotime($pedido['fecha_pedido'])) . '</td>
            <td>' . (!empty($pedido['fecha_entrega_solicitada']) ? date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) : 'Sin fecha') . '</td>
            <td>$' . number_format($pedido['monto_total'], 2) . '</td>
            <td>' . htmlspecialchars($pedido['cli_idcli']) . '</td>
            <td>' . htmlspecialchars($pedido['estado']) . '</td>
            <td>' . htmlspecialchars($pedido['empleado_id']) . '</td>
        </tr>';
    }
    $html .= '
    <tr class="totales">
        <td colspan="4" style="text-align:right;">Total:</td>
        <td>$' . number_format($montoTotal, 2) . '</td>
        <td colspan="3"></td>
    </tr>';
} else {
    $html .= '<tr><td colspan="8">No se encontraron pedidos seleccionados</td></tr>';
}

$html .= '</tbody></table>';

ob_clean();
if ($pdfEngine === 'fpdf') {
    $headers = ['ID','Numero','Fecha','Entrega','Monto','Cliente','Estado','Empleado'];
    $rows = [];
    foreach ($pedidosSeleccionados as $pedido) {
        $rows[] = [
            $pedido['idped'] ?? '',
            $pedido['numped'] ?? '',
            !empty($pedido['fecha_pedido']) ? date('d/m/Y', strtotime($pedido['fecha_pedido'])) : '',
            !empty($pedido['fecha_entrega_solicitada']) ? date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) : 'Sin fecha',
            number_format($pedido['monto_total'] ?? 0, 2),
            $pedido['cli_idcli'] ?? '',
            $pedido['estado'] ?? '',
            $pedido['empleado_id'] ?? ''
        ];
    }
    renderWithFPDF('Pedidos Seleccionados', $headers, $rows, 'Pedidos_Seleccionados.pdf');
} elseif ($pdfEngine === 'mpdf') {
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Pedidos_Seleccionados.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    exit;
} else {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "No se encontró motor PDF. Instala mpdf/mpdf con Composer o coloca libs/FPDF/fpdf.php.";
    exit;
}
