<?php
$user = $user ?? $_SESSION['user'] ?? [];
$logo_empresa = null;
$avatar_empleado = null;
try {
    require_once __DIR__ . '/../../../models/conexion.php';
    $conexion = (new conexion())->get_conexion();
    $stmt_empresa = $conexion->prepare("SELECT logo FROM empresa LIMIT 1");
    $stmt_empresa->execute();
    $empresa_data = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
    $logo_empresa = $empresa_data['logo'] ?? null;
    if (!empty($user['idusu'])) {
        $stmt_usu = $conexion->prepare("SELECT idusu, avatar, avatar_data FROM usu WHERE idusu = ? LIMIT 1");
        $stmt_usu->execute([$user['idusu']]);
        $row = $stmt_usu->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (!empty($row['avatar_data'])) {
                $avatar_empleado = 'avatar.php?tipo=usu&id=' . (int)$row['idusu'];
            } elseif (!empty($row['avatar']) && file_exists(__DIR__ . '/../../../' . $row['avatar'])) {
                $avatar_empleado = $row['avatar'];
            }
        }
    }
} catch (Exception $e) {
    $logo_empresa = null;
    $avatar_empleado = null;
}
?>
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-brand navbar-brand-logo">
            <?php if (!empty($logo_empresa) && file_exists(__DIR__ . '/../../../' . $logo_empresa)): ?>
                <img src="<?= htmlspecialchars($logo_empresa) ?>?v=<?= time() ?>" alt="Logo" class="navbar-logo-img">
            <?php else: ?>
                <i class="fas fa-seedling navbar-logo-fallback"></i>
            <?php endif; ?>
            <div class="navbar-brand-text">
                <span class="navbar-eyebrow">Panel Empleado</span>
                <span class="navbar-app-name">FloralTech</span>
            </div>
        </div>
        <div class="navbar-user">
            <div class="navbar-user-block">
                <span class="navbar-user-avatar-wrap">
                    <?php if (!empty($avatar_empleado)): ?>
                        <img src="<?= htmlspecialchars($avatar_empleado) ?>?v=<?= time() ?>" alt="" class="navbar-user-avatar">
                    <?php else: ?>
                        <span class="navbar-user-avatar navbar-user-avatar-placeholder"><i class="fas fa-user"></i></span>
                    <?php endif; ?>
                </span>
                <span class="user-info-text">
                    <small class="user-label">Sesión</small>
                    <strong class="user-name"><?= htmlspecialchars($user['nombre_completo'] ?? 'Usuario') ?></strong>
                </span>
            </div>
            <div class="navbar-actions">
                <?php if (!empty($navbar_volver_url)): ?>
                    <a href="<?= htmlspecialchars($navbar_volver_url) ?>" class="btn-navbar navbar-volver-btn">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                <?php endif; ?>
                <a href="index.php?ctrl=login&action=logout" class="btn-navbar btn-navbar-logout">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
                </a>
            </div>
        </div>
    </div>
</nav>
