DELIMITER $$

DROP PROCEDURE IF EXISTS sp_registrar_persona $$

CREATE PROCEDURE sp_registrar_persona (
  IN p_apellidos VARCHAR(100),
  IN p_nombres VARCHAR(100),
  IN p_telprimario VARCHAR(20),
  IN p_telalternativo VARCHAR(20),
  IN p_email VARCHAR(100),
  IN p_direccion VARCHAR(200),
  IN p_referencia VARCHAR(200),
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
