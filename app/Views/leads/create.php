<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/leads/create.css') ?>">

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

                <!-- Mensajes de error -->
                <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <form id="formLead" action="<?= base_url('leads/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- PASO 1: Búsqueda de Cliente Existente -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">1. Buscar Cliente Existente</h5>
                        </div>
                        <div class="card-body">
                            <!-- Buscar por Teléfono (NUEVO) -->
                            <div class="alert alert-info">
                                <i class="icon-info"></i> <strong>¿Cliente existente?</strong> Busca primero por teléfono para evitar duplicados y permitir múltiples solicitudes.
                            </div>

                            <div class="form-group">
                                <label for="buscar_telefono">Buscar por Teléfono</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="buscar_telefono" 
                                           placeholder="Ingrese teléfono (9 dígitos)" maxlength="9">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="button" id="btnBuscarTelefono">
                                            <i class="icon-search"></i> Buscar Cliente
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Si el cliente ya existe, sus datos se autocompletarán y podrás crear una nueva solicitud de servicio.
                                </small>
                            </div>

                            <!-- Resultado de búsqueda -->
                            <div id="resultado-busqueda" style="display:none;"></div>

                            <!-- Campo oculto para ID de persona -->
                            <input type="hidden" id="idpersona" name="idpersona" value="<?= !empty($persona) ? $persona['idpersona'] : '' ?>">

                            <?php if (!empty($persona)): ?>
                            <div class="alert alert-success">
                                <i class="icon-check"></i> <strong>Cliente encontrado:</strong> <?= esc($persona['nombres'] . ' ' . $persona['apellidos']) ?>
                            </div>
                            <?php endif; ?>

                            <hr>

                            <!-- Búsqueda por DNI (opcional) -->
                            <div class="form-group">
                                <label for="dni">DNI del Cliente (Opcional)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dni" name="dni" 
                                           value="<?= !empty($persona) ? esc($persona['dni']) : '' ?>"
                                           placeholder="Ingrese DNI de 8 dígitos" maxlength="8"
                                           <?= !empty($persona) ? 'readonly' : '' ?>>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="btnBuscarDni"
                                                <?= !empty($persona) ? 'disabled' : '' ?>>
                                            <i class="icon-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <div id="dni-loading" class="text-primary mt-2" style="display:none;">
                                    <i class="icon-refresh rotating"></i> Buscando...
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombres">Nombres *</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" 
                                               value="<?= !empty($persona) ? esc($persona['nombres']) : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos *</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                               value="<?= !empty($persona) ? esc($persona['apellidos']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono *</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" 
                                               value="<?= !empty($persona) ? esc($persona['telefono']) : '' ?>"
                                               maxlength="9" placeholder="9XXXXXXXX" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correo">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="correo" name="correo"
                                               value="<?= !empty($persona) ? esc($persona['correo']) : '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="iddistrito">Distrito</label>
                                <select class="form-control" id="iddistrito" name="iddistrito">
                                    <option value="">Seleccione un distrito</option>
                                    <?php if (!empty($distritos) && is_array($distritos)): ?>
                                        <?php foreach ($distritos as $distrito): ?>
                                            <option value="<?= $distrito['iddistrito'] ?>"
                                                    <?= (!empty($persona) && $persona['iddistrito'] == $distrito['iddistrito']) ? 'selected' : '' ?>>
                                                <?= esc(isset($distrito['nombre']) ? $distrito['nombre'] : '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No hay distritos disponibles</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Opcional - Ayuda a ubicar al prospecto en el mapa</small>
                                
                                <!-- Alerta de cobertura -->
                                <div id="alertCobertura" class="mt-2" style="display:none;"></div>
                            </div>

                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" 
                                       placeholder="Ej: Av. Principal 123">
                            </div>

                            <div class="form-group">
                                <label for="referencias">Referencias de Ubicación</label>
                                <textarea class="form-control" id="referencias" name="referencias" rows="2"
                                          placeholder="Ej: Frente al parque, cerca del mercado"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- PASO 2: Información de la Solicitud de Servicio -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">2. Información de la Solicitud de Servicio</h5>
                        </div>
                        <div class="card-body">
                            <!-- NUEVO: Tipo de Solicitud -->
                            <div class="alert alert-warning">
                                <i class="icon-info"></i> <strong>Importante:</strong> Especifica dónde se instalará el servicio. Un mismo cliente puede tener múltiples solicitudes en diferentes ubicaciones.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_solicitud">Tipo de Instalación *</label>
                                        <select class="form-control" id="tipo_solicitud" name="tipo_solicitud" required>
                                            <option value="">Seleccione</option>
                                            <option value="Casa">Casa / Hogar</option>
                                            <option value="Negocio"> Negocio / Empresa</option>
                                            <option value="Oficina">Oficina</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                        <small class="text-muted">Indica el tipo de instalación que solicita el cliente</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="distrito_servicio">Distrito de Instalación *</label>
                                        <select class="form-control" id="distrito_servicio" name="distrito_servicio" required>
                                            <option value="">Seleccione distrito</option>
                                            <?php if (!empty($distritos) && is_array($distritos)): ?>
                                                <?php foreach ($distritos as $distrito): ?>
                                                    <option value="<?= $distrito['iddistrito'] ?>">
                                                        <?= esc(isset($distrito['nombre']) ? $distrito['nombre'] : '') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <small class="text-muted">¿Dónde se instalará el servicio?</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion_servicio">Dirección de Instalación del Servicio *</label>
                                <input type="text" class="form-control" id="direccion_servicio" name="direccion_servicio" required
                                       placeholder="Ej: Jr. Comercio 456, Chincha Alta">
                                <small class="text-muted">
                                    <i class="icon-info"></i> Esta dirección puede ser diferente a la dirección personal del cliente
                                </small>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idorigen">¿Cómo nos conoció? *</label>
                                        <select class="form-control" id="idorigen" name="idorigen" required>
                                            <option value="">Seleccione el origen</option>
                                            <?php foreach ($origenes as $origen): ?>
                                            <option value="<?= $origen['idorigen'] ?>" data-nombre="<?= esc($origen['nombre']) ?>">
                                                <?= esc($origen['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Campos dinámicos según origen -->
                                    <div id="campos-dinamicos-origen"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idcampania">Campaña Asociada</label>
                                        <select class="form-control" id="idcampania" name="idcampania">
                                            <option value="">Ninguna</option>
                                            <?php foreach ($campanias as $campania): ?>
                                            <option value="<?= $campania['idcampania'] ?>">
                                                <?= esc($campania['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Selecciona si viene de una campaña específica</small>
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
                                                        <?= esc(isset($modalidad['nombre']) ? $modalidad['nombre'] : '') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
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
                                <label for="medio_comunicacion">Detalle del Medio (opcional)</label>
                                <input type="text" class="form-control" id="medio_comunicacion" name="medio_comunicacion"
                                       placeholder="Ej: WhatsApp +51 999888777, Facebook Inbox">
                            </div>

                            <div class="form-group">
                                <label for="nota_inicial">Nota del Primer Contacto</label>
                                <textarea class="form-control" id="nota_inicial" name="nota_inicial" rows="3"
                                          placeholder="Describe brevemente la conversación inicial con el cliente"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="text-right">
                        <a href="<?= base_url('leads') ?>" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                            <i class="icon-check"></i> Guardar Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Variable de configuración para los archivos JS externos
const BASE_URL = '<?= base_url() ?>';
console.log('BASE_URL definida:', BASE_URL);
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('js/leads/buscar-cliente.js') ?>"></script>
<script src="<?= base_url('js/leads/create.js') ?>"></script>
<script src="<?= base_url('js/leads/campos-dinamicos-origen.js') ?>"></script>
<script>
console.log('Todos los scripts cargados');
console.log('Módulo de búsqueda de cliente cargado');
</script>

<?= $this->endSection() ?>

<?= $this->include('Layouts/footer') ?>
