<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <a href="<?= base_url('/personas/create') ?>" class="btn btn-primary mb-3">Registrar Nueva Persona</a>

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
                            <a href="<?= base_url('/personas/edit/' . $persona['idpersona']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="<?= base_url('/personas/delete/' . $persona['idpersona']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta persona?')">Eliminar</a>
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

</div>

</body>
</html>