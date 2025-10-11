<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<?php
// Inicializa variables para evitar error 500
$pendientes = $pendientes ?? [];
$hoy = $hoy ?? [];
$vencidas = $vencidas ?? [];
$completadas = $completadas ?? [];
$leads = $leads ?? [];
$error = $error ?? null;
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?= esc($error) ?>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Header con estadísticas -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Pendientes</h6>
                                <h2 class="mb-0"><?= count($pendientes) ?></h2>
                            </div>
                            <i class="ti-time" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Para Hoy</h6>
                                <h2 class="mb-0"><?= count($hoy) ?></h2>
                            </div>
                            <i class="ti-calendar" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Vencidas</h6>
                                <h2 class="mb-0"><?= count($vencidas) ?></h2>
                            </div>
                            <i class="ti-alert" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Completadas</h6>
                                <h2 class="mb-0"><?= count($completadas) ?></h2>
                            </div>
                            <i class="ti-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Mis Tareas</h4>
                    <div>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaTarea">
                            <i class="ti-plus"></i> Nueva Tarea
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" placeholder="Buscar tarea..." id="buscarTarea">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" id="filtroPrioridad">
                            <option value="">Todas las prioridades</option>
                            <option value="urgente">Urgente</option>
                            <option value="alta">Alta</option>
                            <option value="media">Media</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" id="filtroTipo">
                            <option value="">Todos los tipos</option>
                            <option value="llamada">Llamada</option>
                            <option value="reunion">Reunión</option>
                            <option value="email">Email</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="seguimiento">Seguimiento</option>
                        </select>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#pendientes">
                            Pendientes <span class="badge bg-warning text-dark"><?= count($pendientes) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#hoy">
                            Hoy <span class="badge bg-info"><?= count($hoy) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#vencidas">
                            Vencidas <span class="badge bg-danger"><?= count($vencidas) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#completadas">
                            Completadas <span class="badge bg-success"><?= count($completadas) ?></span>
                        </a>
                    </li>
                </ul>

                <!-- Contenido de Tabs -->
                <div class="tab-content mt-4">
                    <!-- Tareas Pendientes -->
                    <div id="pendientes" class="tab-pane fade show active">
                        <?php if (!empty($pendientes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaPendientes">
                                <thead>
                                    <tr>
                                        <th width="50"><input type="checkbox" id="selectAll"></th>
                                        <th>Prioridad</th>
                                        <th>Tarea</th>
                                        <th>Lead</th>
                                        <th>Tipo</th>
                                        <th>Vencimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendientes as $tarea): ?>
                                    <tr data-idtarea="<?= $tarea['idtarea'] ?? 0 ?>">
                                        <td><input type="checkbox" class="tarea-check"></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                ($tarea['prioridad'] ?? 'media') == 'urgente' ? 'danger' : 
                                                (($tarea['prioridad'] ?? 'media') == 'alta' ? 'warning' : 
                                                (($tarea['prioridad'] ?? 'media') == 'media' ? 'info' : 'secondary')) 
                                            ?>">
                                                <?= ucfirst($tarea['prioridad'] ?? 'Media') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= esc($tarea['titulo'] ?? 'Sin título') ?></strong>
                                            <?php if (!empty($tarea['descripcion'])): ?>
                                            <br><small class="text-muted"><?= esc(substr($tarea['descripcion'], 0, 60)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($tarea['idlead'])): ?>
                                            <a href="<?= base_url('leads/view/' . $tarea['idlead']) ?>">
                                                <?= esc($tarea['lead_nombre'] ?? 'Lead #' . $tarea['idlead']) ?>
                                            </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sin lead</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <i class="<?= 
                                                    ($tarea['tipo_tarea'] ?? 'general') == 'llamada' ? 'ti-mobile' : 
                                                    (($tarea['tipo_tarea'] ?? 'general') == 'whatsapp' ? 'fab fa-whatsapp' : 
                                                    (($tarea['tipo_tarea'] ?? 'general') == 'email' ? 'ti-email' : 'ti-clipboard')) 
                                                ?>"></i>
                                                <?= ucfirst($tarea['tipo_tarea'] ?? 'General') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($tarea['fecha_vencimiento'])): ?>
                                                <?php 
                                                $vencimiento = strtotime($tarea['fecha_vencimiento']);
                                                $ahora = time();
                                                $dias = floor(($vencimiento - $ahora) / 86400);
                                                $horas = floor(($vencimiento - $ahora) / 3600);
                                                ?>
                                                <?= date('d/m/Y H:i', strtotime($tarea['fecha_vencimiento'])) ?>
                                                <br>
                                                <small class="text-<?= $dias < 0 ? 'danger' : ($dias == 0 ? 'warning' : 'muted') ?>">
                                                    <?php if ($dias < 0): ?>
                                                        Vencida
                                                    <?php elseif ($dias == 0): ?>
                                                        <?= $horas > 0 ? "En $horas horas" : "Hoy" ?>
                                                    <?php else: ?>
                                                        En <?= $dias ?> día(s)
                                                    <?php endif; ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">Sin fecha</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="completarTarea(<?= $tarea['idtarea'] ?>)"
                                                        title="Completar">
                                                    <i class="ti-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="verDetalle(<?= $tarea['idtarea'] ?>)"
                                                        title="Ver detalle">
                                                    <i class="ti-eye"></i>
                                                </button>
                                                <?php if (isset($tarea['tipo_tarea']) && ($tarea['tipo_tarea'] == 'whatsapp' || $tarea['tipo_tarea'] == 'llamada')): ?>
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="contactarLead('<?= $tarea['lead_telefono'] ?? '' ?>', '<?= $tarea['tipo_tarea'] ?>')"
                                                        title="Contactar">
                                                    <i class="<?= $tarea['tipo_tarea'] == 'whatsapp' ? 'fab fa-whatsapp' : 'ti-mobile' ?>"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Acciones masivas -->
                        <div class="mt-3" id="accionesMasivas" style="display: none;">
                            <button class="btn btn-sm btn-success" onclick="completarSeleccionadas()">
                                <i class="ti-check"></i> Completar seleccionadas
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarSeleccionadas()">
                                <i class="ti-trash"></i> Eliminar seleccionadas
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="ti-check text-success" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">¡No tienes tareas pendientes!</h5>
                            <p class="text-muted">Buen trabajo manteniendo tu lista al día</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tareas de Hoy -->
                    <div id="hoy" class="tab-pane fade">
                        <?php if (!empty($hoy)): ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            <?php foreach ($hoy as $tarea): ?>
                            <div class="col">
                                <div class="card h-100 border-info shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-<?= 
                                                $tarea['prioridad'] == 'urgente' ? 'danger' : 
                                                ($tarea['prioridad'] == 'alta' ? 'warning' : 'info') 
                                            ?>">
                                                <?= ucfirst($tarea['prioridad']) ?>
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="<?= 
                                                    ($tarea['tipo_tarea'] ?? 'general') == 'llamada' ? 'ti-mobile' : 
                                                    (($tarea['tipo_tarea'] ?? 'general') == 'whatsapp' ? 'fab fa-whatsapp' : 'ti-clipboard') 
                                                ?>"></i>
                                            </span>
                                        </div>
                                        <h6 class="card-title"><?= esc($tarea['titulo']) ?></h6>
                                        <p class="text-muted small mb-2">
                                            <i class="ti-user"></i> <?= esc($tarea['lead_nombre']) ?>
                                        </p>
                                        <?php if (!empty($tarea['descripcion'])): ?>
                                        <p class="small text-muted"><?= esc($tarea['descripcion']) ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="ti-time"></i> <?= date('H:i', strtotime($tarea['fecha_vencimiento'])) ?>
                                            </small>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-success" 
                                                        onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                                    <i class="ti-check"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" 
                                                        onclick="verDetalle(<?= $tarea['idtarea'] ?>)">
                                                    <i class="ti-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="ti-calendar text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">No tienes tareas programadas para hoy</h5>
                            <p class="text-muted">Disfruta de un día tranquilo o planifica nuevas tareas</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tareas Vencidas -->
                    <div id="vencidas" class="tab-pane fade">
                        <?php if (!empty($vencidas)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="ti-alert me-2"></i>
                            <div>
                                <strong>¡Atención!</strong> Tienes <?= count($vencidas) ?> tarea(s) vencida(s)
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarea</th>
                                        <th>Lead</th>
                                        <th>Venció</th>
                                        <th>Días vencida</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vencidas as $tarea): ?>
                                    <tr class="table-danger">
                                        <td>
                                            <strong><?= esc($tarea['titulo']) ?></strong>
                                            <?php if (!empty($tarea['descripcion'])): ?>
                                            <br><small class="text-muted"><?= esc($tarea['descripcion']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('leads/view/' . $tarea['idlead']) ?>">
                                                <?= esc($tarea['lead_nombre']) ?>
                                            </a>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_vencimiento'])) ?></td>
                                        <td>
                                            <?php 
                                            $dias_vencida = floor((time() - strtotime($tarea['fecha_vencimiento'])) / 86400);
                                            ?>
                                            <span class="badge bg-danger"><?= $dias_vencida ?> día(s)</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                                <i class="ti-check"></i> Completar
                                            </button>
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="reprogramarTarea(<?= $tarea['idtarea'] ?>)">
                                                <i class="ti-reload"></i> Reprogramar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="ti-check text-success" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">¡Excelente!</h5>
                            <p class="text-muted">No tienes tareas vencidas</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tareas Completadas -->
                    <div id="completadas" class="tab-pane fade">
                        <?php if (!empty($completadas)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarea</th>
                                        <th>Lead</th>
                                        <th>Completada</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completadas as $tarea): ?>
                                    <tr>
                                        <td>
                                            <del class="text-muted"><?= esc($tarea['titulo']) ?></del>
                                            <br><small><?= esc($tarea['tipo_tarea'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('leads/view/' . $tarea['idlead']) ?>">
                                                <?= esc($tarea['lead_nombre']) ?>
                                            </a>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_completada'])) ?></td>
                                        <td>
                                            <small><?= esc($tarea['notas_resultado'] ?? 'Sin notas') ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="ti-clipboard text-muted" style="font-size: 4rem;"></i>
                            <p class="mt-3 text-muted">No hay tareas completadas aún</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Tarea -->
<div class="modal fade" id="modalNuevaTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nueva Tarea</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('tareas/crear') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Título de la Tarea *</label>
                                <input type="text" class="form-control" name="titulo" required 
                                       placeholder="Ej: Llamar para seguimiento">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" name="prioridad" required>
                                    <option value="baja">🟢 Baja</option>
                                    <option value="media" selected>🟡 Media</option>
                                    <option value="alta">🟠 Alta</option>
                                    <option value="urgente">🔴 Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" 
                                  placeholder="Detalles adicionales de la tarea..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Tarea *</label>
                                <select class="form-select" name="tipo_tarea" required>
                                    <option value="llamada">📞 Llamada</option>
                                    <option value="whatsapp">💬 WhatsApp</option>
                                    <option value="email">📧 Email</option>
                                    <option value="reunion">🤝 Reunión</option>
                                    <option value="seguimiento">📋 Seguimiento</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Fecha y Hora *</label>
                                <input type="datetime-local" class="form-control" name="fecha_vencimiento" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Lead Asociado</label>
                                <select class="form-select" name="idlead" id="selectLead">
                                    <option value="">Seleccionar lead...</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="ti-search"></i> Escribe para buscar por nombre, teléfono o DNI
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="estado" value="Pendiente">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Completar Tarea -->
<div class="modal fade" id="modalCompletarTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Completar Tarea</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCompletarTarea">
                <input type="hidden" id="idtarea_completar" name="idtarea">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">¿Cómo fue el resultado?</label>
                        <textarea class="form-control" name="notas_resultado" rows="4" 
                                  placeholder="Describe qué se logró con esta tarea..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requiereSeguimiento">
                            <label class="form-check-label" for="requiereSeguimiento">
                                Crear tarea de seguimiento
                            </label>
                        </div>
                    </div>
                    <div id="datosSeguimiento" style="display: none;">
                        <label class="form-label">Fecha de seguimiento</label>
                        <input type="datetime-local" class="form-control" name="fecha_seguimiento">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Completada</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/tareas/tareas-index.js') ?>"></script>
<?= $this->endSection() ?>
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() {
                        return 'Escribe al menos 2 caracteres para buscar';
                    },
                    searching: function() {
                        return 'Buscando leads...';
                    },
                    noResults: function() {
                        return 'No se encontraron leads';
                    },
                    errorLoading: function() {
                        return 'Error al cargar resultados';
                    }
                },
                templateResult: formatLead,
                templateSelection: formatLeadSelection
            });
        }
    });
    
    // Limpiar Select2 cuando se cierra el modal
    $('#modalNuevaTarea').on('hidden.bs.modal', function () {
        $('#selectLead').val(null).trigger('change');
    });
});

