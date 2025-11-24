<?php
session_start();
require_once 'config/db.php';

// Verificar sesión activa
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();

// Obtener el ID del usuario a eliminar
$idUsuario = $_GET['id'] ?? null;

if ($idUsuario) {
    // Verificar si existe el usuario antes de eliminar
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE idUsuario = :id");
    $stmt->bindParam(':id', $idUsuario);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Eliminar el usuario
        $deleteStmt = $db->prepare("DELETE FROM usuarios WHERE idUsuario = :id");
        $deleteStmt->bindParam(':id', $idUsuario);
        $deleteStmt->execute();
    }
}

// Redirigir de nuevo a la gestión de usuarios
header("Location: index.php?ctrl=ManageUsersController&action=index");
exit();
