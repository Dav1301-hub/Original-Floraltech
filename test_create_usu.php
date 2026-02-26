<?php
require_once 'models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$email = 'jorgepb2007@gmail.com';
$stmt = $db->prepare("SELECT COUNT(*) FROM usu WHERE email = ?");
$stmt->execute([$email]);
$count = $stmt->fetchColumn();

echo "Usuarios en 'usu' con correo $email: $count\n\n";

if ($count == 0) {
    echo "¡Creando usuario para que pueda loguearse!\n";
    // Necesitamos crearlo
    $clave = password_hash('12345678', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO usu (username, nombre_completo, telefono, naturaleza, email, clave, tpusu_idtpusu, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['jorgepb2007', 'Jorge Luis Puentes Brochero', '3217837594', 'Sin dirección', $email, $clave, 5, 1]);
    echo "Usuario creado exitosamente. Ya puede entrar con clave 12345678\n";
}
