<?= $this->extend('layouts/base') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/leads/leads-view.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// Inicializa variables para evitar error 500
$lead = $lead ?? [];
$error = $error ?? null;
$seguimientos = $seguimientos ?? [];
$tareas = $tareas ?? [];
$campania = $campania ?? [];
$persona = $persona ?? [];
$etapas = $etapas ?? [];
$modalidades = $modalidades ?? [];
$historial = $historial ?? [];
?>

<div class="row" 
     data-lead-id="<?= $lead['idlead'] ?? '' ?>"
     data-lead-nombre="<?= esc(($lead['nombres'] ?? '') . ' ' . ($lead['apellidos'] ?? '')) ?>"
     data-lead-telefono="<?= esc($lead['telefono'] ?? '') ?>"
     data-lead-direccion="<?= esc($lead['direccion'] ?? 'Sin dirección') ?>"
     data-coordenadas="<?= esc($lead['coordenadas'] ?? '') ?>"
     <?php if (!empty($zona)): ?>
     data-zona-poligono='<?= $zona['poligono'] ?? '' ?>'
     data-zona-color="<?= $zona['color'] ?? '' ?>"
     data-zona-nombre="<?= esc($zona['nombre_zona'] ?? '') ?>"
     <?php endif; ?>>
    <div class="col-12">
        <!-- Encabezado con acciones -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= base_url('leads') ?>" class="btn btn-outline-secondary">
                    <i class="icon-arrow-left"></i> Volver a Leads
                </a>
            </div>
            <div>
                <a href="<?= base_url('leads/edit/' . $lead['idlead']) ?>" class="btn btn-warning">
                    <i class="icon-pencil"></i> Editar
                </a>
                <button class="btn btn-success" data-toggle="modal" data-target="#modalConvertir">
                    <i class="icon-check"></i> Convertir a Cliente
                </button>
                <button class="btn btn-danger" data-toggle="modal" data-target="#modalDescartar">
                    <i class="icon-close"></i> Descartar
                </button>
                
                <!-- Separador -->
                <span class="mx-2">|</span>
                
                <!-- Botones de Asignación y Comunicación -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary btn-sm" id="btnReasignar">
                        <i class="ti-reload"></i> Reasignar
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnSolicitarApoyo">
                        <i class="ti-help-alt"></i> Solicitar Apoyo
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="btnProgramar">
                        <i class="ti-alarm-clock"></i> Programar
                    </button>
                </div>
            </div>
        </div>

        <!-- Información del Lead -->
        <div class="row">
            <!-- Columna Izquierda: Información Principal -->
            <div class="col-md-8">
                <!-- Datos del Cliente -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php 
                            $nombreCompleto = ($lead['nombres'] ?? '') . ' ' . ($lead['apellidos'] ?? '');
                            $iniciales = strtoupper(substr($lead['nombres'] ?? 'L', 0, 1) . substr($lead['apellidos'] ?? 'L', 0, 1));
                            ?>
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:80px;height:80px;">
                                <h2 class="mb-0"><?= $iniciales ?></h2>
                            </div>
                            <div>
                                <h3 class="mb-1"><?= esc($nombreCompleto) ?></h3>
                                <p class="text-muted mb-0">DNI: <?= esc($lead['dni'] ?? 'Sin DNI') ?></p>
                                <span class="badge badge-<?= ($lead['estado'] ?? '') == 'Convertido' ? 'success' : (($lead['estado'] ?? '') == 'Descartado' ? 'danger' : 'info') ?>">
                                    <?= $lead['estado'] ?? 'Activo' ?>
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="icon-phone mr-2"></i>Teléfono:</strong><br>
                                <?= esc($lead['telefono']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="icon-envelope mr-2"></i>Correo:</strong><br>
                                <?= esc($lead['correo'] ?? 'No registrado') ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="icon-location-pin mr-2"></i>Dirección:</strong><br>
                                <?= esc($lead['direccion'] ?? 'No registrado') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="icon-map mr-2"></i>Distrito:</strong><br>
                                <?= esc($lead['distrito_nombre'] ?? 'No registrado') ?></p>
                            </div>
                        </div>

                        <?php if (!empty($lead['referencias'])): ?>
                        <p><strong><i class="icon-info mr-2"></i>Referencias:</strong><br>
                        <?= esc($lead['referencias']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información del Lead -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Lead</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Etapa Actual</label>
                                <h6><?= esc($lead['etapa_nombre'] ?? 'Sin etapa') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Origen</label>
                                <h6><?= esc($lead['origen_nombre'] ?? 'Sin origen') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Campaña</label>
                                <h6><?= esc($lead['campania_nombre'] ?? 'Sin campaña') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Vendedor Asignado</label>
                                <h6><?= esc(session()->get('user_name') ?? 'Sin asignar') ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Fecha de Registro</label>
                                <h6><?= isset($lead['created_at']) ? date('d/m/Y H:i', strtotime($lead['created_at'])) : 'No disponible' ?></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación en Mapa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📍 Ubicación en Mapa</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($lead['coordenadas'])): ?>
                            <div id="miniMapLead" style="height: 350px; width: 100%; border-radius: 8px;"></div>
                            
                            <?php if (!empty($zona)): ?>
                            <div class="alert alert-info mt-3 mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="icon-map-pin mr-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <strong>Zona asignada:</strong> <?= esc($zona['nombre_zona']) ?>
                                        <br>
                                        <small class="text-muted">Este lead está dentro de una zona de campaña activa</small>
                                        <br>
                                        <a href="<?= base_url('crm-campanas/zona-detalle/' . $zona['id_zona']) ?>" class="btn btn-sm btn-primary mt-2">
                                            <i class="icon-eye"></i> Ver Zona Completa
                                        </a>
                                        <a href="<?= base_url('crm-campanas/mapa-campanas') ?>?lead=<?= $lead['idlead'] ?>" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="icon-map"></i> Ver en Mapa General
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="icon-alert-triangle"></i> 
                                <strong>Sin zona asignada</strong>
                                <br>
                                <small>Este lead no está asignado a ninguna zona de campaña.</small>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex align-items-start">
                                    <i class="icon-alert-triangle mr-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <strong>Este lead no tiene coordenadas</strong>
                                        <br>
                                        <small class="text-muted">
                                            Para ver la ubicación en el mapa, necesitas agregar una dirección y geocodificarla.
                                        </small>
                                        <br>
                                        <button class="btn btn-sm btn-primary mt-2" onclick="geocodificarLeadAhora()">
                                            <i class="icon-map-pin"></i> Geocodificar Ahora
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Seguimientos -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Seguimientos</h5>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalSeguimiento">
                            <i class="icon-plus"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($seguimientos)): ?>
                            <div class="timeline">
                                <?php foreach ($seguimientos as $seg): ?>
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= esc($seg['usuario_nombre']) ?></strong>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($seg['fecha'])) ?></small>
                                        </div>
                                        <span class="badge badge-info"><?= esc($seg['modalidad_nombre']) ?></span>
                                        <p class="mt-2 mb-0"><?= nl2br(esc($seg['nota'])) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No hay seguimientos registrados</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Historial -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Historial de Cambios</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($historial)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Acción</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($historial as $h): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></td>
                                            <td><?= esc($h['usuario_nombre'] ?? 'Sistema') ?></td>
                                            <td><span class="badge badge-secondary"><?= esc($h['accion'] ?? 'Seguimiento') ?></span></td>
                                            <td><?= esc($h['descripcion'] ?? $h['nota'] ?? 'Sin descripción') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No hay historial</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Acciones Rápidas -->
            <div class="col-md-4">
                <!-- Cambiar Etapa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Cambiar Etapa</h5>
                    </div>
                    <div class="card-body">
                        <form id="formCambiarEtapa">
                            <div class="form-group">
                                <select class="form-control" id="nueva_etapa" name="idetapa">
                                    <?php foreach ($etapas as $etapa): ?>
                                    <option value="<?= $etapa['idetapa'] ?>" <?= $etapa['idetapa'] == $lead['idetapa'] ? 'selected' : '' ?>>
                                        <?= esc($etapa['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="nota" placeholder="Nota (opcional)" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Mover Etapa</button>
                        </form>
                    </div>
                </div>

                <!-- Tareas -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tareas</h5>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTarea">
                            <i class="icon-plus"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($tareas)): ?>
                            <div class="list-group">
                                <?php foreach ($tareas as $tarea): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= esc($tarea['titulo']) ?></strong>
                                        <span class="badge badge-<?= $tarea['prioridad'] == 'alta' ? 'danger' : 'warning' ?>">
                                            <?= esc($tarea['prioridad']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">Vence: <?= date('d/m/Y', strtotime($tarea['fecha_vencimiento'])) ?></small>
                                    <?php if ($tarea['estado'] != 'Completada'): ?>
                                    <button class="btn btn-sm btn-success mt-2" onclick="completarTarea(<?= $tarea['idtarea'] ?>)">
                                        Completar
                                    </button>
                                    <?php else: ?>
                                    <span class="badge badge-success">Completada</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">Sin tareas</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <a href="tel:<?= $lead['telefono'] ?>" class="btn btn-outline-primary btn-block mb-2">
                            <i class="icon-phone"></i> Llamar
                        </a>
                        <a href="https://wa.me/51<?= $lead['telefono'] ?>" target="_blank" class="btn btn-outline-success btn-block mb-2">
                            <i class="icon-social-whatsapp"></i> WhatsApp
                        </a>
                        <a href="mailto:<?= $lead['correo'] ?>" class="btn btn-outline-info btn-block">
                            <i class="icon-envelope"></i> Enviar Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Seguimiento -->
<div class="modal fade" id="modalSeguimiento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Seguimiento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formSeguimiento">
                <div class="modal-body">
                    <input type="hidden" name="idlead" value="<?= $lead['idlead'] ?>">
                    <div class="form-group">
                        <label>Tipo de Comunicación</label>
                        <select class="form-control" name="idmodalidad" required>
                            <?php foreach ($modalidades as $mod): ?>
                            <option value="<?= $mod['idmodalidad'] ?>"><?= esc($mod['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nota</label>
                        <textarea class="form-control" name="nota" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Crear Tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Tarea</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTarea">
                <div class="modal-body">
                    <input type="hidden" name="idlead" value="<?= $lead['idlead'] ?>">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prioridad</label>
                                <select class="form-control" name="prioridad">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input type="datetime-local" class="form-control" name="fecha_vencimiento" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Convertir -->
<div class="modal fade" id="modalConvertir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Convertir a Cliente</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('leads/convertir/' . $lead['idlead']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>¿Estás seguro de convertir este lead en cliente?</p>
                    <div class="form-group">
                        <label>Número de Contrato (opcional)</label>
                        <input type="text" class="form-control" name="numero_contrato">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Convertir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Descartar -->
<div class="modal fade" id="modalDescartar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Descartar Lead</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('leads/descartar/' . $lead['idlead']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Motivo del Descarte *</label>
                        <textarea class="form-control" name="motivo" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Descartar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAACo2qyElsl8RwIqW3x0peOA_20f7SEHA&libraries=geometry"></script>
<script src="<?= base_url('js/leads/leads-view.js') ?>"></script>

<!-- FUNCIONES DE ASIGNACIÓN - INLINE DIRECTO -->
<script type="text/javascript">
// ============================================
// FUNCIONES DE ASIGNACIÓN DE LEADS
// VERSIÓN: <?= time() ?>
// ============================================
console.log('🚀 INICIANDO CARGA DE FUNCIONES - VERSIÓN:', '<?= time() ?>');
console.log('📍 Ubicación: Inline en view.php');
console.log('⏰ Timestamp:', new Date().toISOString());

var usuariosDisponibles = [];
var baseUrl = '<?= base_url() ?>';

// Cargar usuarios disponibles
fetch(baseUrl + '/lead-asignacion/getUsuariosDisponibles', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        usuariosDisponibles = data.usuarios;
        console.log('✅ Usuarios cargados:', usuariosDisponibles.length);
    }
})
.catch(error => console.error('Error al cargar usuarios:', error));

// Función: Mostrar Modal Reasignar
window.mostrarModalReasignar = function(idlead) {
    console.log('🔄 Reasignar Lead ID:', idlead);
    
    var opcionesUsuarios = usuariosDisponibles.map(u => 
        '<option value="' + u.idusuario + '">' + u.nombre + ' - ' + u.turno + ' (' + u.leads_activos + ' leads)</option>'
    ).join('');
    
    var html = '<div class="modal fade" id="modalReasignar" tabindex="-1">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header bg-primary text-white">' +
        '<h5 class="modal-title">Reasignar Lead</h5>' +
        '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<form id="formReasignar">' +
        '<input type="hidden" name="idlead" value="' + idlead + '">' +
        '<div class="mb-3">' +
        '<label class="form-label">Asignar a:</label>' +
        '<select name="nuevo_usuario" class="form-select" required>' +
        '<option value="">Seleccionar usuario...</option>' +
        opcionesUsuarios +
        '</select>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="form-label">Motivo:</label>' +
        '<textarea name="motivo" class="form-control" rows="3"></textarea>' +
        '</div>' +
        '</form>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>' +
        '<button type="button" class="btn btn-primary" onclick="ejecutarReasignacion()">Reasignar</button>' +
        '</div>' +
        '</div></div></div>';
    
    $('#modalReasignar').remove();
    $('body').append(html);
    $('#modalReasignar').modal('show');
};

// Función: Ejecutar Reasignación
window.ejecutarReasignacion = function() {
    var formData = new FormData(document.getElementById('formReasignar'));
    
    if (!formData.get('nuevo_usuario')) {
        Swal.fire('Error', 'Debes seleccionar un usuario', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Reasignando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch(baseUrl + '/lead-asignacion/reasignar', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo completar la reasignación', 'error');
    });
};

// Función: Mostrar Modal Solicitar Apoyo
window.mostrarModalSolicitarApoyo = function(idlead) {
    console.log('🆘 Solicitar Apoyo Lead ID:', idlead);
    
    var opcionesUsuarios = usuariosDisponibles.map(u => 
        '<option value="' + u.idusuario + '">' + u.nombre + ' - ' + u.turno + '</option>'
    ).join('');
    
    var html = '<div class="modal fade" id="modalSolicitarApoyo" tabindex="-1">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header bg-warning">' +
        '<h5 class="modal-title">Solicitar Apoyo</h5>' +
        '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<form id="formSolicitarApoyo">' +
        '<input type="hidden" name="idlead" value="' + idlead + '">' +
        '<div class="mb-3">' +
        '<label class="form-label">Solicitar apoyo de:</label>' +
        '<select name="usuario_apoyo" class="form-select" required>' +
        '<option value="">Seleccionar usuario...</option>' +
        opcionesUsuarios +
        '</select>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="form-label">Mensaje:</label>' +
        '<textarea name="mensaje" class="form-control" rows="4" required></textarea>' +
        '</div>' +
        '<div class="form-check">' +
        '<input class="form-check-input" type="checkbox" name="urgente" id="urgente">' +
        '<label class="form-check-label" for="urgente">Marcar como URGENTE</label>' +
        '</div>' +
        '</form>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>' +
        '<button type="button" class="btn btn-warning" onclick="ejecutarSolicitarApoyo()">Enviar</button>' +
        '</div>' +
        '</div></div></div>';
    
    $('#modalSolicitarApoyo').remove();
    $('body').append(html);
    $('#modalSolicitarApoyo').modal('show');
};

// Función: Ejecutar Solicitar Apoyo
window.ejecutarSolicitarApoyo = function() {
    var formData = new FormData(document.getElementById('formSolicitarApoyo'));
    
    fetch(baseUrl + '/lead-asignacion/solicitarApoyo', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Solicitud enviada',
                timer: 2000
            });
            $('#modalSolicitarApoyo').modal('hide');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo enviar la solicitud', 'error');
    });
};

