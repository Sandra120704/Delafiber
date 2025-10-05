/**
 * ============================================
 * MAPA DE CAMPAÑAS CRM CON TURF.JS
 * ============================================
 * Gestión de zonas geográficas para campañas de marketing
 * Utiliza Google Maps API + Turf.js para análisis espacial
 */

import * as turf from 'https://cdn.jsdelivr.net/npm/@turf/turf@7/+esm';

// ============================================
// VARIABLES GLOBALES
// ============================================
let mapa;
let zonasPoligonos = [];  // Polígonos dibujados en el mapa
let zonasData = [];       // Datos de zonas desde BD
let prospectosMarkers = [];
let drawingManager;       // Para dibujar zonas
let zonaActual = null;
let infoWindow;

// ============================================
// 1. INICIALIZAR MAPA
// ============================================
export async function inicializarMapaCampanas(idMapa = 'mapCampanas', idCampana = null) {
    try {
        // Verificar que Google Maps esté cargado
        if (typeof google === 'undefined' || !google.maps) {
            throw new Error('Google Maps API no está cargada');
        }
        
        // Crear mapa centrado en Chincha, Ica
        mapa = new google.maps.Map(document.getElementById(idMapa), {
            zoom: 14,
            center: { lat: -13.409347, lng: -76.131756 }, // Chincha Alta, Ica
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true
        });
        infoWindow = new google.maps.InfoWindow();
        
        // Habilitar herramienta de dibujo
        inicializarDrawingManager();
        
        // Cargar zonas existentes si hay campaña
        if (idCampana && idCampana !== '' && idCampana !== null) {
            try {
                await cargarZonasCampana(idCampana);
            } catch (error) {
                console.warn('⚠️ No se pudieron cargar zonas:', error.message);
            }
        } else {
            console.log('ℹ️ No hay campaña seleccionada, mapa listo para dibujar zonas');
        }
        
        console.log('✅ Mapa CRM inicializado correctamente');
        return mapa;
        
    } catch (error) {
        console.error('Error al inicializar mapa:', error);
        throw error;
    }
}

// ============================================
// 2. INICIALIZAR DRAWING MANAGER
// ============================================
function inicializarDrawingManager() {
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: null,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                google.maps.drawing.OverlayType.POLYGON
            ]
        },
        polygonOptions: {
            fillColor: '#3498db',
            fillOpacity: 0.3,
            strokeWeight: 2,
            strokeColor: '#2980b9',
            editable: true,
            draggable: true
        }
    });
    
    drawingManager.setMap(mapa);
    
    // Evento cuando se completa el dibujo
    google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
        manejarNuevaZona(polygon);
    });
}

// ============================================
// 3. CREAR NUEVA ZONA DE CAMPAÑA
// ============================================
async function manejarNuevaZona(polygon) {
    const coordenadas = polygon.getPath().getArray().map(latLng => ({
        lat: latLng.lat(),
        lng: latLng.lng()
    }));
    
    // Calcular área con Turf.js
    const turfPolygon = turf.polygon([[
        ...coordenadas.map(c => [c.lng, c.lat]),
        [coordenadas[0].lng, coordenadas[0].lat]  // Cerrar polígono
    ]]);
    
    const areaM2 = turf.area(turfPolygon);
    const areaKm2 = (areaM2 / 1000000).toFixed(2);
    
    // Calcular centro del polígono
    const centroid = turf.centroid(turfPolygon);
    const [centerLng, centerLat] = centroid.geometry.coordinates;
    
    // Mostrar modal para guardar
    mostrarModalNuevaZona({
        coordenadas: coordenadas,
        area_m2: areaM2,
        area_km2: areaKm2,
        center: { lat: centerLat, lng: centerLng },
        polygon: polygon
    });
}

