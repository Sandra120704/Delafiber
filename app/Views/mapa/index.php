<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Variable global para base URL -->
<script>
    window.baseUrl = '<?= base_url() ?>';
</script>

<div class="container-fluid p-0 mapa-container">
    <div class="row g-0 h-100">
        <!-- Panel Lateral de Controles -->
        <div class="col-md-3 mapa-panel-lateral">
            <div class="p-3">
                <h4 class="mb-3">Mapa</h4>
                
                <!-- Filtros de Capas -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Capas del Mapa</h6>
                    </div>
                    <div class="card-body mapa-controles-capa">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaLeads" checked>
                            <label class="form-check-label" for="capaLeads">
                                Leads Activos
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaConvertidos" checked>
                            <label class="form-check-label" for="capaConvertidos">
                                Clientes (Convertidos)
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaDescartados">
                            <label class="form-check-label" for="capaDescartados">
                                Descartados
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="capaCampanias">
                            <label class="form-check-label" for="capaCampanias">
                                 Campañas por Zona
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="capaEstadisticas">
                            <label class="form-check-label" for="capaEstadisticas">
                                Estadísticas por Distrito
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Leyenda de Colores -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"> Leyenda</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="mapa-leyenda-color" style="background: #007bff;"></div>
                            <small>Lead Activo</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="mapa-leyenda-color" style="background: #28a745;"></div>
                            <small>Cliente (Convertido)</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="mapa-leyenda-color" style="background: #dc3545;"></div>
                            <small>Descartado</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="mapa-leyenda-color" style="background: #fd7e14;"></div>
                            <small>Campaña Activa</small>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"> Estadísticas</h6>
                    </div>
                    <div class="card-body mapa-estadisticas">
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

                <!-- Botones de Control -->
                <div class="row g-2 mt-3">
                    <div class="col-8">
                        <button class="btn btn-primary w-100 mapa-btn-recargar" onclick="recargarMapa()">
                            <i class="ti-reload"></i> Recargar Datos
                        </button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-outline-info w-100" onclick="mostrarEstadisticasUso()" title="Ver uso de API">
                            <i class="ti-bar-chart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mapa Principal -->
        <div class="col-md-9 mapa-area">
            <div id="map"></div>
            
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="mapa-loading-overlay">
                <div class="mapa-loading-content">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <h6 class="mb-1">Cargando mapa...</h6>
                    <small class="text-muted">Procesando datos geográficos</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Dependencias externas -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIxTc7yNv1EdTv6wg6uIAZ55NITSkhqvU&callback=initGoogleMaps&libraries=geometry"></script>

<!-- Google Maps MarkerClusterer (opcional pero recomendado) -->
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Estilos del Mapa -->
<link rel="stylesheet" href="<?= base_url('css/mapa/mapa-styles.css') ?>">

<!-- Módulos JavaScript del Mapa (orden importante) -->
<script src="<?= base_url('js/mapa/mapa-config.js') ?>"></script>
<script src="<?= base_url('js/mapa/mapa-utils.js') ?>"></script>
<script src="<?= base_url('js/mapa/mapa-usage-controller.js') ?>"></script>
<script src="<?= base_url('js/mapa/mapa-data-service.js') ?>"></script>
<script src="<?= base_url('js/mapa/mapa-controller.js') ?>"></script>
<script src="<?= base_url('js/mapa/mapa-main.js') ?>"></script>

<!-- Callback para Google Maps -->
<script>
// Función global que Google Maps llamará cuando esté listo
function initGoogleMaps() {
    console.log('Google Maps API cargada');
    
    // Verificar que nuestros módulos estén listos
    if (typeof MapaController !== 'undefined') {
        // Disparar evento personalizado para que mapa-main.js sepa que Google Maps está listo
        document.dispatchEvent(new CustomEvent('googleMapsReady'));
    } else {
        console.warn(' Módulos del mapa aún no están cargados, reintentando...');
        setTimeout(initGoogleMaps, 100);
    }
}

// Modificar el inicializador para esperar a Google Maps
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM listo, esperando Google Maps API...');
    
    // Si Google Maps ya está cargado, inicializar inmediatamente
    if (window.google && window.google.maps) {
        initGoogleMaps();
    }
    // Si no, el callback initGoogleMaps se ejecutará automáticamente
});
</script>
<?= $this->endSection() ?>
