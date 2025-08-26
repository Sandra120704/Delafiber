DELIMITER $$
DROP PROCEDURE IF EXISTS sp_eliminar_persona $$
CREATE PROCEDURE sp_eliminar_persona (IN p_idpersona INT)
BEGIN
  DELETE FROM personas WHERE idpersona = p_idpersona;
END $$
DELIMITER ;
