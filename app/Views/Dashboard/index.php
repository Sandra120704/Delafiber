<?= view('Layouts/header') ?>

<div class="content-wrapper">
    <!-- Saludo personalizado y resumen rápido -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-banner">
                <h3 class="mb-2">¡Hola, <?= $user_name ?>! 👋</h3>
                <p class="text-muted">Aquí tienes tu resumen del día. Tienes <?= $resumen['tareas_vencidas'] ?> tareas vencidas y <?= $resumen['leads_calientes'] ?> leads calientes esperando.</p>
            </div>
        </div>
    </div>

    <!-- Alertas urgentes -->
    <?php if ($resumen['tareas_vencidas'] > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="ti-alarm-clock mr-2"></i>
                <strong>¡Atención!</strong> Tienes <?= $resumen['tareas_vencidas'] ?> tareas vencidas que necesitan atención inmediata.
                <button class="btn btn-outline-danger btn-sm ml-auto" onclick="scrollToSection('tareas-vencidas')">
                    Ver tareas
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mis Leads Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $resumen['total_leads'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-target text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tareas Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $resumen['tareas_pendientes'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-clipboard text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Conversiones (Este mes)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $resumen['conversiones_mes'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-check-box text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Leads Calientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $resumen['leads_calientes'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-fire text-danger fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Área principal de trabajo -->
    <div class="row">
        <!-- Panel izquierdo: Tareas y Leads urgentes -->
        <div class="col-xl-8 col-lg-7">
            <!-- Tareas vencidas -->
            <?php if (!empty($tareas_vencidas)): ?>
            <div class="card shadow mb-4" id="tareas-vencidas">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">🚨 Tareas Vencidas - Acción Inmediata</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($tareas_vencidas as $tarea): ?>
                    <div class="task-urgent mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?= esc($tarea['titulo']) ?></h6>
                                <p class="text-muted small mb-1">
                                    <strong>Cliente:</strong> <?= esc($tarea['cliente_nombre']) ?> 
                                    <span class="ml-2"><i class="ti-mobile"></i> <?= $tarea['cliente_telefono'] ?></span>
                                </p>
                                <p class="text-danger small">
                                    <i class="ti-alarm-clock"></i> Vencía: <?= date('d/m/Y H:i', strtotime($tarea['fecha_vencimiento'])) ?>
                                </p>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm quick-action" 
                                        data-action="llamar" data-lead="<?= $tarea['idlead'] ?>">
                                    <i class="ti-phone"></i>
                                </button>
                                <button class="btn btn-primary btn-sm quick-action" 
                                        data-action="whatsapp" data-lead="<?= $tarea['idlead'] ?>">
                                    <i class="ti-comment"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm" 
                                        onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                    <i class="ti-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tareas de hoy -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Mis Tareas de Hoy</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($tareas_hoy)): ?>
                        <?php foreach ($tareas_hoy as $tarea): ?>
                        <div class="task-item mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= esc($tarea['titulo']) ?></h6>
                                    <p class="text-muted small mb-1">
                                        <strong><?= esc($tarea['cliente_nombre']) ?></strong>
                                        <span class="ml-2"><i class="ti-mobile"></i> <?= $tarea['cliente_telefono'] ?></span>
                                    </p>
                                    <small class="text-info">
                                        <i class="ti-time"></i> <?= date('H:i', strtotime($tarea['fecha_vencimiento'])) ?>
                                    </small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-success btn-sm quick-action" 
                                            data-action="llamar" data-lead="<?= $tarea['idlead'] ?>"
                                            title="Llamar">
                                        <i class="ti-phone"></i>
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm quick-action" 
                                            data-action="whatsapp" data-lead="<?= $tarea['idlead'] ?>"
                                            title="WhatsApp">
                                        <i class="ti-comment"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="completarTarea(<?= $tarea['idtarea'] ?>)"
                                            title="Completar">
                                        <i class="ti-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="ti-check-box" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="mt-2">¡Excelente! No tienes tareas pendientes para hoy.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Leads que necesitan atención -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">🔥 Leads Calientes - Requieren Atención</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($leads_calientes)): ?>
                        <?php foreach ($leads_calientes as $lead): ?>
                        <div class="lead-card mb-3 p-3 border rounded hover-shadow">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= esc($lead['cliente_nombre']) ?></h6>
                                    <p class="text-muted small mb-1">
                                        <i class="ti-mobile"></i> <?= $lead['telefono'] ?>
                                        <span class="ml-3"><i class="ti-location-pin"></i> <?= esc($lead['distrito']) ?></span>
                                    </p>
                                    <span class="badge badge-warning"><?= esc($lead['etapa']) ?></span>
                                    <small class="text-muted ml-2">
                                        Registro: <?= date('d/m/Y', strtotime($lead['fecha_registro'])) ?>
                                    </small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-success btn-sm quick-action" 
                                            data-action="llamar" data-lead="<?= $lead['idlead'] ?>">
                                        <i class="ti-phone"></i> Llamar
                                    </button>
                                    <button class="btn btn-primary btn-sm quick-action" 
                                            data-action="whatsapp" data-lead="<?= $lead['idlead'] ?>">
                                        <i class="ti-comment"></i> WhatsApp
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="ti-target" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="mt-2">No tienes leads calientes en este momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel derecho: Acciones rápidas y actividad -->
        <div class="col-xl-4 col-lg-5">
            <!-- Acciones rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">⚡ Acciones Rápidas</h6>
                </div>
                <div class="card-body text-center">
                    <a href="<?= base_url('leads/create') ?>" class="btn btn-success btn-lg btn-block mb-3">
                        <i class="ti-plus"></i> Nuevo Lead
                    </a>
                    <div class="row">
                        <div class="col-6">
                            <a href="<?= base_url('leads/pipeline') ?>" class="btn btn-outline-primary btn-block">
                                <i class="ti-layout-grid2"></i><br>Pipeline
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('cotizaciones/create') ?>" class="btn btn-outline-warning btn-block">
                                <i class="ti-receipt"></i><br>Cotizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leads nuevos -->
            <?php if (!empty($leads_nuevos)): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">✨ Leads Nuevos - Sin Contactar</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($leads_nuevos as $lead): ?>
                    <div class="mb-3 p-2 border-left border-info">
                        <h6 class="mb-1"><?= esc($lead['cliente_nombre']) ?></h6>
                        <p class="text-muted small mb-2">
                            <i class="ti-mobile"></i> <?= $lead['telefono'] ?><br>
                            <i class="ti-time"></i> <?= time_elapsed_string($lead['fecha_registro']) ?>
                        </p>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-success quick-action" 
                                    data-action="llamar" data-lead="<?= $lead['idlead'] ?>">
                                <i class="ti-phone"></i>
                            </button>
                            <button class="btn btn-primary quick-action" 
                                    data-action="whatsapp" data-lead="<?= $lead['idlead'] ?>">
                                <i class="ti-comment"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actividad reciente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">📋 Actividad Reciente</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($actividad_reciente)): ?>
                        <?php foreach ($actividad_reciente as $actividad): ?>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="activity-icon mr-3">
                                    <i class="ti-comment-alt text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1"><strong><?= esc($actividad['cliente_nombre']) ?></strong></p>
                                    <p class="text-muted small mb-0"><?= esc($actividad['modalidad']) ?></p>
                                    <small class="text-muted"><?= time_elapsed_string($actividad['fecha']) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <p>No hay actividad reciente</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tips del día -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-dark">💡 Tip del Día</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <strong>Consejo:</strong> Los leads que no han sido contactados en las primeras 2 horas tienen 60% menos probabilidades de convertir. ¡Actúa rápido!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón flotante para nuevo lead -->
<div class="fab-container">
    <a href="<?= base_url('leads/create') ?>" class="fab-button" title="Nuevo Lead">
        <i class="ti-plus"></i>
    </a>
</div>

<!-- Modal para completar tarea -->
<div class="modal fade" id="completarTareaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Completar Tarea</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="completarTareaForm">
                    <input type="hidden" id="tareaId" name="tarea_id">
                    <div class="form-group">
                        <label for="notasResultado">Notas del resultado:</label>
                        <textarea class="form-control" id="notasResultado" name="notas" rows="3" 
                                  placeholder="¿Qué resultado tuvo esta tarea?"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarTareaCompletada()">
                    <i class="ti-check"></i> Completar Tarea
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Incluir CSS y JS específicos del dashboard -->
<link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
<script src="<?= base_url('js/dashboard.js') ?>"></script>

<script>
// Variable global para base_url
const base_url = '<?= base_url() ?>';

// Función auxiliar para time_elapsed_string si no existe
<?php if(!function_exists('time_elapsed_string')): ?>
function time_elapsed_string(fecha) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - new Date(fecha)) / 1000);
    
    if (diffInSeconds < 60) return 'hace unos momentos';
    if (diffInSeconds < 3600) return `hace ${Math.floor(diffInSeconds / 60)} minutos`;
    if (diffInSeconds < 86400) return `hace ${Math.floor(diffInSeconds / 3600)} horas`;
    return `hace ${Math.floor(diffInSeconds / 86400)} días`;
}
<?php endif; ?>
</script>

<?= view('Layouts/footer') ?>