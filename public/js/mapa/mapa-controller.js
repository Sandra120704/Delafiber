/**
 * Controlador Principal del Mapa 
 * Gestiona la lógica principal del mapa y la coordinación entre módulos
 */

class MapaController {
    constructor() {
        this.map = null;
        this.marcadoresLayer = null;
        this.dataService = new MapaDataService();
        this.usageController = new MapaUsageController(); 
        this.initialized = false;
    }

    /**
     * Inicializar el mapa completo
     */
    async init() {
        try {
            console.log('Inicializando Mapa Controller...');
            
            this.inicializarMapa();
            await this.cargarDatos();
            this.configurarEventos();
            
            this.initialized = true;
            console.log('Mapa inicializado correctamente');
            
        } catch (error) {
            console.error('Error inicializando mapa:', error);
            MapaUtils.ocultarCarga();
        }
    }

    /**
     * Configurar el mapa base con Google Maps
     */
    inicializarMapa() {
        console.log('Configurando Google Maps...');
        
        // Crear mapa Google Maps
        this.map = new google.maps.Map(document.getElementById('map'), {
            zoom: MapaConfig.defaultZoom,
            center: MapaConfig.defaultCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: MapaConfig.mapOptions.styles,
            disableDefaultUI: MapaConfig.mapOptions.disableDefaultUI,
            zoomControl: MapaConfig.mapOptions.zoomControl,
            mapTypeControl: MapaConfig.mapOptions.mapTypeControl,
            scaleControl: MapaConfig.mapOptions.scaleControl,
            streetViewControl: MapaConfig.mapOptions.streetViewControl,
            rotateControl: MapaConfig.mapOptions.rotateControl,
            fullscreenControl: MapaConfig.mapOptions.fullscreenControl
        });

        // Inicializar array para marcadores (Google Maps no usa capas como Leaflet)
        this.marcadores = [];
        
        // Si tienes MarkerClusterer disponible, inicialízalo
        if (typeof MarkerClusterer !== 'undefined') {
            this.markerClusterer = new MarkerClusterer(this.map, [], MapaConfig.clusterOptions);
        }
    }

    /**
     * Cargar datos y mostrar marcadores
     */
    async cargarDatos() {
        console.log('Cargando datos...');
        
        // Verificar límites antes de cargar
        if (!this.usageController.canPerformOperation('map_load')) {
            MapaUtils.ocultarCarga();
            return false;
        }
        
        const success = await this.dataService.cargarTodosLosDatos();
        
        if (success) {
            // Registrar uso de la API
            this.usageController.recordUsage('map_load', 1);
            await this.procesarYMostrarMarcadores();
        }
        
        MapaUtils.ocultarCarga();
        return success;
    }

    /**
     * Procesar y mostrar marcadores en Google Maps
     */
    async procesarYMostrarMarcadores() {
        const marcadores = this.dataService.getMarcadores();
        
        if (marcadores.length === 0) {
            MapaUtils.actualizarContadores(0, 0, 0);
            MapaUtils.mostrarSinDatos();
            return;
        }

        let countLeads = 0;
        let countConvertidos = 0;
        let countDescartados = 0;

        // Limpiar marcadores existentes
        this.limpiarMarcadores();

        // Procesar marcadores (limitado para demo)
        const total = Math.min(marcadores.length, MapaConfig.maxMarkersToShow);
        const bounds = new google.maps.LatLngBounds();

        for (let i = 0; i < total; i++) {
            const item = marcadores[i];
            
            try {
                // Obtener coordenadas base del distrito
                const coordsBase = this.dataService.obtenerCoordenadasDistrito(item.distrito);
                
                // Agregar variación aleatoria
                const coords = MapaUtils.generarCoordenadasConVariacion(coordsBase);
                
                // Crear marcador Google Maps
                const color = this.dataService.getColorPorEstado(item.estado);
                const icon = MapaUtils.crearIconoSVG(color);
                
                const marker = new google.maps.Marker({
                    position: coords,
                    map: this.map,
                    icon: icon,
                    title: item.cliente,
                    animation: google.maps.Animation.DROP
                });

                // Crear InfoWindow
                const infoWindow = MapaUtils.crearInfoWindow(MapaUtils.crearPopupHTML(item));
                
                // Agregar evento click
                marker.addListener('click', () => {
                    // Cerrar otros InfoWindows abiertos
                    if (this.currentInfoWindow) {
                        this.currentInfoWindow.close();
                    }
                    infoWindow.open(this.map, marker);
                    this.currentInfoWindow = infoWindow;
                });

                // Guardar marcador
                this.marcadores.push(marker);
                
                // Agregar al clusterer si está disponible
                if (this.markerClusterer) {
                    this.markerClusterer.addMarker(marker);
                }
                
                // Extender bounds
                bounds.extend(coords);

                // Contar por estado
                if (item.estado === 'Convertido') countConvertidos++;
                else if (item.estado === 'Descartado') countDescartados++;
                else countLeads++;
                
            } catch (error) {
                console.error('Error procesando marcador:', item.direccion_completa, error);
            }
        }

        // Actualizar estadísticas en la UI
        MapaUtils.actualizarContadores(countLeads, countConvertidos, countDescartados);

        // Ajustar vista del mapa a los marcadores
        if (this.marcadores.length > 0) {
            this.map.fitBounds(bounds);
        }
    }

