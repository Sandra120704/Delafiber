ALTER TABLE leads ADD COLUMN activo TINYINT DEFAULT 1;

DELIMITER $$
CREATE PROCEDURE sp_eliminar_lead (
  IN p_idlead INT
)
BEGIN
  UPDATE leads 
  SET activo = 0, modificado = NOW()
  WHERE idlead = p_idlead;
END $$
DELIMITER ;
