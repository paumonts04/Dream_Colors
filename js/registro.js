document.addEventListener('DOMContentLoaded', function() { //Espera a cargar
    let datosDiv = document.getElementById('nombres-datos');
    let nombresExistentes = [];

    if (datosDiv && datosDiv.dataset.nombres) {
        try {
            nombresExistentes = JSON.parse(datosDiv.dataset.nombres);
        } catch (e) {
            nombresExistentes = [];
        }
    }

    let formRegistro = document.querySelector('form');
    let inputNombre = document.getElementById('nombre');

    if (!formRegistro || !inputNombre) {
        return;
    }

    formRegistro.addEventListener('submit', function(e) {
        let nombreValor = inputNombre.value.trim();

        if (!nombreValor) {
            return;
        }

        let existe = nombresExistentes.some(function(nombre) {
            return nombre.toLowerCase() === nombreValor.toLowerCase();
        });

        if (existe) {
            e.preventDefault();
            inputNombre.value = '';
            alert('Ese nombre ya existe. Elige otro.');
        }
    });
});