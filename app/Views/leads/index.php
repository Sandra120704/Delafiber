<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leads</title>
</head>
<body>

<div class="container">
  <h1>Listado De Leads</h1>
  <table class="table table-striped">
    <thead>
      <tr class="table-primary">
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Fecha de Asignación</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leads as $lead): ?>
        <tr>
          <td><?= $lead['id'] ?></td>
          <td><?= $lead['nombre'] ?></td>
          <td><?= $lead['email'] ?></td>
          <td><?= $lead['telefono'] ?></td>
          <td><?= $lead['fechaasignacion'] ?></td>
          <td><?= $lead['estado'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>