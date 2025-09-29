<?= $this->extend('Layouts/header') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= base_url('campanias') ?>" class="btn btn-outline-secondary">
                    <i class="icon-arrow-left"></i> Volver
                </a>
            </div>
            <div>
                <a href="<?= base_url('campanias/edit/' . $campania['idcampania']) ?>" class="btn btn-warning">
                    <i class="icon-pencil"></i> Editar
                </a>
                <button class="btn btn-<?= $campania['estado'] == 'Activa' ? 'danger' : 'success' ?>" 
                        onclick="toggleEstado(<?= $campania['idcampania'] ?>)">
                    <?= $campania['estado'] == 'Activa' ? 'Desactivar' : 'Activar' ?>
                </button>
            </div>
        </div>

        <!-- Información de la Campaña -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="mb-2"><?= esc($campania['nombre']) ?></h3>
                                <span class="badge badge-<?= $campania['estado'] == 'Activa' ? 'success' : 'secondary' ?>">
                                    <?= $campania['estado'] ?>
                                </span>
                            </div>
                        </div>

                        <p class="text-muted"><?= esc($campania['descripcion']) ?></p>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fecha Inicio:</strong><br>
                                <?= date('d/m/Y', strtotime($campania['fecha_inicio'])) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha Fin:</strong><br>
                                <?= date('d/m/Y', strtotime($campania['fecha_fin'])) ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Presupuesto:</strong><br>
                                S/ <?= number_format($campania['presupuesto'], 2) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Responsable:</strong><br>
                                <?= esc($campania['responsable_nombre'] ?? 'Sin asignar') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de la Campaña -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Estadísticas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h2 class="text-primary"><?= $estadisticas['total_leads'] ?? 0 ?></h2>
                                    <p class="text-muted">Leads Generados</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h2 class="text-success"><?= $estadisticas['convertidos'] ?? 0 ?></h2>
                                    <p class="text-muted">Convertidos</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h2 class="text-warning"><?= $estadisticas['activos'] ?? 0 ?></h2>
                                    <p class="text-muted">En Proceso</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <?php 
                                    $tasa = 0;
                                    if ($estadisticas['total_leads'] > 0) {
                                        $tasa = ($estadisticas['convertidos'] / $estadisticas['total_leads']) * 100;
                                    }
                                    ?>
                                    <h2 class="text-info"><?= number_format($tasa, 1) ?>%</h2>
                                    <p class="text-muted">Tasa Conversión</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Difusiones/Medios -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Medios de Difusión</h5>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalDifusion">
                            <i class="icon-plus"></i> Agregar Medio
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($difusiones)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Medio</th>
                                        <th>Presupuesto</th>
                                        <th>Leads</th>
                                        <th>Costo/Lead</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($difusiones as $dif): ?>
                                    <tr>
                                        <td><strong><?= esc($dif['medio_nombre']) ?></strong></td>
                                        <td>S/ <?= number_format($dif['presupuesto'], 2) ?></td>
                                        <td><?= $dif['leads_generados'] ?></td>
                                        <td>
                                            <?php 
                                            $cpl = $dif['leads_generados'] > 0 
                                                ? $dif['presupuesto'] / $dif['leads_generados'] 
                                                : 0;
                                            ?>
                                            S/ <?= number_format($cpl, 2) ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="editarDifusion(<?= $dif['iddifusion'] ?>)">
                                                <i class="icon-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted">No hay medios registrados para esta campaña</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div class="col-md-4">
                <!-- Leads de la Campaña -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Leads Recientes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($leads_recientes)): ?>
                        <div class="list-group">
                            <?php foreach ($leads_recientes as $lead): ?>
                            <a href="<?= base_url('leads/view/' . $lead['idlead']) ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <strong><?= esc($lead['cliente']) ?></strong>
                                    <small><?= date('d/m', strtotime($lead['fecha_registro'])) ?></small>
                                </div>
                                <small class="text-muted"><?= esc($lead['telefono']) ?></small>
                                <br>
                                <span class="badge badge-info"><?= esc($lead['etapa_actual']) ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= base_url('leads?campania=' . $campania['idcampania']) ?>" 
                           class="btn btn-sm btn-outline-primary btn-block mt-3">
                            Ver todos los leads
                        </a>
                        <?php else: ?>
                        <p class="text-center text-muted">Sin leads aún</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Progreso del Presupuesto -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Presupuesto</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $presupuesto_usado = 0;
                        if (!empty($difusiones)) {
                            foreach ($difusiones as $dif) {
                                $presupuesto_usado += $dif['presupuesto'];
                            }
                        }
                        $porcentaje = ($campania['presupuesto'] > 0) 
                            ? ($presupuesto_usado / $campania['presupuesto']) * 100 
                            : 0;
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Usado: S/ <?= number_format($presupuesto_usado, 2) ?></span>
                                <span><?= number_format($porcentaje, 1) ?>%</span>
                            </div>
                            <div class="progress mt-2">
                                <div class="progress-bar" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                            <small class="text-muted">
                                Disponible: S/ <?= number_format($campania['presupuesto'] - $presupuesto_usado, 2) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Difusión -->
<div class="modal fade" id="modalDifusion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Medio de Difusión</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('campanias/agregarDifusion') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="idcampania" value="<?= $campania['idcampania'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Medio *</label>
                        <select class="form-control" name="idmedio" required>
                            <option value="">Seleccione...</option>
                            <?php if (!empty($medios)): ?>
                                <?php foreach ($medios as $medio): ?>
                                <option value="<?= $medio['idmedio'] ?>"><?= esc($medio['nombre']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Presupuesto *</label>
                        <input type="number" class="form-control" name="presupuesto" 
                               step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEstado(id) {
    if (confirm('¿Cambiar el estado de la campaña?')) {
        window.location.href = '<?= base_url('campanias/toggleEstado/') ?>' + id;
    }
}
</script>

<?= $this->endSection() ?>