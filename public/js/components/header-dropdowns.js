/**
 * Gestión de Dropdowns del Header
 */

document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que Bootstrap se inicialice
    setTimeout(() => {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            inicializarDropdowns();
            configurarComportamientos();
        } else {
            setTimeout(arguments.callee, 300);
        }
    }, 200);
});

/**
 * Inicializar dropdowns con Bootstrap
 */
function inicializarDropdowns() {
    const toggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    
    toggles.forEach(toggle => {
        // Crear instancia de Bootstrap si no existe
        if (!bootstrap.Dropdown.getInstance(toggle)) {
            new bootstrap.Dropdown(toggle, {
                autoClose: true
            });
        }
        
        // Cerrar otros dropdowns cuando se abre este
        toggle.addEventListener('show.bs.dropdown', function(e) {
            toggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    const instance = bootstrap.Dropdown.getInstance(otherToggle);
                    if (instance) {
                        instance.hide();
                    }
                }
            });
        });
    });
}

/**
 * Configurar comportamientos personalizados
 */
function configurarComportamientos() {
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');
    
    dropdownMenus.forEach(menu => {
        // Limpiar estilos inline cuando el dropdown se cierra completamente
        const parentDropdown = menu.closest('.dropdown');
        const toggle = parentDropdown?.querySelector('[data-bs-toggle="dropdown"]');
        
        if (toggle) {
            toggle.addEventListener('hidden.bs.dropdown', function() {
                menu.style.left = '';
                menu.style.right = '';
                menu.style.transition = '';
            });
        }
        
        menu.addEventListener('click', function(event) {
            const target = event.target;
            
            // Detectar si es una notificación
            const notificacion = target.closest('.notificacion-item');
            if (notificacion) {
                // Ejecutar lógica personalizada (ej: marcar como leída)
                // y cerrar después
                setTimeout(() => cerrarDropdownActual(this), 50);
                return;
            }
            
            // Detectar si es un item del menú de usuario
            const item = target.closest('.dropdown-item');
            const parentDropdown = this.closest('.dropdown');
            const toggle = parentDropdown?.querySelector('[data-bs-toggle="dropdown"]');
            
            if (item && toggle?.id === 'profileDropdown') {
                // Si es un link válido o botón de cerrar sesión
                if (item.tagName === 'A' || item.classList.contains('text-danger')) {
                    setTimeout(() => cerrarDropdownActual(this), 50);
                    return;
                }
            }
            
            // Botón especial: no cerrar dropdown
            if (target.id === 'btn-marcar-todas-leidas') {
                event.stopPropagation();
            }
        });
    });
}

/**
 * Cerrar el dropdown actual
 */
function cerrarDropdownActual(menu) {
    const parentDropdown = menu.closest('.dropdown');
    if (parentDropdown) {
        const toggle = parentDropdown.querySelector('[data-bs-toggle="dropdown"]');
        if (toggle) {
            // Deshabilitar transiciones temporalmente para evitar movimientos
            const originalTransition = menu.style.transition;
            menu.style.transition = 'none';
            
            // Fijar la posición actual antes de cerrar
            const rect = menu.getBoundingClientRect();
            menu.style.left = rect.left + 'px';
            menu.style.right = 'auto';
            
            // Restaurar transición después de un frame
            requestAnimationFrame(() => {
                menu.style.transition = originalTransition;
                
                try {
                    // Cerrar usando Bootstrap
                    if (typeof bootstrap !== 'undefined' && 
                        bootstrap.Dropdown && 
                        typeof bootstrap.Dropdown.getInstance === 'function') {
                        
                        const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                        if (bsDropdown) {
                            bsDropdown.hide();
                        } else {
                            forzarCierreDropdown(menu, toggle);
                        }
                    } else {
                        forzarCierreDropdown(menu, toggle);
                    }
                } catch (error) {
                    forzarCierreDropdown(menu, toggle);
                }
            });
        }
    }
}

/**
 * Cerrar todos los dropdowns
 */
function cerrarTodosLosDropdowns() {
    const toggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    toggles.forEach(toggle => {
        const instance = bootstrap?.Dropdown.getInstance(toggle);
        if (instance) {
            instance.hide();
        }
    });
}

// Exportar funciones globales
window.cerrarDropdownActual = cerrarDropdownActual;
window.cerrarTodosLosDropdowns = cerrarTodosLosDropdowns;