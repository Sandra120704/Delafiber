<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Mis Tareas</h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTarea">
                        <i class="icon-plus"></i> Nueva Tarea
                    </button>
                </div>

                <!-- Mensajes Flash -->
                <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>

                <!-- Filtros -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#pendientes">
                            Pendientes <span class="badge badge-danger"><?= $contadores['pendientes'] ?? 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#hoy">
                            Hoy <span class="badge badge-warning"><?= $contadores['hoy'] ?? 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#completadas">
                            Completadas <span class="badge badge-success"><?= $contadores['completadas'] ?? 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#todas">Todas</a>
                    </li>
                </ul>

                <!-- Contenido de Tabs -->
                <div class="tab-content">
                    <!-- Tab Pendientes -->
                    <div class="tab-pane fade show active" id="pendientes">
                        <?php 
                        $tareasPendientes = array_filter($tareas, function($t) {
                            return $t['estado'] == 'Pendiente' && strtotime($t['fecha_vencimiento']) < time();
                        });
                        ?>
                        <?php if (!empty($tareasPendientes)): ?>
                            <?php foreach ($tareasPendientes as $tarea): ?>
                                <?= view('tareas/_tarea_item', ['tarea' => $tarea]) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="icon-check text-success" style="font-size: 48px;"></i>
                                <p class="text-muted mt-2">No tienes tareas pendientes</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Hoy -->
                    <div class="tab-pane fade" id="hoy">
                        <?php 
                        $tareasHoy = array_filter($tareas, function($t) {
                            return $t['estado'] != 'Completada' && date('Y-m-d', strtotime($t['fecha_vencimiento'])) == date('Y-m-d');
                        });
                        ?>
                        <?php if (!empty($tareasHoy)): ?>
                            <?php foreach ($tareasHoy as $tarea): ?>
                                <?= view('tareas/_tarea_item', ['tarea' => $tarea]) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="icon-calendar text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mt-2">No tienes tareas para hoy</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Completadas -->
                    <div class="tab-pane fade" id="completadas">
                        <?php 
                        $tareasCompletadas = array_filter($tareas, function($t) {
                            return $t['estado'] == 'Completada';
                        });
                        ?>
                        <?php if (!empty($tareasCompletadas)): ?>
                            <?php foreach ($tareasCompletadas as $tarea): ?>
                                <?= view('tareas/_tarea_item', ['tarea' => $tarea]) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="icon-info text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mt-2">No hay tareas completadas</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Todas -->
                    <div class="tab-pane fade" id="todas">
                        <?php if (!empty($tareas)): ?>
                            <?php foreach ($tareas as $tarea): ?>
                                <?= view('tareas/_tarea_item', ['tarea' => $tarea]) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="icon-info text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mt-2">No tienes tareas registradas</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva/Editar Tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTarea" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="idtarea" id="idtarea">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Tarea</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Lead Asociado</label>
                        <select class="form-control" name="idlead" id="idlead">
                            <option value="">Sin asociar</option>
                            <?php foreach ($leads as $lead): ?>
                            <option value="<?= $lead['idlead'] ?>">
                                <?= esc($lead['cliente']) ?> - <?= esc($lead['dni']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titulo" id="titulo" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Vencimiento <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prioridad <span class="text-danger">*</span></label>
                                <select class="form-control" name="prioridad" id="prioridad" required>
                                    <option value="Baja">Baja</option>
                                    <option value="Media" selected>Media</option>
                                    <option value="Alta">Alta</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Estado <span class="text-danger">*</span></label>
                        <select class="form-control" name="estado" id="estado" required>
                            <option value="Pendiente" selected>Pendiente</option>
                            <option value="En Progreso">En Progreso</option>
                            <option value="Completada">Completada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Configurar formulario para crear
$('#modalTarea').on('show.bs.modal', function (e) {
    if (!$(e.relatedTarget).data('tarea')) {
        $('#formTarea')[0].reset();
        $('#idtarea').val('');
        $('#modalTitle').text('Nueva Tarea');
        $('#formTarea').attr('action', '<?= base_url('tareas/store') ?>');
    }
});

function editarTarea(tarea) {
    $('#idtarea').val(tarea.idtarea);
    $('#idlead').val(tarea.idlead);
    $('#titulo').val(tarea.titulo);
    $('#descripcion').val(tarea.descripcion);
    $('#fecha_vencimiento').val(tarea.fecha_vencimiento.replace(' ', 'T'));
    $('#prioridad').val(tarea.prioridad);
    $('#estado').val(tarea.estado);
    
    $('#modalTitle').text('Editar Tarea');
    $('#formTarea').attr('action', '<?= base_url('tareas/update/') ?>' + tarea.idtarea);
    $('#modalTarea').modal('show');
}

function completarTarea(idtarea) {
    if (confirm('¿Marcar esta tarea como completada?')) {
        window.location.href = '<?= base_url('tareas/completar/') ?>' + idtarea;
    }
}

function eliminarTarea(idtarea) {
    if (confirm('¿Estás seguro de eliminar esta tarea?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('tareas/delete/') ?>' + idtarea;
        form.innerHTML = '<?= csrf_field() ?>';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?= $this->endsection() ?>