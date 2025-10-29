<?php
// Controlador para procesar pagos
require_once __DIR__ . '/../models/MprocesarPagos.php';

class CprocesarPagos {
    private $model;
    public function __construct() {
        $this->model = new MprocesarPagos();
    }
    public function obtenerPagosPendientes() {
        return $this->model->getPagosPendientes();
    }
    public function procesarPago($pagoId) {
        return $this->model->procesarPago($pagoId);
    }
}
