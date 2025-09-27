
// Variables globales
var urlBase = window.base_url || '/';
var csrfToken = '';

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    inicializar();
});

// Función principal de inicialización
function inicializar() {
    obtenerTokenCSRF();
    configurarBotones();
    configurarTooltips();
    animarTarjetas();
}

// Obtener token CSRF para formularios
function obtenerTokenCSRF() {
    var metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
        csrfToken = metaToken.getAttribute('content');
    }
}

// Configurar todos los botones de acción
function configurarBotones() {
    // Botones de llamar
    var botonesLlamar = document.querySelectorAll('.quick-action[data-action="llamar"]');
    botonesLlamar.forEach(function(boton) {
        boton.addEventListener('click', function() {
            var leadId = this.getAttribute('data-lead');
            hacerLlamada(leadId, this);
        });
    });

    // Botones de WhatsApp
    var botonesWhatsApp = document.querySelectorAll('.quick-action[data-action="whatsapp"]');
    botonesWhatsApp.forEach(function(boton) {
        boton.addEventListener('click', function() {
            var leadId = this.getAttribute('data-lead');
            enviarWhatsApp(leadId, this);
        });
    });
}

// Función para hacer una llamada
function hacerLlamada(leadId, boton) {
    if (!leadId) {
        mostrarMensaje('Error: No se encontró el ID del lead', 'error');
        return;
    }

    // Mostrar que está cargando
    boton.disabled = true;
    boton.innerHTML = '<i class="ti-reload"></i>';

    // Obtener datos del lead
    fetch(urlBase + 'dashboard/getLeadQuickInfo/' + leadId)
        .then(function(response) {
            return response.json();
        })
        .then(function(datos) {
            if (datos.success) {
                var telefono = datos.lead.telefono;
                var nombre = datos.lead.nombres;
                
                // Abrir el marcador
                window.location.href = 'tel:' + telefono;
                
                // Registrar la llamada
                registrarActividad('llamar', leadId)
                    .then(function() {
                        mostrarMensaje('Llamando a ' + nombre, 'success');
                        restaurarBoton(boton, 'llamar');
                        
                        // Preguntar resultado después de 3 segundos
                        setTimeout(function() {
                            preguntarResultado(leadId, nombre, 'llamada');
                        }, 3000);
                    })
                    .catch(function() {
                        mostrarMensaje('Error al registrar la llamada', 'error');
                        restaurarBoton(boton, 'llamar');
                    });
            } else {
                mostrarMensaje('Error al obtener datos del lead', 'error');
                restaurarBoton(boton, 'llamar');
            }
        })
        .catch(function() {
            mostrarMensaje('Error de conexión', 'error');
            restaurarBoton(boton, 'llamar');
        });
}

// Función para enviar WhatsApp
function enviarWhatsApp(leadId, boton) {
    if (!leadId) {
        mostrarMensaje('Error: No se encontró el ID del lead', 'error');
        return;
    }

    // Mostrar que está cargando
    boton.disabled = true;
    boton.innerHTML = '<i class="ti-reload"></i>';

    // Obtener datos del lead
    fetch(urlBase + 'dashboard/getLeadQuickInfo/' + leadId)
        .then(function(response) {
            return response.json();
        })
        .then(function(datos) {
            if (datos.success) {
                var telefono = datos.lead.telefono;
                var nombre = datos.lead.nombres;
                
                // Mensaje por defecto
                var mensaje = 'Hola ' + nombre + ', te contacto de Delafiber para contarte sobre nuestros servicios de internet por fibra óptica. ¿Te gustaría conocer nuestros planes?';
                
                // Abrir WhatsApp
                var urlWhatsApp = 'https://wa.me/51' + telefono + '?text=' + encodeURIComponent(mensaje);
                window.open(urlWhatsApp, '_blank');
                
                // Registrar la acción
                registrarActividad('whatsapp', leadId)
                    .then(function() {
                        mostrarMensaje('Mensaje enviado a ' + nombre, 'success');
                        restaurarBoton(boton, 'whatsapp');
                    })
                    .catch(function() {
                        mostrarMensaje('Error al registrar el mensaje', 'error');
                        restaurarBoton(boton, 'whatsapp');
                    });
            } else {
                mostrarMensaje('Error al obtener datos del lead', 'error');
                restaurarBoton(boton, 'whatsapp');
            }
        })
        .catch(function() {
            mostrarMensaje('Error de conexión', 'error');
            restaurarBoton(boton, 'whatsapp');
        });
}

// Registrar actividad en el servidor
function registrarActividad(accion, leadId, datosExtra) {
    var formData = new FormData();
    formData.append('action', accion);
    formData.append('lead_id', leadId);
    
    // Agregar datos extra si existen
    if (datosExtra) {
        Object.keys(datosExtra).forEach(function(key) {
            formData.append(key, datosExtra[key]);
        });
    }

    return fetch(urlBase + 'dashboard/quickAction', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(datos) {
        if (!datos.success) {
            throw new Error(datos.message || 'Error en la acción');
        }
        return datos;
    });
}

// Restaurar estado original del botón
function restaurarBoton(boton, tipoAccion) {
    boton.disabled = false;
    
    var iconos = {
        'llamar': '<i class="ti-phone"></i>',
        'whatsapp': '<i class="ti-comment"></i>',
        'completar': '<i class="ti-check"></i>'
    };
    
    boton.innerHTML = iconos[tipoAccion] || '<i class="ti-target"></i>';
}