// Función: Mostrar Modal Programar Seguimiento
window.mostrarModalProgramarSeguimiento = function(idlead) {
    console.log('⏰ Programar Seguimiento Lead ID:', idlead);
    
    var hoy = new Date().toISOString().split('T')[0];
    
    var html = '<div class="modal fade" id="modalProgramarSeguimiento" tabindex="-1">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header bg-success text-white">' +
        '<h5 class="modal-title">Programar Seguimiento</h5>' +
        '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<form id="formProgramarSeguimiento">' +
        '<input type="hidden" name="idlead" value="' + idlead + '">' +
        '<div class="row">' +
        '<div class="col-md-6 mb-3">' +
        '<label class="form-label">Fecha:</label>' +
        '<input type="date" name="fecha" class="form-control" required min="' + hoy + '">' +
        '</div>' +
        '<div class="col-md-6 mb-3">' +
        '<label class="form-label">Hora:</label>' +
        '<input type="time" name="hora" class="form-control" required>' +
        '</div>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="form-label">Tipo:</label>' +
        '<select name="tipo" class="form-select" required>' +
        '<option value="Llamada">📞 Llamada</option>' +
        '<option value="WhatsApp">💬 WhatsApp</option>' +
        '<option value="Visita">🏠 Visita</option>' +
        '<option value="Email">📧 Email</option>' +
        '</select>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="form-label">Notas:</label>' +
        '<textarea name="nota" class="form-control" rows="3" required></textarea>' +
        '</div>' +
        '</form>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>' +
        '<button type="button" class="btn btn-success" onclick="ejecutarProgramarSeguimiento()">Programar</button>' +
        '</div>' +
        '</div></div></div>';
    
    $('#modalProgramarSeguimiento').remove();
    $('body').append(html);
    $('#modalProgramarSeguimiento').modal('show');
};

