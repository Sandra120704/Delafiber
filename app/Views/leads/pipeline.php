<?= $this->extend('Layouts/header') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/pipeline.css') ?>">

<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Pipeline de Ventas</h4>
                        <p class="card-description mb-0">Vista general de todos tus leads por etapa</p>
                    </div>
                    <div>
                        <a href="<?= base_url('leads/create') ?>" class="btn btn-primary">
                            <i class="ti-plus"></i> Nuevo Lead
                        </a>
                        <a href="<?= base_url('leads') ?>" class="btn btn-outline-secondary">
                            <i class="ti-list"></i> Vista Lista
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pipeline Kanban -->
                    <div class="pipeline-container">
                        <?php foreach ($pipeline as $columna): ?>
                        <div class="pipeline-column">
                            <!-- Header de la columna -->
                            <div class="column-header">
                                <h6 class="column-title"><?= esc($columna['etapa_nombre']) ?></h6>
                                <span class="lead-count badge badge-secondary"><?= $columna['total_leads'] ?></span>
                            </div>
                            <!-- Cards de leads -->
                            <div class="column-content" data-etapa="<?= $columna['etapa_id'] ?>">
                                <?php if (!empty($columna['leads'])): ?>
                                    <?php foreach ($columna['leads'] as $lead): ?>
                                    <div class="lead-card" data-lead-id="<?= $lead['idlead'] ?>">
                                        <div class="card-header-lead">
                                            <h6 class="lead-name"><?= esc($lead['nombres'] . ' ' . $lead['apellidos']) ?></h6>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted" data-toggle="dropdown">
                                                    <i class="ti-more-alt"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?= base_url('leads/view/' . $lead['idlead']) ?>">
                                                        <i class="ti-eye"></i> Ver detalles
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item quick-action" 
                                                       data-action="llamar" 
                                                       data-lead="<?= $lead['idlead'] ?>"
                                                       href="#">
                                                        <i class="ti-phone text-success"></i> Llamar
                                                    </a>
                                                    <a class="dropdown-item quick-action" 
                                                       data-action="whatsapp" 
                                                       data-lead="<?= $lead['idlead'] ?>"
                                                       href="#">
                                                        <i class="ti-comment text-primary"></i> WhatsApp
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body-lead">
                                            <p class="lead-phone">
                                                <i class="ti-phone text-muted"></i> <?= esc($lead['telefono']) ?>
                                            </p>
                                            <div class="lead-actions">
                                                <button class="btn btn-sm btn-outline-success quick-action" 
                                                        data-action="llamar" 
                                                        data-lead="<?= $lead['idlead'] ?>"
                                                        title="Llamar">
                                                    <i class="ti-phone"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary quick-action" 
                                                        data-action="whatsapp" 
                                                        data-lead="<?= $lead['idlead'] ?>"
                                                        title="WhatsApp">
                                                    <i class="ti-comment"></i>
                                                </button>
                                                <!-- Botón para avanzar etapa -->
                                                <?php if ($columna['etapa_nombre'] !== 'VENTA'): ?>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="avanzarEtapa(<?= $lead['idlead'] ?>)"
                                                        title="Avanzar etapa">
                                                    <i class="ti-arrow-right"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-column">
                                        <div class="empty-icon">
                                            <i class="ti-folder"></i>
                                        </div>
                                        <p class="text-muted small">Sin leads</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/pipeline.js') ?>"></script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
