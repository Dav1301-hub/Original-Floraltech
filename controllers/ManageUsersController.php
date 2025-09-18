<?php
class ManageUsersController {
    public function index() {
        require_once __DIR__ . '/../models/user.php';
        $userModel = new User();
        $usuarios = $userModel->getAllUsers();
        // Hacer disponible la variable $usuarios en la vista
        include __DIR__ . '/../views/manageusers.php';
    }
}
?>
