/**
 * JavaScript para el Pipeline Kanban de Leads
 * Funcionalidad de Drag & Drop para mover leads entre etapas
 */

class PipelineManager {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
        this.draggedElement = null;
        this.sourceEtapa = null;
        this.init();
    }

    init() {
        const leadCards = document.querySelectorAll('.lead-card');
        const pipelineBodies = document.querySelectorAll('.pipeline-body');
        
        console.log('Pipeline inicializado');
        console.log(`Tarjetas encontradas: ${leadCards.length}`);
        console.log(`Zonas de drop encontradas: ${pipelineBodies.length}`);
        
        if (leadCards.length === 0) {
            console.warn('⚠️ No se encontraron tarjetas con clase .lead-card');
        }
        
        this.initDragEvents(leadCards);
        this.initDropZones(pipelineBodies);
    }

    initDragEvents(leadCards) {
        leadCards.forEach((card, index) => {
            // Asegurar que el atributo draggable esté configurado
            card.setAttribute('draggable', 'true');
            card.style.cursor = 'grab';
            
            // Prevenir drag cuando se hace clic en botones
            const buttons = card.querySelectorAll('.btn, a, button');
            buttons.forEach(btn => {
                btn.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                });
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            });
            
            card.addEventListener('dragstart', (e) => {
                console.log(`Arrastrando tarjeta ${index + 1}`);
                card.style.cursor = 'grabbing';
                this.handleDragStart(e, card);
            });
            
            card.addEventListener('dragend', () => {
                console.log(`✅ Fin de arrastre tarjeta ${index + 1}`);
                card.style.cursor = 'grab';
                this.handleDragEnd(card);
            });
        });
        
        console.log(`✅ Eventos drag configurados en ${leadCards.length} tarjetas`);
    }

    handleDragStart(e, card) {
        this.draggedElement = card;
        this.sourceEtapa = card.closest('.pipeline-column').dataset.etapaId;
        card.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', card.innerHTML);
    }

    handleDragEnd(card) {
        card.classList.remove('dragging');
        this.draggedElement = null;
    }

    initDropZones(pipelineBodies) {
        pipelineBodies.forEach(body => {
            body.addEventListener('dragover', (e) => this.handleDragOver(e, body));
            body.addEventListener('dragleave', () => this.handleDragLeave(body));
            body.addEventListener('drop', (e) => this.handleDrop(e, body));
        });
    }

    handleDragOver(e, body) {
        e.preventDefault();
        
        if (!this.draggedElement) return;
        
        const targetEtapa = parseInt(body.closest('.pipeline-column').dataset.etapaId);
        const sourceEtapa = parseInt(this.sourceEtapa);
        
        // Cambiar cursor según si puede mover o no
        if (targetEtapa < sourceEtapa) {
            e.dataTransfer.dropEffect = 'none'; // No permitir retroceso
            body.classList.remove('drag-over');
            body.classList.add('drag-forbidden');
        } else {
            e.dataTransfer.dropEffect = 'move';
            body.classList.remove('drag-forbidden');
            body.classList.add('drag-over');
        }
    }

    handleDragLeave(body) {
        body.classList.remove('drag-over');
        body.classList.remove('drag-forbidden');
    }

    handleDrop(e, body) {
        e.preventDefault();
        body.classList.remove('drag-over');
        
        if (!this.draggedElement) return;
        
        const targetEtapa = parseInt(body.closest('.pipeline-column').dataset.etapaId);
        const sourceEtapa = parseInt(this.sourceEtapa);
        const targetNombre = body.closest('.pipeline-column').querySelector('h5').textContent.trim();
        const leadId = this.draggedElement.dataset.leadId;
        const leadNombre = this.draggedElement.querySelector('strong').textContent;
        
        // VALIDAR: Solo permitir avanzar, no retroceder
        if (targetEtapa < sourceEtapa) {
            Swal.fire({
                icon: 'warning',
                title: 'No se puede retroceder',
                text: 'Solo puedes mover leads hacia adelante en el proceso, no hacia atrás.',
                confirmButtonColor: '#f39c12',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        // Si es la misma etapa, no hacer nada
        if (targetEtapa === sourceEtapa) {
            return;
        }
        
        // Si mueve a DESCARTADO, pedir confirmación
        if (this.isDescartadoEtapa(targetNombre)) {
            this.confirmarDescarte(leadNombre, leadId, targetEtapa, body);
        } else {
            // Mover normalmente a otras etapas (solo hacia adelante)
            body.appendChild(this.draggedElement);
            this.actualizarEtapa(leadId, targetEtapa);
        }
    }

    isDescartadoEtapa(nombreEtapa) {
        const nombre = nombreEtapa.toUpperCase();
        return nombre.includes('DESCART') || nombre.includes('PERDID');
    }

    confirmarDescarte(leadNombre, leadId, targetEtapa, bodyElement) {
        Swal.fire({
            title: '¿Descartar este lead?',
            html: `¿Estás seguro de mover a <strong>${leadNombre}</strong> a <span class="text-danger">DESCARTADO</span>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="ti-trash"></i> Sí, descartar',
            cancelButtonText: '<i class="ti-close"></i> Cancelar',
            input: 'textarea',
            inputPlaceholder: 'Motivo del descarte (opcional)',
            inputAttributes: {
                maxlength: 200
            }
        }).then((result) => {
            if (result.isConfirmed) {
                bodyElement.appendChild(this.draggedElement);
                this.actualizarEtapa(leadId, targetEtapa, result.value);
            } else {
                this.mostrarNotificacion('Movimiento cancelado', 'info');
            }
        });
    }

    actualizarEtapa(leadId, nuevaEtapa, motivo = null) {
        const formData = new URLSearchParams();
        formData.append('idlead', leadId);
        formData.append('idetapa', nuevaEtapa);
        if (motivo) formData.append('motivo', motivo);
        
        // Agregar CSRF token si existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('csrf_token_name', csrfToken.getAttribute('content'));
        }

        fetch(`${this.baseUrl}/leads/moverEtapa`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.actualizarContadores();
                this.mostrarNotificacion('Lead movido exitosamente', 'success');
            } else {
                location.reload();
                this.mostrarNotificacion('Error al mover el lead', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }

    actualizarContadores() {
        document.querySelectorAll('.pipeline-column').forEach(column => {
            const body = column.querySelector('.pipeline-body');
            const leadCount = body.querySelectorAll('.lead-card').length;
            const badge = column.querySelector('.badge');
            
            if (badge) {
                badge.textContent = leadCount;
            }
            
            // Mostrar/ocultar empty state
            const emptyState = body.querySelector('.empty-state');
            if (leadCount === 0 && !emptyState) {
                body.innerHTML = '<div class="empty-state"><i class="ti-info-alt"></i><p>Sin leads</p></div>';
            } else if (leadCount > 0 && emptyState) {
                emptyState.remove();
            }
        });
    }

    mostrarNotificacion(mensaje, tipo) {
        const iconMap = {
            'success': 'success',
            'error': 'error',
            'info': 'info',
            'warning': 'warning'
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: iconMap[tipo] || 'info',
                title: mensaje,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            console.log(`${tipo.toUpperCase()}: ${mensaje}`);
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (typeof BASE_URL !== 'undefined') {
        window.pipelineManager = new PipelineManager(BASE_URL);
    } else {
        console.error('BASE_URL no está definida');
    }
});
