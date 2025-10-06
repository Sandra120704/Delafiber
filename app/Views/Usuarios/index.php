<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Gestión de Usuarios</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/index') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="bx bx-plus"></i> Nuevo Usuario
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= count($usuarios ?? []) ?></h4>
                    <small>Total Usuarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($usuarios ?? [], fn($u) => $u['activo'] ?? true)) ?></h4>
                    <small>Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($usuarios ?? [], fn($u) => ($u['nombre_rol'] ?? '') === 'vendedor')) ?></h4>
                    <small>Vendedores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($usuarios ?? [], fn($u) => ($u['nombre_rol'] ?? '') === 'admin')) ?></h4>
                    <small>Administradores</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary active" onclick="filtrarUsuarios('todos')">Todos</button>
                        <button class="btn btn-outline-success" onclick="filtrarUsuarios('activos')">Activos</button>
                        <button class="btn btn-outline-danger" onclick="filtrarUsuarios('inactivos')">Inactivos</button>
                        <button class="btn btn-outline-warning" onclick="filtrarUsuarios('vendedores')">Vendedores</button>
                        <button class="btn btn-outline-info" onclick="filtrarUsuarios('admins')">Admins</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar usuarios..." id="buscarUsuario">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Usuarios</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($usuarios)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Información Personal</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Estadísticas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $colors = ['#8e44ad','#2980b9','#16a085','#e67e22','#c0392b'];
                            foreach ($usuarios as $usuario): 
                                $color = $colors[$usuario['idusuario'] % count($colors)];
                                $activo = ($usuario['activo'] ?? 1) == 1;
                            ?>
                            <tr data-rol="<?= strtolower($usuario['nombreRol'] ?? '') ?>" data-estado="<?= $activo ? 'activo' : 'inactivo' ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="user-avatar" style="background:<?= $color ?>; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">
                                                <?= strtoupper(substr($usuario['usuario'] ?? 'U', 0, 2)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= esc($usuario['usuario'] ?? 'Sin usuario') ?></div>
                                            <small class="text-muted">ID: <?= $usuario['idusuario'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= esc($usuario['email'] ?? 'Sin email') ?></div>
                                    <small class="text-muted">
                                        <?php if (!empty($usuario['email'])): ?>
                                            <i class="bx bx-envelope"></i> <?= esc($usuario['email']) ?><br>
                                        <?php endif; ?>
                                        <?php if (!empty($usuario['telefono'])): ?>
                                            <i class="bx bx-phone"></i> <?= esc($usuario['telefono']) ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        ($usuario['nombreRol'] ?? '') === 'Administrador' ? 'danger' : 
                                        (($usuario['nombreRol'] ?? '') === 'Supervisor' ? 'warning' : 'primary') 
                                    ?>">
                                        <?= esc($usuario['nombreRol'] ?? 'Sin rol') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input estado-switch" type="checkbox" 
                                               <?= $activo ? 'checked' : '' ?> 
                                               data-id="<?= $usuario['idusuario'] ?>">
                                        <label class="form-check-label">
                                            <span class="badge bg-<?= $activo ? 'success' : 'secondary' ?>">
                                                <?= $activo ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <div>Leads: <strong><?= $usuario['total_leads'] ?? 0 ?></strong></div>
                                        <div>Tareas: <strong><?= $usuario['total_tareas'] ?? 0 ?></strong></div>
                                        <div>Conversión: <strong><?= $usuario['conversion_rate'] ?? '0' ?>%</strong></div>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-editar" data-id="<?= $usuario['idusuario'] ?>" title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-ver-perfil" data-id="<?= $usuario['idusuario'] ?>" title="Ver perfil">
                                            <i class="bx bx-user"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-resetear-password" data-id="<?= $usuario['idusuario'] ?>" title="Resetear contraseña">
                                            <i class="bx bx-key"></i>
                                        </button>
                                        <?php if (($usuario['nombre_rol'] ?? '') !== 'admin'): ?>
                                        <button class="btn btn-outline-danger btn-eliminar" data-id="<?= $usuario['idusuario'] ?>" title="Eliminar">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bx bx-user display-1 text-muted"></i>
                    <h5 class="text-muted">No hay usuarios registrados</h5>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                        <i class="bx bx-plus"></i> Crear primer usuario
                    </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para crear/editar usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="formUsuario">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">👤 Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idusuario" name="idusuario">
                    <input type="hidden" id="idpersona" name="idpersona">
                    
                    <!-- Tabs para organizar el formulario -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabDatosPersona">
                                📋 Datos de Persona
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabDatosUsuario">
                                🔐 Datos de Usuario
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB 1: DATOS DE PERSONA -->
                        <div class="tab-pane fade show active" id="tabDatosPersona">
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i> Primero registra los datos personales. Puedes buscar por DNI usando la API RENIEC.
                            </div>

                            <!-- Búsqueda por DNI -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">DNI *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="dni" name="dni" maxlength="8" pattern="[0-9]{8}" required>
                                        <button class="btn btn-primary" type="button" id="buscar-dni">
                                            <i class="bx bx-search"></i> Buscar
                                        </button>
                                    </div>
                                    <small class="text-muted">Ingresa el DNI y presiona Buscar</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" maxlength="9" pattern="[0-9]{9}" required>
                                </div>
                            </div>

                            <!-- Nombres y Apellidos -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombres *</label>
                                    <input type="text" class="form-control" id="nombres" name="nombres" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellidos *</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                </div>
                            </div>

                            <!-- Correo y Distrito -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Distrito</label>
                                    <select class="form-select" id="iddistrito" name="iddistrito">
                                        <option value="">Seleccionar distrito</option>
                                        <option value="1">Chincha Alta</option>
                                        <option value="2">Sunampe</option>
                                        <option value="3">Grocio Prado</option>
                                        <option value="4">Pueblo Nuevo</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Dirección y Referencias -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Av. Los Incas 123">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Referencias</label>
                                    <textarea class="form-control" id="referencias" name="referencias" rows="2" placeholder="Ej: Frente al parque, casa de dos pisos"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: DATOS DE USUARIO -->
                        <div class="tab-pane fade" id="tabDatosUsuario">
                            <div class="alert alert-warning">
                                <i class="bx bx-lock"></i> Configura las credenciales de acceso al sistema.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Usuario/Username *</label>
                                    <input type="text" class="form-control" name="usuario" id="usuario" required>
                                    <small class="text-muted">Será usado para iniciar sesión</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control" name="clave" id="clave" required>
                                    <small class="text-muted">Mínimo 6 caracteres</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Rol *</label>
                                    <select class="form-select" name="idrol" id="idrol" required>
                                        <option value="">Seleccionar rol</option>
                                        <?php if (!empty($roles)): ?>
                                            <?php foreach ($roles as $rol): ?>
                                                <option value="<?= $rol['idrol'] ?>">
                                                    <?= esc($rol['nombre']) ?> - <?= esc($rol['descripcion']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" checked>
                                        <label class="form-check-label" for="activoSwitch">
                                            Usuario activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de perfil de usuario -->
<div class="modal fade" id="modalPerfilUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perfil de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoPerfilUsuario">
                <!-- Se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/usuariosJS/usuario.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->endSection() ?>


