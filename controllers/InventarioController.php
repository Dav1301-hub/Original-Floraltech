<?php
require_once __DIR__ . '/../models/InventarioModel.php';
class InventarioController {
    private $model;
    public function __construct() {
        $this->model = new InventarioModel();
    }
    public function obtenerInventario() {
        return $this->model->getInventario();
    }
}
