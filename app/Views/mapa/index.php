<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0" style="height: calc(100vh - 60px);">
    <div class="row g-0 h-100">
        <!-- Panel Lateral de Controles -->
        <div class="col-md-3 bg-white border-end" style="height: 100%; overflow-y: auto;">
            <div class="p-3">
                <h4 class="mb-3">🗺️ Mapa Interactivo</h4>
                
                <!-- Filtros de Capas -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">📍 Capas del Mapa</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaLeads" checked>
                            <label class="form-check-label" for="capaLeads">
                                🎯 Leads Activos
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaConvertidos" checked>
                            <label class="form-check-label" for="capaConvertidos">
                                ✅ Clientes (Convertidos)
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaDescartados">
                            <label class="form-check-label" for="capaDescartados">
                                ❌ Descartados
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaCampanias">
                            <label class="form-check-label" for="capaCampanias">
                                📢 Campañas por Zona
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="capaEstadisticas">
                            <label class="form-check-label" for="capaEstadisticas">
                                📊 Estadísticas por Distrito
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Leyenda de Colores -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">🎨 Leyenda</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background: #007bff; border-radius: 50%; margin-right: 10px;"></div>
                            <small>Lead Activo</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background: #28a745; border-radius: 50%; margin-right: 10px;"></div>
                            <small>Cliente (Convertido)</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background: #dc3545; border-radius: 50%; margin-right: 10px;"></div>
                            <small>Descartado</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background: #fd7e14; border-radius: 50%; margin-right: 10px;"></div>
                            <small>Campaña Activa</small>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">📈 Estadísticas</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Total en Mapa:</small>
                            <h5 class="mb-0" id="totalMarcadores">0</h5>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-1">
                            <small>Leads Activos:</small>
                            <strong id="countLeads">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small>Convertidos:</small>
                            <strong id="countConvertidos" class="text-success">0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small>Descartados:</small>
                            <strong id="countDescartados" class="text-danger">0</strong>
                        </div>
                    </div>
                </div>

                <!-- Botón Recargar -->
                <button class="btn btn-primary w-100 mt-3" onclick="recargarMapa()">
                    <i class="ti-reload"></i> Recargar Datos
                </button>
            </div>
        </div>

        <!-- Mapa Principal -->
        <div class="col-md-9 position-relative" style="height: 100%;">
            <div id="map" style="width: 100%; height: 100%;"></div>
            
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 1000;">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mb-0">Cargando mapa...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet MarkerCluster (para agrupar marcadores) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let map;
let marcadoresLayer;
let marcadoresData = [];
let estadisticasData = [];
let campaniasData = [];

// Inicializar mapa
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando mapa...');
    inicializarMapa();
    cargarDatos();
    configurarEventos();
});

function inicializarMapa() {
    // Centrar en Chincha, Ica
    map = L.map('map').setView([-13.4099, -76.1319], 13);

    // Capa base de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Capa para marcadores con clustering
    marcadoresLayer = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true
    });

    map.addLayer(marcadoresLayer);

    // El loading se ocultará después de cargar datos
    console.log('Mapa inicializado correctamente');
}

