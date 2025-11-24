<?php

class ReportesController {
    private $model;

    public function __construct() {
        require_once __DIR__ . '/../models/mreportes.php';
        $this->model = new Mreportes();
    }

    public function obtenerContexto() {
        // Pedidos
        $dtAll = $this->model->getAll();
        $modalPedidos = $this->filtrarPedidos($dtAll, $_GET);

        // Usuarios
        $dtAllUsu = $this->model->getAllusu();
        $modalUsuarios = $this->filtrarUsuarios($dtAllUsu, $_GET);
        $totalUsuarios = count($dtAllUsu);
        $datos['usuarios']['activos'] = count(array_filter($dtAllUsu, fn($u) => $u['activo'] == 1));

        // Inventario
        $dtAllInv = $this->model->getAllInventario();
        $modalInventario = $this->filtrarInventario($dtAllInv, $_GET);
        $datos['inventario']['productos'] = count($dtAllInv);
        $datos['inventario']['stock_total'] = array_sum(array_column($dtAllInv, 'stock'));

        // Pagos
        $dtAllPagos = $this->model->getAllPagos();
        $modalPagos = $this->filtrarPagos($dtAllPagos, $_GET);

        // Tarjetas resumidas
        $datos['ventas']['pedidos'] = count($dtAll);
        $datos['ventas']['total'] = array_sum(array_column($dtAll, 'monto_total'));

        $datos['pagos']['realizados'] = array_sum(array_map(function($p) {
            return strtolower($p['estado_pag']) === 'completado' ? $p['monto'] : 0;
        }, $dtAllPagos));
        $datos['pagos']['pendientes'] = array_sum(array_map(function($p) {
            return strtolower($p['estado_pag']) === 'pendiente' ? $p['monto'] : 0;
        }, $dtAllPagos));

        return compact(
            'datos',
            'totalUsuarios',
            'dtAll',
            'modalPedidos',
            'dtAllUsu',
            'modalUsuarios',
            'dtAllInv',
            'modalInventario',
            'dtAllPagos',
            'modalPagos'
        );
    }

    private function filtrarPedidos($items, $params) {
        $filtered = $items;
        if (!empty($params['fecha_inicio'])) {
            $filtered = array_filter($filtered, fn($p) => strtotime($p['fecha_pedido']) >= strtotime($params['fecha_inicio']));
        }
        if (!empty($params['fecha_fin'])) {
            $filtered = array_filter($filtered, fn($p) => strtotime($p['fecha_pedido']) <= strtotime($params['fecha_fin'] . ' 23:59:59'));
        }
        if (!empty($params['estado'])) {
            $estado = strtolower($params['estado']);
            $filtered = array_filter($filtered, fn($p) => strtolower($p['estado']) === $estado);
        }
        return $filtered;
    }

    private function filtrarUsuarios($items, $params) {
        if (empty($params['tipo'])) {
            return $items;
        }
        $tipo = strtolower($params['tipo']);
        return array_filter($items, fn($u) => strtolower($u['tipo_usuario']) === $tipo);
    }

    private function filtrarInventario($items, $params) {
        $filtered = $items;
        if (!empty($params['estado_flor'])) {
            $estado = strtolower($params['estado_flor']);
            $filtered = array_filter($filtered, fn($f) => strtolower($f['estado']) === $estado);
        }
        if (!empty($params['naturaleza'])) {
            $nat = strtolower($params['naturaleza']);
            $filtered = array_filter($filtered, fn($f) => strtolower($f['naturaleza']) === $nat);
        }
        if (!empty($params['color'])) {
            $color = strtolower($params['color']);
            $filtered = array_filter($filtered, fn($f) => strtolower($f['color']) === $color);
        }
        return $filtered;
    }

    private function filtrarPagos($items, $params) {
        $filtered = $items;
        if (!empty($params['fecha_inicio'])) {
            $filtered = array_filter($filtered, fn($p) => strtotime($p['fecha_pago']) >= strtotime($params['fecha_inicio']));
        }
        if (!empty($params['fecha_fin'])) {
            $filtered = array_filter($filtered, fn($p) => strtotime($p['fecha_pago']) <= strtotime($params['fecha_fin'] . ' 23:59:59'));
        }
        if (!empty($params['estado_pag'])) {
            $estado = strtolower($params['estado_pag']);
            $filtered = array_filter($filtered, fn($p) => strtolower($p['estado_pag']) === $estado);
        }
        return $filtered;
    }
}
