/**
 * Campos Dinámicos según Origen del Lead
 * Muestra campos adicionales contextuales según el origen seleccionado
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando campos dinámicos de origen...');
    initCamposDinamicosOrigen();
});

function initCamposDinamicosOrigen() {
    const origenSelect = document.getElementById('idorigen');
    const camposDinamicos = document.getElementById('campos-dinamicos-origen');
    
    console.log('📋 Elementos encontrados:', {
        origenSelect: origenSelect,
        camposDinamicos: camposDinamicos
    });
    
    if (!origenSelect || !camposDinamicos) {
        console.error('❌ No se encontraron los elementos necesarios');
        return;
    }
    
    origenSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const nombreOrigen = selectedOption.getAttribute('data-nombre');
        
        console.log('🔄 Origen seleccionado:', nombreOrigen);
        
        // Limpiar campos anteriores
        camposDinamicos.innerHTML = '';
        
        if (!nombreOrigen) {
            console.log('⚠️ No hay origen seleccionado');
            return;
        }
        
        // Configuración de campos según origen
        const camposConfig = {
            'Referido': {
                html: `
                    <div class="form-group mt-3 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="referido_por">¿Quién lo refirió? *</label>
                        <input type="text" class="form-control" id="referido_por" name="referido_por" 
                               placeholder="Nombre del cliente que lo recomendó" required>
                        <small class="text-muted">Nombre de la persona que recomendó nuestro servicio</small>
                    </div>
                `
            },
            'Facebook': {
                html: `
                    <div class="form-group mt-3 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="detalle_facebook">Detalle de Facebook</label>
                        <select class="form-control" id="detalle_facebook" name="detalle_facebook">
                            <option value="">Seleccione</option>
                            <option value="Publicación orgánica">Publicación orgánica</option>
                            <option value="Anuncio pagado">Anuncio pagado</option>
                            <option value="Messenger">Messenger</option>
                            <option value="Comentario">Comentario en publicación</option>
                            <option value="Grupo">Grupo de Facebook</option>
                        </select>
                        <small class="text-muted">¿Cómo nos contactó por Facebook?</small>
                    </div>
                `
            },
            'WhatsApp': {
                html: `
                    <div class="form-group mt-3 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="origen_whatsapp">¿Cómo obtuvo nuestro WhatsApp?</label>
                        <select class="form-control" id="origen_whatsapp" name="origen_whatsapp">
                            <option value="">Seleccione</option>
                            <option value="Publicidad">Vio en publicidad</option>
                            <option value="Referido">Se lo pasó un conocido</option>
                            <option value="Redes sociales">Redes sociales</option>
                            <option value="Búsqueda web">Búsqueda en internet</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                `
            },
            'Publicidad': {
                html: `
                    <div class="form-group mt-3 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="tipo_publicidad">Tipo de Publicidad</label>
                        <select class="form-control" id="tipo_publicidad" name="tipo_publicidad">
                            <option value="">Seleccione</option>
                            <option value="Volante">Volante</option>
                            <option value="Banner">Banner/Letrero</option>
                            <option value="Perifoneo">Perifoneo</option>
                            <option value="Radio">Radio</option>
                            <option value="Periódico">Periódico</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group mt-2 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="ubicacion_publicidad">¿Dónde vio la publicidad?</label>
                        <input type="text" class="form-control" id="ubicacion_publicidad" name="ubicacion_publicidad" 
                               placeholder="Ej: Av. Benavides, Mercado Central">
                    </div>
                `
            },
            'Página Web': {
                html: `
                    <div class="form-group mt-3 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="accion_web">¿Qué hizo en la web?</label>
                        <select class="form-control" id="accion_web" name="accion_web">
                            <option value="">Seleccione</option>
                            <option value="Formulario contacto">Llenó formulario de contacto</option>
                            <option value="Chat">Usó el chat en vivo</option>
                            <option value="Llamó">Llamó al teléfono publicado</option>
                            <option value="WhatsApp web">Click en botón WhatsApp</option>
                        </select>
                    </div>
                `
            },
            'Llamada Directa': {
                html: `
                    <div class="form-group mt-3 campo-dinamico" style="animation: fadeIn 0.3s;">
                        <label for="origen_numero">¿Cómo obtuvo nuestro número?</label>
                        <select class="form-control" id="origen_numero" name="origen_numero">
                            <option value="">Seleccione</option>
                            <option value="Publicidad">Publicidad</option>
                            <option value="Referido">Referido</option>
                            <option value="Internet">Búsqueda en internet</option>
                            <option value="Cliente anterior">Es cliente anterior</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                `
            }
        };
        
        // Mostrar campos correspondientes
        if (camposConfig[nombreOrigen]) {
            console.log('✅ Mostrando campos para:', nombreOrigen);
            camposDinamicos.innerHTML = camposConfig[nombreOrigen].html;
        } else {
            console.log('⚠️ No hay configuración para:', nombreOrigen);
        }
    });
    
    console.log('✅ Event listener agregado correctamente');

    // Si ya hay un origen seleccionado al cargar la página, disparar el handler
    try {
        if (origenSelect.value && origenSelect.value !== '') {
            // Disparar change para mostrar campos iniciales
            origenSelect.dispatchEvent(new Event('change'));
        }
    } catch (err) {
        console.warn('No se pudo disparar evento inicial de origen:', err);
    }
}

// Agregar estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .campo-dinamico {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
