<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
.timeline {
    position: relative;
    padding-left: 0;
}
.timeline-item {
    position: relative;
}
.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 50px;
    bottom: -25px;
    width: 2px;
    background: #e0e0e0;
}
.timeline-badge {
    position: relative;
    z-index: 1;
}
.badge-lg {
    font-size: 0.95rem;
    padding: 0.5rem 0.75rem;
}
</style>

<script src="<?= base_url('js/lead-view.js') ?>"></script>
<script>
function confirmarEliminar() {
    $('#modalEliminar').modal('show');
}
function completarTarea(idtarea) {
    if (confirm('¿Marcar esta tarea como completada?')) {
        window.location.href = '<?= base_url('tareas/completar/') ?>' + idtarea + '?redirect=<?= current_url() ?>';
    }
}
</script>

<?= $this->endSection() ?>
});

// Agregar seguimiento
document.getElementById('formSeguimiento').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('<?= base_url('leads/agregarSeguimiento') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error');
        }
    });
});

// Crear tarea
document.getElementById('formTarea').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('<?= base_url('leads/crearTarea') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error');
        }
    });
});

// Completar tarea
function completarTarea(id) {
    if (confirm('¿Marcar como completada?')) {
        const formData = new FormData();
        formData.append('idtarea', id);
        fetch('<?= base_url('leads/completarTarea') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
}
</script>

<?= $this->endSection() ?>
