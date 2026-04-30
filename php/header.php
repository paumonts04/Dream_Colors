<?php
require_once 'php/conexion.php';

$categoriasServicios = [];
$sqlServicios = "
    SELECT
        categoria.id AS categoria_id,
        categoria.tipo AS categoria_tipo,
        servicios.id AS servicio_id,
        servicios.nombre AS servicio_nombre
    FROM categoria
    INNER JOIN servicios ON servicios.id_categoria = categoria.id
    ORDER BY categoria.id, servicios.nombre
";

$resultadoServicios = $con->query($sqlServicios);

if ($resultadoServicios) {
    while ($fila = $resultadoServicios->fetch_assoc()) {
        $categoriaId = (int) $fila['categoria_id'];

        if (!isset($categoriasServicios[$categoriaId])) {
            $categoriasServicios[$categoriaId] = [
                'tipo' => $fila['categoria_tipo'],
                'servicios' => []
            ];
        }

        if ($fila['servicio_id'] !== null) {
            $categoriasServicios[$categoriaId]['servicios'][] = [
                'id' => (int) $fila['servicio_id'],
                'nombre' => $fila['servicio_nombre']
            ];
        }
    }
}
?>

<header class="site-header">
    <a class="site-logo" href="index.php" aria-label="Dream Colors inicio">Dream Colors</a>

    <form class="site-search" action="#" method="get" role="search">
        <input type="search" name="buscar" placeholder="Buscar" aria-label="Buscar">
    </form>

    <nav class="site-nav" aria-label="Navegacion principal">
        <button class="nav-link services-toggle" type="button" aria-expanded="false" aria-controls="services-menu">
            Servicios
        </button>
        <a href="#precios">Precios</a>
        <a href="#promociones">Promociones</a>
        <a href="#sobre-dream-colors">Sobre Dream Colors</a>
    </nav>

    <section class="services-menu" id="services-menu" aria-label="Servicios">
        <div class="services-menu-inner">
            <?php foreach ($categoriasServicios as $categoria): ?>
                <div class="services-column">
                    <h2><?php echo htmlspecialchars($categoria['tipo']); ?></h2>

                    <?php if (count($categoria['servicios']) > 0): ?>
                        <?php foreach ($categoria['servicios'] as $servicio): ?>
                            <a href="#servicio-<?php echo $servicio['id']; ?>">
                                <?php echo htmlspecialchars($servicio['nombre']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="services-empty">Sin servicios</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</header>
