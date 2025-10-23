<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('leads') ?>">Leads</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('leads/view/' . $lead['idlead']) ?>">Lead #<?= $lead['idlead'] ?></a></li>
                    <li class="breadcrumb-item active">Convertir a Cliente</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información del Lead -->
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Información del Lead</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Cliente:</strong><br>
                        <?= esc($lead['nombres'] . ' ' . $lead['apellidos']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>DNI:</strong><br>
                        <?= esc($lead['dni']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Teléfono:</strong><br>
                        <?= esc($lead['telefono']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Correo:</strong><br>
                        <?= esc($lead['correo'] ?? 'No especificado') ?>
                    </div>
                    <div class="mb-3">
                        <strong>Dirección:</strong><br>
                        <?= esc($lead['direccion']) ?>
                    </div>
                    <?php if (!empty($lead['referencias'])): ?>
                    <div class="mb-3">
                        <strong>Referencias:</strong><br>
                        <?= esc($lead['referencias']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($lead['coordenadas_servicio'])): ?>
                    <div class="mb-3">
                        <strong>Coordenadas GPS:</strong><br>
                        <small class="text-muted"><?= esc($lead['coordenadas_servicio']) ?></small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> <strong>Importante:</strong><br>
                Al convertir este lead, se creará automáticamente:
                <ul class="mb-0 mt-2">
                    <li>Registro en tb_personas (si no existe)</li>
                    <li>Registro en tb_clientes</li>
                    <li>Contrato en tb_contratos</li>
                </ul>
            </div>
        </div>

        <!-- Formulario de Conversión -->
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Datos del Contrato</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('leads/convertirACliente/' . $lead['idlead']) ?>" method="POST" id="formConvertir">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="id_paquete">Paquete a Contratar <span class="text-danger">*</span></label>
                            <select name="id_paquete" id="id_paquete" class="form-control" required>
                                <option value="">Seleccione un paquete</option>
                                <?php foreach ($paquetes as $paquete): ?>
                                    <option value="<?= $paquete['id_paquete'] ?>" 
                                            data-precio="<?= $paquete['precio'] ?>"
                                            <?= old('id_paquete') == $paquete['id_paquete'] ? 'selected' : '' ?>>
                                        <?= esc($paquete['paquete']) ?> - S/ <?= number_format($paquete['precio'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Selecciona el plan de servicio que contratará el cliente</small>
                        </div>

                        <div class="form-group">
                            <label for="id_sector">Sector <span class="text-danger">*</span></label>
                            <select name="id_sector" id="id_sector" class="form-control" required>
                                <option value="">Seleccione un sector</option>
                                <?php foreach ($sectores as $sector): ?>
                                    <option value="<?= $sector['id_sector'] ?>"
                                            <?= old('id_sector') == $sector['id_sector'] ? 'selected' : '' ?>>
                                        <?= esc($sector['sector']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Zona geográfica donde se instalará el servicio</small>
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio del Servicio <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   id="fecha_inicio" 
                                   class="form-control" 
                                   value="<?= old('fecha_inicio', date('Y-m-d')) ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   required>
                            <small class="form-text text-muted">Fecha en la que iniciará el servicio</small>
                        </div>

                        <div class="form-group">
                            <label for="nota_adicional">Notas Adicionales</label>
                            <textarea name="nota_adicional" 
                                      id="nota_adicional" 
                                      class="form-control" 
                                      rows="4"
                                      placeholder="Observaciones, condiciones especiales, etc."><?= old('nota_adicional') ?></textarea>
                            <small class="form-text text-muted">Información adicional que se agregará al contrato</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Confirmación:</strong><br>
                            ¿Estás seguro de convertir este lead a cliente? Esta acción:
                            <ul class="mb-0 mt-2">
                                <li>Creará un contrato en el sistema de gestión</li>
                                <li>Marcará el lead como "convertido"</li>
                                <li>No se puede deshacer</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-check-circle"></i> Convertir a Cliente y Crear Contrato
                            </button>
                            <a href="<?= base_url('leads/view/' . $lead['idlead']) ?>" class="btn btn-secondary btn-lg btn-block">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Confirmación antes de enviar
    $('#formConvertir').on('submit', function(e) {
        e.preventDefault();
        
        const paquete = $('#id_paquete option:selected').text();
        const sector = $('#id_sector option:selected').text();
        
        Swal.fire({
            title: '¿Confirmar conversión?',
            html: `
                <div class="text-left">
                    <p><strong>Cliente:</strong> <?= esc($lead['nombres'] . ' ' . $lead['apellidos']) ?></p>
                    <p><strong>Paquete:</strong> ${paquete}</p>
                    <p><strong>Sector:</strong> ${sector}</p>
                    <hr>
                    <p class="text-danger"><small>Esta acción creará un contrato en el sistema de gestión y no se puede deshacer.</small></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, convertir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Creando contrato en el sistema de gestión',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar formulario
                this.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
