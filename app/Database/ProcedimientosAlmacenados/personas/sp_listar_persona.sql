DELIMITER $$

CREATE PROCEDURE sp_listar_persona()
BEGIN
 SELECT 
  p.idpersona,
        p.nombres,
        p.apellidos,
        p.telprimario,
        p.email,
        d.distrito,
        pr.provincia,
        dp.departamento
    FROM personas p
    JOIN distritos d ON p.iddistrito = d.iddistrito
    JOIN provincias pr ON d.idprovincia = pr.idprovincia
    JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento;
END $$

DELIMITER ;