// Función: Ejecutar Programar Seguimiento
window.ejecutarProgramarSeguimiento = function() {
    var formData = new FormData(document.getElementById('formProgramarSeguimiento'));
    
    fetch(baseUrl + '/lead-asignacion/programarSeguimiento', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Seguimiento programado',
                timer: 2000
            });
            $('#modalProgramarSeguimiento').modal('hide');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo programar el seguimiento', 'error');
    });
};

console.log('✅ Funciones de asignación cargadas correctamente');

// ============================================
// ASIGNAR EVENTOS A LOS BOTONES
// ============================================
var leadId = <?= $lead['idlead'] ?>;

document.getElementById('btnReasignar').addEventListener('click', function() {
    console.log('🔄 Click en Reasignar');
    mostrarModalReasignar(leadId);
});

document.getElementById('btnSolicitarApoyo').addEventListener('click', function() {
    console.log('🆘 Click en Solicitar Apoyo');
    mostrarModalSolicitarApoyo(leadId);
});

document.getElementById('btnProgramar').addEventListener('click', function() {
    console.log('⏰ Click en Programar');
    mostrarModalProgramarSeguimiento(leadId);
});

console.log('🎯 Eventos asignados a los botones');

// ============================================
// VERIFICACIÓN INMEDIATA (sin timeout)
// ============================================
console.log('🔍 VERIFICACIÓN INMEDIATA:');
console.log('  window.mostrarModalReasignar:', typeof window.mostrarModalReasignar);
console.log('  window.mostrarModalSolicitarApoyo:', typeof window.mostrarModalSolicitarApoyo);
console.log('  window.mostrarModalProgramarSeguimiento:', typeof window.mostrarModalProgramarSeguimiento);

