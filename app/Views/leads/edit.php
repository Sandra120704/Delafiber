<h2>Editar Lead</h2>

<form action="<?= site_url('leads/update/' . $lead['idlead']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="idusuarioresponsable" class="form-label">Usuario Responsable</label>
        <input type="number" class="form-control" name="idusuarioresponsable" id="idusuarioresponsable"
               value="<?= esc($lead['idusuarioresponsable']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="fechasignacion" class="form-label">Fecha de Asignación</label>
        <input type="date" class="form-control" name="fechasignacion" id="fechasignacion"
               value="<?= esc($lead['fechasignacion']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select" required>
            <?php foreach (['nuevo', 'contactado', 'interesado', 'no interesado', 'perdido'] as $estado): ?>
                <option value="<?= $estado ?>" <?= $lead['estado'] == $estado ? 'selected' : '' ?>>
                    <?= ucfirst($estado) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="<?= site_url('leads') ?>" class="btn btn-secondary">Volver</a>
</form>