// Formato para mostrar en el dropdown
function formatLead(lead) {
    if (lead.loading) {
        return lead.text;
    }
    
    var $container = $(
        '<div class="select2-result-lead">' +
            '<div class="lead-name">' + lead.text + '</div>' +
            (lead.etapa ? '<small class="text-muted">Etapa: ' + lead.etapa + '</small>' : '') +
        '</div>'
    );
    
    return $container;
}

// Formato para mostrar cuando está seleccionado
function formatLeadSelection(lead) {
    return lead.text || lead.id;
}

function completarTarea(idtarea) {
    document.getElementById('idtarea_completar').value = idtarea;
    const modal = new bootstrap.Modal(document.getElementById('modalCompletarTarea'));
    modal.show();
}

document.getElementById('formCompletarTarea').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= base_url('tareas/completar/') ?>' + document.getElementById('idtarea_completar').value, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Tarea Completada!',
                text: 'La tarea se marcó como completada exitosamente',
                confirmButtonColor: '#3085d6',
                timer: 2000
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo completar la tarea',
                confirmButtonColor: '#d33'
            });
        }
    });
});

document.getElementById('requiereSeguimiento').addEventListener('change', function(e) {
    document.getElementById('datosSeguimiento').style.display = e.target.checked ? 'block' : 'none';
});

