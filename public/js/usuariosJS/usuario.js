$(document).ready(function() {
    // La variable base_url se define en la vista HTML (index.php)
    
    // Buscar persona por DNI (API RENIEC)
    $('#buscar-dni').click(function() {
        const dni = $('#dni').val();
        
        if (dni.length !== 8) {
            Swal.fire('Error', 'El DNI debe tener 8 dígitos', 'error');
            return;
        }

        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Buscando...');

        $.ajax({
            url: `${base_url}/api/personas/buscar?dni=${dni}`,
            method: 'GET',
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success && response.data) {
                // Llenar campos con datos de RENIEC
                $('#nombres').val(response.data.nombres);
                $('#apellidos').val(response.data.apellidoPaterno + ' ' + response.data.apellidoMaterno);
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Encontrado!',
                    text: 'Datos obtenidos de RENIEC',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('No encontrado', 'DNI no encontrado en RENIEC. Ingresa los datos manualmente.', 'warning');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo conectar con la API. Ingresa los datos manualmente.', 'error');
        })
        .always(function() {
            $('#buscar-dni').prop('disabled', false).html('<i class="bx bx-search"></i> Buscar');
        });
    });

    // Cambiar estado activo/inactivo
    $('.estado-switch').change(function() {
        const usuarioId = $(this).data('id');
        const activo = $(this).is(':checked');
        $.post(`${base_url}/usuarios/cambiarEstado/${usuarioId}`, {
            activo: activo ? 1 : 0
        })
        .done(function(response) {
            if (response.success) {
                const badge = $(this).closest('td').find('.badge');
                badge.removeClass('bg-success bg-secondary')
                     .addClass(activo ? 'bg-success' : 'bg-secondary')
                     .text(activo ? 'Activo' : 'Inactivo');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }.bind(this));
    });

    // Crear/Editar usuario
    $('#formUsuario').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const usuarioId = $('#idusuario').val();
        const url = usuarioId ? `${base_url}/usuarios/editar/${usuarioId}` : `${base_url}/usuarios/crear`;
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                $('#modalUsuario').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire('Error', response.message || 'Error al guardar usuario', 'error');
            }
        })
        .fail(function(xhr) {
            console.log('Error:', xhr.responseText);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    });

    // Eliminar usuario
    $(document).on('click', '.btn-eliminar', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${base_url}/usuarios/eliminar/${usuarioId}`,
                    method: 'DELETE',
                    dataType: 'json'
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', 'Usuario eliminado correctamente', 'success');
                        setTimeout(() => location.reload(), 2000);
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'No se pudo eliminar el usuario', 'error');
                });
            }
        });
    });

    // Buscar usuarios
    $('#buscarUsuario').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    // Resetear contraseña
    $(document).on('click', '.btn-resetear-password', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: 'Resetear contraseña',
            input: 'password',
            inputLabel: 'Nueva contraseña',
            inputPlaceholder: 'Ingresa la nueva contraseña',
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.post(`${base_url}/usuarios/resetearPassword/${usuarioId}`, {
                    nueva_password: result.value
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', 'Contraseña actualizada', 'success');
                    }
                });
            }
        });
    });
});

// Filtrar usuarios (función global para onclick)
window.filtrarUsuarios = function(filtro) {
    // Actualizar botones activos
    $('.btn-group button').removeClass('active');
    event.target.classList.add('active');
    
    $('tbody tr').show();
    
    switch(filtro) {
        case 'todos':
            // Mostrar todos
            break;
        case 'activos':
            $('tbody tr[data-estado="inactivo"]').hide();
            break;
        case 'inactivos':
            $('tbody tr[data-estado="activo"]').hide();
            break;
        case 'vendedores':
            $('tbody tr:not([data-rol="vendedor"])').hide();
            break;
        case 'admins':
            $('tbody tr:not([data-rol="admin"])').hide();
            break;
    }
}