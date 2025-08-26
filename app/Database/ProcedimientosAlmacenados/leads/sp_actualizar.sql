DELIMITER $$


CREATE PROCEDURE sp_actualizar_lead (
  IN p_idlead INT,
  IN p_idusuarioresponsable INT,
  IN p_fechasignacion DATE,
  IN p_estado VARCHAR(20),   -- Estado dentro del lead
  IN p_idetapa INT,          -- Nueva etapa en el pipeline
  OUT p_exito BOOLEAN
)
proc_block: BEGIN   -- <<< etiqueta del bloque
  -- Validar estado
  IF p_estado NOT IN ('nuevo', 'contactado', 'interesado', 'no interesado', 'perdido') THEN
    SET p_exito = FALSE;
    LEAVE proc_block;   -- <<< salir del bloque
  END IF;

  -- Actualizar lead
  UPDATE leads
  SET idusuarioresponsable = p_idusuarioresponsable,
      fechasignacion = p_fechasignacion,
      estado = p_estado,
      modificado = NOW()
  WHERE idlead = p_idlead;

  -- Insertar registro en seguimiento automáticamente
  IF ROW_COUNT() > 0 THEN
    INSERT INTO seguimientos (idlead, idetapa, fecha, comentarios, creado)
    VALUES (p_idlead, p_idetapa, CURDATE(), CONCAT('Lead movido a etapa ID: ', p_idetapa), NOW());

    SET p_exito = TRUE;
  ELSE
    SET p_exito = FALSE;
  END IF;
END proc_block $$

DELIMITER ;
