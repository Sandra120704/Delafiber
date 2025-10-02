-- Verificar usuarios en el sistema
USE delafiber;

-- Ver todos los usuarios con sus datos
SELECT 
    u.idusuario,
    u.usuario,
    u.clave,
    u.idpersona,
    u.idrol,
    u.activo,
    COALESCE(CONCAT(p.nombres, ' ', p.apellidos), 'Sin persona') as nombre_completo,
    COALESCE(r.nombre, 'Sin rol') as rol
FROM usuarios u
LEFT JOIN personas p ON u.idpersona = p.idpersona
LEFT JOIN roles r ON u.idrol = r.idrol
ORDER BY u.idusuario;

-- Si no hay usuarios, crear uno de prueba
-- Descomenta estas líneas si no hay usuarios:

-- INSERT INTO usuarios (usuario, clave, idrol, activo) 
-- VALUES ('jperez', '123456', 1, 1);

-- Para activar un usuario inactivo:
-- UPDATE usuarios SET activo = 1 WHERE usuario = 'jperez';

-- Para cambiar contraseña:
-- UPDATE usuarios SET clave = '123456' WHERE usuario = 'jperez';
