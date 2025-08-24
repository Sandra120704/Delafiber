DELIMITER $$

CREATE PROCEDURE sp_eliminar_lead (
  IN p_idlead INT
)
BEGIN
  DELETE FROM leads WHERE idlead = p_idlead;
END $$

DELIMITER ;
