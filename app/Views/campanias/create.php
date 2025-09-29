<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Nueva Campaña</h3>
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
                <form action="<?= base_url('campanias/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre de la Campaña *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= old('nombre') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo">Tipo de Campaña *</label>
                                <select class="form-control" id="tipo" name="tipo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Marketing Digital" <?= old('tipo') == 'Marketing Digital' ? 'selected' : '' ?>>Marketing Digital</option>
                                    <option value="Email Marketing" <?= old('tipo') == 'Email Marketing' ? 'selected' : '' ?>>Email Marketing</option>
                                    <option value="Publicidad" <?= old('tipo') == 'Publicidad' ? 'selected' : '' ?>>Publicidad</option>
                                    <option value="Redes Sociales" <?= old('tipo') == 'Redes Sociales' ? 'selected' : '' ?>>Redes Sociales</option>
                                    <option value="Eventos" <?= old('tipo') == 'Eventos' ? 'selected' : '' ?>>Eventos</option>
                                    <option value="Telemarketing" <?= old('tipo') == 'Telemarketing' ? 'selected' : '' ?>>Telemarketing</option>
                                    <option value="Otro" <?= old('tipo') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="presupuesto">Presupuesto (S/)</label>
                                <input type="number" class="form-control" id="presupuesto" name="presupuesto" 
                                       value="<?= old('presupuesto', '0.00') ?>" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3"><?= old('descripcion') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="<?= old('fecha_inicio', date('Y-m-d')) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin">Fecha de Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="<?= old('fecha_fin') ?>">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('campanias') ?>" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-check"></i> Crear Campaña
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
</script>

<?= $this->endSection() ?>