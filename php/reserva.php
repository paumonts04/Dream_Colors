<?php
require_once __DIR__ . '/conexion.php';

$id_servicio = intval($_GET['id_servicio'] ?? 0);

// Redirigir al login si no ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?redirect=' . urlencode('reserva.php?id_servicio=' . $id_servicio));
    exit();
}

// Comprobar si el usuario está baneado
$id_usuario = (int) $_SESSION['usuario_id'];
$check = $con->prepare("SELECT block FROM usuarios WHERE id = ?");
$check->bind_param("i", $id_usuario);
$check->execute();
$usuario = $check->get_result()->fetch_assoc();
$check->close();

if ($usuario && $usuario['block']) {
    die('
        <div style="text-align:center; padding:50px;">
            <h2>Tu cuenta está bloqueada</h2>
            <p>Has acumulado demasiadas cancelaciones a última hora.</p>
            <p>Contacta con la peluquería para más información.</p>
            <a href="../index.php">Volver al inicio</a>
        </div>
    ');
}

if (!$id_servicio) {
    header('Location: ../index.php');
    exit();
}

$stmt = $con->prepare("
    SELECT s.*, c.tipo AS categoria
    FROM servicios s
    JOIN categoria c ON s.id_categoria = c.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $id_servicio);
$stmt->execute();
$servicio = $stmt->get_result()->fetch_assoc();

if (!$servicio) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar cita — <?= htmlspecialchars($servicio['nombre']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="reserva-container">

    <h2>Reservar cita</h2>

    <!-- Servicio elegido -->
    <div class="reserva-servicio-elegido">
        <?php if ($servicio['imagen']): ?>
            <img src="../img/<?= htmlspecialchars($servicio['imagen']) ?>"
                 alt="<?= htmlspecialchars($servicio['nombre']) ?>">
        <?php endif; ?>
        <div class="reserva-servicio-info">
            <span class="reserva-categoria"><?= htmlspecialchars($servicio['categoria']) ?></span>
            <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
            <div class="reserva-servicio-meta">
                <span>⏱ <?= $servicio['duracion'] ?> min</span>
                <span>💶 <?= number_format($servicio['precio'], 2) ?>€</span>
            </div>
        </div>
        <input type="hidden" id="id_servicio" value="<?= $servicio['id'] ?>">
        <input type="hidden" id="duracion" value="<?= $servicio['duracion'] ?>">
    </div>

    <!-- PASO 1: Elegir fecha -->
    <div class="paso" id="paso1">
        <h3>1. Elige una fecha</h3>
        <input type="date" id="fecha"
               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
               onchange="cargarHoras()">
    </div>

    <!-- PASO 2: Elegir hora -->
    <div class="paso" id="paso2" style="display:none">
        <h3>2. Elige una hora</h3>
        <div id="horas-disponibles"></div>
        <p id="sin-horas" style="display:none">No hay horas disponibles este día. Prueba con otra fecha.</p>
    </div>

    <!-- PASO 3: Confirmar -->
    <div class="paso" id="paso3" style="display:none">
        <h3>3. Confirma tu cita</h3>
        <div class="reserva-resumen">
            <p><strong>Servicio:</strong> <?= htmlspecialchars($servicio['nombre']) ?></p>
            <p><strong>Duración:</strong> <?= $servicio['duracion'] ?> min</p>
            <p><strong>Precio:</strong> <?= number_format($servicio['precio'], 2) ?>€</p>
            <p><strong>Fecha:</strong> <span id="resumen-fecha"></span></p>
            <p><strong>Hora:</strong> <span id="resumen-hora"></span></p>
        </div>
        <button class="btn-reservar" onclick="confirmarReserva()">Confirmar reserva</button>
        <button class="btn-cancelar" onclick="cancelarSeleccion()">Cambiar hora</button>
    </div>

    <!-- Mensaje de éxito -->
    <div id="reserva-exito" style="display:none" class="reserva-exito">
        <h3>¡Cita reservada con éxito!</h3>
        <p>Te esperamos el <strong><span id="exito-fecha"></span></strong> a las <strong><span id="exito-hora"></span></strong>.</p>
        <a href="../index.php" class="btn-reservar">Volver al inicio</a>
    </div>

</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
    let horaSeleccionada = null;

    function cargarHoras() {
        let fecha = document.getElementById('fecha').value;
        let id_servicio = document.getElementById('id_servicio').value;

        if (!fecha) {
            return;
        }

        horaSeleccionada = null;
        document.getElementById('paso3').style.display = 'none';
        document.getElementById('horas-disponibles').innerHTML = '<p>Cargando...</p>';
        document.getElementById('sin-horas').style.display = 'none';
        document.getElementById('paso2').style.display = 'block';

        fetch('get_horas.php?id_servicio=' + id_servicio + '&fecha=' + fecha)
            .then(function(r) { return r.json(); })
            .then(function(horas) {
                let contenedor = document.getElementById('horas-disponibles');
                contenedor.innerHTML = '';

                if (horas.length === 0) {
                    document.getElementById('sin-horas').style.display = 'block';
                    return;
                }

                horas.forEach(function(hora) {
                    let btn = document.createElement('button');
                    btn.textContent = hora;
                    btn.classList.add('btn-hora');
                    btn.onclick = function() {
                        seleccionarHora(hora, btn);
                    };
                    contenedor.appendChild(btn);
                });
            })
            .catch(function() {
                document.getElementById('horas-disponibles').innerHTML =
                    '<p>Error al cargar las horas. Inténtalo de nuevo.</p>';
            });
    }

    function seleccionarHora(hora, btn) {
        let botones = document.querySelectorAll('.btn-hora');
        for (let i = 0; i < botones.length; i++) {
            botones[i].classList.remove('seleccionado');
        }

        btn.classList.add('seleccionado');
        horaSeleccionada = hora;

        let fecha = document.getElementById('fecha').value;
        let partes = fecha.split('-');
        let anio = partes[0];
        let mes = partes[1];
        let dia = partes[2];

        document.getElementById('resumen-fecha').textContent = dia + '/' + mes + '/' + anio;
        document.getElementById('resumen-hora').textContent = hora;
        document.getElementById('paso3').style.display = 'block';
    }

    function cancelarSeleccion() {
        horaSeleccionada = null;
        let botones = document.querySelectorAll('.btn-hora');
        for (let i = 0; i < botones.length; i++) {
            botones[i].classList.remove('seleccionado');
        }
        document.getElementById('paso3').style.display = 'none';
    }

    function confirmarReserva() {
        let id_servicio = document.getElementById('id_servicio').value;
        let fecha = document.getElementById('fecha').value;

        if (!horaSeleccionada || !fecha) {
            alert('Por favor selecciona una fecha y una hora.');
            return;
        }

        let btn = document.querySelector('#paso3 .btn-reservar');
        btn.disabled = true;
        btn.textContent = 'Reservando...';

        fetch('confirmar_reserva.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_servicio: id_servicio,
                fecha: fecha,
                hora: horaSeleccionada
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.ok) {
                document.getElementById('paso1').style.display = 'none';
                document.getElementById('paso2').style.display = 'none';
                document.getElementById('paso3').style.display = 'none';
                document.querySelector('.reserva-servicio-elegido').style.display = 'none';

                let partes = fecha.split('-');
                let anio = partes[0];
                let mes = partes[1];
                let dia = partes[2];
                document.getElementById('exito-fecha').textContent = dia + '/' + mes + '/' + anio;
                document.getElementById('exito-hora').textContent = horaSeleccionada;
                document.getElementById('reserva-exito').style.display = 'block';
            } else {
                alert('Error: ' + res.error);
                btn.disabled = false;
                btn.textContent = 'Confirmar reserva';
            }
        })
        .catch(function() {
            alert('Error de conexión. Inténtalo de nuevo.');
            btn.disabled = false;
            btn.textContent = 'Confirmar reserva';
        });
    }
</script>

</body>
</html>
