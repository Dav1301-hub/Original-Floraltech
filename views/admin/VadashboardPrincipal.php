<?php
if (!isset($totalUsuarios)) {
    $totalUsuarios = 0;
}

$usu = $usu ?? ($_SESSION['user'] ?? []);
$page = $page ?? ($_GET['page'] ?? 'general');
$pg = $_GET['pg'] ?? null;

// Cargar logo y avatar
require_once __DIR__ . '/../../models/conexion.php';
$conexion = (new conexion())->get_conexion();
try {
    $stmt_empresa = $conexion->prepare("SELECT logo FROM empresa LIMIT 1");
    $stmt_empresa->execute();
    $empresa_data = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
    $logo_empresa = $empresa_data['logo'] ?? null;
    
    $id_usuario = $_SESSION['user']['idusu'] ?? null;
    if ($id_usuario) {
        $stmt_usuario = $conexion->prepare("SELECT avatar FROM usu WHERE idusu = :id LIMIT 1");
        $stmt_usuario->execute([':id' => $id_usuario]);
        $usuario_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        $avatar_usuario = $usuario_data['avatar'] ?? null;
    } else {
        $avatar_usuario = null;
    }
} catch (Exception $e) {
    $logo_empresa = null;
    $avatar_usuario = null;
}

$pages = [
    'general'       => 'VadashboardGeneral.php',
    'empleados'     => 'VagestionarEmpleados.php',
    'inventarios'   => null, // redirige a controlador
    'inventario'    => 'Vainventario.php',
    'pedidos'       => 'VagestionPedidos.php',
    'pagos'         => 'VadashboardPagos.php',
    'configuracion' => 'Vaconfiguracion.php',
    'soporte'       => 'Vsoporte.php',
    'auditoria'     => 'VaauditoriaPagos.php',
    'reportes'      => 'Vareportes.php'
];

$pageTitles = [
    'general'       => 'Dashboard',
    'empleados'     => 'Gestion de Usuarios',
    'inventarios'   => 'Inventario',
    'inventario'    => 'Inventario',
    'pedidos'       => 'Gestion de Pedidos',
    'pagos'         => 'Pagos',
    'configuracion' => 'Configuracion',
    'soporte'       => 'Centro de Soporte',
    'auditoria'     => 'Auditoria',
    'reportes'      => 'Reportes'
];

$pgs = [
    'ggp' => 'catin.php'
];

