<?php
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$baseUrl = preg_replace('#/php(?:/.*)?/[^/]*$#', '', $scriptName);

if ($baseUrl === null || $baseUrl === $scriptName) {
    $baseUrl = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
}

$baseUrl = $baseUrl === '' || $baseUrl === '.' ? '' : $baseUrl;
$year = date('Y');
?>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="footer-brand">
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/index.php">Dream Colors</a>
            <p>Peluqueria y estetica con atencion personalizada.</p>
        </div>

        <nav class="footer-links" aria-label="Enlaces del pie de pagina">
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/index.php#servicios">Servicios</a>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/index.php#precios">Precios</a>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/index.php#promociones">Promociones</a>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/index.php#sobre-dream-colors">Sobre nosotros</a>
        </nav>

        <div class="footer-contact">
            <span>info@dreamcolors.local</span>
            <span>+34 000 000 000</span>
            <span>&copy; <?php echo $year; ?> Dream Colors</span>
        </div>
    </div>
</footer>
