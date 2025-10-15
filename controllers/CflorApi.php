<?php
require_once 'models/Mflor.php';
header('Content-Type: application/json');

class CflorApi {
    public function getFlores() {
        $model = new Mflor();
        $flores = $model->getAllFlores();
        echo json_encode($flores);
        exit();
    }
}

// Endpoint simple para AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getFlores') {
    $api = new CflorApi();
    $api->getFlores();
}
