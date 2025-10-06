<?php
// Controlador para procesar pagos
require_once __DIR__ . '/../models/ProcesarPagosModel.php';

class ProcesarPagosController {
    private $model;
    public function __construct() {
        $this->model = new ProcesarPagosModel();
    }
    public function obtenerPagosPendientes() {
        return $this->model->getPagosPendientes();
    }
    public function procesarPago($pagoId) {
        return $this->model->procesarPago($pagoId);
    }
}
