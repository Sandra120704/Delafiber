<?= $this->extend('Layouts/header') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Mis Tareas</h4>

                <!-- Tabs de Filtrado -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#pendientes">
                            Pendientes <span class="badge badge-warning"><?= count($pendientes) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#hoy">
                            Hoy <span class="badge badge-info"><?= count($hoy) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#vencidas">
                            Vencidas <span class="badge badge-danger"><?= count($vencidas) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#completadas">
                            Completadas
                        </a>
                    </li>
                </ul>

                <!-- Contenido de Tabs -->
                <div class="tab-content mt-4">
                    <!-- Tareas Pendientes -->
                    <div id="pendientes" class="tab-pane fade show active">
                        <?php if (!empty($pendientes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Prioridad</th>
                                        <th>Tarea</th>
                                        <th>Lead</th>
                                        <th>Vencimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendientes as $tarea): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $tarea['prioridad'] == 'urgente' ? 'danger' : 
                                                ($tarea['prioridad'] == 'alta' ? 'warning' : 
                                                ($tarea['prioridad'] == 'media' ? 'info' : 'secondary')) 
                                            ?>">
                                                <?= ucfirst($tarea['prioridad']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= esc($tarea['titulo']) ?></strong>
                                            <?php if ($tarea['descripcion']): ?>
                                            <br><small class="text-muted"><?= esc(substr($tarea['descripcion'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('leads/view/' . $tarea['idlead']) ?>">
                                                <?= esc($tarea['lead_nombre']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                            $vencimiento = strtotime($tarea['fecha_vencimiento']);
                                            $ahora = time();
                                            $dias = floor(($vencimiento - $ahora) / 86400);
                                            ?>
                                            <?= date('d/m/Y H:i', strtotime($tarea['fecha_vencimiento'])) ?>
                                            <br>
                                            <small class="text-<?= $dias < 0 ? 'danger' : ($dias == 0 ? 'warning' : 'muted') ?>">
                                                <?= $dias < 0 ? 'Vencida' : ($dias == 0 ? 'Hoy' : "En $dias días") ?>
                                            </small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                                <i class="icon-check"></i> Completar
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="verDetalle(<?= $tarea['idtarea'] ?>)">
                                                <i class="icon-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="icon-check text-success" style="font-size: 3rem;"></i>
                            <p class="mt-2">¡No tienes tareas pendientes!</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tareas de Hoy -->
                    <div id="hoy" class="tab-pane fade">
                        <?php if (!empty($hoy)): ?>
                        <div class="row">
                            <?php foreach ($hoy as $tarea): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h6><?= esc($tarea['titulo']) ?></h6>
                                            <span class="badge badge-<?= 
                                                $tarea['prioridad'] == 'urgente' ? 'danger' : 
                                                ($tarea['prioridad'] == 'alta' ? 'warning' : 'info') 
                                            ?>">
                                                <?= ucfirst($tarea['prioridad']) ?>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-2"><?= esc($tarea['lead_nombre']) ?></p>
                                        <p class="small"><?= esc($tarea['descripcion']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="icon-clock"></i> <?= date('H:i', strtotime($tarea['fecha_vencimiento'])) ?>
                                            </small>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                                Completar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No tienes tareas programadas para hoy</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tareas Vencidas -->
                    <div id="vencidas" class="tab-pane fade">
                        <?php if (!empty($vencidas)): ?>
                        <div class="alert alert-danger">
                            <strong>¡Atención!</strong> Tienes <?= count($vencidas) ?> tarea(s) vencida(s)
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarea</th>
                                        <th>Lead</th>
                                        <th>Venció</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vencidas as $tarea): ?>
                                    <tr class="table-danger">
                                        <td>
                                            <strong><?= esc($tarea['titulo']) ?></strong>
                                            <br><small><?= esc($tarea['descripcion']) ?></small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('leads/view/' . $tarea['idlead']) ?>">
                                                <?= esc($tarea['lead_nombre']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                            $dias_vencida = floor((time() - strtotime($tarea['fecha_vencimiento'])) / 86400);
                                            ?>
                                            <?= date('d/m/Y', strtotime($tarea['fecha_vencimiento'])) ?>
                                            <br><small class="text-danger">Hace <?= $dias_vencida ?> día(s)</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                                <i class="icon-check"></i> Completar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="icon-check text-success" style="font-size: 3rem;"></i>
                            <p class="mt-2">No tienes tareas vencidas</p>
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
                                        </td>
                                        <td>
                                            <a href="<?= base_url('leads/view/' . $tarea['idlead']) ?>">
                                                <?= esc($tarea['lead_nombre']) ?>
                                            </a>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_completado'])) ?></td>
                                        <td>
                                            <small><?= esc($tarea['notas_resultado'] ?? '-') ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No hay tareas completadas</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Completar Tarea -->
<div class="modal fade" id="modalCompletarTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Completar Tarea</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formCompletarTarea">
                <input type="hidden" id="idtarea_completar" name="idtarea">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Notas del Resultado</label>
                        <textarea class="form-control" name="notas_resultado" rows="4" 
                                  placeholder="Describe qué se logró con esta tarea..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Completada</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function completarTarea(idtarea) {
    document.getElementById('idtarea_completar').value = idtarea;
    $('#modalCompletarTarea').modal('show');
}

document.getElementById('formCompletarTarea').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('leads/completarTarea') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al completar la tarea');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
});

function verDetalle(idtarea) {
    // Implementar modal con detalle completo si lo necesitas
    alert('Función en desarrollo');
}
</script>

<?= $this->endSection() ?>