function verDetalle(idtarea) {
    fetch(`<?= base_url('tareas/detalle') ?>/${idtarea}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Tarea: ' + data.tarea.titulo);
            }
        });
}

function reprogramarTarea(idtarea) {
    Swal.fire({
        title: 'Reprogramar Tarea',
        html: '<input type="datetime-local" id="swal-input-fecha" class="swal2-input" style="width: 90%;">',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti-check"></i> Reprogramar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const fecha = document.getElementById('swal-input-fecha').value;
            if (!fecha) {
                Swal.showValidationMessage('Debes seleccionar una fecha');
                return false;
            }
            return fecha;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('tareas/reprogramar') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify({ idtarea: idtarea, nueva_fecha: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Tarea reprogramada exitosamente');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', 'Error al reprogramar');
                }
            });
        }
    });
}

function contactarLead(telefono, tipo) {
    if (tipo === 'whatsapp') {
        window.open(`https://wa.me/51${telefono}?text=Hola,%20te%20contacto%20desde%20Delafiber`, '_blank');
    } else if (tipo === 'llamada') {
        window.location.href = `tel:+51${telefono}`;
    }
}

document.getElementById('selectAll')?.addEventListener('change', function(e) {
    document.querySelectorAll('.tarea-check').forEach(cb => cb.checked = e.target.checked);
    toggleAccionesMasivas();
});

document.querySelectorAll('.tarea-check').forEach(checkbox => {
    checkbox.addEventListener('change', toggleAccionesMasivas);
});

function toggleAccionesMasivas() {
    const checked = document.querySelectorAll('.tarea-check:checked').length;
    document.getElementById('accionesMasivas').style.display = checked > 0 ? 'block' : 'none';
}

function completarSeleccionadas() {
    const ids = Array.from(document.querySelectorAll('.tarea-check:checked'))
        .map(cb => cb.closest('tr').dataset.idtarea);
    
    Swal.fire({
        title: '¿Completar tareas?',
        text: `¿Deseas marcar ${ids.length} tarea(s) como completadas?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti-check"></i> Sí, completar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('tareas/completarMultiples') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', `${ids.length} tarea(s) completadas`);
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    });
}

function eliminarSeleccionadas() {
    const ids = Array.from(document.querySelectorAll('.tarea-check:checked'))
        .map(cb => cb.closest('tr').dataset.idtarea);
    
    Swal.fire({
        title: '¿Eliminar tareas?',
        html: `¿Estás seguro de eliminar <strong>${ids.length} tarea(s)</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti-trash"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('<?= base_url('tareas/eliminarMultiples') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {