<?php
require_once 'models/FlorModel.php';
header('Content-Type: application/json');

class FlorApiController {
    public function getFlores() {
        $model = new FlorModel();
        $flores = $model->getAllFlores();
        echo json_encode($flores);
        exit();
    }
}

// Endpoint simple para AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getFlores') {
    $api = new FlorApiController();
    $api->getFlores();
}
