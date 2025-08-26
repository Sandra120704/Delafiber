DELIMITER $$

CREATE PROCEDURE sp_registrar_persona (
  IN p_nombres VARCHAR(100),
  IN p_apellidos VARCHAR(100),
  IN p_email VARCHAR(100),
  IN p_telprimario VARCHAR(20),
  IN p_telsecundario VARCHAR(20),
  IN p_direccion VARCHAR(200),
  IN p_distrito VARCHAR(100),
  OUT p_idpersona INT
)
BEGIN
  INSERT INTO personas (
    nombres, apellidos, email, telprimario, telsecundario, direccion, distrito, creado
  ) VALUES (
    p_nombres, p_apellidos, p_email, p_telprimario, p_telsecundario, p_direccion, p_distrito, NOW()
  );

  SET p_idpersona = LAST_INSERT_ID();
END $$

DELIMITER ;
