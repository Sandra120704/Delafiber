DELIMITER $$

CREATE PROCEDURE sp_obtener_persona_por_id (
  IN p_idpersona INT
)
BEGIN
  SELECT 
    idpersona,
    nombres,
    apellidos,
    email,
    telprimario,
    telsecundario,
    direccion,
    distrito,
    creado,
    modificado
  FROM personas
  WHERE idpersona = p_idpersona;
END $$

DELIMITER ;
