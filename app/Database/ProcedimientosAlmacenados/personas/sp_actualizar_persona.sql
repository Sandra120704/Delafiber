DELIMITER $$

CREATE PROCEDURE sp_actualizar_persona (
  IN p_idpersona INT,
  IN p_nombres VARCHAR(100),
  IN p_apellidos VARCHAR(100),
  IN p_email VARCHAR(100),
  IN p_telprimario VARCHAR(20),
  IN p_telsecundario VARCHAR(20),
  IN p_direccion VARCHAR(200),
  IN p_distrito VARCHAR(100),
  OUT p_exito BOOLEAN
)
BEGIN
  UPDATE personas
  SET 
    nombres = p_nombres,
    apellidos = p_apellidos,
    email = p_email,
    telprimario = p_telprimario,
    telsecundario = p_telsecundario,
    direccion = p_direccion,
    distrito = p_distrito,
    modificado = NOW()
  WHERE idpersona = p_idpersona;

  SET p_exito = ROW_COUNT() > 0;
END $$

DELIMITER ;