// ============================================
// 4. ASIGNAR PROSPECTOS A ZONAS AUTOMÁTICAMENTE
// ============================================
export async function asignarProspectosAZonas(idCampana) {
    try {
        // Obtener todos los prospectos sin zona asignada
        const response = await fetch(`/crm-campanas/prospectos-sin-zona`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message);
        }
        
        const prospectos = result.data;
        
        // Obtener zonas de la campaña
        const zonasResponse = await fetch(`/crm-campanas/api-zonas-mapa/${idCampana}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const zonasResult = await zonasResponse.json();
        
        if (!zonasResult.success) {
            throw new Error(zonasResult.message);
        }
        
        const zonas = zonasResult.data;
        
        // Convertir zonas a formato Turf
        const zonasGeojson = zonas.map(zona => ({
            id_zona: zona.id_zona,
            nombre: zona.nombre_zona,
            polygon: turf.polygon([[
                ...zona.poligono.map(c => [c.lng, c.lat]),
                [zona.poligono[0].lng, zona.poligono[0].lat]
            ]])
        }));
        
        let asignados = 0;
        const resultados = [];
        
        // Iterar prospectos y asignar a zona
        for (const prospecto of prospectos) {
            if (!prospecto.coordenadas) continue;
            
            const [lat, lng] = prospecto.coordenadas.split(',').map(parseFloat);
            const punto = turf.point([lng, lat]);
            
            // Buscar en qué zona cae el prospecto
            for (const zona of zonasGeojson) {
                if (turf.booleanPointInPolygon(punto, zona.polygon)) {
                    // Actualizar en BD
                    const updateResponse = await fetch('/crm-campanas/asignar-prospecto-zona', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ 
                            id_prospecto: prospecto.idpersona,
                            id_zona: zona.id_zona 
                        })
                    });
                    
                    const updateResult = await updateResponse.json();
                    
                    if (updateResult.success) {
                        asignados++;
                        resultados.push({
                            prospecto: `${prospecto.nombres} ${prospecto.apellidos}`,
                            zona: zona.nombre
                        });
                    }
                    break;
                }
            }
        }
        
        return { 
            total: prospectos.length, 
            asignados,
            resultados 
        };
        
    } catch (error) {
        console.error('Error al asignar prospectos:', error);
        throw error;
    }
}

// ============================================
// 5. VALIDAR SI UN PUNTO ESTÁ EN COBERTURA
// ============================================
export function validarPuntoEnZona(lat, lng, idZona) {
    const zona = zonasData.find(z => z.id_zona === idZona);
    if (!zona) return false;
    
    const punto = turf.point([lng, lat]);
    const polygon = turf.polygon([[
        ...zona.poligono.map(c => [c.lng, c.lat]),
        [zona.poligono[0].lng, zona.poligono[0].lat]
    ]]);
    
    return turf.booleanPointInPolygon(punto, polygon);
}

// ============================================
// 6. EXPANDIR ZONA DE CAMPAÑA
// ============================================
export function expandirZona(idZona, metrosBuffer) {
    const zona = zonasData.find(z => z.id_zona === idZona);
    if (!zona) return null;
    
    const polygon = turf.polygon([[
        ...zona.poligono.map(c => [c.lng, c.lat]),
        [zona.poligono[0].lng, zona.poligono[0].lat]
    ]]);
    
    // Expandir con buffer
    const buffered = turf.buffer(polygon, metrosBuffer / 1000, { units: 'kilometers' });
    
    // Convertir de vuelta a coordenadas
    const nuevasCoordenadas = buffered.geometry.coordinates[0].map(coord => ({
        lat: coord[1],
        lng: coord[0]
    }));
    
    return {
        coordenadas: nuevasCoordenadas,
        area_m2: turf.area(buffered),
        area_km2: (turf.area(buffered) / 1000000).toFixed(2)
    };
}

// ============================================
// 7. DETECTAR SOLAPAMIENTO ENTRE ZONAS
// ============================================
export function detectarSolapamientoZonas(idZona1, idZona2) {
    const zona1 = zonasData.find(z => z.id_zona === idZona1);
    const zona2 = zonasData.find(z => z.id_zona === idZona2);
    
    if (!zona1 || !zona2) return null;
    
    const poly1 = turf.polygon([[
        ...zona1.poligono.map(c => [c.lng, c.lat]),
        [zona1.poligono[0].lng, zona1.poligono[0].lat]
    ]]);
    
    const poly2 = turf.polygon([[
        ...zona2.poligono.map(c => [c.lng, c.lat]),
        [zona2.poligono[0].lng, zona2.poligono[0].lat]
    ]]);
    
    // Calcular intersección
    const interseccion = turf.intersect(turf.featureCollection([poly1, poly2]));
    
    if (interseccion) {
        const areaSolapamiento = turf.area(interseccion);
        return {
            existe: true,
            area_m2: areaSolapamiento,
            area_km2: (areaSolapamiento / 1000000).toFixed(2),
            geometria: interseccion,
            porcentaje_zona1: ((areaSolapamiento / turf.area(poly1)) * 100).toFixed(2),
            porcentaje_zona2: ((areaSolapamiento / turf.area(poly2)) * 100).toFixed(2)
        };
    }
    
    return { existe: false };
}

// ============================================
// 8. OBTENER PROSPECTOS DENTRO DE UNA ZONA
// ============================================
export async function obtenerProspectosPorZona(idZona) {
    try {
        const response = await fetch(`/crm-campanas/api-prospectos-zona/${idZona}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message);
        }
        
        const prospectos = result.data;
        
        // Crear FeatureCollection para análisis
        const puntosProspectos = turf.featureCollection(
            prospectos
                .filter(p => p.coordenadas)
                .map(p => {
                    const [lat, lng] = p.coordenadas.split(',').map(parseFloat);
                    return turf.point([lng, lat], {
                        id_prospecto: p.idpersona,
                        nombre: `${p.nombres} ${p.apellidos}`,
                        telefono: p.telefono
                    });
                })
        );
        
        return puntosProspectos;
        
    } catch (error) {
        console.error('Error al obtener prospectos:', error);
        throw error;
    }
}

