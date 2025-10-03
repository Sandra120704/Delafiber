/**
 * Configuración del Mapa Google Maps
 * Contiene constantes, coordenadas y configuraciones globales
 */

const MapaConfig = {
    // Google Maps API Key
    googleMapsApiKey: 'AIzaSyBIxTc7yNv1EdTv6wg6uIAZ55NITSkhqvU', // API Key configurada
    
    // Configuración inicial del mapa
    defaultCenter: { lat: -13.4099, lng: -76.1319 }, // Chincha Alta (formato Google Maps)
    defaultZoom: 13,
    maxZoom: 20,
    minZoom: 8,
    
    // URLs de la API
    endpoints: {
        leads: window.baseUrl + 'mapa/getLeadsParaMapa',
        estadisticas: window.baseUrl + 'mapa/getEstadisticasPorZona',
        campanias: window.baseUrl + 'mapa/getCampaniasPorZona'
    },
    
    // Coordenadas de distritos de Chincha, Ica (formato Google Maps)
    districtCoordinates: {
        'Chincha Alta': { lat: -13.4099, lng: -76.1319 },
        'Sunampe': { lat: -13.4247, lng: -76.1658 },
        'Grocio Prado': { lat: -13.3156, lng: -76.2269 },
        'Pueblo Nuevo': { lat: -13.4500, lng: -76.1500 },
        'Alto Larán': { lat: -13.3833, lng: -76.1167 },
        'Chavín': { lat: -13.4667, lng: -76.1833 },
        'El Carmen': { lat: -13.5500, lng: -76.1000 },
        'San Juan de Yanac': { lat: -13.2500, lng: -75.9833 },
        'San Pedro de Huacarpana': { lat: -13.2167, lng: -75.9500 },
        'Tambo de Mora': { lat: -13.4500, lng: -76.1833 }
    },
    
    // Colores por estado
    statusColors: {
        'Convertido': '#28a745', // Verde
        'Descartado': '#dc3545', // Rojo
        'Activo': '#007bff',     // Azul
        'default': '#007bff'
    },
    
    // Configuración de Google Maps
    mapOptions: {
        zoom: 13,
        center: { lat: -13.4099, lng: -76.1319 },
        mapTypeId: 'roadmap', // roadmap, satellite, hybrid, terrain
        styles: [], // Estilos personalizados del mapa
        disableDefaultUI: false,
        zoomControl: true,
        mapTypeControl: true,
        scaleControl: true,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: true
    },
    
    // Configuración de clusters (Google Maps)
    clusterOptions: {
        gridSize: 50,
        maxZoom: 15,
        styles: [
            {
                url: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiMwMDdiZmYiLz4KPGI+',
                width: 40,
                height: 40,
                textColor: '#ffffff',
                textSize: 12
            }
        ]
    },
    
    // Límites para demo
    maxMarkersToShow: 50, // Aumentado para Google Maps
    
    // Variación aleatoria para separar marcadores del mismo distrito
    randomVariation: 0.01,
    
    // Configuración de marcadores
    markerOptions: {
        animation: 'DROP', // DROP, BOUNCE
        clickable: true,
        draggable: false
    }
};

// Exportar configuración globalmente
window.MapaConfig = MapaConfig;