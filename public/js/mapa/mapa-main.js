/**
 * Inicializador Principal del Mapa
 * Punto de entrada principal que coordina la inicialización
 */

// Instancia global del controlador del mapa
let mapaController;

/**
 * Inicializar mapa cuando Google Maps esté listo
 */
document.addEventListener('googleMapsReady', async function() {
    console.log('🗺️ Iniciando Mapa Interactivo con Google Maps...');
    
    try {
        // Verificar que todas las dependencias estén cargadas
        if (!verificarDependencias()) {
            throw new Error('Faltan dependencias requeridas');
        }

        // Crear e inicializar controlador
        mapaController = new MapaController();
        await mapaController.init();
        
        console.log('✅ Mapa Google Maps inicializado exitosamente');
        
    } catch (error) {
        console.error('❌ Error inicializando mapa:', error);
        mostrarErrorInicializacion(error);
    }
});

/**
 * Verificar que todas las dependencias estén disponibles
 */
function verificarDependencias() {
    const dependencias = [
        'google',               // Google Maps API
        'Swal',                 // SweetAlert2
        'MapaConfig',           // Configuración
        'MapaDataService',      // Servicio de datos
        'MapaUtils'             // Utilidades
    ];

    for (const dep of dependencias) {
        if (typeof window[dep] === 'undefined') {
            console.error(`❌ Dependencia faltante: ${dep}`);
            return false;
        }
    }

    // Verificar específicamente Google Maps
    if (!window.google || !window.google.maps) {
        console.error('❌ Google Maps API no está cargada');
        return false;
    }

    return true;
}

/**
 * Mostrar error de inicialización
 */
function mostrarErrorInicializacion(error) {
    // Ocultar loading
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }

    // Mostrar error
    Swal.fire({
        icon: 'error',
        title: 'Error de Inicialización',
        text: 'No se pudo inicializar el mapa: ' + error.message,
        footer: 'Intenta recargar la página'
    });
}

/**
 * Función global para recargar el mapa (llamada desde la vista)
 */
window.recargarMapa = function() {
    if (mapaController && mapaController.initialized) {
        mapaController.recargar();
    } else {
        console.warn('El mapa no está inicializado correctamente');
    }
};

/**
 * Función global para mostrar estadísticas de uso
 */
window.mostrarEstadisticasUso = function() {
    if (mapaController && mapaController.initialized) {
        mapaController.mostrarEstadisticasUso();
    } else {
        console.warn('El mapa no está inicializado correctamente');
    }
};

/**
 * Funciones de utilidad globales para compatibilidad
 */
window.MapaApp = {
    getController: () => mapaController,
    isInitialized: () => mapaController && mapaController.initialized,
    reload: () => window.recargarMapa()
};