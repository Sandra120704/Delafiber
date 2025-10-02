<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

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

                    <!-- PASO 1: Búsqueda por DNI -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">1. Buscar o Ingresar Datos del Cliente</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($persona)): ?>
                            <!-- Persona autocompletada -->
                            <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
                            <div class="alert alert-success">
                                <i class="icon-check"></i> <strong>Datos autocompletados</strong> desde el contacto existente.
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="dni">DNI del Cliente</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dni" name="dni" 
                                           value="<?= !empty($persona) ? esc($persona['dni']) : '' ?>"
                                           placeholder="Ingrese DNI de 8 dígitos" maxlength="8" required
                                           <?= !empty($persona) ? 'readonly' : '' ?>>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="btnBuscarDni"
                                                <?= !empty($persona) ? 'disabled' : '' ?>>
                                            <i class="icon-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <?= !empty($persona) ? 'Datos cargados desde contacto existente' : 'Ingrese el DNI y presione buscar para autocompletar los datos' ?>
                                </small>
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
                                <label for="iddistrito">Distrito *</label>
                                <select class="form-control" id="iddistrito" name="iddistrito" required>
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

                    <!-- PASO 2: Información del Lead -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">2. Información del Lead</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idorigen">¿Cómo nos conoció? *</label>
                                        <select class="form-control" id="idorigen" name="idorigen" required>
                                            <option value="">Seleccione el origen</option>
                                            <?php foreach ($origenes as $origen): ?>
                                            <option value="<?= $origen['idorigen'] ?>">
                                                <?= esc($origen['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
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

<style>
.rotating {
    animation: rotate 1s linear infinite;
}
@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
const BASE_URL = '<?= base_url() ?>';

class PersonaManager {
    constructor() {
        this.initEvents();
    }

    initEvents() {
        const btnBuscarDni = document.getElementById('btnBuscarDni');
        const dniInput = document.getElementById('dni');
        const dniLoading = document.getElementById('dni-loading');

        btnBuscarDni.addEventListener('click', () => {
            const dni = dniInput.value.trim();
            if (dni.length !== 8) {
                Swal.fire('Error', 'El DNI debe tener 8 dígitos', 'error');
                return;
            }

            dniLoading.style.display = 'block';
            btnBuscarDni.disabled = true;

            // Primero verificar si ya existe en la BD
            fetch('<?= base_url('personas/verificarDni') ?>?dni=' + dni)
            .then(response => response.json())
            .then(data => {
                if (data.existe) {
                    dniLoading.style.display = 'none';
                    btnBuscarDni.disabled = false;
                    
                    Swal.fire({
                        icon: 'warning',
                        title: '⚠️ Persona Ya Registrada',
                        html: `
                            <div class="text-start">
                                <p><strong>Esta persona ya está en el sistema:</strong></p>
                                <ul class="list-unstyled">
                                    <li>👤 <strong>Nombre:</strong> ${data.persona.nombres} ${data.persona.apellidos}</li>
                                    <li>📞 <strong>Teléfono:</strong> ${data.persona.telefono || 'No registrado'}</li>
                                    <li>📧 <strong>Correo:</strong> ${data.persona.correo || 'No registrado'}</li>
                                </ul>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Usar estos datos',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Autocompletar con datos existentes
                            document.getElementById('nombres').value = data.persona.nombres;
                            document.getElementById('apellidos').value = data.persona.apellidos;
                            document.getElementById('telefono').value = data.persona.telefono || '';
                            document.getElementById('correo').value = data.persona.correo || '';
                            if (data.persona.iddistrito) {
                                document.getElementById('iddistrito').value = data.persona.iddistrito;
                            }
                            
                            // Agregar campo hidden con idpersona
                            let hiddenInput = document.getElementById('idpersona_hidden');
                            if (!hiddenInput) {
                                hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'idpersona';
                                hiddenInput.id = 'idpersona_hidden';
                                document.getElementById('formLead').appendChild(hiddenInput);
                            }
                            hiddenInput.value = data.persona.idpersona;
                            
                            Swal.fire({
                                icon: 'success',
                                title: '✅ Datos Cargados',
                                text: 'Ahora completa la información del lead',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                    return;
                }

                // Si no existe, buscar en RENIEC
                fetch('<?= base_url('api/personas/buscar') ?>?dni=' + dni)
                .then(response => response.json())
                .then(data => {
                    dniLoading.style.display = 'none';
                    btnBuscarDni.disabled = false;
                    
                    if (data.success && data.persona) {
                        document.getElementById('nombres').value = data.persona.nombres || '';
                        document.getElementById('apellidos').value = data.persona.apellidos || '';
                        
                        Swal.fire({
                            icon: 'success',
                            title: '✅ Datos de RENIEC',
                            text: 'Completa los demás campos',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        document.getElementById('telefono').focus();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'DNI no encontrado',
                            text: 'Puedes registrar manualmente los datos',
                            confirmButtonText: 'Entendido'
                        });
                        document.getElementById('nombres').focus();
                    }
                })
                .catch(error => {
                    dniLoading.style.display = 'none';
                    btnBuscarDni.disabled = false;
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo conectar al servidor', 'error');
                });
            });
        });

        // Validación del formulario
        const form = document.getElementById('formLead');
        form.addEventListener('submit', function(e) {
            const telefono = document.getElementById('telefono').value;
            
            // Validar teléfono
            if (telefono.length !== 9 || !telefono.startsWith('9')) {
                e.preventDefault();
                alert('El teléfono debe tener 9 dígitos y comenzar con 9');
                document.getElementById('telefono').focus();
                return false;
            }

            // Deshabilitar botón de guardar
            document.getElementById('btnGuardar').disabled = true;
            document.getElementById('btnGuardar').innerHTML = '<i class="icon-refresh rotating"></i> Guardando...';
        });

        // Permitir búsqueda with Enter en DNI
        dniInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnBuscarDni.click();
            }
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.personaManager = new PersonaManager();
});

// Funciones globales para compatibilidad (si se necesitan)
function activarBotonesEliminar() {
    console.log('Función legacy - ahora manejada por PersonaManager');
}
function activarBotonesEditar() {
    console.log('Función legacy - ahora manejada por PersonaManager');
}
function activarBotonesConvertirLead() {
    console.log('Función legacy - ahora manejada por PersonaManager');
}
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->endSection() ?>
<?= $this->include('Layouts/footer') ?>