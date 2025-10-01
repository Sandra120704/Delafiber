<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">📅 Calendario de Tareas</h3>
                    <p class="text-muted mb-0">Gestiona tus tareas y seguimientos</p>
                </div>
                <div>
                    <a href="<?= base_url('tareas') ?>" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="ti-list"></i> Vista Lista
                    </a>
                    <button class="btn btn-primary btn-sm" id="btnNuevaTarea">
                        <i class="ti-plus"></i> Nueva Tarea
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leyenda de colores -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <small class="text-muted me-2">Prioridad:</small>
                        <span class="badge" style="background-color: #dc3545;">Urgente</span>
                        <span class="badge" style="background-color: #fd7e14;">Alta</span>
                        <span class="badge" style="background-color: #007bff;">Media</span>
                        <span class="badge" style="background-color: #6c757d;">Baja</span>
                        <span class="badge" style="background-color: #28a745;">Completada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTareaTitle">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTarea">
                    <input type="hidden" id="idtarea" name="idtarea">
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_tarea" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo_tarea" name="tipo_tarea" required>
                                <option value="llamada">📞 Llamada</option>
                                <option value="reunion">🤝 Reunión</option>
                                <option value="seguimiento">📋 Seguimiento</option>
                                <option value="instalacion">🔧 Instalación</option>
                                <option value="visita">🏠 Visita</option>
                                <option value="email">📧 Email</option>
                                <option value="otro">📝 Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="prioridad" class="form-label">Prioridad *</label>
                            <select class="form-select" id="prioridad" name="prioridad" required>
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha y Hora *</label>
                            <input type="datetime-local" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="idlead" class="form-label">Lead Asociado</label>
                            <select class="form-select" id="idlead" name="idlead">
                                <option value="">Sin lead</option>
                                <?php foreach ($leads as $lead): ?>
                                    <option value="<?= $lead['idlead'] ?>">
                                        <?= esc($lead['cliente'] ?? 'Lead #' . $lead['idlead']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3" id="estadoContainer" style="display: none;">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Completada">Completada</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnEliminarTarea" style="display: none;">
                    <i class="ti-trash"></i> Eliminar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarTarea">
                    <i class="ti-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.global.min.js'></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const modalTarea = new bootstrap.Modal(document.getElementById('modalTarea'));
    const formTarea = document.getElementById('formTarea');
    let tareaEditando = null;

    // Inicializar FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
        },
        height: 'auto',
        navLinks: true,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        
        // Cargar eventos
        events: function(info, successCallback, failureCallback) {
            fetch(`<?= base_url('tareas/getTareasCalendario') ?>?start=${info.startStr}&end=${info.endStr}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => successCallback(data))
            .catch(error => {
                console.error('Error:', error);
                failureCallback(error);
            });
        },

        // Click en fecha vacía - Crear nueva tarea
        select: function(info) {
            abrirModalNuevaTarea(info.startStr);
        },

        // Click en evento existente - Ver/Editar
        eventClick: function(info) {
            abrirModalEditarTarea(info.event);
        },

        // Drag & Drop - Cambiar fecha
        eventDrop: function(info) {
            actualizarFechaTarea(info.event.id, info.event.startStr);
        },

        // Resize evento
        eventResize: function(info) {
            actualizarFechaTarea(info.event.id, info.event.startStr);
        }
    });

    calendar.render();

    // Botón nueva tarea
    document.getElementById('btnNuevaTarea').addEventListener('click', function() {
        abrirModalNuevaTarea();
    });

    // Guardar tarea
    document.getElementById('btnGuardarTarea').addEventListener('click', function() {
        if (!formTarea.checkValidity()) {
            formTarea.reportValidity();
            return;
        }

        const idtarea = document.getElementById('idtarea').value;
        const data = {
            idtarea: idtarea || null,
            titulo: document.getElementById('titulo').value,
            descripcion: document.getElementById('descripcion').value,
            tipo_tarea: document.getElementById('tipo_tarea').value,
            prioridad: document.getElementById('prioridad').value,
            fecha_vencimiento: document.getElementById('fecha_vencimiento').value,
            idlead: document.getElementById('idlead').value || null,
            estado: document.getElementById('estado').value || 'Pendiente'
        };

        const url = idtarea 
            ? '<?= base_url('tareas/actualizarTarea') ?>'
            : '<?= base_url('tareas/crearTareaCalendario') ?>';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                modalTarea.hide();
                calendar.refetchEvents();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo guardar la tarea'
            });
        });
    });

    // Eliminar tarea
    document.getElementById('btnEliminarTarea').addEventListener('click', function() {
        const idtarea = document.getElementById('idtarea').value;
        
        Swal.fire({
            title: '¿Eliminar tarea?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('tareas/eliminarTarea') ?>/${idtarea}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: result.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        modalTarea.hide();
                        calendar.refetchEvents();
                    }
                });
            }
        });
    });

    // Funciones auxiliares
    function abrirModalNuevaTarea(fecha = null) {
        tareaEditando = null;
        formTarea.reset();
        document.getElementById('modalTareaTitle').textContent = 'Nueva Tarea';
        document.getElementById('idtarea').value = '';
        document.getElementById('estadoContainer').style.display = 'none';
        document.getElementById('btnEliminarTarea').style.display = 'none';
        
        if (fecha) {
            const fechaLocal = new Date(fecha + 'T09:00:00');
            document.getElementById('fecha_vencimiento').value = 
                fechaLocal.toISOString().slice(0, 16);
        }
        
        modalTarea.show();
    }

    function abrirModalEditarTarea(event) {
        tareaEditando = event;
        const props = event.extendedProps;
        
        document.getElementById('modalTareaTitle').textContent = 'Editar Tarea';
        document.getElementById('idtarea').value = event.id;
        document.getElementById('titulo').value = event.title;
        document.getElementById('descripcion').value = props.descripcion || '';
        document.getElementById('tipo_tarea').value = props.tipo_tarea;
        document.getElementById('prioridad').value = props.prioridad;
        document.getElementById('estado').value = props.estado;
        document.getElementById('idlead').value = props.idlead || '';
        
        const fechaLocal = new Date(event.start);
        document.getElementById('fecha_vencimiento').value = 
            fechaLocal.toISOString().slice(0, 16);
        
        document.getElementById('estadoContainer').style.display = 'block';
        document.getElementById('btnEliminarTarea').style.display = 'inline-block';
        
        modalTarea.show();
    }

    function actualizarFechaTarea(id, nuevaFecha) {
        fetch('<?= base_url('tareas/actualizarFechaTarea') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: id,
                fecha_vencimiento: nuevaFecha
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Fecha actualizada',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                calendar.refetchEvents();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            calendar.refetchEvents();
        });
    }
});
</script>

<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}

.fc-event {
    cursor: pointer;
    border-radius: 4px;
    padding: 2px 4px;
}

.fc-event:hover {
    opacity: 0.9;
    transform: scale(1.02);
    transition: all 0.2s;
}

.fc-daygrid-event {
    white-space: normal !important;
}

.badge {
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 500;
}
</style>
<?= $this->endSection() ?>
