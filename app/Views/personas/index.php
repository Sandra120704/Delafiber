<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <title>Lista de Personas</title>
</head>
<body>

<div class="container mt-4">
  <h2>Listado De Personas</h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearPersona">
        Registrar Nueva Persona
    </button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Distrito</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($personas)): ?>
                <?php foreach ($personas as $persona): ?>
                    <tr>
                        <td><?= $persona['idpersona'] ?></td>
                        <td><?= $persona['apellidos'] ?></td>
                        <td><?= $persona['nombres'] ?></td>
                        <td><?= $persona['telprimario'] ?></td>
                        <td><?= $persona['email'] ?></td>
                        <td><?= $persona['distrito'] ?? '—' ?></td>
                        <td>
                            <a class="btn btn-sm btn-warning btnEditarPersona" data-bs-target="#modalCrearPersona" data-id="<?= $persona['idpersona'] ?>">Editar</a>

                            <a href="<?= base_url('/personas/delete/' . $persona['idpersona']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta persona?')">Eliminar</a>

                            <?php if (empty($persona['es_lead']) || !$persona['es_lead']): ?>
                                <a href="<?= base_url('/personas/convertir/' . $persona['idpersona']) ?>" class="btn btn-sm btn-primary">Convertir en Lead</a>
                            <?php else: ?>
                                <span class="badge bg-success">Lead</span>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay personas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?= view('personas/create', ['distritos' => $distritos]) ?>
    <?php if (session()->getFlashdata('error')): ?>
        <script>
        var myModal = new bootstrap.Modal(document.getElementById('modalCrearPersona'));
        myModal.show();
        </script>
    <?php endif; ?>



</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).on('click', '.btnEditarPersona', function() {
        const id = $(this).data('id');

        $.get('/personas/formulario-editar/' + id, function(html) {
            $('#modalPersona .modal-content').html(html);
            var modal = new bootstrap.Modal(document.getElementById('modalPersona'));
            modal.show();
        }).fail(function(err) {
            console.error("Error cargando el modal:", err);
        });
    });
</script>



</body>
</html>