// ============================================
// 9. CALCULAR DENSIDAD DE PROSPECTOS
// ============================================
export function calcularDensidadProspectos(idZona, totalProspectos) {
    const zona = zonasData.find(z => z.id_zona === idZona);
    if (!zona) return 0;
    
    const areaKm2 = zona.area_m2 / 1000000;
    const densidad = totalProspectos / areaKm2;
    
    return {
        densidad_por_km2: densidad.toFixed(2),
        clasificacion: densidad > 100 ? 'Alta' : densidad > 50 ? 'Media' : 'Baja',
        color: densidad > 100 ? '#e74c3c' : densidad > 50 ? '#f39c12' : '#27ae60'
    };
}

// ============================================
// 10. CARGAR ZONAS DESDE BD
// ============================================
async function cargarZonasCampana(idCampana = null) {
    try {
        const url = idCampana 
            ? `/crm-campanas/api-zonas-mapa/${idCampana}`
            : `/crm-campanas/api-zonas-mapa`;
            
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message);
        }
        
        zonasData = result.data;
        
        // Limpiar polígonos anteriores
        zonasPoligonos.forEach(item => item.polygon.setMap(null));
        zonasPoligonos = [];
        
        // Renderizar en mapa
        zonasData.forEach(zona => {
            renderizarZonaEnMapa(zona);
        });
        
        console.log(`${zonasData.length} zonas cargadas`);
        
    } catch (error) {
        console.error('Error al cargar zonas:', error);
        throw error;
    }
}

