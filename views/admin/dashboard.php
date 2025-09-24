<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloralTech - Sistema Integral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-admin.css">
</head>
<body>
    <?php
    if (!isset($totalUsuarios)) {
        $totalUsuarios = 0;
    }
    ?>
    <div class="d-flex flex-column" style="min-height: 100vh; background: #f7f7fb;">
        <!-- Barra superior única -->
        <div class="w-100 px-4 pt-3 pb-2" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%); border-radius: 16px; margin: 24px auto 0 auto; max-width: 98%; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="d-flex align-items-center">
                <span class="fs-3 fw-bold text-white me-3"><i class="fa-solid fa-seedling me-2"></i>FloralTech</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="fw-bold text-white me-3">Bienvenido, <?= isset($usu['nombre_completo']) ? htmlspecialchars($usu['nombre_completo']) : 'Administrador' ?></span>
                <a href="index.php?ctrl=login&action=logout" class="btn btn-light fw-bold"><i class="fa-solid fa-sign-out-alt me-1"></i> Cerrar Sesión</a>
            </div>
        </div>
        <div class="d-flex flex-row flex-grow-1" style="width: 100%;">
            <!-- Sidebar -->
            <aside class="bg-white border-end shadow-sm" style="width: 250px; min-width: 220px; border-radius: 16px; margin: 24px 0 24px 24px; height: calc(100vh - 120px);">
                <ul class="nav flex-column p-3">
                    <li class="nav-item mb-2"><a class="nav-link" href="?page=general"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=empleados"><i class="fas fa-users me-2"></i> Gestión de Empleados</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=inventarios"><i class="fa-solid fa-server me-2"></i> Inventario</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=pedidos"><i class="fas fa-shopping-cart me-2"></i> Gestión de Pedidos</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=configuracion"><i class="fas fa-cogs me-2"></i> Configuración</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=auditoria"><i class="fas fa-clipboard-list me-2"></i> Auditoría</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="index.php?ctrl=dashboard&action=admin&page=reportes"><i class="fas fa-chart-bar me-2"></i> Reportes</a></li>
                </ul>
            </aside>
            <!-- Main Content -->
            <div class="main-content flex-grow-1 d-flex flex-column align-items-center justify-content-center" id="mainContent" style="margin: 24px 24px 24px 0; min-height: calc(100vh - 120px);">
                <div class="w-100" style="max-width: 1100px;">
                    <?php
                    // Priorizar $page si está definida (desde controlador), sino usar $_GET['page']
                    if (!isset($page)) {
                        $page = $_GET['page'] ?? 'general';
                    }
                    
                    $pages = [
                        'general' => 'dashboard_general.php',
                        'empleados' => 'dgemp.php',
                        'inventarios' => null, // Redirigir al controlador
                        'inventario' => 'inventario.php', // Desde el controlador cinventario
                        'pedidos' => 'gestion_pedidos.php',
                        'pagos' => 'dashboard_pago.php',
                        'configuracion' => 'configuracion.php',
                        'auditoria' => 'auditoria_pago.php',
                        'reportes' => 'reportes_pago.php'
                    ];
                    
                    if ($page === 'inventarios') {
                        // Redirigir al controlador de inventario
                        header('Location: index.php?ctrl=cinventario');
                        exit;
                    } elseif ($page === 'general') {
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>