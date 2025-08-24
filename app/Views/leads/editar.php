<h2>Editar Lead</h2>
<form action="<?= site_url('leads/update/' . $lead['idlead']) ?>" method="post">
    <label>Usuario responsable</label>
    <input type="number" name="idusuarioresponsable" value="<?= $lead['idusuarioresponsable'] ?>" class="form-control">

    <label>Fecha Asignación</label>
    <input type="date" name="fechasignacion" value="<?= $lead['fechasignacion'] ?>" class="form-control">

    <label>Estado</label>
    <select name="estado" class="form-control">
        <option <?= $lead['estado'] == 'nuevo' ? 'selected' : '' ?>>nuevo</option>
        <option <?= $lead['estado'] == 'contactado' ? 'selected' : '' ?>>contactado</option>
        <option <?= $lead['estado'] == 'interesado' ? 'selected' : '' ?>>interesado</option>
        <option <?= $lead['estado'] == 'no interesado' ? 'selected' : '' ?>>no interesado</option>
        <option <?= $lead['estado'] == 'perdido' ? 'selected' : '' ?>>perdido</option>
    </select>

    <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
</form>
