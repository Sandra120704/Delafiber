DELIMITER $$

CREATE PROCEDURE sp_registrar_lead (
  IN p_iddifusion INT,
  IN p_idpersona INT,
  IN p_idusuarioregistro INT,
  IN p_idusuarioresponsable INT,
  IN p_fechasignacion DATE,
  OUT p_idlead INT
)
BEGIN
  DECLARE personaExiste INT;

  -- Verificar si la persona existe
  SELECT COUNT(*) INTO personaExiste FROM personas WHERE idpersona = p_idpersona;

  IF personaExiste = 1 THEN
    INSERT INTO leads (
      iddifusion, idpersona, idusuarioregistro, idusuarioresponsable, fechasignacion
    ) VALUES (
      p_iddifusion, p_idpersona, p_idusuarioregistro, p_idusuarioresponsable, p_fechasignacion
    );

    SET p_idlead = LAST_INSERT_ID();
  ELSE
    SET p_idlead = NULL;
  END IF;
END $$

DELIMITER ;