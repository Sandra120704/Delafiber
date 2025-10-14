/**
 * Sistema de Notificaciones en Tiempo Real
 * Polling cada 30 segundos
 */

// baseUrl ya está declarado globalmente en header.php
let ultimaConsulta = null;
let pollingInterval = null;

/**
 * Inicializar sistema de notificaciones
 */
document.addEventListener('DOMContentLoaded', function() {
    cargarNotificacionesIniciales();
    iniciarPolling();
    inicializarEventos();
});

/**
 * Cargar notificaciones iniciales
 */
async function cargarNotificacionesIniciales() {
    try {
        const response = await fetch(`${baseUrl}/notificaciones/getNoLeidas`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            actualizarBadge(data.total);
            mostrarNotificacionesEnDropdown(data.notificaciones);
            ultimaConsulta = new Date().toISOString();
        }
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    }
}

/**
 * Iniciar polling automático
 */
function iniciarPolling() {
    // Polling cada 30 segundos
    pollingInterval = setInterval(async () => {
        try {
            const response = await fetch(
                `${baseUrl}/notificaciones/poll?ultima_consulta=${ultimaConsulta}`,
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            const data = await response.json();

            if (data.success) {
                // Si hay notificaciones nuevas
                if (data.nuevas && data.nuevas.length > 0) {
                    actualizarBadge(data.total_no_leidas);
                    mostrarNotificacionesNuevas(data.nuevas);
                    
                    // Mostrar toast para la primera notificación
                    mostrarToastNotificacion(data.nuevas[0]);
                }

                ultimaConsulta = data.timestamp;
            }
        } catch (error) {
            console.error('Error en polling:', error);
        }
    }, 30000); // 30 segundos
}

/**
 * Actualizar badge de notificaciones
 */
function actualizarBadge(total) {
    const badge = document.getElementById('notificaciones-badge');
    if (badge) {
        if (total > 0) {
            badge.textContent = total > 99 ? '99+' : total;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Mostrar notificaciones en dropdown
 */
function mostrarNotificacionesEnDropdown(notificaciones) {
    const container = document.getElementById('notificaciones-lista');
    if (!container) return;

    if (notificaciones.length === 0) {
        container.innerHTML = `
            <div class="dropdown-item text-center text-muted py-4">
                <i class="mdi mdi-bell-off mdi-24px"></i>
                <p class="mb-0 mt-2">No tienes notificaciones</p>
            </div>
        `;
        return;
    }

    container.innerHTML = notificaciones.map(n => `
        <a href="${n.url || '#'}" class="dropdown-item notificacion-item ${n.leida ? 'leida' : 'no-leida'}" 
           data-id="${n.idnotificacion}" onclick="marcarComoLeida(${n.idnotificacion})">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    ${getIconoNotificacion(n.tipo)}
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">${n.titulo}</h6>
                    <p class="mb-1 text-muted small">${n.mensaje}</p>
                    <small class="text-muted">${formatearFecha(n.created_at)}</small>
                </div>
            </div>
        </a>
        <div class="dropdown-divider"></div>
    `).join('');
}

/**
 * Mostrar notificaciones nuevas (agregar al inicio)
 */
function mostrarNotificacionesNuevas(nuevas) {
    const container = document.getElementById('notificaciones-lista');
    if (!container) return;

    const html = nuevas.map(n => `
        <a href="${n.url || '#'}" class="dropdown-item notificacion-item no-leida notificacion-nueva" 
           data-id="${n.idnotificacion}" onclick="marcarComoLeida(${n.idnotificacion})">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    ${getIconoNotificacion(n.tipo)}
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">${n.titulo}</h6>
                    <p class="mb-1 text-muted small">${n.mensaje}</p>
                    <small class="text-muted">${formatearFecha(n.created_at)}</small>
                </div>
            </div>
        </a>
        <div class="dropdown-divider"></div>
    `).join('');

    container.insertAdjacentHTML('afterbegin', html);

    // Animar entrada
    setTimeout(() => {
        document.querySelectorAll('.notificacion-nueva').forEach(el => {
            el.classList.add('fade-in');
        });
    }, 100);
}

/**
 * Mostrar toast de notificación nueva
 */
function mostrarToastNotificacion(notificacion) {
    // Verificar si el navegador soporta notificaciones
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(notificacion.titulo, {
            body: notificacion.mensaje,
            icon: '/images/logo-delafiber.png',
            badge: '/images/logo-delafiber.png'
        });
    }

    // Toast visual en la página
    const toast = document.createElement('div');
    toast.className = 'toast-notificacion';
    toast.innerHTML = `
        <div class="toast-header">
            ${getIconoNotificacion(notificacion.tipo)}
            <strong class="me-auto ms-2">${notificacion.titulo}</strong>
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
        <div class="toast-body">
            ${notificacion.mensaje}
        </div>
    `;

    document.body.appendChild(toast);

    // Mostrar con animación
    setTimeout(() => toast.classList.add('show'), 100);

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Marcar notificación como leída
 */
async function marcarComoLeida(id) {
    try {
        await fetch(`${baseUrl}/notificaciones/marcarLeida/${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Actualizar UI
        const item = document.querySelector(`[data-id="${id}"]`);
        if (item) {
            item.classList.remove('no-leida');
            item.classList.add('leida');
        }

        // Recargar contador
        cargarNotificacionesIniciales();

    } catch (error) {
        console.error('Error al marcar como leída:', error);
    }
}

/**
 * Marcar todas como leídas
 */
async function marcarTodasLeidas() {
    try {
        const response = await fetch(`${baseUrl}/notificaciones/marcarTodasLeidas`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Actualizar UI
            document.querySelectorAll('.notificacion-item').forEach(item => {
                item.classList.remove('no-leida');
                item.classList.add('leida');
            });

            actualizarBadge(0);

            Swal.fire({
                icon: 'success',
                title: 'Todas las notificaciones marcadas como leídas',
                timer: 1500,
                showConfirmButton: false
            });
        }

    } catch (error) {
        console.error('Error:', error);
    }
}

/**
 * Obtener icono según tipo de notificación
 */
function getIconoNotificacion(tipo) {
    const iconos = {
        'lead_asignado': '<i class="mdi mdi-account-plus text-primary mdi-24px"></i>',
        'lead_reasignado': '<i class="mdi mdi-account-switch text-info mdi-24px"></i>',
        'tarea_asignada': '<i class="mdi mdi-calendar-check text-success mdi-24px"></i>',
        'tarea_vencida': '<i class="mdi mdi-calendar-alert text-danger mdi-24px"></i>',
        'apoyo_urgente': '<i class="mdi mdi-alert text-danger mdi-24px"></i>',
        'solicitud_apoyo': '<i class="mdi mdi-account-multiple text-warning mdi-24px"></i>',
        'seguimiento_programado': '<i class="mdi mdi-clock-outline text-info mdi-24px"></i>',
        'transferencia_masiva': '<i class="mdi mdi-package-variant text-primary mdi-24px"></i>',
        'default': '<i class="mdi mdi-bell text-secondary mdi-24px"></i>'
    };

    return iconos[tipo] || iconos['default'];
}

/**
 * Formatear fecha relativa
 */
function formatearFecha(fecha) {
    const ahora = new Date();
    const fechaNotif = new Date(fecha);
    const diff = Math.floor((ahora - fechaNotif) / 1000); // segundos

    if (diff < 60) return 'Hace un momento';
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
    if (diff < 604800) return `Hace ${Math.floor(diff / 86400)} días`;
    
    return fechaNotif.toLocaleDateString('es-PE', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

/**
 * Inicializar eventos
 */
function inicializarEventos() {
    // Botón de marcar todas como leídas
    const btnMarcarTodas = document.getElementById('btn-marcar-todas-leidas');
    if (btnMarcarTodas) {
        btnMarcarTodas.addEventListener('click', marcarTodasLeidas);
    }

    // Solicitar permiso para notificaciones del navegador
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

/**
 * Detener polling (útil al cerrar sesión)
 */
function detenerPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// Exportar funciones globales
window.marcarComoLeida = marcarComoLeida;
window.marcarTodasLeidas = marcarTodasLeidas;
window.detenerPolling = detenerPolling;

// Detener polling al cerrar/recargar página
window.addEventListener('beforeunload', detenerPolling);
