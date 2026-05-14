<?php
$baseUrl = '/dream_colors';
?>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="footer-brand">
            <a href="<?= $baseUrl ?>/index.php">Dream Colors</a>
            <p>Peluqueria y estetica con atencion personalizada.</p>
        </div>

        <nav class="footer-links" aria-label="Enlaces del pie de pagina">
            <a href="<?= $baseUrl ?>/index.php#servicios">Servicios</a>
            <a href="<?= $baseUrl ?>/index.php#precios">Precios</a>
            <a href="<?= $baseUrl ?>/index.php#promociones">Promociones</a>
            <a href="<?= $baseUrl ?>/index.php#sobre-dream-colors">Sobre nosotros</a>
        </nav>

        <div class="footer-contact">
            <span>info@dreamcolors.local</span>
            <span>+34 000 000 000</span>
            <span>&copy; <?php echo $year; ?> Dream Colors</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/php/login-admin.php" style="font-size: 0.7rem; opacity: 0.4; display: inline-block; margin-top: 8px;">admin</a>
        </div>
    </div>
</footer>
