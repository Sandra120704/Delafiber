<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="<?= base_url('crm-campanas/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('crm-campanas/zonas-index/' . $zona['id_campana']) ?>">Zonas</a></li>
                        <li class="breadcrumb-item active"><?= esc($zona['nombre_zona']) ?></li>
                    </ol>
                </nav>
                <h3 class="mb-1"> <?= esc($zona['nombre_zona']) ?></h3>
                <p class="text-muted mb-0">
                    Campaña: <strong><?= esc($zona['nombre_campana']) ?></strong>
                </p>
            </div>
            <div class="btn-group">
                <a href="<?= base_url('crm-campanas/mapa-campanas/' . $zona['id_campana']) ?>" class="btn btn-outline-primary">
                    <i class="icon-map"></i> Ver en Mapa
                </a>
                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalEditarZona">
                    <i class="icon-edit"></i> Editar
                </button>
            </div>
        </div>

        <!-- Información General -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-2" style="font-size: 2rem;">📊</div>
                        <h6 class="text-muted mb-1">Área</h6>
                        <h4 class="mb-0"><?= number_format($zona['area_km2'], 2) ?> km²</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-2" style="font-size: 2rem;">👥</div>
                        <h6 class="text-muted mb-1">Prospectos</h6>
                        <h4 class="mb-0"><?= $zona['total_prospectos'] ?? 0 ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-2" style="font-size: 2rem;">🎯</div>
                        <h6 class="text-muted mb-1">Agentes</h6>
                        <h4 class="mb-0"><?= $zona['agentes_asignados'] ?? 0 ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-2" style="font-size: 2rem;">
                            <?php if ($zona['prioridad'] === 'Alta'): ?>
                                🔴
                            <?php elseif ($zona['prioridad'] === 'Media'): ?>
                                🟡
                            <?php else: ?>
                                🔵
                            <?php endif; ?>
                        </div>
                        <h6 class="text-muted mb-1">Prioridad</h6>
                        <h4 class="mb-0"><?= esc($zona['prioridad']) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#prospectos">
                    <i class="icon-users"></i> Prospectos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#asignaciones">
                    <i class="icon-user-check"></i> Agentes Asignados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#metricas">
                    <i class="icon-trending-up"></i> Métricas
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab Prospectos -->
            <div class="tab-pane fade show active" id="prospectos">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Lista de Prospectos</h6>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalRegistrarInteraccion">
                                <i class="icon-plus"></i> Registrar Interacción
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($prospectos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaProspectos">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>Dirección</th>
                                        <th>Interacciones</th>
                                        <th>Última Interacción</th>
                                        <th>Resultado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prospectos as $prospecto): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($prospecto['nombres'] . ' ' . $prospecto['apellidos']) ?></strong>
                                        </td>
                                        <td><?= esc($prospecto['telefono']) ?></td>
                                        <td><?= esc($prospecto['correo']) ?></td>
                                        <td class="small"><?= esc($prospecto['direccion']) ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= $prospecto['total_interacciones'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td class="small">
                                            <?= !empty($prospecto['ultima_interaccion']) ? date('d/m/Y H:i', strtotime($prospecto['ultima_interaccion'])) : '-' ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($prospecto['ultimo_resultado'])): ?>
                                                <span class="badge badge-<?= $prospecto['ultimo_resultado'] === 'Convertido' ? 'success' : ($prospecto['ultimo_resultado'] === 'Interesado' ? 'warning' : 'secondary') ?>">
                                                    <?= esc($prospecto['ultimo_resultado']) ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="verHistorial(<?= $prospecto['idpersona'] ?>)">
                                                <i class="icon-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="nuevaInteraccion(<?= $prospecto['idpersona'] ?>)">
                                                <i class="icon-phone"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon-info"></i> No hay prospectos asignados a esta zona.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab Asignaciones -->
            <div class="tab-pane fade" id="asignaciones">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Agentes Asignados</h6>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalAsignarAgente">
                                <i class="icon-user-plus"></i> Asignar Agente
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($asignaciones)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Agente</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha Asignación</th>
                                        <th>Meta Contactos</th>
                                        <th>Interacciones</th>
                                        <th>Conversiones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($asignaciones as $asignacion): ?>
                                    <tr>
                                        <td><strong><?= esc($asignacion['agente_nombre']) ?></strong></td>
                                        <td><?= esc($asignacion['agente_correo']) ?></td>
                                        <td><?= esc($asignacion['rol_nombre']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($asignacion['fecha_asignacion'])) ?></td>
                                        <td><?= $asignacion['meta_contactos'] ?? 0 ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= $asignacion['interacciones_realizadas'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">
                                                <?= $asignacion['conversiones_logradas'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="desasignarAgente(<?= $asignacion['id_asignacion'] ?>)">
                                                <i class="icon-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="icon-alert-triangle"></i> No hay agentes asignados a esta zona.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab Métricas -->
            <div class="tab-pane fade" id="metricas">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Evolución de Métricas (Últimos 30 días)</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($metricas)): ?>
                        <canvas id="chartMetricas" height="100"></canvas>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon-info"></i> No hay métricas disponibles para esta zona.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Inicializar DataTable
    <?php if (!empty($prospectos)): ?>
    $(document).ready(function() {
        $('#tablaProspectos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[5, 'desc']]
        });
    });
    <?php endif; ?>

    // Gráfico de métricas
    <?php if (!empty($metricas)): ?>
    const ctx = document.getElementById('chartMetricas').getContext('2d');
    const metricas = <?= json_encode($metricas) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: metricas.map(m => m.fecha),
            datasets: [
                {
                    label: 'Contactados',
                    data: metricas.map(m => m.contactados),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)'
                },
                {
                    label: 'Interesados',
                    data: metricas.map(m => m.interesados),
                    borderColor: '#f39c12',
                    backgroundColor: 'rgba(243, 156, 18, 0.1)'
                },
                {
                    label: 'Convertidos',
                    data: metricas.map(m => m.convertidos),
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
    <?php endif; ?>

    // Funciones auxiliares
    function verHistorial(idProspecto) {
        window.location.href = `/personas/view/${idProspecto}`;
    }

    function nuevaInteraccion(idProspecto) {
        // Abrir modal de nueva interacción
        $('#modalRegistrarInteraccion').modal('show');
        $('#id_prospecto_interaccion').val(idProspecto);
    }

    function desasignarAgente(idAsignacion) {
        if (confirm('¿Estás seguro de desasignar este agente de la zona?')) {
            fetch(`/crm-campanas/desasignar-zona-agente/${idAsignacion}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('✅ Agente desasignado');
                    location.reload();
                } else {
                    alert('❌ Error: ' + result.message);
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
