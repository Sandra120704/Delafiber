
<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/personas.css') ?>">

<h4>Registro de personas</h4>
<a href="/personas/create" class="btn btn-success btn-sm mb-2">Agregar persona</a>

<table class="table table-sm">
  <colgroup>
    <col width="10%">
    <col width="25%">
    <col width="25%">
    <col width="20%">
    <col width="20%">
  </colgroup>
  <thead>
    <tr>
      <th>ID</th>
      <th>Apellidos</th>
      <th>Nombres</th>
      <th>Teléfono</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($personas)): ?>
      <?php foreach ($personas as $persona): ?>
        <tr>
          <td><?= esc($persona['idpersona']) ?></td>
          <td><?= esc($persona['apellidos']) ?></td>
          <td><?= esc($persona['nombres']) ?></td>
          <td><?= esc($persona['telefono']) ?></td>
          <td>
            <a href="/personas/edit/<?= esc($persona['idpersona']) ?>" class="btn btn-sm btn-info btn-editar" data-id="<?= esc($persona['idpersona']) ?>">Editar</a>
            <a href="/personas/delete/<?= esc($persona['idpersona']) ?>" class="btn btn-sm btn-danger btn-eliminar" data-id="<?= esc($persona['idpersona']) ?>">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5">No hay personas registradas.</td></tr>
    <?php endif; ?>
*** End Patch
  </tbody>
</table>

<?= $footer ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
</script>
<script src="<?= base_url('js/personasJS/personas.js') ?>"></script>