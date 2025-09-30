<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <h4>Reportes - Prueba</h4>
        
        <div class="card">
            <div class="card-body">
                <h5>KPIs Básicos</h5>
                <ul>
                    <li>Total Leads: <?= $kpis['total_leads'] ?? 0 ?></li>
                    <li>Leads del Mes: <?= $kpis['leads_mes'] ?? 0 ?></li>
                    <li>Tareas Pendientes: <?= $kpis['tareas_pendientes'] ?? 0 ?></li>
                    <li>Campañas Activas: <?= $kpis['campanias_activas'] ?? 0 ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