function render_admin_view(string $filePath, array $context = []): void
{
    if (!file_exists($filePath)) {
        echo '<div class="alert alert-warning mb-0">Vista no encontrada.</div>';
        return;
    }

    ob_start();
    if (!empty($context)) {
        // Extrae variables de contexto para la vista incluida
        extract($context, EXTR_SKIP);
    }
    include $filePath;
    $content = ob_get_clean();

    if (preg_match('/<head[^>]*>(.*?)<\\/head>/is', $content, $matches)) {
        // Descartar head propio de la vista para evitar estilos que rompan el layout
        $content = str_replace($matches[0], '', $content);
    }

    // Quitar wrappers de documento
    $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
    $content = preg_replace('/<html[^>]*>/i', '', $content);
    $content = preg_replace('/<\\/html>/i', '', $content);
    $content = preg_replace('/<body[^>]*>/i', '', $content);
    $content = preg_replace('/<\\/body>/i', '', $content);

    // Quitar estilos y links incrustados de las vistas antiguas para no pisar el tema unificado
    $content = preg_replace('#<style[^>]*>.*?</style>#is', '', $content);
    $content = preg_replace('#<link[^>]*rel=[\"\\\']stylesheet[\"\\\'][^>]*>#i', '', $content);

    echo $content;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloralTech - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-admin.css?v=2">
    <link rel="stylesheet" href="assets/css/admin-unificado.css?v=2">
    <!-- Charts and Calendar Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
</head>
<body class="app-shell">
    <header class="topbar">
        <div class="brand">
            <?php if (!empty($logo_empresa) && file_exists(__DIR__ . '/../../' . $logo_empresa)): ?>
                <img src="<?= htmlspecialchars($logo_empresa) ?>?v=<?= time() ?>" alt="Logo" style="height: 40px; width: auto; object-fit: contain; margin-right: 10px;">
            <?php else: ?>
                <i class="fa-solid fa-seedling"></i>
            <?php endif; ?>
            <div>
                <div style="font-size:13px; color:#94a3b8;">Panel administrativo</div>
                <div style="font-size:16px;">FloralTech</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="sidebar-toggle" id="toggleSidebar" aria-label="Mostrar/Ocultar menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="user-chip">
                <?php if (!empty($avatar_usuario) && file_exists(__DIR__ . '/../../' . $avatar_usuario)): ?>
                    <img src="<?= htmlspecialchars($avatar_usuario) ?>?v=<?= time() ?>" alt="Avatar" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;">
                <?php else: ?>
                    <i class="fa-regular fa-circle-user"></i>
                <?php endif; ?>
                <div class="d-flex flex-column">
                    <small style="color:#94a3b8;">Sesion</small>
                    <strong><?= htmlspecialchars($usu['nombre_completo'] ?? 'Administrador') ?></strong>
                </div>
            </div>
            <a href="index.php?ctrl=login&action=logout" class="btn btn-logout">
                <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Salir
            </a>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="section-label">Panel</div>
            <a class="nav-link <?= $page === 'general' ? 'active' : '' ?>" href="?page=general"><i class="fas fa-gauge"></i>Dashboard</a>
            <div class="section-label">Operaciones</div>
            <a class="nav-link <?= $page === 'empleados' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=empleados"><i class="fas fa-users"></i>Gestion de Usuarios</a>
            <a class="nav-link <?= $page === 'inventarios' || $page === 'inventario' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=inventarios"><i class="fa-solid fa-server"></i>Inventario</a>
            <a class="nav-link <?= $page === 'pedidos' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=pedidos"><i class="fas fa-cart-shopping"></i>Gestion de Pedidos</a>
            <div class="section-label">Soporte</div>
            <a class="nav-link <?= $page === 'soporte' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=soporte"><i class="fas fa-life-ring"></i>Centro de Soporte</a>
            <div class="section-label">Control</div>
            <a class="nav-link <?= $page === 'configuracion' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=configuracion"><i class="fas fa-sliders"></i>Configuracion</a>
            <a class="nav-link <?= $page === 'auditoria' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=auditoria"><i class="fas fa-clipboard-list"></i>Auditoria</a>
            <a class="nav-link <?= $page === 'reportes' ? 'active' : '' ?>" href="index.php?ctrl=dashboard&action=admin&page=reportes"><i class="fas fa-chart-bar"></i>Reportes</a>
        </aside>

                <main class="main-panel" style="overflow-x:hidden;">
            <div class="content-area" style="overflow-x:hidden;">
                <div class="content-slot">
                <?php
                if ($page === 'inventarios') {
                    header('Location: index.php?ctrl=cinventario');
                    exit;
                }

                if ($pg && isset($pgs[$pg])) {
                    $file = $pgs[$pg];
                    $filePath = __DIR__ . '/' . $file;
                    render_admin_view($filePath);
                } else {
                    $file = $pages[$page] ?? $pages['general'];
                    $filePath = __DIR__ . '/' . $file;

                    // Permitir que un controlador pase $ctx preconstruido
                    if (!isset($ctx) || !is_array($ctx)) {
                        $ctx = [];
                    }
                    if ($file && file_exists($filePath)) {
                        switch ($file) {
                            case 'Vainventario.php':
                                // El contexto ya debe estar preparado por Cinventario
                                // Solo pasar si existe y no está vacío
                                break;
                            case 'VaauditoriaPagos.php':
                                require_once __DIR__ . '/../../controllers/AdminAuditoriaController.php';
                                $auditoriaCtrl = new AdminAuditoriaController();
                                $ctx = $auditoriaCtrl->obtenerContexto();
                                break;
                            case 'Vareportes.php':
                                require_once __DIR__ . '/../../controllers/ReportesController.php';
                                $reportesCtrl = new ReportesController();
                                $ctx = $reportesCtrl->obtenerContexto();
                                break;
                            case 'VagestionarEmpleados.php':
                                require_once __DIR__ . '/../../controllers/AdminEmpleadosController.php';
                                $empCtrl = new AdminEmpleadosController();
                                $ctx = $empCtrl->obtenerContexto();
                                break;
                        }
                        render_admin_view($filePath, $ctx);
                    } else {
                        echo '<div class="alert alert-warning">Pagina no encontrada: ' . htmlspecialchars($page) . '</div>';
                    }
                }
                ?>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            if (btn && sidebar) {
                btn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }
        });
    </script>
</body>
</html>


