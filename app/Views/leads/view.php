<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php
// Inicializa variables para evitar error 500
$lead = $lead ?? [];
$error = $error ?? null;
$seguimientos = $seguimientos ?? [];
$tareas = $tareas ?? [];
$campania = $campania ?? [];
$persona = $persona ?? [];
$etapas = $etapas ?? [];
$modalidades = $modalidades ?? [];
$historial = $historial ?? [];
?>

<div class="row">
    <div class="col-12">
        <!-- Encabezado con acciones -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= base_url('leads') ?>" class="btn btn-outline-secondary">
                    <i class="icon-arrow-left"></i> Volver a Leads
                </a>
            </div>
            <div>
                <a href="<?= base_url('leads/edit/' . $lead['idlead']) ?>" class="btn btn-warning">
                    <i class="icon-pencil"></i> Editar
                </a>
                <button class="btn btn-success" data-toggle="modal" data-target="#modalConvertir">
                    <i class="icon-check"></i> Convertir a Cliente
                </button>
                <button class="btn btn-danger" data-toggle="modal" data-target="#modalDescartar">
                    <i class="icon-close"></i> Descartar
                </button>
            </div>
        </div>

        <!-- Información del Lead -->
        <div class="row">
            <!-- Columna Izquierda: Información Principal -->
            <div class="col-md-8">
                <!-- Datos del Cliente -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php 
                            $nombreCompleto = ($lead['nombres'] ?? '') . ' ' . ($lead['apellidos'] ?? '');
                            $iniciales = strtoupper(substr($lead['nombres'] ?? 'L', 0, 1) . substr($lead['apellidos'] ?? 'L', 0, 1));
                            ?>
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:80px;height:80px;">
                                <h2 class="mb-0"><?= $iniciales ?></h2>
                            </div>
                            <div>
                                <h3 class="mb-1"><?= esc($nombreCompleto) ?></h3>
                                <p class="text-muted mb-0">DNI: <?= esc($lead['dni'] ?? 'Sin DNI') ?></p>
                                <span class="badge badge-<?= ($lead['estado'] ?? '') == 'Convertido' ? 'success' : (($lead['estado'] ?? '') == 'Descartado' ? 'danger' : 'info') ?>">
                                    <?= $lead['estado'] ?? 'Activo' ?>
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="icon-phone mr-2"></i>Teléfono:</strong><br>
                                <?= esc($lead['telefono']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="icon-envelope mr-2"></i>Correo:</strong><br>
                                <?= esc($lead['correo'] ?? 'No registrado') ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="icon-location-pin mr-2"></i>Dirección:</strong><br>
                                <?= esc($lead['direccion'] ?? 'No registrado') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="icon-map mr-2"></i>Distrito:</strong><br>
                                <?= esc($lead['distrito_nombre'] ?? 'No registrado') ?></p>
                            </div>
                        </div>

                        <?php if (!empty($lead['referencias'])): ?>
                        <p><strong><i class="icon-info mr-2"></i>Referencias:</strong><br>
                        <?= esc($lead['referencias']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información del Lead -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Lead</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Etapa Actual</label>
                                <h6><?= esc($lead['etapa_nombre'] ?? 'Sin etapa') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Origen</label>
                                <h6><?= esc($lead['origen_nombre'] ?? 'Sin origen') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Campaña</label>
                                <h6><?= esc($lead['campania_nombre'] ?? 'Sin campaña') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Vendedor Asignado</label>
                                <h6><?= esc(session()->get('user_name') ?? 'Sin asignar') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Fecha de Registro</label>
                                <h6><?= isset($lead['created_at']) ? date('d/m/Y H:i', strtotime($lead['created_at'])) : 'No disponible' ?></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seguimientos -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Seguimientos</h5>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalSeguimiento">
                            <i class="icon-plus"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($seguimientos)): ?>
                            <div class="timeline">
                                <?php foreach ($seguimientos as $seg): ?>
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= esc($seg['usuario_nombre']) ?></strong>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($seg['fecha'])) ?></small>
                                        </div>
                                        <span class="badge badge-info"><?= esc($seg['modalidad_nombre']) ?></span>
                                        <p class="mt-2 mb-0"><?= nl2br(esc($seg['nota'])) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No hay seguimientos registrados</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Historial -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Historial de Cambios</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($historial)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Acción</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($historial as $h): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></td>
                                            <td><?= esc($h['usuario_nombre'] ?? 'Sistema') ?></td>
                                            <td><span class="badge badge-secondary"><?= esc($h['accion'] ?? 'Seguimiento') ?></span></td>
                                            <td><?= esc($h['descripcion'] ?? $h['nota'] ?? 'Sin descripción') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No hay historial</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Acciones Rápidas -->
            <div class="col-md-4">
                <!-- Cambiar Etapa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Cambiar Etapa</h5>
                    </div>
                    <div class="card-body">
                        <form id="formCambiarEtapa">
                            <div class="form-group">
                                <select class="form-control" id="nueva_etapa" name="idetapa">
                                    <?php foreach ($etapas as $etapa): ?>
                                    <option value="<?= $etapa['idetapa'] ?>" <?= $etapa['idetapa'] == $lead['idetapa'] ? 'selected' : '' ?>>
                                        <?= esc($etapa['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="nota" placeholder="Nota (opcional)" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Mover Etapa</button>
                        </form>
                    </div>
                </div>

                <!-- Tareas -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tareas</h5>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTarea">
                            <i class="icon-plus"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($tareas)): ?>
                            <div class="list-group">
                                <?php foreach ($tareas as $tarea): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= esc($tarea['titulo']) ?></strong>
                                        <span class="badge badge-<?= $tarea['prioridad'] == 'alta' ? 'danger' : 'warning' ?>">
                                            <?= esc($tarea['prioridad']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">Vence: <?= date('d/m/Y', strtotime($tarea['fecha_vencimiento'])) ?></small>
                                    <?php if ($tarea['estado'] != 'Completada'): ?>
                                    <button class="btn btn-sm btn-success mt-2" onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                        Completar
                                    </button>
                                    <?php else: ?>
                                    <span class="badge badge-success">Completada</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">Sin tareas</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <a href="tel:<?= $lead['telefono'] ?>" class="btn btn-outline-primary btn-block mb-2">
                            <i class="icon-phone"></i> Llamar
                        </a>
                        <a href="https://wa.me/51<?= $lead['telefono'] ?>" target="_blank" class="btn btn-outline-success btn-block mb-2">
                            <i class="icon-social-whatsapp"></i> WhatsApp
                        </a>
                        <a href="mailto:<?= $lead['correo'] ?>" class="btn btn-outline-info btn-block">
                            <i class="icon-envelope"></i> Enviar Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Seguimiento -->
<div class="modal fade" id="modalSeguimiento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Seguimiento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formSeguimiento">
                <div class="modal-body">
                    <input type="hidden" name="idlead" value="<?= $lead['idlead'] ?>">
                    <div class="form-group">
                        <label>Tipo de Comunicación</label>
                        <select class="form-control" name="idmodalidad" required>
                            <?php foreach ($modalidades as $mod): ?>
                            <option value="<?= $mod['idmodalidad'] ?>"><?= esc($mod['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nota</label>
                        <textarea class="form-control" name="nota" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Crear Tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Tarea</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTarea">
                <div class="modal-body">
                    <input type="hidden" name="idlead" value="<?= $lead['idlead'] ?>">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prioridad</label>
                                <select class="form-control" name="prioridad">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input type="datetime-local" class="form-control" name="fecha_vencimiento" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Convertir -->
<div class="modal fade" id="modalConvertir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Convertir a Cliente</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('leads/convertir/' . $lead['idlead']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>¿Estás seguro de convertir este lead en cliente?</p>
                    <div class="form-group">
                        <label>Número de Contrato (opcional)</label>
                        <input type="text" class="form-control" name="numero_contrato">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Convertir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Descartar -->
<div class="modal fade" id="modalDescartar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Descartar Lead</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('leads/descartar/' . $lead['idlead']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Motivo del Descarte *</label>
                        <textarea class="form-control" name="motivo" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Descartar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline-item {
    position: relative;
    padding-left: 35px;
    padding-bottom: 20px;
    border-left: 2px solid #e0e0e0;
}
.timeline-item:last-child {
    border-left: none;
}
.timeline-marker {
    position: absolute;
    left: -7px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
}
</style>

<script>
// Cambiar etapa
document.getElementById('formCambiarEtapa').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('idlead', <?= $lead['idlead'] ?>);
    
    fetch('<?= base_url('leads/moverEtapa') ?>', {
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

// Agregar seguimiento
document.getElementById('formSeguimiento').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= base_url('leads/agregarSeguimiento') ?>', {
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

// Crear tarea
document.getElementById('formTarea').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= base_url('leads/crearTarea') ?>', {
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

// Completar tarea
function completarTarea(id) {
    if (confirm('¿Marcar como completada?')) {
        const formData = new FormData();
        formData.append('idtarea', id);
        
        fetch('<?= base_url('leads/completarTarea') ?>', {
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
</script>

<?= $this->endSection() ?>
<?= $this->include('Layouts/footer') ?>