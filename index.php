<?php
require_once 'php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: php/login.php");
    exit();
}

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
    </main>
</body>
</html>
