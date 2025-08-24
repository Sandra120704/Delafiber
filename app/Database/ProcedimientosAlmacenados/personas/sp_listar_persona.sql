DELIMITER $$

CREATE PROCEDURE sp_listar_personas()
BEGIN
    SELECT 
        p.idpersona,
        p.nombres,
        p.apellidos,
        p.telprimario,
        p.telalternativo,
        p.email,
        p.direccion,
        p.referencia,
        d.distrito,
        pr.provincia,
        dp.departamento
    FROM personas p
    JOIN distritos d ON p.iddistrito = d.iddistrito
    JOIN provincias pr ON d.idprovincia = pr.idprovincia
    JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento;
END $$

DELIMITER ;

SHOW PROCEDURE STATUS WHERE Db = 'delafiber' AND Name = 'sp_listar_personas';

SHOW PROCEDURE STATUS WHERE Db = 'delafiber';

SHOW PROCEDURE STATUS WHERE Db = 'delafiber' AND Name = 'sp_listar_personas';

DROP PROCEDURE IF EXISTS sp_listar_personas;

USE delafiber;
USE medicamentos;

