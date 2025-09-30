<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
            <h3 class="mb-0">Editar Campaña</h3>
            <a href="<?= base_url('campanias') ?>" class="btn btn-outline-secondary">
                <i class="icon-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('campanias/update/' . $campania['idcampania']) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre de la Campaña *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= old('nombre', $campania['nombre']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo">Tipo de Campaña *</label>
                                <select class="form-control" id="tipo" name="tipo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Marketing Digital" <?= old('tipo', $campania['tipo'] ?? '') == 'Marketing Digital' ? 'selected' : '' ?>>Marketing Digital</option>
                                    <option value="Email Marketing" <?= old('tipo', $campania['tipo'] ?? '') == 'Email Marketing' ? 'selected' : '' ?>>Email Marketing</option>
                                    <option value="Publicidad" <?= old('tipo', $campania['tipo'] ?? '') == 'Publicidad' ? 'selected' : '' ?>>Publicidad</option>
                                    <option value="Redes Sociales" <?= old('tipo', $campania['tipo'] ?? '') == 'Redes Sociales' ? 'selected' : '' ?>>Redes Sociales</option>
                                    <option value="Eventos" <?= old('tipo', $campania['tipo'] ?? '') == 'Eventos' ? 'selected' : '' ?>>Eventos</option>
                                    <option value="Telemarketing" <?= old('tipo', $campania['tipo'] ?? '') == 'Telemarketing' ? 'selected' : '' ?>>Telemarketing</option>
                                    <option value="Otro" <?= old('tipo', $campania['tipo'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="presupuesto">Presupuesto (S/)</label>
                                <input type="number" class="form-control" id="presupuesto" name="presupuesto" 
                                       value="<?= old('presupuesto', $campania['presupuesto']) ?>" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3"><?= old('descripcion', $campania['descripcion']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="<?= old('fecha_inicio', $campania['fecha_inicio']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin">Fecha de Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="<?= old('fecha_fin', $campania['fecha_fin']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" 
                                   value="1" <?= old('activo', $campania['activo'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activo">
                                Campaña Activa
                            </label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('campanias') ?>" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-check"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validar que fecha fin sea mayor que fecha inicio
document.getElementById('fecha_fin').addEventListener('change', function() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = this.value;
    
    if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
        alert('La fecha de fin debe ser posterior a la fecha de inicio');
        this.value = '';
    }
});

// Cerrar alert con botón (sin jQuery)
document.querySelectorAll('.alert .close').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const alert = btn.closest('.alert');
        if (alert) alert.classList.remove('show');
        if (alert) alert.classList.add('d-none');
    });
});
</script>
<?= $this->endSection() ?>