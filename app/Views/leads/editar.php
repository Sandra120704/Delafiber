<?= $this->extend('Layouts/header') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4 class="card-title"><i class="ti-pencil"></i> Editar Lead</h4>
                    <p class="card-description">Modifica los datos del prospecto</p>
                </div>
                <div class="card-body">
                    <!-- Mensajes de error/éxito -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <i class="ti-alert"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('leads/update/'.$lead['idlead']) ?>" id="formEditarLead">
                        <?= csrf_field() ?>
                        <!-- DATOS PERSONALES -->
                        <div class="form-section">
                            <h5 class="section-title">Datos Personales</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombres">Nombres <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control"
                                               id="nombres"
                                               name="nombres"
                                               value="<?= esc($lead['nombres']) ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control"
                                               id="apellidos"
                                               name="apellidos"
                                               value="<?= esc($lead['apellidos']) ?>"
                                               required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control"
                                               id="telefono"
                                               name="telefono"
                                               maxlength="9"
                                               value="<?= esc($lead['telefono']) ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dni">DNI</label>
                                        <input type="text"
                                               class="form-control"
                                               id="dni"
                                               name="dni"
                                               maxlength="8"
                                               value="<?= esc($lead['dni']) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="correo">Correo Electrónico</label>
                                <input type="email"
                                       class="form-control"
                                       id="correo"
                                       name="correo"
                                       value="<?= esc($lead['correo']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text"
                                       class="form-control"
                                       id="direccion"
                                       name="direccion"
                                       value="<?= esc($lead['direccion']) ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="distrito">Distrito</label>
                                        <select class="form-control" id="distrito" name="distrito">
                                            <option value="">Seleccionar distrito</option>
                                            <?php foreach ($distritos as $distrito): ?>
                                                <option value="<?= $distrito['iddistrito'] ?>"
                                                    <?= $lead['iddistrito'] == $distrito['iddistrito'] ? 'selected' : '' ?>>
                                                    <?= $distrito['distrito_nombre'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="referencias">Referencias</label>
                                        <input type="text"
                                               class="form-control"
                                               id="referencias"
                                               name="referencias"
                                               value="<?= esc($lead['referencias']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- INFORMACIÓN DEL LEAD -->
                        <hr>
                        <div class="form-section">
                            <h5 class="section-title">Información del Lead</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="origen">¿Cómo nos conoció? <span class="text-danger">*</span></label>
                                        <select class="form-control" id="origen" name="origen" required>
                                            <option value="">Seleccionar origen</option>
                                            <?php foreach ($origenes as $origen): ?>
                                                <option value="<?= $origen['idorigen'] ?>"
                                                    <?= $lead['idorigen'] == $origen['idorigen'] ? 'selected' : '' ?>>
                                                    <?= $origen['nombre'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medio_comunicacion">Medio de comunicación preferido</label>
                                        <select class="form-control" id="medio_comunicacion" name="medio_comunicacion">
                                            <option value="">Seleccionar</option>
                                            <option value="WhatsApp" <?= $lead['medio_comunicacion'] == 'WhatsApp' ? 'selected' : '' ?>>WhatsApp</option>
                                            <option value="Llamada" <?= $lead['medio_comunicacion'] == 'Llamada' ? 'selected' : '' ?>>Llamada telefónica</option>
                                            <option value="Correo" <?= $lead['medio_comunicacion'] == 'Correo' ? 'selected' : '' ?>>Correo electrónico</option>
                                            <option value="Presencial" <?= $lead['medio_comunicacion'] == 'Presencial' ? 'selected' : '' ?>>Visita presencial</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="observaciones">Observaciones iniciales</label>
                                <textarea class="form-control"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"><?= esc($lead['observaciones'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <!-- BOTONES -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="ti-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti-check"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ...puedes reutilizar los estilos y scripts del formulario de nuevo lead si lo deseas... -->

<?= $this->endSection() ?>
