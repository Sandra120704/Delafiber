document.addEventListener('DOMContentLoaded', function() {
    // Cambiar etapa
    document.getElementById('formCambiarEtapa').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('idlead', LEAD_ID);
        fetch(BASE_URL + 'leads/moverEtapa', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al mover etapa');
            }
        });
    });

    // Agregar seguimiento
    document.getElementById('formSeguimiento').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(BASE_URL + 'leads/agregarSeguimiento', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error');
            }
        });
    });

    // Crear tarea
    document.getElementById('formTarea').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(BASE_URL + 'leads/crearTarea', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error');
            }
        });
    });
});

// Completar tarea
function completarTarea(id) {
    if (confirm('¿Marcar como completada?')) {
        const formData = new FormData();
        formData.append('idtarea', id);
        fetch(BASE_URL + 'leads/completarTarea', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
}

// Define BASE_URL y LEAD_ID si no existen
if (typeof BASE_URL === 'undefined') {
    window.BASE_URL = window.location.origin + '/';
}
if (typeof LEAD_ID === 'undefined') {
    window.LEAD_ID = document.querySelector('[name="idlead"]').value;
}
