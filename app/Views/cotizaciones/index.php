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
                                <option value="Borrador">Borrador</option>
                                <option value="Enviada">Enviada</option>
                                <option value="Aceptada">Aceptada</option>
                                <option value="Rechazada">Rechazada</option>
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
                                                    <strong><?= esc($cotizacion['servicios'] ?? 'Sin servicios') ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= esc($cotizacion['numero_cotizacion'] ?? '') ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>S/ <?= number_format($cotizacion['total'] ?? 0, 2) ?></strong>
                                                    <?php if (isset($cotizacion['subtotal']) && $cotizacion['subtotal'] > 0): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            Subtotal: S/ <?= number_format($cotizacion['subtotal'], 2) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = [
                                                    'Borrador' => 'bg-secondary',
                                                    'Enviada' => 'bg-info',
                                                    'Aceptada' => 'bg-success',
                                                    'Rechazada' => 'bg-danger'
                                                ];
                                                ?>
                                                <span class="badge <?= $badgeClass[$cotizacion['estado']] ?? 'bg-secondary' ?>">
                                                    <?= esc($cotizacion['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (isset($cotizacion['fecha_envio']) && $cotizacion['fecha_envio']): ?>
                                                    <small>Enviada: <?= date('d/m/Y', strtotime($cotizacion['fecha_envio'])) ?></small>
                                                <?php elseif (isset($cotizacion['fecha_respuesta']) && $cotizacion['fecha_respuesta']): ?>
                                                    <small>Respuesta: <?= date('d/m/Y', strtotime($cotizacion['fecha_respuesta'])) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">-</small>
                                                <?php endif; ?>
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
                                                    
                                                    <?php if ($cotizacion['estado'] === 'Borrador' || $cotizacion['estado'] === 'Enviada'): ?>
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
                                                                <?php if ($cotizacion['estado'] === 'Borrador'): ?>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="cambiarEstado(<?= $cotizacion['idcotizacion'] ?>, 'Enviada')">
                                                                        <i class="ti-email text-info me-2"></i>Enviar
                                                                    </a>
                                                                </li>
                                                                <?php endif; ?>
                                                                <?php if ($cotizacion['estado'] === 'Enviada'): ?>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="cambiarEstado(<?= $cotizacion['idcotizacion'] ?>, 'Aceptada')">
                                                                        <i class="ti-check text-success me-2"></i>Aceptar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="cambiarEstado(<?= $cotizacion['idcotizacion'] ?>, 'Rechazada')">
                                                                        <i class="ti-close text-danger me-2"></i>Rechazar
                                                                    </a>
                                                                </li>
                                                                <?php endif; ?>
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
    const mensajes = {
        'Enviada': 'enviar',
        'Aceptada': 'aceptar',
        'Rechazada': 'rechazar'
    };
    
    if (!confirm(`¿Está seguro de ${mensajes[nuevoEstado] || 'cambiar'} esta cotización?`)) {
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
