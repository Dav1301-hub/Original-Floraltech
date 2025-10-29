<?php
class CempleadoPagos {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function gestionPagos() {
        try {
            // Obtener todos los datos necesarios
            $pagosPendientes = $this->model->obtenerPagosPorEstado('Pendiente');
            $pagosCompletados = $this->model->obtenerPagosCompletadosRecientes();
            
            // Verificar y preparar datos para la vista
            $datosVista = [
                'pagosPendientes' => is_array($pagosPendientes) ? $pagosPendientes : [],
                'pagosCompletados' => is_array($pagosCompletados) ? $pagosCompletados : []
            ];

            // Incluir la vista con los datos
            extract($datosVista);
            require 'views/empleado/gestion.php';
            
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error en gestión de pagos: " . $e->getMessage());
            $_SESSION['error'] = "Ocurrió un error al cargar los pagos";
            header("Location: /empleado/dashboard");
            exit();
        }
    }

    public function actualizarEstadoPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
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
            
            header("Location: /empleado/pagos/gestion");
            exit();
        }
    }
}
?>