<?php
require_once(__DIR__ . '/../models/mreportes.php');

$mreportes = new Mreportes();

$idped   = isset($_REQUEST['idped'])   ? $_REQUEST['idped']   : null;
$ope     = isset($_REQUEST['ope'])     ? $_REQUEST['ope']     : null;
$idusu   = isset($_REQUEST['idusu'])   ? $_REQUEST['idusu']   : null;
$idtflor = isset($_REQUEST['idtflor']) ? $_REQUEST['idtflor'] : null;
$idpago  = isset($_REQUEST['idpago'])  ? $_REQUEST['idpago']  : null;
$dtOne   = null;

/* ===============================
   🔹 REPORTE DE PEDIDOS
   =============================== */
if ($ope === "ver" && $idped) {
    $todos = $mreportes->getAll();
    foreach ($todos as $reporte) {
        if ($reporte['idped'] == $idped) {
            $dtOne = $reporte;
            break;
        }
    }
}

$dtAll = $mreportes->getAll();

/* --- FILTRO PARA EL MODAL DE PEDIDOS --- */
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

/* ===============================
   🔹 REPORTE DE USUARIOS
   =============================== */
if ($ope === "ver_usuario" && $idusu) {
    $todosUsu = $mreportes->getAllusu();
    foreach ($todosUsu as $usuario) {
        if ($usuario['idusu'] == $idusu) {
            $dtOne = $usuario;
            break;
        }
    }
}

$dtAllUsu = $mreportes->getAllusu();

/* --- FILTRO PARA EL MODAL DE USUARIOS --- */
$modalUsuarios = $dtAllUsu ?? [];
if (!empty($_GET['tipo'])) {
    $modalUsuarios = array_filter($modalUsuarios, function($u) {
        return strtolower($u['tipo_usuario']) === strtolower($_GET['tipo']);
    });
}
$totalUsuarios = count($dtAllUsu);
$datos['usuarios']['activos'] = count(array_filter($dtAllUsu, fn($u) => $u['activo'] == 1));


/* ===============================
   🔹 REPORTE DE INVENTARIO
   =============================== */
if ($ope === "ver_inventario" && $idtflor) {
    $todasFlores = $mreportes->getAllInventario();
    foreach ($todasFlores as $flor) {
        if ($flor['idtflor'] == $idtflor) {
            $dtOne = $flor;
            break;
        }
    }
}

$dtAllInv = $mreportes->getAllInventario();

/* --- FILTRO PARA EL MODAL DE INVENTARIO --- */
$modalInventario = $dtAllInv ?? [];


if (!empty($_GET['naturaleza'])) {
    $modalInventario = array_filter($modalInventario, function($f) {
        return strtolower($f['naturaleza']) === strtolower($_GET['naturaleza']);
    });
}
if (!empty($_GET['color'])) {
    $modalInventario = array_filter($modalInventario, function($f) {
        return strtolower($f['color']) === strtolower($_GET['color']);
    });
}
if (!empty($_GET['estado'])) {
    $modalInventario = array_filter($modalInventario, function($f) {
        return strtolower($f['estado']) === strtolower($_GET['estado']);
    });
}

/* --- TOTALES DE INVENTARIO --- */
$totalFlores = count($dtAllInv);
$totalStock  = array_sum(array_column($dtAllInv, 'stock'));
$totalValor  = array_sum(array_column($dtAllInv, 'valor_total'));
// Prueba temporal:


/* ===============================
   🔹 REPORTE DE PAGOS
   =============================== */
if ($ope === "ver" && $idpago) {
    $todos = $mreportes->getAllPagos();
    foreach ($todos as $reporte) {
        if ($reporte['idpago'] == $idpago) {
            $dtOne = $reporte;
            break;
        }
    }
}

$dtAllPagos = $mreportes->getAllPagos();

/* --- FILTRO PARA EL MODAL DE PEDIDOS --- */
$modalPagos = $dtAllPagos ?? [];

if (!empty($_GET['fecha_inicio'])) {
    $modalPagos = array_filter($modalPagos, function($q) {
        return strtotime($q['fecha_pago']) >= strtotime($_GET['fecha_inicio']);
    });
}
if (!empty($_GET['fecha_fin'])) {
    $modalPagos = array_filter($modalPagos, function($q) {
        return strtotime($q['fecha_pago']) <= strtotime($_GET['fecha_fin'] . ' 23:59:59');
    });
}
if (!empty($_GET['estado_pag'])) {
    $modalPagos = array_filter($modalPagos, function($q) {
        return strtolower($q['estado_pag']) === strtolower($_GET['estado_pag']);
    });
}
?>
