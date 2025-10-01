<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container">
    <h2><?= esc($title) ?></h2>
    <p>Aquí se mostrarán las estadísticas de servicios.</p>
    <!-- Agrega gráficos o tablas aquí -->
</div>
<?= $this->endSection() ?>