async function cargarDatos() {
    try {
        console.log('Cargando datos del mapa...');
        
        // Cargar leads
        const responseLeads = await fetch('<?= base_url('mapa/getLeadsParaMapa') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        console.log('Response status:', responseLeads.status);
        const dataLeads = await responseLeads.json();
        console.log('Leads data:', dataLeads);
        
        if (dataLeads.success) {
            marcadoresData = dataLeads.marcadores;
            console.log('Total marcadores:', marcadoresData.length);
            await geocodificarYMostrar();
        } else {
            console.error('Error en respuesta de leads');
        }

        // Cargar estadísticas
        const responseStats = await fetch('<?= base_url('mapa/getEstadisticasPorZona') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const dataStats = await responseStats.json();
        
        if (dataStats.success) {
            estadisticasData = dataStats.estadisticas;
            console.log('Estadísticas cargadas:', estadisticasData.length);
        }

        // Cargar campañas
        const responseCamp = await fetch('<?= base_url('mapa/getCampaniasPorZona') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const dataCamp = await responseCamp.json();
        
        if (dataCamp.success) {
            campaniasData = dataCamp.campanias;
            console.log('Campañas cargadas:', campaniasData.length);
        }

        console.log('Datos cargados completamente');
        
        // Ocultar loading
        document.getElementById('loadingOverlay').style.display = 'none';

    } catch (error) {
        console.error('Error cargando datos:', error);
        document.getElementById('loadingOverlay').style.display = 'none';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos del mapa: ' + error.message
        });
    }
}

async function geocodificarYMostrar() {
    let countLeads = 0;
    let countConvertidos = 0;
    let countDescartados = 0;

    // Limpiar marcadores existentes
    marcadoresLayer.clearLayers();

    // Si no hay datos, mostrar mensaje
    if (marcadoresData.length === 0) {
        document.getElementById('totalMarcadores').textContent = '0';
        document.getElementById('countLeads').textContent = '0';
        document.getElementById('countConvertidos').textContent = '0';
        document.getElementById('countDescartados').textContent = '0';
        
        Swal.fire({
            icon: 'info',
            title: 'Sin datos',
            text: 'No hay leads con direcciones registradas para mostrar en el mapa',
            timer: 3000
        });
        return;
    }

    // Mostrar progreso
    let procesados = 0;
    const total = Math.min(marcadoresData.length, 20); // Limitar a 20 para demo

    for (let i = 0; i < total; i++) {
        const item = marcadoresData[i];
        
        try {
            // Usar coordenadas aproximadas por distrito (más rápido)
            const coords = obtenerCoordenadasDistrito(item.distrito);
            
            if (coords) {
                // Agregar variación aleatoria para separar marcadores del mismo distrito
                const lat = coords.lat + (Math.random() - 0.5) * 0.01;
                const lon = coords.lon + (Math.random() - 0.5) * 0.01;
                
                // Crear marcador
                const color = getColorPorEstado(item.estado);
                const icon = crearIconoPersonalizado(color);
                
                const marker = L.marker([lat, lon], { icon: icon })
                    .bindPopup(crearPopupHTML(item));
                
                marcadoresLayer.addLayer(marker);

                // Contar por estado
                if (item.estado === 'Convertido') countConvertidos++;
                else if (item.estado === 'Descartado') countDescartados++;
                else countLeads++;
            }
            
            procesados++;
            
        } catch (error) {
            console.error('Error procesando:', item.direccion_completa, error);
        }
    }

    // Actualizar estadísticas
    document.getElementById('totalMarcadores').textContent = countLeads + countConvertidos + countDescartados;
    document.getElementById('countLeads').textContent = countLeads;
    document.getElementById('countConvertidos').textContent = countConvertidos;
    document.getElementById('countDescartados').textContent = countDescartados;

    // Ajustar vista del mapa a los marcadores
    if (marcadoresLayer.getLayers().length > 0) {
        map.fitBounds(marcadoresLayer.getBounds(), { padding: [50, 50] });
    }
}

// Coordenadas aproximadas de distritos de Chincha, Ica
function obtenerCoordenadasDistrito(distrito) {
    const coordenadas = {
        'Chincha Alta': { lat: -13.4099, lon: -76.1319 },
        'Sunampe': { lat: -13.4247, lon: -76.1658 },
        'Grocio Prado': { lat: -13.3156, lon: -76.2269 },
        'Pueblo Nuevo': { lat: -13.4500, lon: -76.1500 },
        'Alto Larán': { lat: -13.3833, lon: -76.1167 },
        'Chavín': { lat: -13.4667, lon: -76.1833 },
        'El Carmen': { lat: -13.5500, lon: -76.1000 },
        'San Juan de Yanac': { lat: -13.2500, lon: -75.9833 },
        'San Pedro de Huacarpana': { lat: -13.2167, lon: -75.9500 },
        'Tambo de Mora': { lat: -13.4500, lon: -76.1833 }
    };

    return coordenadas[distrito] || { lat: -13.4099, lon: -76.1319 }; // Chincha Alta por defecto
}

function getColorPorEstado(estado) {
    switch (estado) {
        case 'Convertido': return '#28a745'; // Verde
        case 'Descartado': return '#dc3545'; // Rojo
        default: return '#007bff'; // Azul
    }
}

function crearIconoPersonalizado(color) {
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            background-color: ${color};
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        "></div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
}

function crearPopupHTML(item) {
    const estadoBadge = item.estado === 'Convertido' 
        ? '<span class="badge bg-success">Cliente</span>'
        : item.estado === 'Descartado'
        ? '<span class="badge bg-danger">Descartado</span>'
        : '<span class="badge bg-primary">Lead Activo</span>';

    return `
        <div style="min-width: 250px;">
            <h6 class="mb-2">
                <strong>${item.cliente}</strong>
                ${estadoBadge}
            </h6>
            <hr class="my-2">
            <p class="mb-1"><small><strong>📍 Dirección:</strong></small><br>
               <small>${item.direccion}</small></p>
            <p class="mb-1"><small><strong>📍 Distrito:</strong> ${item.distrito || 'N/A'}</small></p>
            <p class="mb-1"><small><strong>📞 Teléfono:</strong> ${item.telefono || 'N/A'}</small></p>
            <p class="mb-1"><small><strong>📧 Correo:</strong> ${item.correo || 'N/A'}</small></p>
            ${item.etapa ? `<p class="mb-1"><small><strong>📊 Etapa:</strong> ${item.etapa}</small></p>` : ''}
            ${item.origen ? `<p class="mb-1"><small><strong>🎯 Origen:</strong> ${item.origen}</small></p>` : ''}
            ${item.campania ? `<p class="mb-1"><small><strong>📢 Campaña:</strong> ${item.campania}</small></p>` : ''}
            ${item.vendedor ? `<p class="mb-1"><small><strong>👤 Vendedor:</strong> ${item.vendedor}</small></p>` : ''}
            <hr class="my-2">
            <div class="d-flex gap-2">
                <a href="<?= base_url('leads/ver') ?>/${item.id}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="ti-eye"></i> Ver Lead
                </a>
                <a href="https://wa.me/51${item.telefono}" class="btn btn-sm btn-success" target="_blank">
                    <i class="ti-mobile"></i> WhatsApp
                </a>
            </div>
        </div>
    `;
}

function configurarEventos() {
    // Toggle de capas
    document.getElementById('capaLeads').addEventListener('change', filtrarMarcadores);
    document.getElementById('capaConvertidos').addEventListener('change', filtrarMarcadores);
    document.getElementById('capaDescartados').addEventListener('change', filtrarMarcadores);
    document.getElementById('capaCampanias').addEventListener('change', toggleCapaCampanias);
    document.getElementById('capaEstadisticas').addEventListener('change', toggleCapaEstadisticas);
}

function filtrarMarcadores() {
    const mostrarLeads = document.getElementById('capaLeads').checked;
    const mostrarConvertidos = document.getElementById('capaConvertidos').checked;
    const mostrarDescartados = document.getElementById('capaDescartados').checked;

    marcadoresLayer.clearLayers();

    marcadoresData.forEach(item => {
        const esLead = !item.estado || item.estado === 'Activo';
        const esConvertido = item.estado === 'Convertido';
        const esDescartado = item.estado === 'Descartado';

        const mostrar = (esLead && mostrarLeads) || 
                       (esConvertido && mostrarConvertidos) || 
                       (esDescartado && mostrarDescartados);

        if (mostrar) {
            // Aquí se agregarían los marcadores filtrados
            // (simplificado para el ejemplo)
        }
    });

    geocodificarYMostrar();
}

function toggleCapaCampanias() {
    const mostrar = document.getElementById('capaCampanias').checked;
    
    if (mostrar) {
        Swal.fire({
            icon: 'info',
            title: 'Campañas por Zona',
            html: generarHTMLCampanias(),
            width: 600
        });
    }
}

function toggleCapaEstadisticas() {
    const mostrar = document.getElementById('capaEstadisticas').checked;
    
    if (mostrar) {
        Swal.fire({
            icon: 'info',
            title: 'Estadísticas por Distrito',
            html: generarHTMLEstadisticas(),
            width: 700
        });
    }
}

function generarHTMLCampanias() {
    if (campaniasData.length === 0) {
        return '<p class="text-muted">No hay campañas activas con datos de ubicación</p>';
    }

    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr><th>Campaña</th><th>Distrito</th><th>Leads</th><th>Conversiones</th></tr></thead><tbody>';
    
    campaniasData.forEach(camp => {
        html += `<tr>
            <td><small><strong>${camp.campania}</strong></small></td>
            <td><small>${camp.distrito}</small></td>
            <td><small>${camp.leads_generados}</small></td>
            <td><small class="text-success">${camp.conversiones}</small></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    return html;
}

function generarHTMLEstadisticas() {
    if (estadisticasData.length === 0) {
        return '<p class="text-muted">No hay estadísticas disponibles</p>';
    }

    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr><th>Distrito</th><th>Total</th><th>Activos</th><th>Convertidos</th><th>Tasa Conv.</th></tr></thead><tbody>';
    
    estadisticasData.forEach(stat => {
        html += `<tr>
            <td><small><strong>${stat.distrito}</strong></small></td>
            <td><small>${stat.total_leads}</small></td>
            <td><small class="text-primary">${stat.activos}</small></td>
            <td><small class="text-success">${stat.convertidos}</small></td>
            <td><small>${stat.tasa_conversion}%</small></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    return html;
}

function recargarMapa() {
    document.getElementById('loadingOverlay').style.display = 'flex';
    marcadoresLayer.clearLayers();
    cargarDatos().then(() => {
        document.getElementById('loadingOverlay').style.display = 'none';
        Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: 'Datos del mapa actualizados',
            timer: 1500,
            showConfirmButton: false
        });
    });
}
</script>

<style>
/* Estilos personalizados para el mapa */
.leaflet-popup-content {
    margin: 10px;
}

.custom-marker {
    background: transparent;
    border: none;
}

/* Cluster personalizado */
.marker-cluster-small {
    background-color: rgba(0, 123, 255, 0.6);
}

.marker-cluster-small div {
    background-color: rgba(0, 123, 255, 0.8);
    color: white;
    font-weight: bold;
}

.marker-cluster-medium {
    background-color: rgba(253, 126, 20, 0.6);
}

.marker-cluster-medium div {
    background-color: rgba(253, 126, 20, 0.8);
    color: white;
    font-weight: bold;
}

.marker-cluster-large {
    background-color: rgba(220, 53, 69, 0.6);
}

.marker-cluster-large div {
    background-color: rgba(220, 53, 69, 0.8);
    color: white;
    font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
    .col-md-3 {
        position: absolute;
        z-index: 1000;
        width: 300px;
        height: 100%;
        left: -300px;
        transition: left 0.3s;
    }
    
    .col-md-3.show {
        left: 0;
    }
}
</style>
<?= $this->endSection() ?>
