<?php
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$email = 'jorgepb2007@gmail.com';
$stmt = $db->prepare("SELECT idusu, email, activo, intentos_fallidos, fecha_bloqueo, motivo_bloqueo FROM usu WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

print_r($user);

// Si está bloqueado o con intentos, los reseteamos por si acaso
if ($user && ($user['activo'] == 0 || $user['intentos_fallidos'] > 0)) {
    $stmt = $db->prepare("UPDATE usu SET activo = 1, intentos_fallidos = 0, fecha_bloqueo = NULL, motivo_bloqueo = NULL WHERE email = ?");
    $stmt->execute([$email]);
    echo "\nUsuario DESBLOQUEADO.\n";
}
