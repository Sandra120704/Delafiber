<?= $this->extend('Layouts/base') ?>
<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="ti-layout-grid2"></i> Pipeline de Ventas</h3>
            <div>
                <a href="<?= base_url('leads') ?>" class="btn btn-outline-secondary">
                    <i class="ti-list"></i> Vista Lista
                </a>
                <a href="<?= base_url('leads/create') ?>" class="btn btn-primary">
                    <i class="ti-plus"></i> Nuevo Lead
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Kanban -->
<div class="pipeline-container">
    <div class="pipeline-scroll">
        <?php if (!empty($pipeline)): ?>
            <?php foreach ($pipeline as $etapa): ?>
                <div class="pipeline-column" data-etapa-id="<?= $etapa['etapa_id'] ?>">
                    <div class="pipeline-header">
                        <h5 class="mb-0"><?= esc($etapa['etapa_nombre']) ?></h5>
                        <span class="badge badge-light"><?= $etapa['total_leads'] ?></span>
                    </div>
                    
                    <div class="pipeline-body" id="etapa-<?= $etapa['etapa_id'] ?>">
                        <?php if (!empty($etapa['leads'])): ?>
                            <?php foreach ($etapa['leads'] as $lead): ?>
                                <div class="lead-card" data-lead-id="<?= $lead['idlead'] ?>" draggable="true">
                                    <div class="lead-card-header">
                                        <strong><?= esc($lead['nombres']) ?> <?= esc($lead['apellidos']) ?></strong>
                                    </div>
                                    <div class="lead-card-body">
                                        <!-- Teléfono -->
                                        <div class="lead-info">
                                            <i class="ti-mobile text-success"></i>
                                            <span><?= esc($lead['telefono']) ?></span>
                                        </div>
                                    </div>
                                    <div class="lead-card-actions">
                                        <a href="<?= base_url('leads/view/' . $lead['idlead']) ?>" 
                                           class="btn btn-sm btn-light" title="Ver detalles">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <a href="https://wa.me/51<?= esc($lead['telefono']) ?>?text=Hola%20<?= urlencode($lead['nombres']) ?>,%20te%20contacto%20desde%20Delafiber" 
                                           target="_blank" class="btn btn-sm btn-success" title="WhatsApp">
                                            <i class="ti-comment"></i>
                                        </a>
                                        <a href="tel:<?= esc($lead['telefono']) ?>" 
                                           class="btn btn-sm btn-info" title="Llamar">
                                            <i class="ti-headphone-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="ti-info-alt"></i>
                                <p>Sin leads</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="ti-info-alt" style="font-size: 48px; opacity: 0.3;"></i>
                <p class="text-muted mt-3">No hay datos de pipeline disponibles</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Pipeline Container */
.pipeline-container {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.pipeline-scroll {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding-bottom: 20px;
}

/* Pipeline Column */
.pipeline-column {
    min-width: 320px;
    max-width: 320px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.pipeline-header {
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pipeline-header h5 {
    color: white;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pipeline-header .badge {
    background: rgba(255,255,255,0.3);
    color: white;
    font-weight: bold;
}

.pipeline-body {
    padding: 15px;
    min-height: 400px;
    max-height: 70vh;
    overflow-y: auto;
    flex: 1;
}

/* Lead Card */
.lead-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 12px;
    cursor: move;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.lead-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.lead-card.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.lead-card-header {
    margin-bottom: 8px;
}

.lead-card-header strong {
    font-size: 14px;
    color: #333;
    display: block;
}

.lead-card-body {
    margin-bottom: 10px;
}

.lead-info {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #666;
}

.lead-info i {
    font-size: 14px;
}

.lead-card-actions {
    display: flex;
    gap: 5px;
    justify-content: flex-end;
}

.lead-card-actions .btn {
    padding: 4px 8px;
    font-size: 12px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state i {
    font-size: 48px;
    opacity: 0.3;
    display: block;
    margin-bottom: 10px;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Drag Over Effect */
.pipeline-body.drag-over {
    background: #f0f7ff;
    border: 2px dashed #667eea;
}

/* Responsive */
@media (max-width: 768px) {
    .pipeline-column {
        min-width: 280px;
        max-width: 280px;
    }
}

/* Scrollbar */
.pipeline-scroll::-webkit-scrollbar {
    height: 8px;
}

.pipeline-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.pipeline-scroll::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.pipeline-scroll::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Drag and Drop Functionality
let draggedElement = null;
let sourceEtapa = null;

document.addEventListener('DOMContentLoaded', function() {
    const leadCards = document.querySelectorAll('.lead-card');
    const pipelineBodies = document.querySelectorAll('.pipeline-body');
    
    // Drag Start
    leadCards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedElement = this;
            sourceEtapa = this.closest('.pipeline-column').dataset.etapaId;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        });
        
        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedElement = null;
        });
    });
    
    // Drag Over
    pipelineBodies.forEach(body => {
        body.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });
        
        body.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        // Drop
        body.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedElement) {
                const targetEtapa = this.closest('.pipeline-column').dataset.etapaId;
                const targetNombre = this.closest('.pipeline-column').querySelector('h5').textContent.trim();
                const leadId = draggedElement.dataset.leadId;
                const leadNombre = draggedElement.querySelector('strong').textContent;
                const bodyElement = this;
                
                // Si mueve a DESCARTADO, pedir confirmación
                if (targetNombre.toUpperCase().includes('DESCART') || targetNombre.toUpperCase().includes('PERDID')) {
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
                            // Usuario confirmó, mover el lead
                            bodyElement.appendChild(draggedElement);
                            actualizarEtapa(leadId, targetEtapa, sourceEtapa, result.value);
                        } else {
                            // Usuario canceló, no hacer nada
                            showToast('info', 'Movimiento cancelado');
                        }
                    });
                } else {
                    // Mover normalmente a otras etapas
                    this.appendChild(draggedElement);
                    actualizarEtapa(leadId, targetEtapa, sourceEtapa);
                }
            }
        });
    });
});

// Actualizar etapa via AJAX
function actualizarEtapa(leadId, nuevaEtapa, etapaAnterior) {
    fetch('<?= base_url('leads/moverEtapa') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `idlead=${leadId}&idetapa=${nuevaEtapa}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar contadores
            actualizarContadores();
            
            // Mostrar notificación
            mostrarNotificacion('Lead movido exitosamente', 'success');
        } else {
            // Revertir cambio
            location.reload();
            mostrarNotificacion('Error al mover el lead', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        location.reload();
    });
}

// Actualizar contadores de leads por etapa
function actualizarContadores() {
    document.querySelectorAll('.pipeline-column').forEach(column => {
        const etapaId = column.dataset.etapaId;
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

// Notificación bonita con SweetAlert2
function mostrarNotificacion(mensaje, tipo) {
    if (tipo === 'success') {
        showToast('success', mensaje);
    } else {
        showToast('error', mensaje);
    }
}
</script>

<?= $this->endSection() ?>
<?= $this->include('Layouts/footer') ?>
