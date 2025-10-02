-- Verificar usuario jperes
USE delafiber;

SELECT 
    u.idusuario,
    u.usuario,
    u.clave,
    u.idpersona,
    u.idrol,
    u.activo,
    p.nombres,
    p.apellidos,
    r.nombre as rol
FROM usuarios u
LEFT JOIN personas p ON u.idpersona = p.idpersona
LEFT JOIN roles r ON u.idrol = r.idrol
WHERE u.usuario = 'jperes';

-- Ver todos los usuarios
SELECT 
    u.idusuario,
    u.usuario,
    u.idpersona,
    u.activo
FROM usuarios u;
