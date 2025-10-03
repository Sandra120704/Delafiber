/**
 * Servicio de Datos del Mapa
 * Maneja todas las peticiones AJAX y gestión de datos
 */

class MapaDataService {
    constructor() {
        this.marcadoresData = [];
        this.estadisticasData = [];
        this.campaniasData = [];
    }

    /**
     * Cargar todos los datos necesarios para el mapa
     */
    async cargarTodosLosDatos() {
        try {
            console.log('Cargando datos del mapa...');
            
            // Cargar datos en paralelo
            const [leadsResponse, statsResponse, campResponse] = await Promise.all([
                this.cargarLeads(),
                this.cargarEstadisticas(),
                this.cargarCampanias()
            ]);

            console.log('Todos los datos cargados exitosamente');
            return true;
            
        } catch (error) {
            console.error('Error cargando datos:', error);
            this.mostrarErrorCarga(error);
            return false;
        }
    }

    /**
     * Cargar leads desde la API
     */
    async cargarLeads() {
        const response = await fetch(MapaConfig.endpoints.leads, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            this.marcadoresData = data.marcadores;
            console.log('Leads cargados:', this.marcadoresData.length);
            return data;
        } else {
            throw new Error('Error en la respuesta de leads');
        }
    }

    /**
     * Cargar estadísticas desde la API
     */
    async cargarEstadisticas() {
        const response = await fetch(MapaConfig.endpoints.estadisticas, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            this.estadisticasData = data.estadisticas;
            console.log('Estadísticas cargadas:', this.estadisticasData.length);
            return data;
        } else {
            console.warn('No se pudieron cargar las estadísticas');
            return { success: false };
        }
    }

    /**
     * Cargar campañas desde la API
     */
    async cargarCampanias() {
        const response = await fetch(MapaConfig.endpoints.campanias, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            this.campaniasData = data.campanias;
            console.log('Campañas cargadas:', this.campaniasData.length);
            return data;
        } else {
            console.warn('No se pudieron cargar las campañas');
            return { success: false };
        }
    }

    /**
     * Obtener coordenadas por distrito (formato Google Maps)
     */
    obtenerCoordenadasDistrito(distrito) {
        const coords = MapaConfig.districtCoordinates[distrito] || MapaConfig.districtCoordinates['Chincha Alta'];
        return {
            lat: coords.lat,
            lng: coords.lng
        };
    }

    /**
     * Obtener color por estado
     */
    getColorPorEstado(estado) {
        return MapaConfig.statusColors[estado] || MapaConfig.statusColors.default;
    }

    /**
     * Filtrar marcadores por estado
     */
    filtrarMarcadoresPorEstado(mostrarLeads, mostrarConvertidos, mostrarDescartados) {
        return this.marcadoresData.filter(item => {
            const esLead = !item.estado || item.estado === 'Activo';
            const esConvertido = item.estado === 'Convertido';
            const esDescartado = item.estado === 'Descartado';

            return (esLead && mostrarLeads) || 
                   (esConvertido && mostrarConvertidos) || 
                   (esDescartado && mostrarDescartados);
        });
    }

    /**
     * Mostrar error de carga
     */
    mostrarErrorCarga(error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos del mapa: ' + error.message
        });
    }

    /**
     * Getters para acceso a los datos
     */
    getMarcadores() { return this.marcadoresData; }
    getEstadisticas() { return this.estadisticasData; }
    getCampanias() { return this.campaniasData; }
}

// Exportar servicio globalmente
window.MapaDataService = MapaDataService;