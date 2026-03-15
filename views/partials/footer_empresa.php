<?php
/**
 * Footer global con datos de la empresa (configuración guardada en admin).
 * Incluir en todas las vistas: <?php include __DIR__ . '/../partials/footer_empresa.php'; ?>
 */
$footer_empresa = null;
try {
    require_once __DIR__ . '/../../models/conexion.php';
    $conn_footer = new conexion();
    $db_footer = $conn_footer->get_conexion();
    try {
        $stmt = $db_footer->prepare("SELECT nombre, direccion, telefono, email_contacto, horarios_apertura, logo, facebook, instagram, whatsapp, footer_activo FROM empresa LIMIT 1");
        $stmt->execute();
        $footer_empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        try {
            $stmt = $db_footer->prepare("SELECT nombre, direccion, telefono, email_contacto, horarios_apertura, logo, facebook, instagram, whatsapp FROM empresa LIMIT 1");
            $stmt->execute();
            $footer_empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e2) {
            $stmt = $db_footer->prepare("SELECT nombre, direccion, telefono, email_contacto, horarios_apertura FROM empresa LIMIT 1");
            $stmt->execute();
            $footer_empresa = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($footer_empresa) {
                $footer_empresa['logo'] = $footer_empresa['facebook'] = $footer_empresa['instagram'] = $footer_empresa['whatsapp'] = null;
            }
        }
        if ($footer_empresa && !isset($footer_empresa['footer_activo'])) {
            $footer_empresa['footer_activo'] = 1;
        }
    }
} catch (Exception $e) {
    $footer_empresa = [];
}
$emp = $footer_empresa ?: [];
$footer_activo = (int) ($emp['footer_activo'] ?? 1);
$nombre_empresa = $emp['nombre'] ?? 'FloralTech';
$direccion = trim($emp['direccion'] ?? '');
$telefono = trim($emp['telefono'] ?? '');
$email_contacto = trim($emp['email_contacto'] ?? '');
$horarios = trim($emp['horarios_apertura'] ?? '');
$facebook = trim($emp['facebook'] ?? '');
$instagram = trim($emp['instagram'] ?? '');
$whatsapp = trim($emp['whatsapp'] ?? '');
$logo_footer = null;
if (!empty($emp['logo']) && file_exists(__DIR__ . '/../../' . $emp['logo'])) {
    $logo_footer = $emp['logo'];
}

