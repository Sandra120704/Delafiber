/**
 * JavaScript para Vista de Lead
 */

const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
const leadId = document.querySelector('[data-lead-id]')?.dataset.leadId;

// Inicializar mapa si hay coordenadas
const coordenadas = document.querySelector('[data-coordenadas]')?.dataset.coordenadas;
if (coordenadas) {
    document.addEventListener('DOMContentLoaded', function() {
        initMiniMap();
    });
}

function initMiniMap() {
    const coords = coordenadas.split(',');
    const lat = parseFloat(coords[0]);
    const lng = parseFloat(coords[1]);
    
    // Crear mapa
    const map = new google.maps.Map(document.getElementById('miniMapLead'), {
        zoom: 16,
        center: { lat, lng },
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true,
        zoomControl: true
    });
    
    // Datos del lead
    const leadNombre = document.querySelector('[data-lead-nombre]')?.dataset.leadNombre || '';
    const leadTelefono = document.querySelector('[data-lead-telefono]')?.dataset.leadTelefono || '';
    const leadDireccion = document.querySelector('[data-lead-direccion]')?.dataset.leadDireccion || 'Sin dirección';
    
    // Marker del lead
    const marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        title: leadNombre,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#e74c3c',
            fillOpacity: 1,
            strokeColor: '#c0392b',
            strokeWeight: 2
        },
        animation: google.maps.Animation.DROP
    });
    
    // InfoWindow del marker
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px;">
                <h6 style="margin: 0 0 8px 0;">${leadNombre}</h6>
                <p style="margin: 0; font-size: 13px;">
                    <i class="icon-phone"></i> ${leadTelefono}<br>
                    <i class="icon-map-pin"></i> ${leadDireccion}
                </p>
            </div>
        `
    });
    
    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });
    
    // Dibujar zona si está asignada
    const zonaPoligono = document.querySelector('[data-zona-poligono]')?.dataset.zonaPoligono;
    const zonaColor = document.querySelector('[data-zona-color]')?.dataset.zonaColor;
    const zonaNombre = document.querySelector('[data-zona-nombre]')?.dataset.zonaNombre;
    
    if (zonaPoligono) {
        try {
            const zonaCoords = JSON.parse(zonaPoligono);
            const zonaPolygon = new google.maps.Polygon({
                paths: zonaCoords,
                strokeColor: zonaColor,
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: zonaColor,
                fillOpacity: 0.2,
                map: map
            });
            
            // Ajustar zoom para mostrar zona completa
            const bounds = new google.maps.LatLngBounds();
            zonaCoords.forEach(coord => {
                bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
            });
            map.fitBounds(bounds);
            
            // Agregar label de zona
            const zonaCentro = bounds.getCenter();
            new google.maps.Marker({
                position: zonaCentro,
                map: map,
                label: {
                    text: zonaNombre,
                    color: '#fff',
                    fontSize: '14px',
                    fontWeight: 'bold'
                },
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 0
                }
            });
        } catch (error) {
            console.error('Error al dibujar zona:', error);
        }
    }
}

// Función para geocodificar lead sin coordenadas
function geocodificarLeadAhora() {
    alert('Funcionalidad de geocodificación manual próximamente. Por ahora, edita el lead y agrega una dirección.');
}

// Cambiar etapa
const formCambiarEtapa = document.getElementById('formCambiarEtapa');
if (formCambiarEtapa) {
    formCambiarEtapa.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('idlead', leadId);
        
        fetch(`${baseUrl}/leads/moverEtapa`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al mover etapa');
            }
        });
    });
}

// Agregar seguimiento
const formSeguimiento = document.getElementById('formSeguimiento');
if (formSeguimiento) {
    formSeguimiento.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(`${baseUrl}/leads/agregarSeguimiento`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error');
            }
        });
    });
}

// Crear tarea
const formTarea = document.getElementById('formTarea');
if (formTarea) {
    formTarea.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(`${baseUrl}/leads/crearTarea`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error');
            }
        });
    });
}

// Completar tarea
function completarTarea(id) {
    if (confirm('¿Marcar como completada?')) {
        const formData = new FormData();
        formData.append('idtarea', id);
        
        fetch(`${baseUrl}/leads/completarTarea`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
}
