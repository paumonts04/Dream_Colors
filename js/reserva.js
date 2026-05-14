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
                btn.type = 'button';
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

document.addEventListener('DOMContentLoaded', function() {
    let fechaInput = document.getElementById('fecha');
    let btnConfirmar = document.querySelector('#paso3 .btn-reservar');
    let btnCancelar = document.querySelector('#paso3 .btn-cancelar');

    if (fechaInput) {
        fechaInput.addEventListener('change', cargarHoras);
    }

    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', function(e) {
            e.preventDefault();
            confirmarReserva();
        });
    }

    if (btnCancelar) {
        btnCancelar.addEventListener('click', function(e) {
            e.preventDefault();
            cancelarSeleccion();
        });
    }
});