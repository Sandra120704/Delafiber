<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="ti-receipt me-2"></i>Cotizaciones
                    </h4>
                    <a href="<?= base_url('cotizaciones/create') ?>" class="btn btn-primary">
                        <i class="ti-plus me-1"></i>Nueva Cotización
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="filtro-estado">
                                <option value="">Todos los estados</option>
                                <option value="vigente">Vigente</option>
                                <option value="aceptada">Aceptada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="vencida">Vencida</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="buscar-cliente" placeholder="Buscar cliente...">
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                                <i class="ti-refresh me-1"></i>Limpiar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de cotizaciones -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Vigencia</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cotizaciones)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti-receipt" style="font-size: 48px;"></i>
                                                <p class="mt-2">No hay cotizaciones registradas</p>
                                                <a href="<?= base_url('cotizaciones/create') ?>" class="btn btn-primary">
                                                    Crear primera cotización
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cotizaciones as $cotizacion): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#<?= $cotizacion['idcotizacion'] ?></span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= esc($cotizacion['cliente_nombre']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= esc($cotizacion['cliente_telefono']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= esc($cotizacion['servicio_nombre']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= esc($cotizacion['velocidad']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>S/ <?= number_format($cotizacion['precio_cotizado'], 2) ?></strong>
                                                    <?php if ($cotizacion['descuento_aplicado'] > 0): ?>
                                                        <br>
                                                        <small class="text-success">
                                                            -<?= $cotizacion['descuento_aplicado'] ?>% desc.
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = [
                                                    'vigente' => 'bg-success',
                                                    'aceptada' => 'bg-primary',
                                                    'rechazada' => 'bg-danger',
                                                    'vencida' => 'bg-secondary'
                                                ];
                                                ?>
                                                <span class="badge <?= $badgeClass[$cotizacion['estado']] ?? 'bg-secondary' ?>">
                                                    <?= ucfirst($cotizacion['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $fechaVencimiento = date('Y-m-d', strtotime($cotizacion['created_at'] . ' + ' . $cotizacion['vigencia_dias'] . ' days'));
                                                $diasRestantes = (strtotime($fechaVencimiento) - time()) / (60 * 60 * 24);
                                                ?>
                                                <div>
                                                    <small><?= date('d/m/Y', strtotime($fechaVencimiento)) ?></small>
                                                    <?php if ($cotizacion['estado'] === 'vigente'): ?>
                                                        <br>
                                                        <?php if ($diasRestantes > 0): ?>
                                                            <small class="text-warning">
                                                                <?= ceil($diasRestantes) ?> días
                                                            </small>
                                                        <?php else: ?>
                                                            <small class="text-danger">Vencida</small>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y H:i', strtotime($cotizacion['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('cotizaciones/show/' . $cotizacion['idcotizacion']) ?>" 
                                                       class="btn btn-sm btn-outline-info" title="Ver detalles">
                                                        <i class="ti-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($cotizacion['estado'] === 'vigente'): ?>
                                                        <a href="<?= base_url('cotizaciones/edit/' . $cotizacion['idcotizacion']) ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                                            <i class="ti-pencil"></i>
                                                        </a>
                                                        
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                    data-bs-toggle="dropdown" title="Cambiar estado">
                                                                <i class="ti-settings"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="cambiarEstado(<?= $cotizacion['idcotizacion'] ?>, 'aceptada')">
                                                                        <i class="ti-check text-success me-2"></i>Aceptar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="cambiarEstado(<?= $cotizacion['idcotizacion'] ?>, 'rechazada')">
                                                                        <i class="ti-close text-danger me-2"></i>Rechazar
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?= base_url('cotizaciones/pdf/' . $cotizacion['idcotizacion']) ?>" 
                                                       class="btn btn-sm btn-outline-danger" title="Descargar PDF" target="_blank">
                                                        <i class="ti-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filtros en tiempo real
document.getElementById('filtro-estado').addEventListener('change', function() {
    filtrarTabla();
});

document.getElementById('buscar-cliente').addEventListener('keyup', function() {
    filtrarTabla();
});

function filtrarTabla() {
    const estadoFiltro = document.getElementById('filtro-estado').value.toLowerCase();
    const clienteFiltro = document.getElementById('buscar-cliente').value.toLowerCase();
    const filas = document.querySelectorAll('tbody tr');

    filas.forEach(function(fila) {
        if (fila.cells.length === 1) return; // Skip empty state row
        
        const estado = fila.cells[4].textContent.toLowerCase();
        const cliente = fila.cells[1].textContent.toLowerCase();
        
        const mostrarEstado = !estadoFiltro || estado.includes(estadoFiltro);
        const mostrarCliente = !clienteFiltro || cliente.includes(clienteFiltro);
        
        fila.style.display = (mostrarEstado && mostrarCliente) ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('filtro-estado').value = '';
    document.getElementById('buscar-cliente').value = '';
    filtrarTabla();
}

// Cambiar estado de cotización
function cambiarEstado(idcotizacion, nuevoEstado) {
    if (!confirm(`¿Está seguro de ${nuevoEstado === 'aceptada' ? 'aceptar' : 'rechazar'} esta cotización?`)) {
        return;
    }

    fetch(`<?= base_url('cotizaciones/cambiarEstado') ?>/${idcotizacion}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `estado=${nuevoEstado}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar el estado');
    });
}
</script>

<?= $this->endSection() ?>
