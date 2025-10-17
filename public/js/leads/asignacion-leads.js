/**
 * Sistema de Asignación y Reasignación de Leads
 * Comunicación entre usuarios
 */

// Variables globales
var usuariosDisponibles = [];
var baseUrl = window.location.origin;

// Obtener baseUrl del meta tag cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Obtener baseUrl del meta tag
    var metaBase = document.querySelector('meta[name="base-url"]');
    if (metaBase) {
        baseUrl = metaBase.content;
    }
    
    // Inicializar
    cargarUsuariosDisponibles();
    inicializarEventos();
});

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
var eventosInicializados = false;

function inicializarEventos() {
    // Evitar inicializar eventos múltiples veces
    if (eventosInicializados) return;
    eventosInicializados = true;
    
    // Usar delegación de eventos en el body para evitar duplicados
    document.body.addEventListener('click', function(e) {
        // Solo procesar si el clic es directamente en un botón, no en inputs/textareas
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            return; // No hacer nada si es un campo de formulario
        }
        
        // Botón de reasignar
        const btnReasignar = e.target.closest('.btn-reasignar-lead');
        if (btnReasignar) {
            e.preventDefault();
            e.stopPropagation();
            const idlead = btnReasignar.dataset.idlead;
            mostrarModalReasignar(idlead);
            return;
        }

        // Botón de solicitar apoyo
        const btnApoyo = e.target.closest('.btn-solicitar-apoyo');
        if (btnApoyo) {
            e.preventDefault();
            e.stopPropagation();
            const idlead = btnApoyo.dataset.idlead;
            mostrarModalSolicitarApoyo(idlead);
            return;
        }

        // Botón de programar seguimiento
        const btnProgramar = e.target.closest('.btn-programar-seguimiento');
        if (btnProgramar) {
            e.preventDefault();
            e.stopPropagation();
            const idlead = btnProgramar.dataset.idlead;
            mostrarModalProgramarSeguimiento(idlead);
            return;
        }
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
                                <label class="form-label">
                                    <i class="mdi mdi-magnify"></i> Buscar y asignar usuario:
                                </label>
                                <select name="nuevo_usuario" id="selectUsuarioReasignar" class="form-select" required>
                                    <option value="">Escribe para buscar usuario...</option>
                                    ${usuariosDisponibles.map(u => `
                                        <option value="${u.idusuario}" 
                                                data-turno="${u.turno}"
                                                data-leads="${u.leads_activos}"
                                                data-tareas="${u.tareas_pendientes}">
                                            ${u.nombre} - ${u.turno} | ${u.leads_activos} leads | ${u.tareas_pendientes} tareas
                                        </option>
                                    `).join('')}
                                </select>
                                <div id="infoUsuarioSeleccionado" class="mt-2" style="display:none;">
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="mdi mdi-account"></i> <span id="nombreUsuario"></span></strong><br>
                                        <small>
                                            <i class="mdi mdi-clock"></i> Turno: <span id="turnoUsuario"></span> | 
                                            <i class="mdi mdi-account-group"></i> Leads activos: <span id="leadsUsuario"></span> | 
                                            <i class="mdi mdi-checkbox-marked-circle"></i> Tareas pendientes: <span id="tareasUsuario"></span>
                                        </small>
                                    </div>
                                </div>
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
    const modalExistente = document.getElementById('modalReasignar');
    if (modalExistente) {
        $('#modalReasignar').modal('hide');
        modalExistente.remove();
    }
    
    // Limpiar todos los backdrops residuales
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
    
    // Agregar nuevo modal
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Esperar un momento para que la limpieza se complete
    setTimeout(function() {
        const $modal = $('#modalReasignar');
        $modal.modal('show');
        
        // Esperar a que el modal esté completamente visible
        $modal.on('shown.bs.modal', function() {
            // Inicializar Select2 para búsqueda de usuarios
            if (typeof inicializarBuscadorUsuarios === 'function') {
                console.log('Inicializando buscador de usuarios en modal reasignar');
                inicializarBuscadorUsuarios('#selectUsuarioReasignar', {
                    placeholder: 'Escribe para buscar usuario...',
                    dropdownParent: $modal,
                    allowClear: true
                });
            } else {
                console.warn('Función inicializarBuscadorUsuarios no disponible');
            }
        });
    }, 100);

    // Toggle campos de tarea
    document.getElementById('crearTarea').addEventListener('change', function() {
        document.getElementById('camposTarea').style.display = this.checked ? 'block' : 'none';
    });
    
    // Limpiar Select2 al cerrar modal
    $('#modalReasignar').on('hidden.bs.modal', function() {
        if (typeof destruirBuscador === 'function') {
            destruirBuscador('#selectUsuarioReasignar');
        }
    });
    
    // Event listener para mostrar info del usuario seleccionado
    setTimeout(() => {
        const selectUsuario = document.getElementById('selectUsuarioReasignar');
        if (selectUsuario) {
            selectUsuario.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const infoDiv = document.getElementById('infoUsuarioSeleccionado');
                
                if (this.value) {
                    document.getElementById('nombreUsuario').textContent = selectedOption.text.split(' - ')[0];
                    document.getElementById('turnoUsuario').textContent = selectedOption.dataset.turno;
                    document.getElementById('leadsUsuario').textContent = selectedOption.dataset.leads;
                    document.getElementById('tareasUsuario').textContent = selectedOption.dataset.tareas;
                    infoDiv.style.display = 'block';
                } else {
                    infoDiv.style.display = 'none';
                }
            });
        }
    }, 500);
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
                                <label class="form-label">
                                    <i class="mdi mdi-magnify"></i> Buscar usuario para solicitar apoyo:
                                </label>
                                <select name="usuario_apoyo" id="selectUsuarioApoyo" class="form-select" required>
                                    <option value="">Escribe para buscar usuario...</option>
                                    ${usuariosDisponibles.map(u => `
                                        <option value="${u.idusuario}"
                                                data-turno="${u.turno}"
                                                data-leads="${u.leads_activos}"
                                                data-tareas="${u.tareas_pendientes}">
                                            ${u.nombre} - ${u.turno} | ${u.leads_activos} leads | ${u.tareas_pendientes} tareas
                                        </option>
                                    `).join('')}
                                </select>
                                <div id="infoUsuarioApoyo" class="mt-2" style="display:none;">
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="mdi mdi-account"></i> <span id="nombreUsuarioApoyo"></span></strong><br>
                                        <small>
                                            <i class="mdi mdi-clock"></i> Turno: <span id="turnoUsuarioApoyo"></span> | 
                                            <i class="mdi mdi-account-group"></i> Leads activos: <span id="leadsUsuarioApoyo"></span> | 
                                            <i class="mdi mdi-checkbox-marked-circle"></i> Tareas pendientes: <span id="tareasUsuarioApoyo"></span>
                                        </small>
                                    </div>
                                </div>
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

    // Remover modal anterior si existe
    const modalExistente = document.getElementById('modalSolicitarApoyo');
    if (modalExistente) {
        $('#modalSolicitarApoyo').modal('hide');
        modalExistente.remove();
    }
    
    // Limpiar todos los backdrops residuales
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
    
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Esperar un momento para que la limpieza se complete
    setTimeout(function() {
        const $modal = $('#modalSolicitarApoyo');
        $modal.modal('show');
        
        // Esperar a que el modal esté completamente visible
        $modal.on('shown.bs.modal', function() {
            // Inicializar Select2 para búsqueda de usuarios
            if (typeof inicializarBuscadorUsuarios === 'function') {
                console.log('Inicializando buscador de usuarios en modal apoyo');
                inicializarBuscadorUsuarios('#selectUsuarioApoyo', {
                    placeholder: 'Escribe para buscar usuario...',
                    dropdownParent: $modal,
                    allowClear: true
                });
            } else {
                console.warn('Función inicializarBuscadorUsuarios no disponible');
            }
        });
    }, 100);
    
    // Limpiar Select2 al cerrar modal
    $('#modalSolicitarApoyo').on('hidden.bs.modal', function() {
        if (typeof destruirBuscador === 'function') {
            destruirBuscador('#selectUsuarioApoyo');
        }
    });
    
    // Event listener para mostrar info del usuario seleccionado
    setTimeout(() => {
        const selectUsuario = document.getElementById('selectUsuarioApoyo');
        if (selectUsuario) {
            selectUsuario.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const infoDiv = document.getElementById('infoUsuarioApoyo');
                
                if (this.value) {
                    document.getElementById('nombreUsuarioApoyo').textContent = selectedOption.text.split(' - ')[0];
                    document.getElementById('turnoUsuarioApoyo').textContent = selectedOption.dataset.turno;
                    document.getElementById('leadsUsuarioApoyo').textContent = selectedOption.dataset.leads;
                    document.getElementById('tareasUsuarioApoyo').textContent = selectedOption.dataset.tareas;
                    infoDiv.style.display = 'block';
                } else {
                    infoDiv.style.display = 'none';
                }
            });
        }
    }, 500);
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
                                    <input type="date" name="fecha" id="fechaSeguimiento" class="form-control" required
                                        min="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora:</label>
                                    <div class="row">
                                        <div class="col-5">
                                            <select name="hora" id="horaSeguimiento" class="form-select" required>
                                                <option value="">HH</option>
                                                ${[8,9,10,11,12,1,2,3,4,5,6,7,8].map(h => `<option value="${h}">${h.toString().padStart(2,'0')}</option>`).join('')}
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select name="minutos" id="minutosSeguimiento" class="form-select" required>
                                                <option value="00">00</option>
                                                <option value="15">15</option>
                                                <option value="30">30</option>
                                                <option value="45">45</option>
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <select name="periodo" id="periodoSeguimiento" class="form-select" required>
                                                <option value="AM">AM</option>
                                                <option value="PM" selected>PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    <small class="text-muted">Horario laboral: 8:00 AM - 8:00 PM</small>
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

    // Remover modal anterior si existe
    const modalExistente = document.getElementById('modalProgramarSeguimiento');
    if (modalExistente) {
        $('#modalProgramarSeguimiento').modal('hide');
        modalExistente.remove();
    }
    
    // Limpiar todos los backdrops residuales
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
    
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Esperar un momento para que la limpieza se complete
    setTimeout(function() {
        $('#modalProgramarSeguimiento').modal('show');
    }, 100);
}

