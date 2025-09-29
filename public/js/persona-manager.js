class PersonaManager {
    constructor() {
        this.elements = this.getElements();
        this.init();
    }

    getElements() {
        return {
            // Formulario
            dniInput: document.getElementById('dni'),
            btnBuscar: document.getElementById('buscar-dni'),
            apellidosInput: document.getElementById('apellidos'),
            nombresInput: document.getElementById('nombres'),
            telefonoInput: document.getElementById('telefono'),
            buscando: document.getElementById('searching'),

            // Botones CRUD en tabla (si existen)
            btnEditar: document.querySelectorAll('.btn-editar'),
            btnEliminar: document.querySelectorAll('.btn-eliminar'),
            btnConvertirLead: document.querySelectorAll('.btn-convertir-lead')
        };
    }

    init() {
        // --- Validaciones de inputs ---
        this.elements.dniInput?.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 8);
        });

        this.elements.telefonoInput?.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 9);
        });

        // --- Buscar DNI ---
        this.elements.btnBuscar?.addEventListener('click', () => this.buscarDNI());

        // --- Eventos CRUD (solo si existen en la vista actual) ---
        this.elements.btnEditar?.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                window.location.href = BASE_URL + 'personas/edit/' + id;
            });
        });

        this.elements.btnEliminar?.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                Swal.fire({
                    title: '¿Eliminar persona?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = BASE_URL + 'personas/delete/' + id;
                    }
                });
            });
        });

        this.elements.btnConvertirLead?.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                Swal.fire({
                    title: '¿Convertir en Lead?',
                    text: '¿Deseas convertir esta persona en un lead?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, convertir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = BASE_URL + 'leads/create?persona_id=' + id;
                    }
                });
            });
        });
    }

    async buscarDNI() {
        const dni = this.elements.dniInput.value.trim();

        if (dni.length !== 8) {
            Swal.fire({
                icon: 'warning',
                title: 'DNI inválido',
                text: 'Ingrese un DNI válido de 8 dígitos'
            });
            return;
        }

        this.elements.buscando.classList.remove('d-none');
        this.elements.btnBuscar.disabled = true;

        try {
            const res = await fetch(`${API_BUSCAR_DNI}?q=${dni}`);
            const data = await res.json();

            if (data.success) {
                if (data.registrado) {
                    Swal.fire({
                        icon: 'info',
                        title: 'DNI registrado',
                        html: `El DNI <b>${dni}</b> ya está registrado por:<br><b>${data.nombres} ${data.apepaterno || ''} ${data.apematerno || ''}</b>`
                    });
                    this.elements.nombresInput.value = '';
                    this.elements.apellidosInput.value = '';
                    return;
                }

                // Autocompletar en el form
                this.elements.nombresInput.value = data.nombres || '';
                this.elements.apellidosInput.value = `${data.apepaterno || ''} ${data.apematerno || ''}`.trim();

                this.elements.telefonoInput?.focus();

                Swal.fire({
                    icon: 'success',
                    title: 'Datos encontrados',
                    text: 'Complete la información restante'
                });

            } else {
                this.elements.nombresInput.value = '';
                this.elements.apellidosInput.value = '';
                Swal.fire({
                    icon: 'info',
                    title: 'No encontrado',
                    text: data.message || 'No se encontró información para este DNI'
                });
            }

        } catch (error) {
            console.error('Error al buscar DNI:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al consultar el DNI'
            });
        } finally {
            this.elements.buscando.classList.add('d-none');
            this.elements.btnBuscar.disabled = false;
        }
    }
}

// Iniciar manager en cualquier página
document.addEventListener('DOMContentLoaded', () => {
    window.personaManager = new PersonaManager();
});
