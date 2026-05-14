<?php
require_once 'conexion.php';

$id = intval($_GET['id'] ?? 0); //Validacion
if (!$id) {
    header('Location: ../index.php');
    exit();
}

$stmt = $con->prepare("
    SELECT s.*, c.tipo AS categoria
    FROM servicios s
    JOIN categoria c ON s.id_categoria = c.id
    WHERE s.id = ?
");

if (!$stmt) {
    die('No se pudo cargar el servicio.');
}

$stmt->bind_param("i", $id);
$stmt->execute();
$servicio = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$servicio) {
    header('Location: ../index.php');
    exit();
}

$nombreServicio      = $servicio['nombre']      ?? 'Servicio';
$categoriaServicio   = $servicio['categoria']   ?? 'Servicio';
$descripcionServicio = trim((string) ($servicio['descripcion'] ?? ''));
$duracionServicio    = $servicio['duracion']    ?? null;
$precioServicio      = (float) ($servicio['precio'] ?? 0);
$videoServicio       = trim((string) ($servicio['video']   ?? ''));
$imagenServicio      = trim((string) ($servicio['imagen']  ?? ''));
$reservaUrl          = 'reserva.php?id_servicio=' . (int) $servicio['id'];

// Fallback de imágenes por nombre normalizado
$servicioMediaClass = 'servicio-media-pos-center';

// Banner del header según categoría
$bannerMap = [
    'Peluquería'             => 'cabello',
    'Tratamientos Capilares' => 'capilar',
    'Estética'               => 'facial',
];
$headerBanner = $bannerMap[$categoriaServicio] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nombreServicio) ?> - Dream Colors</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="servicio-body">

<?php include __DIR__ . '/header.php'; ?>

<main class="servicio-detalle">

    <!-- Panel izquierdo: imagen / vídeo ocupa toda la altura -->
    <div class="servicio-media-wrapper">
        <?php if ($videoServicio !== ''): ?>
            <video
                class="servicio-media <?= htmlspecialchars($servicioMediaClass) ?>"
                src="../videos/<?= htmlspecialchars($videoServicio) ?>"
                autoplay muted loop playsinline
            ></video>
        <?php elseif ($imagenServicio !== ''): ?>
            <img
                class="servicio-media <?= htmlspecialchars($servicioMediaClass) ?>"
                src="../img/<?= htmlspecialchars($imagenServicio) ?>"
                alt="<?= htmlspecialchars($nombreServicio) ?>"
            >
        <?php else: ?>
            <div class="servicio-media-placeholder">
                <span><?= htmlspecialchars($nombreServicio) ?></span>
            </div>
        <?php endif; ?>
        <!-- Degradado lateral para suavizar la transición con el panel de info -->
        <div class="servicio-media-velo"></div>
    </div>

    <!-- Panel derecho: información del servicio -->
    <div class="servicio-info">

        <span class="servicio-categoria">
            <?= htmlspecialchars($categoriaServicio) ?>
        </span>

        <h1><?= htmlspecialchars($nombreServicio) ?></h1>

        <p class="servicio-descripcion">
            <?= htmlspecialchars(
                $descripcionServicio !== ''
                    ? $descripcionServicio
                    : 'Descripción pendiente de completar.'
            ) ?>
        </p>

        <div class="servicio-divider"></div>

        <div class="servicio-meta">
            <?php if ($duracionServicio): ?>
                <span class="meta-badge">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <circle cx="7" cy="7" r="5.5"/>
                        <path d="M7 4.5V7l1.5 1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= (int) $duracionServicio ?> min
                </span>
            <?php endif; ?>
            <span class="meta-badge meta-precio">
                <?= number_format($precioServicio, 2) ?> €
            </span>
        </div>

        <a href="<?= htmlspecialchars($reservaUrl) ?>" class="btn-reservar">
            Reservar cita
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M2 7h10M8 3l4 4-4 4"/>
            </svg>
        </a>

    </div>
</main>

<?php include 'footer.php'; ?>

<script src="../js/header-scroll.js"></script>
</body>
</html>