// Preguntar resultado de una acción
function preguntarResultado(leadId, nombreCliente, tipoAccion) {
    var mensaje = '¿Cómo fue la ' + tipoAccion + ' con ' + nombreCliente + '?';
    
    // Crear modal simple
    var modal = crearModalResultado(leadId, mensaje);
    document.body.appendChild(modal);
    
    // Mostrar modal si jQuery está disponible
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $(modal).modal('show');
        
        // Limpiar al cerrar
        $(modal).on('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    }
}

// Crear modal para resultado
function crearModalResultado(leadId, mensaje) {
    var modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modalResultado';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resultado de la Acción</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>${mensaje}</p>
                    <div class="form-group">
                        <label>¿Cómo fue?</label>
                        <div class="btn-group d-flex" role="group">
                            <button type="button" class="btn btn-success flex-fill resultado-btn" data-resultado="exitoso">
                                Exitoso
                            </button>
                            <button type="button" class="btn btn-warning flex-fill resultado-btn" data-resultado="sin_respuesta">
                                Sin respuesta
                            </button>
                            <button type="button" class="btn btn-danger flex-fill resultado-btn" data-resultado="no_interesado">
                                No interesado
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Comentarios:</label>
                        <textarea class="form-control" id="comentariosResultado" rows="3" 
                                  placeholder="Comentarios adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarResultado(${leadId})">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Configurar botones de resultado
    var botonesResultado = modal.querySelectorAll('.resultado-btn');
    botonesResultado.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Quitar selección de otros botones
            botonesResultado.forEach(function(b) {
                b.classList.remove('active');
            });
            // Marcar este como seleccionado
            this.classList.add('active');
        });
    });
    
    return modal;
}

// Guardar resultado de la acción
function guardarResultado(leadId) {
    var modal = document.getElementById('modalResultado');
    var botonSeleccionado = modal.querySelector('.resultado-btn.active');
    var comentarios = document.getElementById('comentariosResultado').value;
    
    if (!botonSeleccionado) {
        mostrarMensaje('Por favor selecciona un resultado', 'warning');
        return;
    }
    
    var resultado = botonSeleccionado.getAttribute('data-resultado');
    
    // Registrar el resultado
    registrarActividad('resultado', leadId, {
        resultado: resultado,
        comentarios: comentarios
    })
    .then(function() {
        mostrarMensaje('Resultado guardado correctamente', 'success');
        
        // Cerrar modal
        if (typeof $ !== 'undefined') {
            $('#modalResultado').modal('hide');
        }
    })
    .catch(function() {
        mostrarMensaje('Error al guardar el resultado', 'error');
    });
}

// Completar una tarea
function completarTarea(tareaId) {
    var modal = document.getElementById('completarTareaModal');
    var inputTareaId = document.getElementById('tareaId');
    
    if (inputTareaId) {
        inputTareaId.value = tareaId;
    }
    
    // Mostrar modal
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $(modal).modal('show');
    }
}

// Guardar tarea completada
function guardarTareaCompletada() {
    var tareaId = document.getElementById('tareaId').value;
    var notas = document.getElementById('notasResultado').value;
    
    if (!tareaId) {
        mostrarMensaje('Error: ID de tarea no encontrado', 'error');
        return;
    }
    
    var formData = new FormData();
    formData.append('tarea_id', tareaId);
    formData.append('notas', notas);
    
    fetch(urlBase + 'dashboard/completarTarea', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(datos) {
        if (datos.success) {
            mostrarMensaje('Tarea completada exitosamente', 'success');
            
            // Cerrar modal
            if (typeof $ !== 'undefined') {
                $('#completarTareaModal').modal('hide');
            }
            
            // Recargar página después de un momento
            setTimeout(function() {
                location.reload();
            }, 1500);
        } else {
            mostrarMensaje(datos.message || 'Error al completar tarea', 'error');
        }
    })
    .catch(function() {
        mostrarMensaje('Error de conexión', 'error');
    });
}

// Mostrar mensajes al usuario
function mostrarMensaje(mensaje, tipo) {
    tipo = tipo || 'info';
    
    // Crear elemento de notificación
    var notificacion = document.createElement('div');
    notificacion.className = 'alert alert-' + (tipo === 'error' ? 'danger' : tipo) + ' alert-dismissible fade show';
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    `;
    
    notificacion.innerHTML = `
        ${mensaje}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(function() {
        if (notificacion.parentNode) {
            notificacion.remove();
        }
    }, 5000);
}

// Scroll suave a una sección
function scrollToSection(sectionId) {
    var elemento = document.getElementById(sectionId);
    if (elemento) {
        elemento.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Configurar tooltips
function configurarTooltips() {
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
}

// Animar tarjetas al cargar
function animarTarjetas() {
    var tarjetas = document.querySelectorAll('.card');
    tarjetas.forEach(function(tarjeta, indice) {
        setTimeout(function() {
            tarjeta.style.opacity = '1';
            tarjeta.style.transform = 'translateY(0)';
        }, indice * 100);
    });
}

// Validar teléfono peruano
function validarTelefono(telefono) {
    var patron = /^9\d{8}$/;
    return patron.test(telefono);
}

// Formatear teléfono
function formatearTelefono(telefono) {
    if (telefono && telefono.length === 9) {
        return telefono.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
    }
    return telefono;
}

// Detectar dispositivo móvil
function esMobil() {
    return window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Ajustar para móvil
function ajustarParaMobil() {
    if (esMobil()) {
        document.body.classList.add('es-mobil');
        
        // Hacer botones más grandes en móvil
        var botonesPequeños = document.querySelectorAll('.btn-sm');
        botonesPequeños.forEach(function(boton) {
            boton.classList.remove('btn-sm');
        });
    }
}

// Ejecutar al cargar
document.addEventListener('DOMContentLoaded', ajustarParaMobil);