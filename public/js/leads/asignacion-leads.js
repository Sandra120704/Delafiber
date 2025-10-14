/**
 * Sistema de Asignación y Reasignación de Leads
 * Comunicación entre usuarios
 */

// Variables globales
var usuariosDisponibles = [];
var baseUrl = window.location.origin;

// Obtener baseUrl del meta tag cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    var metaBase = document.querySelector('meta[name="base-url"]');
    if (metaBase) {
        baseUrl = metaBase.content;
    }
});

/**
 * Inicializar sistema de asignación cuando el DOM esté listo
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        cargarUsuariosDisponibles();
        inicializarEventos();
    });
})();

/**
 * Cargar lista de usuarios disponibles
 */
async function cargarUsuariosDisponibles() {
    try {
        const response = await fetch(`${baseUrl}/lead-asignacion/getUsuariosDisponibles`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            usuariosDisponibles = data.usuarios;
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
    }
}

/**
 * Inicializar eventos
 */
function inicializarEventos() {
    
    // Botón de reasignar
    const btnsReasignar = document.querySelectorAll('.btn-reasignar-lead');
    btnsReasignar.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const idlead = this.dataset.idlead;
            mostrarModalReasignar(idlead);
        });
    });

    // Botón de solicitar apoyo
    const btnsSolicitar = document.querySelectorAll('.btn-solicitar-apoyo');
    btnsSolicitar.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const idlead = this.dataset.idlead;
            mostrarModalSolicitarApoyo(idlead);
        });
    });

    // Botón de programar seguimiento
    const btnsProgramar = document.querySelectorAll('.btn-programar-seguimiento');
    btnsProgramar.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const idlead = this.dataset.idlead;
            mostrarModalProgramarSeguimiento(idlead);
        });
    });
}

/**
 * Mostrar modal de reasignación
 */
window.mostrarModalReasignar = function(idlead) {
    const html = `
        <div class="modal fade" id="modalReasignar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-account-switch"></i> Reasignar Lead
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formReasignar">
                            <input type="hidden" name="idlead" value="${idlead}">
                            
                            <div class="mb-3">
                                <label class="form-label">Asignar a:</label>
                                <select name="nuevo_usuario" class="form-select" required>
                                    <option value="">Seleccionar usuario...</option>
                                    ${usuariosDisponibles.map(u => `
                                        <option value="${u.idusuario}">
                                            ${u.nombre} - ${u.turno} 
                                            (${u.leads_activos} leads, ${u.tareas_pendientes} tareas)
                                        </option>
                                    `).join('')}
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Motivo de reasignación:</label>
                                <textarea name="motivo" class="form-control" rows="3" 
                                    placeholder="Ej: Cliente prefiere horario de tarde, zona no corresponde, etc."></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="crearTarea" name="crear_tarea">
                                    <label class="form-check-label" for="crearTarea">
                                        Crear tarea de seguimiento programada
                                    </label>
                                </div>
                            </div>

                            <div id="camposTarea" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha:</label>
                                        <input type="date" name="fecha_tarea" class="form-control" 
                                            min="${new Date().toISOString().split('T')[0]}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora:</label>
                                        <input type="time" name="hora_tarea" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                El usuario recibirá una notificación inmediata sobre esta asignación.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="ejecutarReasignacion()">
                            <i class="mdi mdi-check"></i> Reasignar Lead
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remover modal anterior si existe
    document.getElementById('modalReasignar')?.remove();
    
    // Agregar nuevo modal
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Mostrar modal con jQuery (compatible con Bootstrap 4/5)
    $('#modalReasignar').modal('show');

    // Toggle campos de tarea
    document.getElementById('crearTarea').addEventListener('change', function() {
        document.getElementById('camposTarea').style.display = this.checked ? 'block' : 'none';
    });
}

/**
 * Ejecutar reasignación
 */
window.ejecutarReasignacion = async function() {
    const form = document.getElementById('formReasignar');
    const formData = new FormData(form);

    // Validar
    if (!formData.get('nuevo_usuario')) {
        Swal.fire('Error', 'Debes seleccionar un usuario', 'error');
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Reasignando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch(`${baseUrl}/lead-asignacion/reasignar`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo completar la reasignación', 'error');
    }
}

/**
 * Mostrar modal de solicitar apoyo
 */
window.mostrarModalSolicitarApoyo = function(idlead) {
    const html = `
        <div class="modal fade" id="modalSolicitarApoyo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="mdi mdi-account-multiple"></i> Solicitar Apoyo
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formSolicitarApoyo">
                            <input type="hidden" name="idlead" value="${idlead}">
                            
                            <div class="mb-3">
                                <label class="form-label">Solicitar apoyo de:</label>
                                <select name="usuario_apoyo" class="form-select" required>
                                    <option value="">Seleccionar usuario...</option>
                                    ${usuariosDisponibles.map(u => `
                                        <option value="${u.idusuario}">${u.nombre} - ${u.turno}</option>
                                    `).join('')}
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mensaje:</label>
                                <textarea name="mensaje" class="form-control" rows="4" required
                                    placeholder="Describe en qué necesitas apoyo..."></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="urgente" name="urgente">
                                    <label class="form-check-label" for="urgente">
                                        <span class="badge bg-danger">URGENTE</span> Marcar como urgente
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                El lead seguirá asignado a ti. Solo estás solicitando ayuda.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-warning" onclick="ejecutarSolicitarApoyo()">
                            <i class="mdi mdi-send"></i> Enviar Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('modalSolicitarApoyo')?.remove();
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Mostrar modal con jQuery
    $('#modalSolicitarApoyo').modal('show');
}

