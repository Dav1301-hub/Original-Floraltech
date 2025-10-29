<?php
require_once 'models/conexion.php';
require_once 'models/Mpago.php';

class empleadoPagos {
    private $model;

    public function __construct() {
        try {
            $conn = new conexion();
            $db = $conn->get_conexion();
            $this->model = new Mpago($db);
        } catch (Exception $e) {
            error_log("Error inicializando empleadoPagos: " . $e->getMessage());
            $_SESSION['error'] = "Error de conexión a la base de datos";
        }
    }

    public function index() {
        $this->gestionPagos();
    }

    public function gestionPagos() {
        try {
            // Verificar que es un empleado
            if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 2) {
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }

            // Obtener todos los datos necesarios
            $pagosPendientes = $this->model->obtenerPagosPorEstado('Pendiente');
            $pagosCompletados = $this->model->obtenerPagosPorEstado('Completado');
            
            // Verificar y preparar datos para la vista
            $datosVista = [
                'pagosPendientes' => is_array($pagosPendientes) ? $pagosPendientes : [],
                'pagosCompletados' => is_array($pagosCompletados) ? $pagosCompletados : []
            ];

            // Incluir la vista con los datos
            extract($datosVista);
            require 'views/empleado/gestion_pagos.php';
            
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error en gestión de pagos: " . $e->getMessage());
            $_SESSION['error'] = "Ocurrió un error al cargar los pagos";
            header("Location: index.php?ctrl=empleado&action=index");
            exit();
        }
    }

    public function actualizarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Verificar que es un empleado
                if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 2) {
                    header('Location: index.php?ctrl=login&action=index');
                    exit();
                }

                $idPago = filter_input(INPUT_POST, 'id_pago', FILTER_VALIDATE_INT);
                $nuevoEstado = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_STRING);

                if (!$idPago || !$nuevoEstado) {
                    throw new Exception("Datos de pago inválidos");
                }

                if ($this->model->actualizarEstadoPago($idPago, $nuevoEstado)) {
                    $_SESSION['mensaje'] = "Estado del pago actualizado correctamente";
                } else {
                    $_SESSION['error'] = "No se pudo actualizar el estado del pago";
                }
                
            } catch (Exception $e) {
                error_log("Error al actualizar estado de pago: " . $e->getMessage());
                $_SESSION['error'] = "Error al procesar la solicitud";
            }
            
            header("Location: index.php?ctrl=empleadoPagos&action=gestionPagos");
            exit();
        }
    }

    public function verDetalle() {
        try {
            // Verificar que es un empleado
            if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 2) {
                header('Location: index.php?ctrl=login&action=index');
                exit();
            }

            $idPago = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$idPago) {
                $_SESSION['error'] = "ID de pago inválido";
                header("Location: index.php?ctrl=empleadoPagos&action=gestionPagos");
                exit();
            }

            $detallePago = $this->model->obtenerDetallePago($idPago);
            
            if (!$detallePago) {
                $_SESSION['error'] = "Pago no encontrado";
                header("Location: index.php?ctrl=empleadoPagos&action=gestionPagos");
                exit();
            }

            // Pasar datos a la vista
            extract(['detallePago' => $detallePago]);
            require 'views/empleado/detalle_pago.php';
            
        } catch (Exception $e) {
            error_log("Error al ver detalle de pago: " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar el detalle del pago";
            header("Location: index.php?ctrl=empleadoPagos&action=gestionPagos");
            exit();
        }
    }
}
?>