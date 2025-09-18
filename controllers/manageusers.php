<?php
// controllers/manage-users.php
header('Content-Type: application/json');


require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/user.php';

$user = new User();
$users = $user->getAllUsers();

echo json_encode($users);
?>