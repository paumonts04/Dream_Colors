<?php
require_once __DIR__ . '/conexion.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ../index.php');
    exit();
}

$stmt = $con->prepare("
    SELECT s.*, c.tipo AS categoria
    FROM servicios s
    LEFT JOIN categoria c ON s.id_categoria = c.id
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

$nombreServicio = $servicio['nombre'] ?? 'Servicio';
$categoriaServicio = $servicio['categoria'] ?? 'Servicio';
$descripcionServicio = trim((string) ($servicio['descripcion'] ?? ''));
$duracionServicio = $servicio['duracion'] ?? null;
$precioServicio = (float) ($servicio['precio'] ?? 0);
$videoServicio = trim((string) ($servicio['video'] ?? ''));
$imagenServicio = trim((string) ($servicio['imagen'] ?? ''));
$reservaPath = __DIR__ . '/reserva/reserva.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nombreServicio) ?> - Dream Colors</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="servicio-detalle">
    <?php if ($videoServicio !== ''): ?>
        <video src="../videos/<?= htmlspecialchars($videoServicio) ?>" autoplay muted loop playsinline></video>
    <?php elseif ($imagenServicio !== ''): ?>
        <img src="../img/<?= htmlspecialchars($imagenServicio) ?>" alt="<?= htmlspecialchars($nombreServicio) ?>">
    <?php else: ?>
        <div class="servicio-media-placeholder">
            <span><?= htmlspecialchars($nombreServicio) ?></span>
        </div>
    <?php endif; ?>

    <div class="servicio-info">
        <span class="servicio-categoria"><?= htmlspecialchars($categoriaServicio) ?></span>
        <h1><?= htmlspecialchars($nombreServicio) ?></h1>
        <p class="servicio-descripcion">
            <?= htmlspecialchars($descripcionServicio !== '' ? $descripcionServicio : 'Descripcion pendiente de completar.') ?>
        </p>

        <div class="servicio-meta">
            <?php if ($duracionServicio): ?>
                <span><?= (int) $duracionServicio ?> min</span>
            <?php endif; ?>
            <span><?= number_format($precioServicio, 2) ?> EUR</span>
        </div>

        <?php if (isset($_SESSION['usuario_id']) && file_exists($reservaPath)): ?>
            <a href="reserva/reserva.php?id_servicio=<?= (int) $servicio['id'] ?>" class="btn-reservar">
                Reservar cita
            </a>
        <?php elseif (isset($_SESSION['usuario_id'])): ?>
            <span class="btn-reservar btn-reservar-disabled">
                Reserva no disponible todavia
            </span>
        <?php else: ?>
            <a href="login.php?redirect=servicio.php?id=<?= (int) $servicio['id'] ?>" class="btn-reservar">
                Inicia sesion para reservar
            </a>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script src="../js/header-scroll.js"></script>
</body>
</html>
