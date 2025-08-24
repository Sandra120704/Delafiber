DELIMITER $$

CREATE PROCEDURE sp_registrar_persona(
  IN p_apellidos VARCHAR(100),
  IN p_nombres VARCHAR(100),
  IN p_telprimario VARCHAR(9),
  IN p_telalternativo VARCHAR(9),
  IN p_email VARCHAR(100),
  IN p_direccion TEXT,
  IN p_referencia TEXT,
  IN p_iddistrito INT,
  OUT p_idpersona INT
)
BEGIN 
  INSERT INTO personas(
    apellidos, nombres, telprimario, telalternativo, email, direccion, referencia, iddistrito, creado
  ) VALUES (
    p_apellidos, p_nombres, p_telprimario, p_telalternativo, p_email, p_direccion, p_referencia, p_iddistrito, NOW()
  );
  SET p_idpersona = LAST_INSERT_ID();
END $$

DELIMITER ;