DELIMITER $$

CREATE PROCEDURE sp_eliminar_persona (
  IN p_idpersona INT,
  OUT p_exito BOOLEAN
)
BEGIN
  DELETE FROM personas WHERE idpersona = p_idpersona;
  SET p_exito = ROW_COUNT() > 0;
END $$

DELIMITER ;