// ============================================
// 11. RENDERIZAR ZONA EN MAPA
// ============================================
function renderizarZonaEnMapa(zona) {
    const polygon = new google.maps.Polygon({
        paths: zona.poligono,
        strokeColor: zona.color || '#3498db',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: zona.color || '#3498db',
        fillOpacity: 0.3,
        map: mapa,
        editable: false,
        draggable: false
    });
    
    // Agregar evento click
    polygon.addListener('click', (event) => {
        mostrarInfoZona(zona, event.latLng);
    });
    
    // Agregar evento hover
    polygon.addListener('mouseover', () => {
        polygon.setOptions({
            fillOpacity: 0.5,
            strokeWeight: 3
        });
    });
    
    polygon.addListener('mouseout', () => {
        polygon.setOptions({
            fillOpacity: 0.3,
            strokeWeight: 2
        });
    });
    
    zonasPoligonos.push({ 
        id_zona: zona.id_zona, 
        polygon,
        data: zona
    });
}

// ============================================
// 12. MOSTRAR INFO DE ZONA
// ============================================
function mostrarInfoZona(zona, position) {
    const content = `
        <div class="info-zona" style="max-width: 300px;">
            <h5 style="margin: 0 0 10px 0; color: ${zona.color};">
                📍 ${zona.nombre_zona}
            </h5>
            <p style="margin: 5px 0; font-size: 13px;">
                <strong>Prioridad:</strong> 
                <span class="badge badge-${zona.prioridad === 'Alta' ? 'danger' : zona.prioridad === 'Media' ? 'warning' : 'info'}">
                    ${zona.prioridad}
                </span>
            </p>
            <p style="margin: 5px 0; font-size: 13px;">
                <strong>Área:</strong> ${(zona.area_m2 / 1000000).toFixed(2)} km²
            </p>
            <p style="margin: 5px 0; font-size: 13px;">
                <strong>Prospectos:</strong> ${zona.total_prospectos || 0}
            </p>
            <p style="margin: 5px 0; font-size: 13px;">
                <strong>Agentes:</strong> ${zona.agentes_asignados || 0}
            </p>
            <div style="margin-top: 10px;">
                <a href="/crm-campanas/zona-detalle/${zona.id_zona}" 
                   class="btn btn-sm btn-primary" 
                   style="font-size: 12px;">
                    Ver Detalle
                </a>
            </div>
        </div>
    `;
    
    infoWindow.setContent(content);
    infoWindow.setPosition(position);
    infoWindow.open(mapa);
}

// ============================================
// 13. GUARDAR ZONA EN BD
// ============================================
export async function guardarZonaCampana(datos) {
    try {
        const response = await fetch('/crm-campanas/guardar-zona', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(datos)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message);
        }
        
        console.log('Zona guardada:', result);
        return result;
        
    } catch (error) {
        console.error('Error al guardar zona:', error);
        throw error;
    }
}

