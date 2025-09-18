<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floraltech - Gestión de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/pagos/assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>


    <div class="container-fluid">
        <div class="row">
            <?php if (isset($_SESSION['rol'])): ?>
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <?php include 'partials/sidebar_admin.php'; ?>
                <?php elseif ($_SESSION['rol'] === 'empleado'): ?>
                    <?php include 'partials/sidebar_empleado.php'; ?>
                <?php elseif ($_SESSION['rol'] === 'cliente'): ?>
                    <?php include 'partials/sidebar_cliente.php'; ?>
                <?php endif; ?>
            <?php endif; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-<?= $_SESSION['mensaje_tipo'] ?? 'success' ?> mt-3">
                        <?= htmlspecialchars($_SESSION['mensaje']) ?>
                        <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
                    </div>
                <?php endif; ?>