DELIMITER $$
DROP PROCEDURE IF EXISTS sp_listar_personas $$
CREATE PROCEDURE sp_listar_personas ()
BEGIN
  SELECT 
    p.idpersona,
    p.apellidos,
    p.nombres,
    p.email,
    p.telprimario,
    p.telalternativo,
    p.direccion,
    p.referencia,
    p.iddistrito,
    d.distrito AS distrito,
    p.creado,
    p.modificado
  FROM personas p
  JOIN distritos d ON d.iddistrito = p.iddistrito;
END $$
DELIMITER ;
