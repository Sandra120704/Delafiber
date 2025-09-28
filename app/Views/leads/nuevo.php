<!-- Este archivo solo debe contener el modal, sin llamadas a $this->section() ni $this->endSection() -->
<!-- Inclúyelo en la vista principal (index.php) con: <?php include 'nuevo.php'; ?> -->

<div class="modal fade" id="modalNuevoLead" tabindex="-1" role="dialog" aria-labelledby="modalNuevoLeadLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalNuevoLeadLabel">
          <i class="ti-user"></i> Registro de Prospecto
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= base_url('leads/store') ?>" method="post" autocomplete="off">
        <div class="modal-body">
          <!-- ...formulario de persona y lead igual al anterior... -->
          <!-- ...existing code from previous modal... -->
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <i class="ti-check"></i> Guardar Lead
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