    /**
     * Limpiar marcadores del mapa
     */
    limpiarMarcadores() {
        // Limpiar marcadores individuales
        this.marcadores.forEach(marker => {
            marker.setMap(null);
        });
        this.marcadores = [];
        
        // Limpiar clusterer si existe
        if (this.markerClusterer) {
            this.markerClusterer.clearMarkers();
        }
        
        // Cerrar InfoWindow actual si existe
        if (this.currentInfoWindow) {
            this.currentInfoWindow.close();
        }
    }

    /**
     * Configurar eventos de la interfaz
     */
    configurarEventos() {
        // Toggle de capas
        document.getElementById('capaLeads').addEventListener('change', () => this.filtrarMarcadores());
        document.getElementById('capaConvertidos').addEventListener('change', () => this.filtrarMarcadores());
        document.getElementById('capaDescartados').addEventListener('change', () => this.filtrarMarcadores());
        document.getElementById('capaCampanias').addEventListener('change', () => this.toggleCapaCampanias());
        document.getElementById('capaEstadisticas').addEventListener('change', () => this.toggleCapaEstadisticas());
    }

    /**
     * Filtrar marcadores según las capas seleccionadas
     */
    filtrarMarcadores() {
        const mostrarLeads = document.getElementById('capaLeads').checked;
        const mostrarConvertidos = document.getElementById('capaConvertidos').checked;
        const mostrarDescartados = document.getElementById('capaDescartados').checked;

        // Obtener marcadores filtrados
        const marcadoresFiltrados = this.dataService.filtrarMarcadoresPorEstado(
            mostrarLeads, mostrarConvertidos, mostrarDescartados
        );

        // Redesplegar marcadores
        this.redesplegarMarcadores(marcadoresFiltrados);
    }

    /**
     * Redesplegar marcadores filtrados (Google Maps)
     */
    redesplegarMarcadores(marcadoresFiltrados) {
        this.limpiarMarcadores();
        
        let countLeads = 0;
        let countConvertidos = 0;
        let countDescartados = 0;
        const bounds = new google.maps.LatLngBounds();

        marcadoresFiltrados.forEach(item => {
            const coordsBase = this.dataService.obtenerCoordenadasDistrito(item.distrito);
            const coords = MapaUtils.generarCoordenadasConVariacion(coordsBase);
            
            const color = this.dataService.getColorPorEstado(item.estado);
            const icon = MapaUtils.crearIconoSVG(color);
            
            const marker = new google.maps.Marker({
                position: coords,
                map: this.map,
                icon: icon,
                title: item.cliente,
                animation: google.maps.Animation.DROP
            });

            // InfoWindow
            const infoWindow = MapaUtils.crearInfoWindow(MapaUtils.crearPopupHTML(item));
            marker.addListener('click', () => {
                if (this.currentInfoWindow) {
                    this.currentInfoWindow.close();
                }
                infoWindow.open(this.map, marker);
                this.currentInfoWindow = infoWindow;
            });

            this.marcadores.push(marker);
            
            if (this.markerClusterer) {
                this.markerClusterer.addMarker(marker);
            }
            
            bounds.extend(coords);

            // Contar por estado
            if (item.estado === 'Convertido') countConvertidos++;
            else if (item.estado === 'Descartado') countDescartados++;
            else countLeads++;
        });

        MapaUtils.actualizarContadores(countLeads, countConvertidos, countDescartados);
        
        // Ajustar vista si hay marcadores
        if (this.marcadores.length > 0) {
            this.map.fitBounds(bounds);
        }
    }

    /**
     * Toggle capa de campañas
     */
    toggleCapaCampanias() {
        const mostrar = document.getElementById('capaCampanias').checked;
        
        if (mostrar) {
            const campanias = this.dataService.getCampanias();
            Swal.fire({
                icon: 'info',
                title: 'Campañas por Zona',
                html: MapaUtils.generarHTMLCampanias(campanias),
                width: 600
            });
        }
    }

    /**
     * Toggle capa de estadísticas
     */
    toggleCapaEstadisticas() {
        const mostrar = document.getElementById('capaEstadisticas').checked;
        
        if (mostrar) {
            const estadisticas = this.dataService.getEstadisticas();
            Swal.fire({
                icon: 'info',
                title: 'Estadísticas por Distrito',
                html: MapaUtils.generarHTMLEstadisticas(estadisticas),
                width: 700
            });
        }
    }

    /**
     * Recargar mapa completo (Google Maps)
     */
    async recargar() {
        // Verificar límites de recarga
        if (!this.usageController.canPerformOperation('reload')) {
            return false;
        }
        
        MapaUtils.mostrarCarga();
        this.limpiarMarcadores();
        
        const success = await this.cargarDatos();
        
        if (success) {
            MapaUtils.mostrarExito('Actualizado', 'Datos del mapa actualizados');
        }
        
        MapaUtils.ocultarCarga();
        return success;
    }
    
    /**
     * Mostrar estadísticas de uso
     */
    mostrarEstadisticasUso() {
        this.usageController.showUsageDashboard();
    }
}

// Exportar controlador globalmente
window.MapaController = MapaController;