if (typeof window.mostrarModalReasignar === 'function') {
    console.log('✅✅✅ TODAS LAS FUNCIONES DISPONIBLES ✅✅✅');
    console.log('👉 Los botones deben funcionar ahora');
    
    // Mostrar mensaje de éxito en la página
    setTimeout(function() {
        Swal.fire({
            icon: 'success',
            title: '¡Sistema Cargado!',
            text: 'Los botones de asignación están listos para usar',
            timer: 2000,
            showConfirmButton: false
        });
    }, 500);
} else {
    console.error('❌❌❌ ERROR: FUNCIONES NO DISPONIBLES ❌❌❌');
    console.error('🔄 SOLUCIÓN: Presiona Ctrl+Shift+Delete y limpia el caché');
    
    // Mostrar alerta grande
    Swal.fire({
        icon: 'error',
        title: 'Caché del Navegador',
        html: '<strong>Las funciones no se cargaron correctamente.</strong><br><br>' +
              'Por favor, limpia el caché:<br>' +
              '1. Presiona <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>Delete</kbd><br>' +
              '2. Selecciona "Archivos en caché"<br>' +
              '3. Click en "Borrar datos"<br>' +
              '4. Recarga esta página',
        showConfirmButton: true,
        confirmButtonText: 'Entendido'
    });
}
</script>
<?= $this->endSection() ?>

<?= $this->include('Layouts/footer') ?>