
<?php
session_start();
// Endpoint público para servir el formulario de nuevo pedido como fragmento
// Permite acceso desde AJAX en el dashboard admin

// Permitir parámetros GET: fecha
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$_GET['fragment'] = '1'; // Forzar modo fragmento
if ($fecha) {
    $_GET['fecha'] = $fecha;
}
// Incluir el formulario como fragmento
include __DIR__ . '/../views/cliente/nuevo_pedido.php';
