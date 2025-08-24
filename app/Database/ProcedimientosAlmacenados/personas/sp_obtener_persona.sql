DELIMITER $$

CREATE PROCEDURE sp_obtener_persona(
  IN p_idpersona INT
)
BEGIN
  SELECT 
        p.*,
        d.distrito,
        pr.provincia,
        dp.departamento
    FROM personas p
    JOIN distritos d ON p.iddistrito = d.iddistrito
    JOIN provincias pr ON d.idprovincia = pr.idprovincia
    JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento
    WHERE p.idpersona = p_idpersona;
END $$

DELIMITER ;
