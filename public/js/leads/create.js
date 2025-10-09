/**
 * JavaScript para el formulario de creación de Leads
 * Maneja búsqueda por DNI, validaciones y verificación de cobertura
 */

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text || '').replace(/[&<>"']/g, m => map[m]);
}

class PersonaManager {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
        this.initEvents();
    }

    initEvents() {
        const btnBuscarDni = document.getElementById('btnBuscarDni');
        const dniInput = document.getElementById('dni');
        const dniLoading = document.getElementById('dni-loading');
        
        if (!btnBuscarDni || !dniInput) return;
        
        // Verificar cobertura al seleccionar distrito
        this.initVerificarCobertura();

        btnBuscarDni.addEventListener('click', () => {
            const dni = dniInput.value.trim();
            if (dni.length !== 8) {
                Swal.fire('Error', 'El DNI debe tener 8 dígitos', 'error');
                return;
            }

            dniLoading.style.display = 'block';
            btnBuscarDni.disabled = true;

            // Primero verificar si ya existe en la BD
            fetch(`${this.baseUrl}/personas/verificarDni?dni=${dni}`)
            .then(response => response.json())
            .then(data => {
                if (data.existe) {
                    dniLoading.style.display = 'none';
                    btnBuscarDni.disabled = false;
                    
                    const personaNombreSafe = escapeHtml(data.persona.nombres || '');
                    const personaApellidosSafe = escapeHtml(data.persona.apellidos || '');
                    const personaTelefonoSafe = escapeHtml(data.persona.telefono || 'No registrado');
                    const personaCorreoSafe = escapeHtml(data.persona.correo || 'No registrado');

                    Swal.fire({
                        icon: 'warning',
                        title: '⚠️ Persona Ya Registrada',
                        html: `
                            <div class="text-start">
                                <p><strong>Esta persona ya está en el sistema:</strong></p>
                                <ul class="list-unstyled">
                                    <li>👤 <strong>Nombre:</strong> ${personaNombreSafe} ${personaApellidosSafe}</li>
                                    <li>📞 <strong>Teléfono:</strong> ${personaTelefonoSafe}</li>
                                    <li>📧 <strong>Correo:</strong> ${personaCorreoSafe}</li>
                                </ul>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Usar estos datos',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.autocompletarDatos(data.persona);
                        }
                    });
                    return;
                }

                // Si no existe, buscar en RENIEC
                this.buscarEnReniec(dni, dniLoading, btnBuscarDni);
            })
            .catch(error => {
                dniLoading.style.display = 'none';
                btnBuscarDni.disabled = false;
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo conectar al servidor', 'error');
            });
        });

        // Validación del formulario
        const form = document.getElementById('formLead');
        if (form) {
            form.addEventListener('submit', this.validarFormulario.bind(this));
        }

        // Permitir búsqueda con Enter en DNI
        dniInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnBuscarDni.click();
            }
        });
    }

    buscarEnReniec(dni, dniLoading, btnBuscarDni) {
        fetch(`${this.baseUrl}/api/personas/buscar?dni=${dni}`)
        .then(response => response.json())
        .then(data => {
            dniLoading.style.display = 'none';
            btnBuscarDni.disabled = false;
            
            if (data.success && data.persona) {
                document.getElementById('nombres').value = data.persona.nombres || '';
                document.getElementById('apellidos').value = data.persona.apellidos || '';
                
                Swal.fire({
                    icon: 'success',
                    title: '✅ Datos de RENIEC',
                    text: 'Completa los demás campos',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                document.getElementById('telefono').focus();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'DNI no encontrado',
                    text: 'Puedes registrar manualmente los datos',
                    confirmButtonText: 'Entendido'
                });
                document.getElementById('nombres').focus();
            }
        })
        .catch(error => {
            dniLoading.style.display = 'none';
            btnBuscarDni.disabled = false;
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo conectar al servidor', 'error');
        });
    }

    autocompletarDatos(persona) {
        const nombresEl = document.getElementById('nombres');
        const apellidosEl = document.getElementById('apellidos');
        const telefonoEl = document.getElementById('telefono');
        const correoEl = document.getElementById('correo');
        const distritoEl = document.getElementById('iddistrito');

        if (nombresEl) nombresEl.value = escapeHtml(persona.nombres || '');
        if (apellidosEl) apellidosEl.value = escapeHtml(persona.apellidos || '');
        if (telefonoEl) telefonoEl.value = escapeHtml(persona.telefono || '');
        if (correoEl) correoEl.value = escapeHtml(persona.correo || '');
        if (distritoEl && persona.iddistrito) distritoEl.value = persona.iddistrito;

        // Agregar campo hidden con idpersona
        let hiddenInput = document.getElementById('idpersona_hidden');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'idpersona';
            hiddenInput.id = 'idpersona_hidden';
            const form = document.getElementById('formLead');
            if (form) form.appendChild(hiddenInput);
        }
        if (hiddenInput) hiddenInput.value = persona.idpersona;

        Swal.fire({
            icon: 'success',
            title: 'Datos Cargados',
            text: 'Ahora completa la información del lead',
            timer: 2000,
            showConfirmButton: false
        });
    }

    validarFormulario(e) {
        const telefono = document.getElementById('telefono').value;
        
        // Validar teléfono
        if (telefono.length !== 9 || !telefono.startsWith('9')) {
            e.preventDefault();
            Swal.fire('Error', 'El teléfono debe tener 9 dígitos y comenzar con 9', 'error');
            document.getElementById('telefono').focus();
            return false;
        }

        // Deshabilitar botón de guardar
        const btnGuardar = document.getElementById('btnGuardar');
        if (btnGuardar) {
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="icon-refresh rotating"></i> Guardando...';
        }
    }
    
    // Verificar cobertura de zonas en el distrito seleccionado
    initVerificarCobertura() {
        const distritoSelect = document.getElementById('iddistrito');
        const alertCobertura = document.getElementById('alertCobertura');
        
        if (!distritoSelect || !alertCobertura) return;
        
        distritoSelect.addEventListener('change', async () => {
            const distrito = distritoSelect.value;
            
            if (!distrito) {
                alertCobertura.style.display = 'none';
                return;
            }
            
            try {
                const response = await fetch(`${this.baseUrl}/leads/verificar-cobertura?distrito=${distrito}`);
                const result = await response.json();
                
                if (result.success) {
                    this.mostrarAlertaCobertura(result, alertCobertura);
                }
            } catch (error) {
                console.error('Error al verificar cobertura:', error);
                alertCobertura.className = 'alert alert-danger mt-2';
                alertCobertura.innerHTML = `
                    <i class="icon-x-circle"></i> 
                    Error al verificar cobertura. Por favor, intenta de nuevo.
                `;
                alertCobertura.style.display = 'block';
            }
        });
    }

    mostrarAlertaCobertura(result, alertCobertura) {
        if (result.tiene_cobertura) {
            // Hay cobertura - Mostrar alerta verde
            alertCobertura.className = 'alert alert-success mt-2';
            
            let zonasHtml = '';
            if (result.zonas && result.zonas.length > 0) {
                zonasHtml = '<div class="mt-2"><small><strong>Zonas activas:</strong></small><ul class="mb-0 mt-1">';
                result.zonas.forEach(zona => {
                    zonasHtml += `<li><small>${escapeHtml(zona.nombre_zona)} (${escapeHtml(zona.campania_nombre)})</small></li>`;
                });
                zonasHtml += '</ul></div>';
            }
            
            alertCobertura.innerHTML = `
                <div class="d-flex align-items-start">
                    <i class="icon-check-circle mr-2" style="font-size: 1.2rem;"></i>
                    <div>
                        <strong>¡Excelente!</strong> ${escapeHtml(result.mensaje)}
                        <br><small class="text-muted">El lead será asignado automáticamente a una zona al guardar.</small>
                        ${zonasHtml}
                    </div>
                </div>
            `;
        } else {
            // No hay cobertura - Mostrar alerta amarilla
            alertCobertura.className = 'alert alert-warning mt-2';
            alertCobertura.innerHTML = `
                <div class="d-flex align-items-start">
                    <i class="icon-alert-triangle mr-2" style="font-size: 1.2rem;"></i>
                    <div>
                        <strong>Atención:</strong> ${escapeHtml(result.mensaje)}
                        <br><small class="text-muted">El lead se registrará pero no se asignará a ninguna campaña activa.</small>
                        <br><small class="text-muted">Puedes crear zonas en el <a href="${this.baseUrl}/crm-campanas/mapa-campanas" target="_blank">mapa de campañas</a>.</small>
                    </div>
                </div>
            `;
        }
        
        alertCobertura.style.display = 'block';
        
        // Animación suave
        alertCobertura.style.opacity = '0';
        setTimeout(() => {
            alertCobertura.style.transition = 'opacity 0.3s';
            alertCobertura.style.opacity = '1';
        }, 10);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // La variable BASE_URL debe ser definida en la vista
    if (typeof BASE_URL !== 'undefined') {
        window.personaManager = new PersonaManager(BASE_URL);
    } else {
        console.error('BASE_URL no está definida');
    }
});
