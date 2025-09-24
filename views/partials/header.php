<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloralTech - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/dashboard-admin.css">
    <link rel="stylesheet" href="../../assets/inventario.css">
</head>
<body>
    <!-- Navbar de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="?ctrl=dashboard&action=admin">
                <i class="fas fa-seedling me-2"></i>FloralTech Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="?ctrl=logout&action=index">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal con padding para navbar fija -->
    <div class="container-fluid" style="padding-top: 80px;">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['mensaje_tipo'] ?? 'success' ?> mt-3">
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
            </div>
        <?php endif; ?>