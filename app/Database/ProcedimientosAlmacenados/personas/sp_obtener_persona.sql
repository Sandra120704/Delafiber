DELIMITER $$
DROP PROCEDURE IF EXISTS sp_obtener_persona_por_id $$
CREATE PROCEDURE sp_obtener_persona_por_id (
  IN p_idpersona INT
)
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
  JOIN distritos d ON d.iddistrito = p.iddistrito
  WHERE p.idpersona = p_idpersona;
END $$
DELIMITER ;
