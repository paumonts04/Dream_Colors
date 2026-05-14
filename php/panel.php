<?php
require_once 'conexion.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

$success = "";
$error = "";

// AGREGAR SERVICIO
if (isset($_POST['agregar_servicio'])) {
    $cat = $_POST['id_categoria'];
    $nombre = $_POST['nombre'];
    $desc = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $duracion = $_POST['duracion'];
    $imagen = $_POST['imagen'];
    $video = $_POST['video'];

    if ($cat && $nombre && $precio) {
        $sql = "INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion, imagen, video) 
                VALUES ('$cat', '$nombre', '$desc', '$precio', '$duracion', '$imagen', '$video')";
        if ($con->query($sql)) {
            $success = "Servicio agregado!";
        } else {
            $error = "Error al agregar: " . $con->error;
        }
    } else {
        $error = "Falta completar campos obligatorios.";
    }
}

// ACTUALIZAR SERVICIO
if (isset($_POST['actualizar_servicio'])) {
    $id = $_POST['id_servicio'];
    $cat = $_POST['id_categoria'];
    $nombre = $_POST['nombre'];
    $desc = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $duracion = $_POST['duracion'];
    $imagen = $_POST['imagen'];
    $video = $_POST['video'];

    if ($id && $nombre && $precio) {
        $sql = "UPDATE servicios SET id_categoria='$cat', nombre='$nombre', descripcion='$desc', 
                precio='$precio', duracion='$duracion', imagen='$imagen', video='$video' WHERE id=$id";
        if ($con->query($sql)) {
            $success = "Servicio actualizado!";
        } else {
            $error = "Error al actualizar: " . $con->error;
        }
    } else {
        $error = "Falta completar campos obligatorios.";
    }
}

// ELIMINAR SERVICIO
if (isset($_POST['eliminar_servicio'])) {
    $id = $_POST['id_servicio'];
    $sql = "DELETE FROM servicios WHERE id=$id";
    if ($con->query($sql)) {
        $success = "Servicio eliminado!";
    } else {
        $error = "Error al eliminar: " . $con->error;
    }
}

// CAMBIAR PASSWORD
if (isset($_POST['cambiar_password'])) {
    $pass_actual = $_POST['password_actual'];
    $pass_nueva = $_POST['password_nueva'];
    $pass_confirmar = $_POST['password_confirmar'];

    // Obtener password del admin
    $sql = "SELECT password FROM usuarios WHERE id={$_SESSION['admin_id']}";
    $result = $con->query($sql);
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($pass_actual, $admin['password'])) {
        if ($pass_nueva == $pass_confirmar && strlen($pass_nueva) >= 4) {
            $pass_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET password='$pass_hash' WHERE id={$_SESSION['admin_id']}";
            if ($con->query($sql)) {
                $success = "Contraseña actualizada!";
            } else {
                $error = "Error al actualizar: " . $con->error;
            }
        } else {
            $error = "Las contraseñas no coinciden o son muy cortas.";
        }
    } else {
        $error = "Contraseña actual incorrecta.";
    }
}

// OBTENER CATEGORÍAS
$categorias = [];
$result = $con->query("SELECT id, tipo FROM categoria");
while ($row = $result->fetch_assoc()) {
    $categorias[$row['id']] = $row['tipo'];
}

