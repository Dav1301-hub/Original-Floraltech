<?php

class AdminAuditoriaController {
    private $model;

    public function __construct() {
        require_once __DIR__ . '/../models/conexion.php';
        require_once __DIR__ . '/../models/AuditoriaModel.php';
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        $this->model = new AuditoriaModel($db);
    }

    public function obtenerContexto() {
        $fechaFiltroPagos = $_GET['fecha_pagos'] ?? date('Y-m-d');

        // Guardar proyección si llega POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['objetivo_monto'], $_POST['objetivo_fecha_inicio'], $_POST['objetivo_fecha_fin'])) {
            $data = [
                'titulo' => trim($_POST['objetivo_titulo'] ?? 'Meta de pagos'),
                'monto' => max(0, floatval($_POST['objetivo_monto'])),
                'inicio' => $_POST['objetivo_fecha_inicio'] ?: $_POST['objetivo_fecha_fin'],
                'fin' => $_POST['objetivo_fecha_fin'],
                'creado_por' => $_SESSION['user']['idusu'] ?? null,
                'notas' => trim($_POST['objetivo_notas'] ?? '')
            ];
            $this->model->guardarProyeccion($data);
        }

        $resumenAuditoria = $this->model->resumenAuditoriaPagos();
        $accionesPorTipo = $this->model->accionesPorEstado();
        $actividadSemanal = $this->model->actividadSemanal();

        [$usuariosTotales, $usuariosActivos, $usuariosActivosHoy] = $this->model->usuariosActivosResumen();
        $usuariosRecientes = $this->model->usuariosRecientes();

        [$productosActivos, $productosActivosDetalle] = $this->model->productosActivosResumen();

        $pagosMes = $this->model->pagosMes();

        $proyeccionActiva = $this->model->proyeccionActiva();
        $avanceProy = $this->model->avanceProyeccion($proyeccionActiva['fecha_inicio'], $proyeccionActiva['fecha_fin']);

        $avanceActual = floatval($avanceProy['monto'] ?? 0);
        $porcentajeAvance = ($proyeccionActiva['monto_objetivo'] ?? 0) > 0
            ? min(100, round(($avanceActual / $proyeccionActiva['monto_objetivo']) * 100, 1))
            : 0;
        $fechaLimiteDate = strtotime($proyeccionActiva['fecha_fin']);
        $vencido = $fechaLimiteDate && time() > $fechaLimiteDate;
        $cumplido = $avanceActual >= floatval($proyeccionActiva['monto_objetivo']);

        $pagosPorFecha = $this->model->pagosPorFecha($fechaFiltroPagos);
        $listadoAuditoria = $this->model->listadoAuditoria();

        return compact(
            'fechaFiltroPagos',
            'resumenAuditoria',
            'accionesPorTipo',
            'actividadSemanal',
            'usuariosTotales',
            'usuariosActivos',
            'usuariosActivosHoy',
            'usuariosRecientes',
            'productosActivos',
            'productosActivosDetalle',
            'pagosMes',
            'proyeccionActiva',
            'avanceProy',
            'avanceActual',
            'porcentajeAvance',
            'fechaLimiteDate',
            'vencido',
            'cumplido',
            'pagosPorFecha',
            'listadoAuditoria'
        );
    }
}
