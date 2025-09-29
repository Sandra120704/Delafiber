document.addEventListener('DOMContentLoaded', function() {
    const btnBuscarDni = document.getElementById('btnBuscarDni');
    const dniInput = document.getElementById('dni');
    const dniLoading = document.getElementById('dni-loading');
    const btnGuardar = document.getElementById('btnGuardar');
    const form = document.getElementById('formLead');

    btnBuscarDni.addEventListener('click', function() {
        const dni = dniInput.value.trim();
        if (dni.length !== 8) {
            alert('El DNI debe tener 8 dígitos');
            return;
        }
        dniLoading.style.display = 'block';
        btnBuscarDni.disabled = true;

        fetch('/leads/buscarPorDni?dni=' + dni, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            dniLoading.style.display = 'none';
            btnBuscarDni.disabled = false;

            if (data.success) {
                const personaData = data.data;
                document.getElementById('nombres').value = personaData.nombres || '';
                if (personaData.apellido_paterno && personaData.apellido_materno) {
                    document.getElementById('apellidos').value = personaData.apellido_paterno + ' ' + personaData.apellido_materno;
                } else if (personaData.apellidos) {
                    document.getElementById('apellidos').value = personaData.apellidos;
                }
                if (personaData.telefono) document.getElementById('telefono').value = personaData.telefono;
                if (personaData.correo) document.getElementById('correo').value = personaData.correo;
                if (personaData.direccion) document.getElementById('direccion').value = personaData.direccion;
                if (personaData.iddistrito) document.getElementById('iddistrito').value = personaData.iddistrito;
                if (!personaData.telefono) document.getElementById('telefono').focus();
            } else {
                alert(data.message || 'No se encontraron datos para este DNI. Complete manualmente.');
                document.getElementById('nombres').focus();
            }
        })
        .catch(() => {
            dniLoading.style.display = 'none';
            btnBuscarDni.disabled = false;
            alert('Error al buscar DNI. Por favor, ingrese los datos manualmente.');
        });
    });

    form.addEventListener('submit', function(e) {
        const telefono = document.getElementById('telefono').value;
        if (telefono.length !== 9 || !telefono.startsWith('9')) {
            e.preventDefault();
            alert('El teléfono debe tener 9 dígitos y comenzar con 9');
            document.getElementById('telefono').focus();
            return false;
        }
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="icon-refresh rotating"></i> Guardando...';
    });

    dniInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnBuscarDni.click();
        }
    });
});
