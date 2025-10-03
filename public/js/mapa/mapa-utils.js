/**
 * Utilidades del Mapa Google Maps
 */

class MapaUtils {
    
    /**
     * Crear icono personalizado para marcadores Google Maps
     */
    static crearIconoPersonalizado(color) {
        return {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: color,
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 3,
            scale: 12,
            anchor: new google.maps.Point(0, 0)
        };
    }
    
    /**
     * Crear icono SVG personalizado (alternativa más visual)
     */
    static crearIconoSVG(color) {
        const svg = `
            <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="16" r="12" fill="${color}" stroke="#ffffff" stroke-width="3"/>
            </svg>
        `;
        
        return {
            url: 'data:image/svg+xml;base64,' + btoa(svg),
            scaledSize: new google.maps.Size(32, 32),
            anchor: new google.maps.Point(16, 16)
        };
    }

    /**
     * Crear HTML del popup para un marcador
     */
    static crearPopupHTML(item) {
        const estadoBadge = this.getEstadoBadge(item.estado);

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
                    <a href="${window.baseUrl}leads/ver/${item.id}" class="btn btn-sm btn-primary" target="_blank">
                        <i class="ti-eye"></i> Ver Lead
                    </a>
                    <a href="https://wa.me/51${item.telefono}" class="btn btn-sm btn-success" target="_blank">
                        <i class="ti-mobile"></i> WhatsApp
                    </a>
                </div>
            </div>
        `;
    }

    /**
     * Obtener badge HTML según el estado
     */
    static getEstadoBadge(estado) {
        switch (estado) {
            case 'Convertido':
                return '<span class="badge bg-success">Cliente</span>';
            case 'Descartado':
                return '<span class="badge bg-danger">Descartado</span>';
            default:
                return '<span class="badge bg-primary">Lead Activo</span>';
        }
    }

    /**
     * Generar coordenadas con variación aleatoria (formato Google Maps)
     */
    static generarCoordenadasConVariacion(coordenadas) {
        const variation = MapaConfig.randomVariation;
        return {
            lat: coordenadas.lat + (Math.random() - 0.5) * variation,
            lng: coordenadas.lng + (Math.random() - 0.5) * variation
        };
    }
    
    /**
     * Crear InfoWindow personalizada para Google Maps
     */
    static crearInfoWindow(contenido) {
        return new google.maps.InfoWindow({
            content: contenido,
            maxWidth: 300,
            pixelOffset: new google.maps.Size(0, -10)
        });
    }

    /**
     * Generar HTML para mostrar campañas
     */
    static generarHTMLCampanias(campanias) {
        if (campanias.length === 0) {
            return '<p class="text-muted">No hay campañas activas con datos de ubicación</p>';
        }

        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Campaña</th><th>Distrito</th><th>Leads</th><th>Conversiones</th></tr></thead><tbody>';
        
        campanias.forEach(camp => {
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

    /**
     * Generar HTML para mostrar estadísticas
     */
    static generarHTMLEstadisticas(estadisticas) {
        if (estadisticas.length === 0) {
            return '<p class="text-muted">No hay estadísticas disponibles</p>';
        }

        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Distrito</th><th>Total</th><th>Activos</th><th>Convertidos</th><th>Tasa Conv.</th></tr></thead><tbody>';
        
        estadisticas.forEach(stat => {
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

    /**
     * Actualizar contadores en el panel lateral
     */
    static actualizarContadores(countLeads, countConvertidos, countDescartados) {
        const total = countLeads + countConvertidos + countDescartados;
        
        document.getElementById('totalMarcadores').textContent = total;
        document.getElementById('countLeads').textContent = countLeads;
        document.getElementById('countConvertidos').textContent = countConvertidos;
        document.getElementById('countDescartados').textContent = countDescartados;
    }

    /**
     * Mostrar overlay de carga
     */
    static mostrarCarga() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    /**
     * Ocultar overlay de carga
     */
    static ocultarCarga() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    /**
     * Mostrar notificación de éxito
     */
    static mostrarExito(titulo, mensaje, tiempo = 1500) {
        Swal.fire({
            icon: 'success',
            title: titulo,
            text: mensaje,
            timer: tiempo,
            showConfirmButton: false
        });
    }

    /**
     * Mostrar información sin datos
     */
    static mostrarSinDatos() {
        Swal.fire({
            icon: 'info',
            title: 'Sin datos',
            text: 'No hay leads con direcciones registradas para mostrar en el mapa',
            timer: 3000
        });
    }
}

// Exportar utilidades globalmente
window.MapaUtils = MapaUtils;