/**
 * Ejecutar solicitud de apoyo
 */
window.ejecutarSolicitarApoyo = async function() {
    const form = document.getElementById('formSolicitarApoyo');
    const formData = new FormData(form);

    try {
        const response = await fetch(`${baseUrl}/lead-asignacion/solicitarApoyo`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Solicitud enviada',
                text: 'El usuario recibirá tu solicitud de apoyo',
                timer: 2000
            });
            $('#modalSolicitarApoyo').modal('hide');
        } else {
            Swal.fire('Error', data.message, 'error');
        }

    } catch (error) {
        Swal.fire('Error', 'No se pudo enviar la solicitud', 'error');
    }
}

/**
 * Mostrar modal de programar seguimiento
 */
window.mostrarModalProgramarSeguimiento = function(idlead) {
    const html = `
        <div class="modal fade" id="modalProgramarSeguimiento" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-calendar-clock"></i> Programar Seguimiento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formProgramarSeguimiento">
                            <input type="hidden" name="idlead" value="${idlead}">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha:</label>
                                    <input type="date" name="fecha" class="form-control" required
                                        min="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora:</label>
                                    <input type="time" name="hora" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipo de seguimiento:</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="Llamada">📞 Llamada telefónica</option>
                                    <option value="WhatsApp">💬 WhatsApp</option>
                                    <option value="Visita">🏠 Visita presencial</option>
                                    <option value="Email">📧 Email</option>
                                    <option value="Seguimiento">📋 Seguimiento general</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notas:</label>
                                <textarea name="nota" class="form-control" rows="3" required
                                    placeholder="Ej: Llamar para confirmar interés en plan 100 Mbps"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Recordatorio:</label>
                                <select name="recordatorio" class="form-select">
                                    <option value="">Sin recordatorio</option>
                                    <option value="15">15 minutos antes</option>
                                    <option value="30">30 minutos antes</option>
                                    <option value="60">1 hora antes</option>
                                    <option value="1440">1 día antes</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="ejecutarProgramarSeguimiento()">
                            <i class="mdi mdi-check"></i> Programar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('modalProgramarSeguimiento')?.remove();
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Mostrar modal con jQuery
    $('#modalProgramarSeguimiento').modal('show');
}

/**
 * Ejecutar programación de seguimiento
 */
window.ejecutarProgramarSeguimiento = async function() {
    const form = document.getElementById('formProgramarSeguimiento');
    const formData = new FormData(form);

    try {
        const response = await fetch(`${baseUrl}/lead-asignacion/programarSeguimiento`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Seguimiento programado',
                text: 'Recibirás una notificación en la fecha indicada',
                timer: 2000
            });
            $('#modalProgramarSeguimiento').modal('hide');
        } else {
            Swal.fire('Error', data.message, 'error');
        }

    } catch (error) {
        Swal.fire('Error', 'No se pudo programar el seguimiento', 'error');
    }
}