/**
 * Ejecutar programación de seguimiento
 */
window.ejecutarProgramarSeguimiento = async function() {
    const form = document.getElementById('formProgramarSeguimiento');
    
    // Obtener valores
    const hora = parseInt(document.getElementById('horaSeguimiento').value);
    const minutos = document.getElementById('minutosSeguimiento').value;
    const periodo = document.getElementById('periodoSeguimiento').value;
    
    // Validar que se hayan seleccionado todos los campos
    if (!hora || !minutos || !periodo) {
        Swal.fire('Error', 'Por favor selecciona la hora completa (hora, minutos y AM/PM)', 'error');
        return;
    }
    
    // Convertir a formato 24 horas
    let hora24 = hora;
    if (periodo === 'PM' && hora !== 12) {
        hora24 = hora + 12;
    } else if (periodo === 'AM' && hora === 12) {
        hora24 = 0;
    }
    
    // Validar horario laboral (8 AM - 8 PM = 8:00 - 20:00)
    if (hora24 < 8 || hora24 > 20 || (hora24 === 20 && minutos !== '00')) {
        Swal.fire({
            icon: 'warning',
            title: 'Horario no laboral',
            text: 'Por favor selecciona un horario entre 8:00 AM y 8:00 PM',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Crear FormData con la hora en formato 24 horas
    const formData = new FormData(form);
    const horaCompleta = `${hora24.toString().padStart(2, '0')}:${minutos}`;
    formData.set('hora', horaCompleta);
    formData.delete('minutos');
    formData.delete('periodo');

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
                text: `Programado para ${horaCompleta} (${hora}:${minutos} ${periodo})`,
                timer: 2500
            });
            $('#modalProgramarSeguimiento').modal('hide');
        } else {
            Swal.fire('Error', data.message, 'error');
        }

    } catch (error) {
        Swal.fire('Error', 'No se pudo programar el seguimiento', 'error');
    }
}
