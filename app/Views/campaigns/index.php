<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Gestión de Campañas</h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCampania">
                        <i class="icon-plus"></i> Nueva Campaña
                    </button>
                </div>

                <!-- Mensajes Flash -->
                <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>

                <!-- Tabla de Campañas -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Presupuesto</th>
                                <th>Leads</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($campanias)): ?>
                                <?php foreach ($campanias as $campania): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($campania['nombre']) ?></strong><br>
                                        <small class="text-muted"><?= esc($campania['descripcion']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= esc($campania['tipo']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($campania['fecha_inicio'])) ?></td>
                                    <td>
                                        <?php if ($campania['fecha_fin']): ?>
                                            <?= date('d/m/Y', strtotime($campania['fecha_fin'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin fecha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>S/ <?= number_format($campania['presupuesto'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-primary"><?= $campania['total_leads'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($campania['activo']): ?>
                                            <span class="badge badge-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    onclick="editarCampania(<?= htmlspecialchars(json_encode($campania)) ?>)">
                                                <i class="icon-pencil"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmarEliminar(<?= $campania['idcampania'] ?>)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="icon-info text-muted" style="font-size: 48px;"></i>
                                        <p class="text-muted mt-2">No hay campañas registradas</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva/Editar Campaña -->
<div class="modal fade" id="modalCampania" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCampania" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="idcampania" id="idcampania">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Campaña</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" id="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo <span class="text-danger">*</span></label>
                                <select class="form-control" name="tipo" id="tipo" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Email">Email</option>
                                    <option value="Redes Sociales">Redes Sociales</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Telemarketing">Telemarketing</option>
                                    <option value="Evento">Evento</option>
                                    <option value="Publicidad">Publicidad</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Presupuesto (S/)</label>
                                <input type="number" class="form-control" name="presupuesto" id="presupuesto" step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" checked>
                            <label class="custom-control-label" for="activo">Campaña Activa</label>
                        </div>
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

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta campaña?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Configurar formulario para crear o editar
$('#modalCampania').on('show.bs.modal', function (e) {
    if (!$(e.relatedTarget).data('campania')) {
        // Modo Crear
        $('#formCampania')[0].reset();
        $('#idcampania').val('');
        $('#modalTitle').text('Nueva Campaña');
        $('#formCampania').attr('action', '<?= base_url('campanias/store') ?>');
    }
});

function editarCampania(campania) {
    // Llenar formulario con datos de la campaña
    $('#idcampania').val(campania.idcampania);
    $('#nombre').val(campania.nombre);
    $('#tipo').val(campania.tipo);
    $('#descripcion').val(campania.descripcion);
    $('#fecha_inicio').val(campania.fecha_inicio);
    $('#fecha_fin').val(campania.fecha_fin);
    $('#presupuesto').val(campania.presupuesto);
    $('#activo').prop('checked', campania.activo == 1);
    
    $('#modalTitle').text('Editar Campaña');
    $('#formCampania').attr('action', '<?= base_url('campanias/update/') ?>' + campania.idcampania);
    $('#modalCampania').modal('show');
}

function confirmarEliminar(idcampania) {
    const formEliminar = document.getElementById('formEliminar');
    formEliminar.action = '<?= base_url('campanias/delete/') ?>' + idcampania;
    $('#modalEliminar').modal('show');
}
</script>

<?= $this->endsection() ?>