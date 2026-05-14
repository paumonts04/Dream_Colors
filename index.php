<?php
require_once 'php/conexion.php';

$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Colors</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="home-body">
    <main class="home-wrapper">
        <?php include 'php/header.php'; ?>

        <section class="hero-stage" aria-label="Video principal">
            <video class="hero-video" autoplay muted loop playsinline preload="metadata">
                <source src="videos/hero.webm" type="video/webm">
                Tu navegador no puede reproducir este video.
            </video>
        </section>

        <section class="split-videos" id="servicios" aria-label="Videos de servicios">
            <video class="split-video" autoplay muted loop playsinline preload="metadata">
                <source src="videos/liso.webm" type="video/webm">
                Tu navegador no puede reproducir este video.
            </video>

            <video class="split-video" autoplay muted loop playsinline preload="metadata">
                <source src="videos/planxa.webm" type="video/webm">
                Tu navegador no puede reproducir este video.
            </video>
        </section>
    </main>
    <?php include 'php/footer.php'; ?>
    <script src="js/header-scroll.js"></script>
</body>
</html>
