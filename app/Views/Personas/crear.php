<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/personas.css') ?>">

<div class="container-fluid mt-4 custom-container">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-10">
      <!-- Encabezado -->
      <div class="mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><?= isset($persona) ? 'Editar Persona' : 'Registro De Personas' ?></h3>
        <a href="<?= base_url('personas') ?>" class="btn btn-outline-secondary btn-sm">
          <i class="ti-arrow-left me-1"></i> Lista de personas
        </a>
      </div>

      <!-- Formulario -->
      <form action="<?= base_url('personas/guardar') ?>" id="form-persona" method="POST" autocomplete="off" class="w-100">
        <div class="card shadow-sm">
          <div class="card-body">
            <!-- Campo DNI con búsqueda -->
            <div class="form-group mb-3">
              <label for="dni" class="form-label">Buscar DNI</label>
              <small class="d-none text-muted" id="searching">Buscando...</small>
              <div class="input-group">
                <input type="text" class="form-control" name="dni" id="dni"
                       maxlength="8" minlength="8" required autofocus
                       value="<?= esc($persona['dni'] ?? '') ?>">
                <button class="btn btn-outline-success" type="button" id="buscar-dni">
                  <i class="ti-search"></i> Buscar
                </button>
              </div>
            </div>

            <!-- Campos de nombres y apellidos -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" name="apellidos" id="apellidos" required
                       value="<?= esc($persona['apellidos'] ?? '') ?>" readonly>
              </div>
              <div class="col-md-6">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" name="nombres" id="nombres" required
                       value="<?= esc($persona['nombres'] ?? '') ?>" readonly>
              </div>
            </div>

            <!-- Correo y teléfono -->
            <div class="row g-3 mb-3">
              <div class="col-md-8">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo" id="correo"
                       value="<?= esc($persona['correo'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" name="telefono" id="telefono"
                       maxlength="9" pattern="[0-9]*" inputmode="numeric"
                       title="Solo se permiten números" required
                       value="<?= esc($persona['telefono'] ?? '') ?>">
              </div>
            </div>

            <!-- Dirección y distrito -->
            <div class="row g-3 mb-3">
              <div class="col-md-8">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" name="direccion" id="direccion"
                       value="<?= esc($persona['direccion'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label for="iddistrito" class="form-label">Distrito</label>
                <select class="form-select" name="iddistrito" id="iddistrito" required>
                  <option value="">Seleccione...</option>
                  <?php foreach ($distritos as $d): ?>
                    <option value="<?= $d['iddistrito'] ?>"
                      <?= (isset($persona) && $persona['iddistrito'] == $d['iddistrito']) ? 'selected' : '' ?>>
                      <?= esc($d['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Referencias -->
            <div class="row g-3 mb-3">
              <div class="col-12">
                <label for="referencias" class="form-label">Referencia</label>
                <input type="text" class="form-control" name="referencias" id="referencias"
                       value="<?= esc($persona['referencias'] ?? '') ?>">
              </div>
            </div>

            <!-- Campo oculto para ID -->
            <input type="hidden" name="idpersona" value="<?= esc($persona['idpersona'] ?? '') ?>">
          </div>

          <!-- Footer del card con botones -->
          <div class="card-footer text-end bg-transparent">
            <button class="btn btn-outline-secondary btn-sm me-2" type="reset">
              <i class="ti-trash me-1"></i> Limpiar
            </button>
            <button class="btn btn-primary btn-sm" type="submit">
              <i class="ti-save me-1"></i> Guardar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Contenedor para modales -->
<div id="modalContainer"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const BASE_URL = "<?= rtrim(base_url(), '/') ?>/";

  // Verificar DNI duplicado en tiempo real
  let dniTimeout;
  document.getElementById('dni').addEventListener('input', function() {
    const dni = this.value;
    
    if (dni.length === 8) {
      clearTimeout(dniTimeout);
      dniTimeout = setTimeout(() => {
        fetch(`${BASE_URL}personas/verificarDni?dni=${dni}`)
          .then(response => response.json())
          .then(data => {
            if (data.existe) {
              Swal.fire({
                icon: 'warning',
                title: '⚠️ DNI Ya Registrado',
                html: `
                  <div class="text-start">
                    <p><strong>Este DNI ya pertenece a:</strong></p>
                    <ul class="list-unstyled">
                      <li>👤 <strong>Nombre:</strong> ${data.persona.nombres} ${data.persona.apellidos}</li>
                      <li>📞 <strong>Teléfono:</strong> ${data.persona.telefono || 'No registrado'}</li>
                      <li>📧 <strong>Correo:</strong> ${data.persona.correo || 'No registrado'}</li>
                    </ul>
                    <p class="text-muted small mt-3">No puedes registrar el mismo DNI dos veces.</p>
                  </div>
                `,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3085d6'
              });
              
              // Limpiar el campo DNI
              document.getElementById('dni').value = '';
              document.getElementById('dni').focus();
            }
          });
      }, 500); // Esperar 500ms después de que el usuario deje de escribir
    }
  });

  // Función para buscar persona por DNI (RENIEC)
  document.getElementById('buscar-dni').addEventListener('click', function() {
    const dni = document.getElementById('dni').value;
    const searchingElement = document.getElementById('searching');

    if (dni.length === 8) {
      searchingElement.classList.remove('d-none');

      // Primero verificar si ya existe en la BD
      fetch(`${BASE_URL}personas/verificarDni?dni=${dni}`)
        .then(response => response.json())
        .then(data => {
          if (data.existe) {
            searchingElement.classList.add('d-none');
            Swal.fire({
              icon: 'error',
              title: '❌ Persona Ya Registrada',
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
              confirmButtonText: 'Ver Contacto',
              cancelButtonText: 'Cancelar',
              confirmButtonColor: '#3085d6'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = `${BASE_URL}personas`;
              }
            });
            return;
          }

          // Si no existe, buscar en RENIEC
          fetch(`${BASE_URL}api/personas/buscar?dni=${dni}`)
            .then(response => response.json())
            .then(data => {
              searchingElement.classList.add('d-none');

              if (data.success && data.persona) {
                // Autocompletar campos
                document.getElementById('apellidos').value = data.persona.apellidos || '';
                document.getElementById('nombres').value = data.persona.nombres || '';
                document.getElementById('telefono').value = data.persona.telefono || '';
                document.getElementById('correo').value = data.persona.correo || '';
                document.getElementById('direccion').value = data.persona.direccion || '';
                
                if (data.persona.iddistrito) {
                  document.getElementById('iddistrito').value = data.persona.iddistrito;
                }
                
                document.getElementById('apellidos').readOnly = true;
                document.getElementById('nombres').readOnly = true;
                
                Swal.fire({
                  icon: 'success',
                  title: '✅ Datos Encontrados',
                  text: data.message || 'Datos obtenidos correctamente',
                  timer: 2000,
                  showConfirmButton: false
                });
              } else {
                Swal.fire({
                  icon: 'info',
                  title: 'DNI no encontrado',
                  text: 'Puedes registrar manualmente los datos',
                  confirmButtonText: 'Entendido'
                });

                // Habilitar campos para nuevo registro
                document.getElementById('apellidos').readOnly = false;
                document.getElementById('nombres').readOnly = false;
                document.getElementById('apellidos').focus();
              }
            })
            .catch(error => {
              searchingElement.classList.add('d-none');
              console.error('Error:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo conectar al servidor'
              });
            });
        });
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'DNI inválido',
        text: 'El DNI debe tener 8 dígitos'
      });
    }
  });

  // Validación del formulario antes de enviar
  document.getElementById('form-persona').addEventListener('submit', function(e) {
    const telefono = document.getElementById('telefono').value;
    const dni = document.getElementById('dni').value;
    
    if (dni.length !== 8) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'DNI inválido',
        text: 'El DNI debe tener 8 dígitos'
      });
      return;
    }
    
    if (telefono.length !== 9) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Teléfono inválido',
        text: 'El teléfono debe tener 9 dígitos'
      });
    }
  });
</script>
<script src="<?= base_url('js/personasJS/personas.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->endSection() ?>