// Tema del footer según la vista: cliente (morado), empleado (teal), admin (azul)
// Se puede forzar desde la vista definiendo $footer_theme antes del include ('cliente'|'empleado'|'admin')
if (!isset($footer_theme) || !in_array($footer_theme, ['cliente', 'empleado', 'admin'], true)) {
    $footer_theme = 'cliente';
    if (isset($_GET['ctrl'])) {
        $ctrl = (string)$_GET['ctrl'];
        if ($ctrl === 'empleado') {
            $footer_theme = 'empleado';
        } elseif ($ctrl === 'dashboard' && isset($_GET['action']) && $_GET['action'] === 'admin') {
            $footer_theme = 'admin';
        } elseif ($ctrl === 'cinventario') {
            $footer_theme = 'admin';
        }
    }
}
// Solo mostrar footer en zona cliente y si está activado en configuración
if ($footer_activo !== 1) {
    return;
}
?>
<style>
.footer-empresa { color: #e2e8f0; padding: 2.5rem 0 1.25rem; margin-top: auto; }
.footer-empresa a { text-decoration: none; transition: color 0.2s; }
.footer-empresa a:hover { color: #fff; }
.footer-empresa .footer-logo { max-height: 42px; width: auto; object-fit: contain; }
.footer-empresa .footer-brand { font-weight: 700; font-size: 1.1rem; }
.footer-empresa .footer-item { display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.5rem; }
.footer-empresa .footer-item i { margin-top: 0.2rem; width: 1rem; text-align: center; }
.footer-empresa .footer-social a { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; color: #e2e8f0; margin-right: 0.5rem; transition: background 0.2s, color 0.2s; }
.footer-empresa .footer-social a:hover { color: #fff; }
.footer-empresa .footer-bottom { margin-top: 1.5rem; padding-top: 1rem; text-align: center; font-size: 0.85rem; }
.footer-empresa .footer-tagline { font-size: 0.9rem; opacity: 0.9; margin-top: 0.25rem; }
.footer-empresa .footer-section-title { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; margin-bottom: 1rem; opacity: 0.95; }
.footer-empresa .footer-cta-whatsapp { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; background: rgba(255,255,255,0.2); color: #fff; font-weight: 600; transition: background 0.2s, transform 0.15s; }
.footer-empresa .footer-cta-whatsapp:hover { background: rgba(255,255,255,0.35); color: #fff; transform: translateY(-1px); }
.footer-empresa .footer-help-text { font-size: 0.9rem; margin-bottom: 0.75rem; }
.footer-empresa .footer-quick-desc { font-size: 0.875rem; line-height: 1.5; opacity: 0.9; }
.footer-empresa .footer-placeholder { opacity: 0.7; font-style: italic; }
/* Cliente: mismo gradiente que navbar (--cli-header-bg) */
.footer-empresa.footer-cliente { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.footer-empresa.footer-cliente a { color: rgba(255,255,255,0.9); }
.footer-empresa.footer-cliente .footer-item i { color: rgba(255,255,255,0.85); }
.footer-empresa.footer-cliente .footer-social a { background: rgba(255,255,255,0.2); }
.footer-empresa.footer-cliente .footer-social a:hover { background: rgba(255,255,255,0.35); }
.footer-empresa.footer-cliente .footer-bottom { border-top: 1px solid rgba(255,255,255,0.25); color: rgba(255,255,255,0.9); }
.footer-empresa.footer-cliente .text-uppercase { color: rgba(255,255,255,0.9); }
/* Empleado: mismo gradiente que navbar (--emp-header-bg) */
.footer-empresa.footer-empleado { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); }
.footer-empresa.footer-empleado a { color: rgba(255,255,255,0.9); }
.footer-empresa.footer-empleado .footer-item i { color: rgba(255,255,255,0.85); }
.footer-empresa.footer-empleado .footer-social a { background: rgba(255,255,255,0.2); }
.footer-empresa.footer-empleado .footer-social a:hover { background: rgba(255,255,255,0.35); }
.footer-empresa.footer-empleado .footer-bottom { border-top: 1px solid rgba(255,255,255,0.25); color: rgba(255,255,255,0.9); }
.footer-empresa.footer-empleado .text-uppercase { color: rgba(255,255,255,0.9); }
/* Admin: mismo gradiente que topbar */
.footer-empresa.footer-admin { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
.footer-empresa.footer-admin a { color: rgba(255,255,255,0.9); }
.footer-empresa.footer-admin .footer-item i { color: rgba(255,255,255,0.85); }
.footer-empresa.footer-admin .footer-social a { background: rgba(255,255,255,0.2); }
.footer-empresa.footer-admin .footer-social a:hover { background: rgba(255,255,255,0.35); }
.footer-empresa.footer-admin .footer-bottom { border-top: 1px solid rgba(255,255,255,0.25); color: rgba(255,255,255,0.9); }
.footer-empresa.footer-admin .text-uppercase { color: rgba(255,255,255,0.9); }
</style>
<footer class="footer-empresa footer-<?= $footer_theme ?>">
    <div class="container">
        <div class="row g-4">
            <!-- Columna 1: Marca y contacto -->
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <?php if ($logo_footer): ?>
                        <img src="<?= htmlspecialchars($logo_footer) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($nombre_empresa) ?>" class="footer-logo">
                    <?php else: ?>
                        <i class="fas fa-seedling fa-2x" style="opacity: 0.9;"></i>
                    <?php endif; ?>
                    <span class="footer-brand"><?= htmlspecialchars($nombre_empresa) ?></span>
                </div>
                <p class="footer-tagline mb-3">Tu florería, gestionada con sencillez. Pedidos, inventario y clientes en un solo lugar.</p>
                <h6 class="footer-section-title text-uppercase">Dónde estamos</h6>
                <?php if ($direccion): ?>
                    <div class="footer-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($direccion) ?></span>
                    </div>
                <?php else: ?>
                    <div class="footer-item footer-placeholder"><i class="fas fa-map-marker-alt"></i><span>Dirección en Configuración</span></div>
                <?php endif; ?>
                <?php if ($telefono): ?>
                    <div class="footer-item">
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?= preg_replace('/\s+/', '', $telefono) ?>"><?= htmlspecialchars($telefono) ?></a>
                    </div>
                <?php endif; ?>
                <?php if ($email_contacto): ?>
                    <div class="footer-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?= htmlspecialchars($email_contacto) ?>"><?= htmlspecialchars($email_contacto) ?></a>
                    </div>
                <?php endif; ?>
                <h6 class="footer-section-title text-uppercase mt-3">Horario</h6>
                <?php if ($horarios): ?>
                    <div class="footer-item">
                        <i class="fas fa-clock"></i>
                        <span><?= nl2br(htmlspecialchars($horarios)) ?></span>
                    </div>
                <?php else: ?>
                    <div class="footer-item footer-placeholder"><i class="fas fa-clock"></i><span>Consultar en tienda</span></div>
                <?php endif; ?>
            </div>
            <!-- Columna 2: Redes sociales -->
            <div class="col-lg-4">
                <h6 class="footer-section-title text-uppercase">Síguenos</h6>
                <p class="footer-quick-desc mb-3">Mantente al día de novedades, ofertas y consejos. ¡Te esperamos en redes!</p>
                <div class="footer-social">
                    <?php if ($facebook): ?>
                        <a href="<?= htmlspecialchars(strpos($facebook, 'http') === 0 ? $facebook : 'https://' . $facebook) ?>" target="_blank" rel="noopener" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if ($instagram): ?>
                        <a href="<?= htmlspecialchars(strpos($instagram, 'http') === 0 ? $instagram : 'https://instagram.com/' . ltrim($instagram, '@')) ?>" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if ($whatsapp): ?>
                        <?php $wa = preg_replace('/[^0-9]/', '', $whatsapp); ?>
                        <a href="https://wa.me/<?= $wa ?>" target="_blank" rel="noopener" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
                <?php if (!$facebook && !$instagram && !$whatsapp): ?>
                    <p class="footer-placeholder small mt-2 mb-0">Añade tus redes en Admin → Configuración</p>
                <?php endif; ?>
                <p class="footer-quick-desc mt-4 mb-0">FloralTech es el sistema de gestión integral para florerías: pedidos, catálogo, inventario y facturación.</p>
            </div>
            <!-- Columna 3: Ayuda y CTA -->
            <div class="col-lg-4">
                <h6 class="footer-section-title text-uppercase">¿Necesitas ayuda?</h6>
                <p class="footer-help-text">¿Dudas sobre un pedido o quieres hacer una consulta? Escríbenos y te respondemos lo antes posible.</p>
                <?php if ($whatsapp): ?>
                    <?php $wa = preg_replace('/[^0-9]/', '', $whatsapp); ?>
                    <a href="https://wa.me/<?= $wa ?>" target="_blank" rel="noopener" class="footer-cta-whatsapp mb-3">
                        <i class="fab fa-whatsapp fa-lg"></i> Escribir por WhatsApp
                    </a>
                <?php elseif ($email_contacto): ?>
                    <a href="mailto:<?= htmlspecialchars($email_contacto) ?>" class="footer-cta-whatsapp mb-3">
                        <i class="fas fa-envelope"></i> Enviar un correo
                    </a>
                <?php else: ?>
                    <p class="footer-placeholder small">Configura WhatsApp o email en Admin para ofrecer contacto directo.</p>
                <?php endif; ?>
                <p class="footer-quick-desc mb-0">Gracias por confiar en <?= htmlspecialchars($nombre_empresa) ?>. Trabajamos para que tu experiencia sea la mejor.</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($nombre_empresa) ?>. Todos los derechos reservados.
        </div>
    </div>
</footer>
