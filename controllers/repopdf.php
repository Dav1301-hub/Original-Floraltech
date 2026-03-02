<?php
require_once __DIR__ . '/../models/mreportes.php';

// Cargar logo de la empresa para PDFs
require_once __DIR__ . '/../models/conexion.php';
$conexion = (new conexion())->get_conexion();
$logo_empresa_path = null;
$nombre_empresa = 'FloralTech';
try {
    $stmt_empresa = $conexion->prepare("SELECT logo, nombre FROM empresa LIMIT 1");
    $stmt_empresa->execute();
    $empresa_data = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
    if ($empresa_data && !empty($empresa_data['logo'])) {
        $logo_path = __DIR__ . '/../' . $empresa_data['logo'];
        if (file_exists($logo_path)) {
            $logo_empresa_path = $logo_path;
        }
    }
    $nombre_empresa = $empresa_data['nombre'] ?? 'FloralTech';
} catch (Exception $e) {
    // Usar valores por defecto
}

// Seleccionar motor PDF disponible (mPDF si existe, FPDF si no)
$mpdfPath = dirname(__DIR__) . '/vendor/autoload.php';
$fpdfPath = __DIR__ . '/../libs/FPDF/fpdf.php';
$mpdfClassPath = dirname(__DIR__) . '/vendor/mpdf/mpdf/src/Mpdf.php';
// Check if both autoload exists AND mpdf is actually installed
$pdfEngine = (file_exists($mpdfPath) && file_exists($mpdfClassPath)) ? 'mpdf' : (file_exists($fpdfPath) ? 'fpdf' : null);

if ($pdfEngine === 'mpdf') {
    require_once $mpdfPath;
} elseif ($pdfEngine === 'fpdf') {
    require_once $fpdfPath;
}

$mreportes = new Mreportes();

/**
 * Fallback sencillo con FPDF si no hay vendor/autoload.
 */
function renderWithFPDF($titulo, $headers, $rows, $fileName, $graficoBase64 = null) {
    global $logo_empresa_path, $nombre_empresa;
    
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    
    // Agregar logo si existe
    if ($logo_empresa_path) {
        $pdf->Image($logo_empresa_path, 10, 8, 40);
        $pdf->SetXY(55, 10);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 10, utf8_decode($nombre_empresa), 0, 1);
        $pdf->SetXY(55, 18);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, utf8_decode($titulo), 0, 1);
    } else {
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
    }
    
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
    
    // Agregar gráfico si se proporcionó
    if ($graficoBase64) {
        $graficoData = str_replace('data:image/png;base64,', '', $graficoBase64);
        $graficoData = str_replace(' ', '', $graficoData);
        $imagenBinaria = base64_decode($graficoData);
        
        if ($imagenBinaria !== false) {
            $tempPath = __DIR__ . '/../temp_grafico.png';
            file_put_contents($tempPath, $imagenBinaria);
            
            // Agregar nueva página para el gráfico
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, utf8_decode('Análisis Gráfico'), 0, 1, 'C');
            $pdf->Ln(5);
            
            // Insertar imagen centrada
            $pdf->Image($tempPath, 20, $pdf->GetY(), 240, 120);
            
            // Limpiar archivo temporal
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
    
    $pdf->Output('D', $fileName);
    exit;
}

