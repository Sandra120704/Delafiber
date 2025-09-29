<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Registrar Nuevo Lead</h4>
                    <a href="<?= base_url('leads') ?>" class="btn btn-outline-secondary">
                        <i class="icon-arrow-left"></i> Volver
                    </a>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form id="formLead" action="<?= base_url('leads/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- Aquí va todo tu formulario (PASO 1 y PASO 2) -->
                    <?php include('partials/form_lead.php'); ?>
                    
                    <div class="text-right">
                        <a href="<?= base_url('leads') ?>" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                            <i class="icon-check"></i> Guardar Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.rotating {
    animation: rotate 1s linear infinite;
}
@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<!-- Cargar JS desde archivo externo -->
<script src="<?= base_url('js/lead-create.js') ?>"></script>

<?= $this->endSection() ?>
