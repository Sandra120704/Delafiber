<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/leads/create.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/leads/toast-notifications.css') ?>">

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

                <!-- Indicador de Progreso -->
                <div class="mb-4">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" id="progressBar" role="progressbar" style="width: 50%"></div>
                    </div>
                    <div class="text-center mt-2">
                        <span class="badge badge-primary" id="stepIndicator">Paso 1 de 2</span>
                    </div>
                </div>

                <form id="formLead" action="<?= base_url('leads/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- ============================================ -->
                    <!-- PASO 1: CLIENTE -->
                    <!-- ============================================ -->
                    <div id="paso1">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="icon-user"></i> Paso 1: Datos del Cliente</h5>
                            </div>
                            <div class="card-body">
                                <!-- Búsqueda rápida -->
                                <div class="alert alert-info mb-4">
                                    <strong><i class="icon-magnifier"></i> ¿Cliente existente?</strong> Busca por teléfono o DNI para evitar duplicados
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="buscar_telefono">Buscar por Teléfono</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="buscar_telefono" 
                                                   placeholder="9 dígitos" maxlength="9">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" type="button" id="btnBuscarTelefono">
                                                    <i class="icon-magnifier"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="dni">O buscar por DNI</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="dni" name="dni" 
                                                   placeholder="8 dígitos" maxlength="8">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="btnBuscarDni">
                                                    <i class="icon-magnifier"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                        <div id="dni-loading" class="text-primary mt-2" style="display:none;">
                                            <i class="icon-refresh rotating"></i> Consultando RENIEC...
                                        </div>
                                    </div>
                                </div>

                                <!-- Resultado de búsqueda -->
                                <div id="resultado-busqueda" style="display:none;"></div>

                                <hr>

                                <!-- Formulario de datos -->
                                <input type="hidden" id="idpersona" name="idpersona" value="">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombres">Nombres *</label>
                                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apellidos">Apellidos *</label>
                                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono *</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" 
                                                   maxlength="9" placeholder="9XXXXXXXX" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="correo">Correo Electrónico</label>
                                            <input type="email" class="form-control" id="correo" name="correo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones Paso 1 -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('leads') ?>" class="btn btn-light">
                                <i class="icon-close"></i> Cancelar
                            </a>
                            <button type="button" class="btn btn-primary btn-lg" id="btnSiguiente">
                                Siguiente <i class="icon-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- PASO 2: SOLICITUD DE SERVICIO -->
                    <!-- ============================================ -->
                    <div id="paso2" style="display:none;">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="icon-home"></i> Paso 2: ¿Dónde Instalará el Servicio?</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning mb-4">
                                    <strong><i class="icon-info"></i> Importante:</strong> Un cliente puede tener múltiples solicitudes en diferentes ubicaciones (casa, negocio, etc.)
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tipo_solicitud">Tipo de Instalación *</label>
                                            <select class="form-control" id="tipo_solicitud" name="tipo_solicitud" required>
                                                <option value="">Seleccione</option>
                                                <option value="Casa">🏠 Casa / Hogar</option>
                                                <option value="Negocio">🏪 Negocio / Empresa</option>
                                                <option value="Oficina">🏢 Oficina</option>
                                                <option value="Otro">📍 Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="iddistrito">Distrito *</label>
                                            <select class="form-control" id="iddistrito" name="iddistrito" required>
                                                <option value="">Seleccione</option>
                                                <?php if (!empty($distritos) && is_array($distritos)): ?>
                                                    <?php foreach ($distritos as $distrito): ?>
                                                        <option value="<?= $distrito['iddistrito'] ?>">
                                                            <?= esc($distrito['nombre']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor para mensaje de cobertura de zonas -->
                                <div id="alerta-cobertura-zona" style="display: none;"></div>

                                <div class="form-group">
                                    <label for="direccion">Dirección de Instalación *</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required
                                           placeholder="Ej: Av. Principal 123, Chincha Alta">
                                </div>

                                <div class="form-group">
                                    <label for="referencias">Referencias de Ubicación</label>
                                    <textarea class="form-control" id="referencias" name="referencias" rows="2"
                                              placeholder="Ej: Frente al parque, cerca del mercado"></textarea>
                                </div>

                                <hr>

                                <!-- Origen y Campaña -->
                                <h6 class="mb-3"><i class="icon-chart"></i> Origen del Contacto</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="idorigen">¿Cómo nos conoció? *</label>
                                            <select class="form-control" id="idorigen" name="idorigen" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($origenes as $origen): ?>
                                                <option value="<?= $origen['idorigen'] ?>" data-nombre="<?= esc($origen['nombre']) ?>">
                                                    <?= esc($origen['nombre']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Campos dinámicos aparecerán aquí -->
                                        <div id="campos-dinamicos-origen"></div>
                                        
                                        <!-- Campo de campaña oculto (solo para referencia de opciones) -->
                                        <div style="display: none;">
                                            <select class="form-control" id="idcampania" name="idcampania">
                                                <option value="">Ninguna</option>
                                                <?php foreach ($campanias as $campania): ?>
                                                <option value="<?= $campania['idcampania'] ?>">
                                                    <?= esc($campania['nombre']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="idmodalidad">Medio de Contacto Inicial *</label>
                                            <select class="form-control" id="idmodalidad" name="idmodalidad" required>
                                                <option value="">Seleccione</option>
                                                <?php if (isset($modalidades) && is_array($modalidades)): ?>
                                                    <?php foreach ($modalidades as $modalidad): ?>
                                                        <option value="<?= $modalidad['idmodalidad'] ?>">
                                                            <?= esc($modalidad['nombre']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medio_comunicacion">Detalle del Medio</label>
                                            <input type="text" class="form-control" id="medio_comunicacion" name="medio_comunicacion"
                                                   placeholder="Ej: WhatsApp +51 999888777">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Asignación -->
                                <h6 class="mb-3"><i class="icon-user-following"></i> Asignación y Seguimiento</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="idusuario_asignado">Asignar a *</label>
                                            <select class="form-control" id="idusuario_asignado" name="idusuario_asignado" required>
                                                <option value="<?= session()->get('idusuario') ?>" selected>
                                                    👤 Yo mismo (<?= session()->get('nombre') ?>)
                                                </option>
                                                <?php if (isset($vendedores) && is_array($vendedores)): ?>
                                                    <?php foreach ($vendedores as $vendedor): ?>
                                                        <?php if ($vendedor['idusuario'] != session()->get('idusuario')): ?>
                                                            <option value="<?= $vendedor['idusuario'] ?>" data-turno="<?= esc($vendedor['turno']) ?>">
                                                                <?= esc($vendedor['nombre']) ?> - <?= esc($vendedor['nombreRol']) ?>
                                                                <?php if (!empty($vendedor['turno'])): ?>
                                                                    (<?= ucfirst($vendedor['turno']) ?>)
                                                                <?php endif; ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <small class="text-muted">
                                                💡 Si el cliente prefiere horario específico, asigna según turno
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="idetapa">Etapa Inicial</label>
                                            <select class="form-control" id="idetapa" name="idetapa">
                                                <?php foreach ($etapas as $etapa): ?>
                                                <option value="<?= $etapa['idetapa'] ?>" <?= $etapa['orden'] == 1 ? 'selected' : '' ?>>
                                                    <?= esc($etapa['nombre']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nota_inicial">Nota del Primer Contacto</label>
                                    <textarea class="form-control" id="nota_inicial" name="nota_inicial" rows="3"
                                              placeholder="Describe brevemente la conversación inicial..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Botones Paso 2 -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-light" id="btnAtras">
                                <i class="icon-arrow-left"></i> Atrás
                            </button>
                            <button type="submit" class="btn btn-success btn-lg" id="btnGuardar">
                                <i class="icon-check"></i> Guardar Lead
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';
</script>

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('js/leads/wizard.js') ?>"></script>
<script src="<?= base_url('js/leads/buscar-cliente.js') ?>"></script>
<script src="<?= base_url('js/leads/create.js') ?>"></script>
<script src="<?= base_url('js/leads/campos-dinamicos-origen.js') ?>"></script>

<?= $this->endSection() ?>