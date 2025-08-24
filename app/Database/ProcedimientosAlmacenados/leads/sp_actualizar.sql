DELIMITER $$

CREATE PROCEDURE sp_actualizar_lead (
  IN p_idlead INT,
  IN p_idusuarioresponsable INT,
  IN p_fechasignacion DATE,
  IN p_estado ENUM('nuevo', 'contactado', 'interesado', 'no interesado', 'perdido'),
  OUT p_exito BOOLEAN
)
BEGIN
  UPDATE leads
  SET idusuarioresponsable = p_idusuarioresponsable,
      fechasignacion = p_fechasignacion,
      estado = p_estado,
      modificado = NOW()
  WHERE idlead = p_idlead;

  SET p_exito = ROW_COUNT() > 0;
END $$

DELIMITER ;
