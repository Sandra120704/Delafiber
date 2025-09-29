<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Gestión de Leads</h4>
                    <div>
                        <a href="<?= base_url('leads/exportar') ?>" class="btn btn-outline-success">
                            <i class="icon-download"></i> Exportar
                        </a>
                        <a href="<?= base_url('leads/create') ?>" class="btn btn-primary">
                            <i class="icon-plus"></i> Nuevo Lead
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <form action="<?= base_url('leads') ?>" method="GET" id="filtrosForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Buscar</label>
                                        <input type="text" class="form-control" name="busqueda" 
                                               placeholder="Nombre, DNI, teléfono..." 
                                               value="<?= $filtros_aplicados['busqueda'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Etapa</label>
                                        <select class="form-control" name="etapa">
                                            <option value="">Todas</option>
                                            <?php foreach ($etapas as $etapa): ?>
                                            <option value="<?= $etapa['idetapa'] ?>" 
                                                    <?= (isset($filtros_aplicados['idetapa']) && $filtros_aplicados['idetapa'] == $etapa['idetapa']) ? 'selected' : '' ?>>
                                                <?= esc($etapa['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Campaña</label>
                                        <select class="form-control" name="campania">
                                            <option value="">Todas</option>
                                            <?php foreach ($campanias as $campania): ?>
                                            <option value="<?= $campania['idcampania'] ?>"
                                                    <?= (isset($filtros_aplicados['idcampania']) && $filtros_aplicados['idcampania'] == $campania['idcampania']) ? 'selected' : '' ?>>
                                                <?= esc($campania['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control" name="estado">
                                            <option value="">Activos</option>
                                            <option value="Convertido" <?= (isset($filtros_aplicados['estado']) && $filtros_aplicados['estado'] == 'Convertido') ? 'selected' : '' ?>>Convertidos</option>
                                            <option value="Descartado" <?= (isset($filtros_aplicados['estado']) && $filtros_aplicados['estado'] == 'Descartado') ? 'selected' : '' ?>>Descartados</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="<?= base_url('leads') ?>" class="btn btn-light">Limpiar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Leads -->
                <!-- Solución para error DataTables: revisa que la tabla tenga el mismo número de columnas en <thead> y <tbody>, y que no haya celdas vacías o mal formadas. Además, si usas AJAX en DataTables, asegúrate que el JSON devuelto tenga la estructura esperada. -->

                <table id="tableLeads" class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Campaña</th>
                            <th>Etapa</th>
                            <th>Origen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><?= $lead['idlead'] ?></td>
                            <td><?= esc($lead['nombres']) ?> <?= esc($lead['apellidos']) ?></td>
                            <td><?= esc($lead['telefono']) ?></td>
                            <td><?= esc($lead['campania']) ?></td>
                            <td><?= esc($lead['etapa']) ?></td>
                            <td><?= esc($lead['origen']) ?></td>
                            <td>
                                <!-- Acciones aquí -->
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Resumen -->
                <div class="mt-3">
                    <p class="text-muted">Total de leads: <strong><?= count($leads) ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#tableLeads').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[0, "desc"]], // Ordenar por la primera columna   
        "pageLength": 25,
        "columnDefs": [
            { "orderable": false, "targets": 6 } // Última columna (Acciones) no ordenable
        ]
    });
});
</script>


<?= $this->endSection() ?>
<?= $this->include('Layouts/footer') ?>