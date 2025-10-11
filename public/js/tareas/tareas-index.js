/**
 * JavaScript para Listado de Tareas
 * Archivo: public/js/tareas/tareas-index.js
 */

const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';

// Inicializar Select2 cuando se abre el modal
$(document).ready(function() {
    console.log('✅ Select2 inicializando...');
    
    // Inicializar cuando se muestra el modal
    $('#modalNuevaTarea').on('shown.bs.modal', function () {
        console.log('✅ Modal abierto');
        
        if (!$('#selectLead').hasClass('select2-hidden-accessible')) {
            console.log('✅ Inicializando Select2 en #selectLead');
            
            $('#selectLead').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar lead por nombre, teléfono o DNI...',
                allowClear: true,
                dropdownParent: $('#modalNuevaTarea'),
                ajax: {
                    url: baseUrl + '/tareas/buscarLeads',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() {
                        return 'Escribe al menos 2 caracteres para buscar';
                    },
                    searching: function() {
                        return 'Buscando leads...';
                    },
                    noResults: function() {
                        return 'No se encontraron leads';
                    },
                    errorLoading: function() {
                        return 'Error al cargar resultados';
                    }
                },
                templateResult: formatLead,
                templateSelection: formatLeadSelection
            });
        }
    });
    
    // Limpiar Select2 cuando se cierra el modal
    $('#modalNuevaTarea').on('hidden.bs.modal', function () {
        $('#selectLead').val(null).trigger('change');
    });
});

// Formato para mostrar en el dropdown
function formatLead(lead) {
    if (lead.loading) {
        return lead.text;
    }
    
    var $container = $(
        '<div class="select2-result-lead">' +
            '<div class="lead-name">' + lead.text + '</div>' +
            (lead.etapa ? '<small class="text-muted">Etapa: ' + lead.etapa + '</small>' : '') +
        '</div>'
    );
    
    return $container;
}

// Formato para mostrar cuando está seleccionado
function formatLeadSelection(lead) {
    return lead.text || lead.id;
}

window.completarTarea = function(idtarea) {
    document.getElementById('idtarea_completar').value = idtarea;
    const modal = new bootstrap.Modal(document.getElementById('modalCompletarTarea'));
    modal.show();
};

document.getElementById('formCompletarTarea')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(baseUrl + '/tareas/completar/' + document.getElementById('idtarea_completar').value, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Tarea Completada!',
                text: 'La tarea se marcó como completada exitosamente',
                confirmButtonColor: '#3085d6',
                timer: 2000
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo completar la tarea',
                confirmButtonColor: '#d33'
            });
        }
    });
});

document.getElementById('requiereSeguimiento')?.addEventListener('change', function(e) {
    const datosSeguimiento = document.getElementById('datosSeguimiento');
    if (datosSeguimiento) {
        datosSeguimiento.style.display = e.target.checked ? 'block' : 'none';
    }
});

window.verDetalle = function(idtarea) {
    fetch(`${baseUrl}/tareas/detalle/${idtarea}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Tarea: ' + data.tarea.titulo);
            }
        });
};

window.reprogramarTarea = function(idtarea) {
    Swal.fire({
        title: 'Reprogramar Tarea',
        html: '<input type="datetime-local" id="swal-input-fecha" class="swal2-input" style="width: 90%;">',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti-check"></i> Reprogramar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const fecha = document.getElementById('swal-input-fecha').value;
            if (!fecha) {
                Swal.showValidationMessage('Debes seleccionar una fecha');
                return false;
            }
            return fecha;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/tareas/reprogramar', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify({ idtarea: idtarea, nueva_fecha: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Tarea reprogramada exitosamente');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', 'Error al reprogramar');
                }
            });
        }
    });
};

window.contactarLead = function(telefono, tipo) {
    if (tipo === 'whatsapp') {
        window.open(`https://wa.me/51${telefono}?text=Hola,%20te%20contacto%20desde%20Delafiber`, '_blank');
    } else if (tipo === 'llamada') {
        window.location.href = `tel:+51${telefono}`;
    }
};

document.getElementById('selectAll')?.addEventListener('change', function(e) {
    document.querySelectorAll('.tarea-check').forEach(cb => cb.checked = e.target.checked);
    toggleAccionesMasivas();
});

document.querySelectorAll('.tarea-check').forEach(checkbox => {
    checkbox.addEventListener('change', toggleAccionesMasivas);
});

function toggleAccionesMasivas() {
    const checked = document.querySelectorAll('.tarea-check:checked').length;
    const accionesMasivas = document.getElementById('accionesMasivas');
    if (accionesMasivas) {
        accionesMasivas.style.display = checked > 0 ? 'block' : 'none';
    }
}

window.completarSeleccionadas = function() {
    const ids = Array.from(document.querySelectorAll('.tarea-check:checked'))
        .map(cb => cb.closest('tr').dataset.idtarea);
    
    Swal.fire({
        title: '¿Completar tareas?',
        text: `¿Deseas marcar ${ids.length} tarea(s) como completadas?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti-check"></i> Sí, completar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/tareas/completarMultiples', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', `${ids.length} tarea(s) completadas`);
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    });
};

window.eliminarSeleccionadas = function() {
    const ids = Array.from(document.querySelectorAll('.tarea-check:checked'))
        .map(cb => cb.closest('tr').dataset.idtarea);
    
    Swal.fire({
        title: '¿Eliminar tareas?',
        html: `¿Estás seguro de eliminar <strong>${ids.length} tarea(s)</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti-trash"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(baseUrl + '/tareas/eliminarMultiples', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', `${ids.length} tarea(s) eliminadas`);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron eliminar las tareas',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
};
