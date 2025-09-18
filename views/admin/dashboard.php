<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloralTech - Sistema Integral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php
    if (!isset($totalUsuarios)) {
        $totalUsuarios = 0;
    }
    ?>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <aside class="bg-white border-end" style="width: 250px; min-width: 220px;">
            <ul class="nav flex-column p-3">
                    <li class="nav-item mb-2"><a class="nav-link" href="?page=general"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=empleados"><i class="fas fa-users me-2"></i> Gestión de Empleados</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=inventarios"><i class="fa-solid fa-server me-2"></i> Inventario</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=pedidos"><i class="fas fa-shopping-cart me-2"></i> Gestión de Pedidos</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=pagos"><i class="fas fa-credit-card me-2"></i> Gestión de Pagos</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=configuracion"><i class="fas fa-cogs me-2"></i> Configuración</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=auditoria"><i class="fas fa-clipboard-list me-2"></i> Auditoría</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=reportes"><i class="fas fa-chart-bar me-2"></i> Reportes</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=FlorController&action=index"><i class="fas fa-spa me-2"></i> Gestión de Flores</a></li>
            </ul>
            
            <div class="p-3 border-top mt-auto">
                <span class="d-block mb-2">¡Hola, <?= isset($usu['nombre_completo']) ? explode(' ', $usu['nombre_completo'])[0] : 'Administrador' ?>!</span>
                <a href="index.php?ctrl=login&action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </aside>
        <div class="main-content" id="mainContent">
            <?php
            $page = $_GET['page'] ?? 'general';
            $pages = [
                'general' => 'dashboard_general.php',
                'empleados' => 'dgemp.php',
                'inventarios' => 'inventario.php',
                'pedidos' => 'gestion_pedidos.php',
                'pagos' => 'dashboard_pago.php',
                'configuracion' => 'configuracion.php',
                'auditoria' => 'auditoria_pago.php',
                'reportes' => 'reportes_pago.php'
            ];
            if ($page === 'general') {
                include __DIR__ . '/dashboard_general.php';
            } elseif ($page === 'empleados') {
                include __DIR__ . '/dgemp.php';
            } else {
                $file = isset($pages[$page]) ? $pages[$page] : $pages['general'];
                $filePath = __DIR__ . '/' . $file;
                if ($file && file_exists($filePath)) {
                    include $filePath;
                } else {
                    echo '<div class="alert alert-warning">Página no encontrada.</div>';
                }
            }
            ?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>