// ============================================
// 14. MOSTRAR MODAL NUEVA ZONA
// ============================================
function mostrarModalNuevaZona(datos) {
    // Desactivar modo de dibujo
    drawingManager.setDrawingMode(null);
    
    // Crear modal dinámicamente
    const modalHTML = `
        <div class="modal fade" id="modalNuevaZona" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Zona de Campaña</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevaZona">
                            <div class="form-group">
                                <label>Nombre de la Zona *</label>
                                <input type="text" class="form-control" id="nombre_zona" required>
                            </div>
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea class="form-control" id="descripcion" rows="3"></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Prioridad</label>
                                    <select class="form-control" id="prioridad">
                                        <option value="Media" selected>Media</option>
                                        <option value="Alta">Alta</option>
                                        <option value="Baja">Baja</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Color</label>
                                    <input type="color" class="form-control" id="color" value="#3498db">
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <strong>Área calculada:</strong> ${datos.area_km2} km² (${datos.area_m2.toFixed(0)} m²)
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarZona">Guardar Zona</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    $('#modalNuevaZona').remove();
    
    // Agregar al DOM
    $('body').append(modalHTML);
    
    // Mostrar modal
    $('#modalNuevaZona').modal('show');
    
    // Manejar guardado
    $('#btnGuardarZona').on('click', async function() {
        const idCampana = $('#id_campana_select').val();
        
        if (!idCampana) {
            alert('Debe seleccionar una campaña');
            return;
        }
        
        const datosZona = {
            id_campana: idCampana,
            nombre_zona: $('#nombre_zona').val(),
            descripcion: $('#descripcion').val(),
            coordenadas: datos.coordenadas,
            color: $('#color').val(),
            prioridad: $('#prioridad').val(),
            area_m2: datos.area_m2
        };
        
        try {
            const result = await guardarZonaCampana(datosZona);
            
            $('#modalNuevaZona').modal('hide');
            
            // Actualizar color del polígono
            datos.polygon.setOptions({
                fillColor: datosZona.color,
                strokeColor: datosZona.color,
                editable: false,
                draggable: false
            });
            
            // Recargar zonas
            await cargarZonasCampana(idCampana);
            
            alert('Zona creada exitosamente');
            
        } catch (error) {
            alert('Error al guardar zona: ' + error.message);
        }
    });
    
    // Limpiar al cerrar
    $('#modalNuevaZona').on('hidden.bs.modal', function() {
        if (!datos.polygon.get('id_zona')) {
            datos.polygon.setMap(null);
        }
    });
}

// ============================================
// 15. CARGAR PROSPECTOS EN MAPA
// ============================================
export async function cargarProspectosEnMapa(idZona = null) {
    try {
        // Limpiar markers anteriores
        prospectosMarkers.forEach(marker => marker.setMap(null));
        prospectosMarkers = [];
        
        let prospectos;
        
        if (idZona) {
            const result = await obtenerProspectosPorZona(idZona);
            prospectos = result.features.map(f => ({
                ...f.properties,
                lat: f.geometry.coordinates[1],
                lng: f.geometry.coordinates[0]
            }));
        } else {
            // Cargar todos los prospectos con coordenadas
            const response = await fetch('/crm-campanas/prospectos-sin-zona', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            prospectos = result.data.map(p => {
                const [lat, lng] = p.coordenadas.split(',').map(parseFloat);
                return { ...p, lat, lng };
            });
        }
        
        // Crear markers
        prospectos.forEach(prospecto => {
            const marker = new google.maps.Marker({
                position: { lat: prospecto.lat, lng: prospecto.lng },
                map: mapa,
                title: prospecto.nombre || `${prospecto.nombres} ${prospecto.apellidos}`,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: '#e74c3c',
                    fillOpacity: 0.8,
                    strokeColor: '#c0392b',
                    strokeWeight: 2
                }
            });
            
            marker.addListener('click', () => {
                const content = `
                    <div style="max-width: 250px;">
                        <h6>${prospecto.nombre || `${prospecto.nombres} ${prospecto.apellidos}`}</h6>
                        <p style="margin: 5px 0; font-size: 13px;">
                            ${prospecto.telefono || 'Sin teléfono'}
                        </p>
                        <p style="margin: 5px 0; font-size: 13px;">
                            ${prospecto.correo || 'Sin email'}
                        </p>
                    </div>
                `;
                infoWindow.setContent(content);
                infoWindow.open(mapa, marker);
            });
            
            prospectosMarkers.push(marker);
        });
        
        console.log(`${prospectos.length} prospectos cargados en mapa`);
        
    } catch (error) {
        console.error('Error al cargar prospectos:', error);
    }
}

// ============================================
// EXPORTAR FUNCIONES PÚBLICAS
// ============================================
window.MapaCampanas = {
    inicializar: inicializarMapaCampanas,
    asignarProspectos: asignarProspectosAZonas,
    validarPunto: validarPuntoEnZona,
    expandirZona: expandirZona,
    detectarSolapamiento: detectarSolapamientoZonas,
    calcularDensidad: calcularDensidadProspectos,
    cargarProspectos: cargarProspectosEnMapa,
    guardarZona: guardarZonaCampana
};

console.log('Módulo MapaCampanas.js cargado');
