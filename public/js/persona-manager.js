class PersonaManager {
    constructor() {
        this.elements = this.getElements();
        this.init();
    }

    getElements() {
        return {
            dniInput: document.getElementById('dni'),
            btnBuscar: document.getElementById('buscar-dni'),
            apellidosInput: document.getElementById('apellidos'),
            nombresInput: document.getElementById('nombres'),
            buscando: document.getElementById('searching')
        };
    }

    init() {
        this.elements.dniInput?.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 8);
        });

        document.getElementById('telefono')?.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 9);
        });

        this.elements.btnBuscar?.addEventListener('click', () => this.buscarDNI());
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

                this.elements.nombresInput.value = data.nombres || '';
                this.elements.apellidosInput.value = `${data.apepaterno || ''} ${data.apematerno || ''}`.trim();

                const telefonoInput = document.getElementById('telefono');
                telefonoInput?.focus();

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

document.addEventListener('DOMContentLoaded', () => {
    window.personaManager = new PersonaManager();
});