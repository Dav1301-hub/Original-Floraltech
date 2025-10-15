<?php
require_once __DIR__ . '/../models/FlorModel.php';

class FlorController {
    private $model;

    public function __construct() {
        $this->model = new FlorModel();
    }

    public function index() {
        $flores = $this->model->getAllFlores();
        require_once 'views/admin/gestionar_flores.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $naturaleza = $_POST['naturaleza'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $stock = intval($_POST['stock'] ?? 0);
            $precio = floatval($_POST['precio'] ?? 0);
            $this->model->addFlor($nombre, $naturaleza, $descripcion, $stock, $precio);
            header('Location: index.php?ctrl=FlorController&action=index');
            exit();
        }
        require_once 'views/admin/agregar_flor.php';
    }

    public function edit() {
        $id = intval($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $naturaleza = $_POST['naturaleza'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $stock = intval($_POST['stock'] ?? 0);
            $precio = floatval($_POST['precio'] ?? 0);
            $this->model->updateFlor($id, $nombre, $naturaleza, $descripcion, $stock, $precio);
            header('Location: index.php?ctrl=FlorController&action=index');
            exit();
        }
        $flor = $this->model->getFlor($id);
        require_once 'views/admin/editar_flor.php';
    }

    public function delete() {
        $id = intval($_GET['id'] ?? 0);
        $this->model->deleteFlor($id);
        header('Location: index.php?ctrl=FlorController&action=index');
        exit();
    }
}
