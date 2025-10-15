<?php
require_once 'models/mconfig.php';

class cconfig {
    private $model;

    public function __construct() {
        $this->model = new mconfig();
    }

    /**
     * Muestra la vista principal de configuración
     */
    public function index() {
    // Obtener la configuración actual del sistema
    $config = $this->model->getCurrentConfig();
        
    // Cargar la vista correspondiente
    require_once 'views/admin/VaconfigAvanzada.php';
    }

    /**
     * Muestra el formulario de edición de configuración
     */
    public function edit() {
        // Verificar permisos (aquí puedes agregar tu lógica de autorización)
        // if (!tienePermiso('editar_configuracion')) {
        //     header('Location: index.php?ctrl=cconfig&action=index');
        //     exit();
        // }
        
        $config = $this->model->getCurrentConfig();
        require_once 'views/config/edit.php';
    }

    /**
     * Procesa la actualización de la configuración
     */
    public function update() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Cargar datos del formulario al modelo
            $this->model->loadFromArray($_POST);
            
            // Establecer el ID del usuario que realiza la modificación
            // (aquí deberías obtener el ID del usuario logueado)
            $this->model->setIdUsuMod(isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
            
            // Intentar actualizar
            if ($this->model->update()) {
                $_SESSION['success_message'] = "Configuración actualizada correctamente";
                
                // Registrar el cambio en el historial si es necesario
                $this->registrarCambio($_POST);
            } else {
                $_SESSION['error_message'] = "Error al actualizar la configuración";
            }
            
            header('Location: index.php?ctrl=cconfig&action=index');
            exit();
        }
    }

    /**
     * Muestra el historial de cambios de configuración
     */
    public function history() {
        // Aquí implementarías la lógica para obtener el historial de cambios
        // Esto dependerá de cómo tengas implementado el registro de cambios
        
        // $historial = obtenerHistorialConfiguracion();
        // require_once 'views/config/history.php';
        
        // Por ahora redirigimos al índice
        header('Location: index.php?ctrl=cconfig&action=index');
        exit();
    }

    /**
     * Registra un cambio en la configuración (método auxiliar)
     */
    private function registrarCambio($nuevaConfig) {
        // Implementar lógica para registrar cambios en un log o tabla de historial
        // Esto es opcional pero recomendado para auditoría
        
        // Ejemplo básico:
        /*
        $oldConfig = $this->model->getCurrentConfig();
        $cambios = [];
        
        foreach ($oldConfig as $key => $value) {
            if (isset($nuevaConfig[$key]) && $nuevaConfig[$key] != $value) {
                $cambios[$key] = [
                    'old' => $value,
                    'new' => $nuevaConfig[$key]
                ];
            }
        }
        
        if (!empty($cambios)) {
            guardarEnHistorial($cambios, $_SESSION['user_id']);
        }
        */
    }

    /**
     * Restaura una configuración anterior (opcional)
     */
    public function restore($idHistorial) {
        // Implementar lógica para restaurar una configuración anterior
        // Esto sería útil si tienes un sistema de historial/backup
        
        // Por ahora redirigimos al índice
        header('Location: index.php?ctrl=cconfig&action=index');
        exit();
    }
}