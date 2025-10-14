<?= $this->extend('layouts/base') ?>

<?= $this->section('styles') ?>
<!-- Calendario CSS -->
<link rel="stylesheet" href="<?= base_url('css/tareas/calendario.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0"> Calendario de Tareas</h3>
                    <p class="text-muted mb-0">Gestiona tus tareas y seguimientos</p>
                </div>
                <div>
                    <a href="<?= base_url('tareas') ?>" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="ti-list"></i> Vista Lista
                    </a>
                    <button class="btn btn-primary btn-sm" id="btnNuevaTarea">
                        <i class="ti-plus"></i> Nueva Tarea
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leyenda de colores -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <small class="text-muted me-2">Prioridad:</small>
                        <span class="badge" style="background-color: #dc3545;">Urgente</span>
                        <span class="badge" style="background-color: #fd7e14;">Alta</span>
                        <span class="badge" style="background-color: #007bff;">Media</span>
                        <span class="badge" style="background-color: #6c757d;">Baja</span>
                        <span class="badge" style="background-color: #28a745;">Completada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTareaTitle">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTarea">
                    <input type="hidden" id="idtarea" name="idtarea">
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_tarea" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo_tarea" name="tipo_tarea" required>
                                <option value="llamada">Llamada</option>
                                <option value="reunion">Reunión</option>
                                <option value="seguimiento">Seguimiento</option>
                                <option value="instalacion">Instalación</option>
                                <option value="visita">Visita</option>
                                <option value="email">Email</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="prioridad" class="form-label">Prioridad *</label>
                            <select class="form-select" id="prioridad" name="prioridad" required>
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha y Hora *</label>
                            <input type="datetime-local" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="idlead" class="form-label">Lead Asociado</label>
                            <select class="form-select" id="idlead" name="idlead">
                                <option value="">Sin lead</option>
                                <?php foreach ($leads as $lead): ?>
                                    <option value="<?= $lead['idlead'] ?>">
                                        <?= esc($lead['cliente'] ?? 'Lead #' . $lead['idlead']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3" id="estadoContainer" style="display: none;">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Completada">Completada</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnEliminarTarea" style="display: none;">
                    <i class="ti-trash"></i> Eliminar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarTarea">
                    <i class="ti-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Calendario JS -->
<script src="<?= base_url('js/tareas/calendario.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