// OBTENER SERVICIOS
$servicios = [];
$result = $con->query("SELECT s.*, c.tipo AS categoria FROM servicios s 
                       JOIN categoria c ON s.id_categoria = c.id 
                       ORDER BY s.id_categoria, s.nombre");
while ($row = $result->fetch_assoc()) {
    $servicios[] = $row;
}

// SERVICIO A EDITAR
$servicio_edicion = null;
if (isset($_GET['id']) && $_GET['tab'] == 'editar') {
    $id = $_GET['id'];
    $result = $con->query("SELECT * FROM servicios WHERE id=$id");
    $servicio_edicion = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración Dream Colors</title>
    <link rel="stylesheet" href="../css/admin-panel.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-label">Dream Colors</span>
            <span class="brand-sub">Panel Admin</span>
        </div>

        <nav class="sidebar-nav">
            <a href="?tab=servicios"
               class="<?= (!isset($_GET['tab']) || $_GET['tab'] === 'servicios') ? 'active' : '' ?>">
                Servicios
            </a>
            <a href="?tab=agregar"
               class="<?= (isset($_GET['tab']) && $_GET['tab'] === 'agregar') ? 'active' : '' ?>">
                Agregar Servicio
            </a>
            <a href="?tab=password"
               class="<?= (isset($_GET['tab']) && $_GET['tab'] === 'password') ? 'active' : '' ?>">
                Contraseña
            </a>
            <a href="logout-admin.php" class="nav-logout">
                Cerrar Sesión
            </a>
        </nav>

        <div class="sidebar-footer">
            <span class="admin-name"><?= $_SESSION['admin_user'] ?></span>
            <span class="admin-role">Administrador</span>
        </div>
    </aside>

    <main class="main-content">

        <div class="page-header">
            <h1>Panel de Administración</h1>
            <p class="page-header-sub">Gestiona los servicios y configuración de Dream Colors</p>
        </div>
        <div class="divider"></div>

        <!-- Alerts -->
        <?php if ($success): ?>
        <div class="alert alert-success">
            <span class="alert-dot"></span>
            <?= $success ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-dot"></span>
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="tab-container <?= (!isset($_GET['tab']) || $_GET['tab'] === 'servicios') ? 'active' : '' ?>">

            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Servicios</div>
                    <div class="stat-value"><?= count($servicios) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Categorías</div>
                    <div class="stat-value"><?= count($categorias) ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Servicios</h2>
                    <a href="?tab=agregar" class="btn btn-primary">Agregar</a>
                </div>

                <?php if (empty($servicios)): ?>
                <div class="empty-state">
                    <p>No hay servicios registrados aún.</p>
                </div>
                <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Categoría</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Duración</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $srv): ?>
                            <tr>
                                <td><span class="id-badge"><?= $srv['id'] ?></span></td>
                                <td><span class="cat-pill"><?= htmlspecialchars($srv['categoria'] ?? 'Sin categoría') ?></span></td>
                                <td style="font-weight:500;"><?= htmlspecialchars($srv['nombre']) ?></td>
                                <td class="price-cell"><?= number_format($srv['precio'], 2) ?> €</td>
                                <td class="duration-cell"><?= $srv['duracion'] ? $srv['duracion'] . ' min' : '—' ?></td>
                                <td>
                                    <div class="action-group">
                                        <a href="?tab=editar&id=<?= $srv['id'] ?>" class="btn btn-edit">Editar</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este servicio?');">
                                            <input type="hidden" name="id_servicio" value="<?= $srv['id'] ?>">
                                            <button type="submit" name="eliminar_servicio" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-container <?= (isset($_GET['tab']) && $_GET['tab'] === 'agregar') ? 'active' : '' ?>">
            <div class="card">
                <div class="card-header">
                    <h2>Agregar Nuevo Servicio</h2>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-section">
                        <p class="form-section-title">Información principal</p>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="cat_add">Categoría <span>*</span></label>
                                <select id="cat_add" name="id_categoria" class="form-control" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach ($categorias as $id => $tipo): ?>
                                    <option value="<?= $id ?>"><?= htmlspecialchars($tipo) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="nom_add">Nombre del Servicio <span>*</span></label>
                                <input type="text" id="nom_add" name="nombre" class="form-control" placeholder="Ej: Tratamiento facial" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full">
                                <label class="form-label" for="desc_add">Descripción</label>
                                <textarea id="desc_add" name="descripcion" class="form-control" placeholder="Describe el servicio brevemente…"></textarea>
                            </div>
                        </div>

                        <p class="form-section-title">Precio y duración</p>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="precio_add">Precio (EUR) <span>*</span></label>
                                <input type="number" id="precio_add" name="precio" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="duracion_add">Duración (minutos)</label>
                                <input type="number" id="duracion_add" name="duracion" class="form-control" min="1" placeholder="Ej: 60">
                            </div>
                        </div>

                        <p class="form-section-title">Archivos multimedia</p>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="imagen_add">Nombre Imagen</label>
                                <input type="text" id="imagen_add" name="imagen" class="form-control" placeholder="imagen.webp">
                                <span class="form-hint">Nombre del archivo en el servidor</span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="video_add">Nombre Video</label>
                                <input type="text" id="video_add" name="video" class="form-control" placeholder="video.webm">
                                <span class="form-hint">Nombre del archivo en el servidor</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="agregar_servicio" class="btn btn-primary btn-lg">Agregar Servicio</button>
                            <a href="?tab=servicios" class="btn btn-ghost btn-lg">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'editar' && $servicio_edicion): ?>
        <div class="tab-container active">
            <div class="card">
                <div class="card-header">
                    <h2>Editar Servicio</h2>
                    <span class="id-badge">#<?= $servicio_edicion['id'] ?></span>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-section">
                        <input type="hidden" name="id_servicio" value="<?= $servicio_edicion['id'] ?>">

                        <p class="form-section-title">Información principal</p>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="cat_edit">Categoría <span>*</span></label>
                                <select id="cat_edit" name="id_categoria" class="form-control" required>
                                    <?php foreach ($categorias as $id => $tipo): ?>
                                    <option value="<?= $id ?>" <?= $id === $servicio_edicion['id_categoria'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="nom_edit">Nombre del Servicio <span>*</span></label>
                                <input type="text" id="nom_edit" name="nombre" class="form-control"
                                       value="<?= htmlspecialchars($servicio_edicion['nombre']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full">
                                <label class="form-label" for="desc_edit">Descripción</label>
                                <textarea id="desc_edit" name="descripcion" class="form-control"><?= htmlspecialchars($servicio_edicion['descripcion'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <p class="form-section-title">Precio y duración</p>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="precio_edit">Precio (EUR) <span>*</span></label>
                                <input type="number" id="precio_edit" name="precio" class="form-control"
                                       step="0.01" value="<?= $servicio_edicion['precio'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="duracion_edit">Duración (minutos)</label>
                                <input type="number" id="duracion_edit" name="duracion" class="form-control"
                                       value="<?= $servicio_edicion['duracion'] ?? '' ?>">
                            </div>
                        </div>

                        <p class="form-section-title">Archivos multimedia</p>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="imagen_edit">Nombre Imagen</label>
                                <input type="text" id="imagen_edit" name="imagen" class="form-control"
                                       value="<?= htmlspecialchars($servicio_edicion['imagen'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="video_edit">Nombre Video</label>
                                <input type="text" id="video_edit" name="video" class="form-control"
                                       value="<?= htmlspecialchars($servicio_edicion['video'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="actualizar_servicio" class="btn btn-primary btn-lg">Guardar Cambios</button>
                            <a href="?tab=servicios" class="btn btn-ghost btn-lg">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="tab-container <?= (isset($_GET['tab']) && $_GET['tab'] === 'password') ? 'active' : '' ?>">
            <div class="card password-card">
                <div class="card-header">
                    <h2>Cambiar Contraseña</h2>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-section">

                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" for="pass_actual">Contraseña Actual <span>*</span></label>
                            <input type="password" id="pass_actual" name="password_actual" class="form-control" required>
                        </div>

                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" for="pass_nueva">Nueva Contraseña <span>*</span></label>
                            <input type="password" id="pass_nueva" name="password_nueva" class="form-control" required>
                            <span class="form-hint">Mínimo 4 caracteres</span>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="pass_confirmar">Confirmar Contraseña <span>*</span></label>
                            <input type="password" id="pass_confirmar" name="password_confirmar" class="form-control" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="cambiar_password" class="btn btn-primary btn-lg">Cambiar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>
</body>
</html>