// Estilos base para todos los reportes
$baseCss = '
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #2c3e50; font-size: 12px; }
        .header-logo { text-align: center; margin-bottom: 20px; }
        .header-logo img { max-width: 200px; max-height: 80px; }
        .header-logo h2 { margin: 10px 0 5px 0; color: #1a5276; }
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
        // Pasar el gráfico si se recibió
        $graficoUsuarios = !empty($_POST['grafico_usuarios']) ? $_POST['grafico_usuarios'] : null;
        renderWithFPDF('Reporte de Usuarios', $headers, $rows, 'Usuarios_Seleccionados.pdf', $graficoUsuarios);
    }

    // Generar header con logo para mPDF
    $headerHtml = '';
    if ($logo_empresa_path) {
        $logo_data = base64_encode(file_get_contents($logo_empresa_path));
        $logo_extension = pathinfo($logo_empresa_path, PATHINFO_EXTENSION);
        $headerHtml = '<div class="header-logo">
            <img src="data:image/' . $logo_extension . ';base64,' . $logo_data . '" alt="Logo">
            <h2>' . htmlspecialchars($nombre_empresa) . '</h2>
        </div>';
    }

    $html = $baseCss;
    $html .= $headerHtml;
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
    
    // Agregar gráfico si se envió
    if (!empty($_POST['grafico_usuarios'])) {
        $graficoData = $_POST['grafico_usuarios'];
        // Remover el prefijo data:image/png;base64,
        if (strpos($graficoData, 'data:image/png;base64,') === 0) {
            $graficoData = str_replace('data:image/png;base64,', '', $graficoData);
        }
        $html .= '<div style="page-break-before: avoid; margin-top: 20px;">
            <h3 style="color: #333; margin-bottom: 10px;">Distribución por Rol</h3>
            <img src="data:image/png;base64,' . $graficoData . '" style="max-width: 100%; height: auto;" />
        </div>';
    }

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
        // Pasar el gráfico si se recibió
        $graficoInventario = !empty($_POST['grafico_inventario']) ? $_POST['grafico_inventario'] : null;
        renderWithFPDF('Inventario de Flores', $headers, $rows, 'Inventario_Flores.pdf', $graficoInventario);
    }

    // Generar header con logo
    $headerHtml = '';
    if ($logo_empresa_path) {
        $logo_data = base64_encode(file_get_contents($logo_empresa_path));
        $ext = pathinfo($logo_empresa_path, PATHINFO_EXTENSION);
        $headerHtml .= '<div style="text-align:left; margin-bottom:20px;">';
        $headerHtml .= '<img src="data:image/' . $ext . ';base64,' . $logo_data . '" style="max-width:100px; margin-bottom:10px;" />';
        $headerHtml .= '<h2 style="margin:0; color:#10b981;">' . htmlspecialchars($nombre_empresa) . '</h2>';
        $headerHtml .= '</div>';
    }
    
    $html = $baseCss;
    $html .= $headerHtml;
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
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4-L',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 20,
        'margin_bottom' => 20,
        'margin_header' => 10,
        'margin_footer' => 10
    ]);
    $mpdf->WriteHTML($html);
    
    // Agregar gráfico si se envió
    if (!empty($_POST['grafico_inventario'])) {
        $graficoData = $_POST['grafico_inventario'];
        // Limpiar el prefijo data:image si existe
        $graficoData = str_replace('data:image/png;base64,', '', $graficoData);
        $graficoData = str_replace(' ', '', $graficoData);
        $imagenBinaria = base64_decode($graficoData);
        
        if ($imagenBinaria !== false) {
            // Guardar temporalmente en el directorio del proyecto
            $tempPath = __DIR__ . '/../temp_grafico.png';
            file_put_contents($tempPath, $imagenBinaria);
            
            // Agregar título del gráfico
            $mpdf->WriteHTML('<h3 style="color: #10b981; margin: 20px 0 15px 0; font-size: 16px; text-align: center;">📊 Top 10 Productos por Stock</h3>');
            
            // Insertar imagen usando el método Image de mPDF
            $mpdf->Image($tempPath, 0, '', 250, 150, 'png', '', true, false);
            
            // Limpiar archivo temporal
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
    
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
        // Pasar el gráfico si se recibió
        $graficoPagos = !empty($_POST['grafico_pagos']) ? $_POST['grafico_pagos'] : null;
        renderWithFPDF('Pagos Seleccionados', $headers, $rows, 'Pagos_Seleccionados.pdf', $graficoPagos);
    }

    // Generar header con logo
    $headerHtml = '';
    if ($logo_empresa_path) {
        $logo_data = base64_encode(file_get_contents($logo_empresa_path));
        $logo_extension = pathinfo($logo_empresa_path, PATHINFO_EXTENSION);
        $headerHtml = '<div class="header-logo">
            <img src="data:image/' . $logo_extension . ';base64,' . $logo_data . '" alt="Logo">
            <h2>' . htmlspecialchars($nombre_empresa) . '</h2>
        </div>';
    }

    $html = $baseCss;
    $html .= $headerHtml;
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
    
    // Agregar gráfico si se envió
    if (!empty($_POST['grafico_pagos'])) {
        $graficoData = $_POST['grafico_pagos'];
        if (strpos($graficoData, 'data:image/png;base64,') === 0) {
            $graficoData = str_replace('data:image/png;base64,', '', $graficoData);
        }
        $html .= '<div style="page-break-before: avoid; margin-top: 20px;">
            <h3 style="color: #333; margin-bottom: 10px;">Estados de Pagos</h3>
            <img src="data:image/png;base64,' . $graficoData . '" style="max-width: 100%; height: auto;" />
        </div>';
    }

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

// Generar header con logo
$headerHtml = '';
if ($logo_empresa_path) {
    $logo_data = base64_encode(file_get_contents($logo_empresa_path));
    $logo_extension = pathinfo($logo_empresa_path, PATHINFO_EXTENSION);
    $headerHtml = '<div class="header-logo">
        <img src="data:image/' . $logo_extension . ';base64,' . $logo_data . '" alt="Logo">
        <h2>' . htmlspecialchars($nombre_empresa) . '</h2>
    </div>';
}

$html = $baseCss;
$html .= $headerHtml;
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

// Agregar gráfico si se envió
if (!empty($_POST['grafico_ventas'])) {
    $graficoData = $_POST['grafico_ventas'];
    if (strpos($graficoData, 'data:image/png;base64,') === 0) {
        $graficoData = str_replace('data:image/png;base64,', '', $graficoData);
    }
    $html .= '<div style="page-break-before: avoid; margin-top: 20px;">
        <h3 style="color: #333; margin-bottom: 10px;">Tendencia de Ventas (Últimos 7 días)</h3>
        <img src="data:image/png;base64,' . $graficoData . '" style="max-width: 100%; height: auto;" />
    </div>';
}

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
    // Pasar el gráfico si se recibió
    $graficoVentas = !empty($_POST['grafico_ventas']) ? $_POST['grafico_ventas'] : null;
    renderWithFPDF('Pedidos Seleccionados', $headers, $rows, 'Pedidos_Seleccionados.pdf', $graficoVentas);
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
